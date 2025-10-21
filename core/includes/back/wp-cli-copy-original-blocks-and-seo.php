<?php

if(!defined('ABSPATH')){exit;}

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class Copy_Original_Blocks_And_SEO extends WP_CLI_Command
    {

        private $original_lang;
        private $translated_langs = array();

        public function __invoke()
        {
            WP_CLI::log('🔄 Почнемо копіювання блоків та SEO...');

            $this->get_original_language();
            $this->get_translated_languages();

            if (empty($this->original_lang)) {
                WP_CLI::error('Не визначена оригінальна мова');
            }

            if (empty($this->translated_langs)) {
                WP_CLI::error('Не знайдено мов для перекладу');
            }

            WP_CLI::log(sprintf('🌍 Оригінальна мова: %s', $this->original_lang));
            WP_CLI::log(sprintf('📝 Мови перекладу: %s', implode(', ', $this->translated_langs)));

            $args = array(
                'post_type' => 'page',
                'numberposts' => -1,
                'post_status' => 'publish',
            );

            $posts = get_posts($args);

            if (empty($posts)) {
                WP_CLI::warning('Не знайдено постів');
                return;
            }

            WP_CLI::log(sprintf('📋 Знайдено %d постів', count($posts)));

            $progress = WP_CLI\Utils\make_progress_bar('Обробка постів', count($posts));

            foreach ($posts as $post) {
                $this->process_post($post);
                $progress->tick();
            }

            $progress->finish();
            WP_CLI::success('✅ Готово!');
        }

        private function get_original_language()
        {
            if (function_exists('icl_get_default_language')) {
                $this->original_lang = icl_get_default_language();
            }
        }

        private function get_translated_languages()
        {
            if (function_exists('icl_get_languages')) {
                $languages = icl_get_languages();
                foreach ($languages as $lang_code => $lang_data) {
                    if ($lang_code !== $this->original_lang) {
                        $this->translated_langs[] = $lang_code;
                    }
                }
            }
        }

        private function process_post($post)
        {
            $post_id = $post->ID;
            $post_lang = $this->get_post_language($post_id);

            if (!$post_lang) {
                WP_CLI::log(sprintf('⏭️  ID %d: Не визначена мова', $post_id));
                return;
            }

            $blocks = parse_blocks($post->post_content);
            $yoast_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
            $yoast_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);

            if (empty($blocks) && empty($yoast_title) && empty($yoast_desc)) {
                return;
            }

            $trid = $this->get_trid($post_id);

            if (!$trid) {
                return;
            }

            $translations = $this->get_translations_by_trid($trid);

            if (empty($translations)) {
                return;
            }

            foreach ($translations as $lang => $translated_post_id) {
                if ($translated_post_id === $post_id) {
                    continue;
                }

                $this->copy_blocks_to_post($translated_post_id, $blocks);
                $this->copy_yoast_seo($translated_post_id, $yoast_title, $yoast_desc);

                WP_CLI::log(sprintf('✓ ID %d (%s) → ID %d (%s): Скопійовано', $post_id, $post_lang, $translated_post_id, $lang));
            }
        }

        private function get_post_language($post_id)
        {
            global $wpdb;

            $lang = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type IN ('post_page', 'page')",
                    $post_id
                )
            );

            return $lang;
        }

        private function get_trid($post_id)
        {
            global $wpdb;

            $trid = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type IN ('post_page', 'page')",
                    $post_id
                )
            );

            return $trid;
        }

        private function get_translations_by_trid($trid)
        {
            global $wpdb;

            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT language_code, element_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d AND element_type IN ('post_page', 'page')",
                    $trid
                )
            );

            $translations = array();
            foreach ($results as $result) {
                $translations[$result->language_code] = $result->element_id;
            }

            return $translations;
        }

        private function copy_blocks_to_post($post_id, $blocks)
        {
            global $wpdb;

            $blocks_html = '';

            foreach ($blocks as $block) {
                $blocks_html .= serialize_block($block);
            }

            $wpdb->update(
                $wpdb->posts,
                array('post_content' => $blocks_html),
                array('ID' => $post_id),
                array('%s'),
                array('%d')
            );
        }

        private function copy_yoast_seo($post_id, $title, $desc)
        {
            if ($title) {
                update_post_meta($post_id, '_yoast_wpseo_title', $title);
            }

            if ($desc) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', $desc);
            }
        }
    }

    WP_CLI::add_command('copy-original-blocks-and-seo-fields-in-pages', 'Copy_Original_Blocks_And_SEO');

}
