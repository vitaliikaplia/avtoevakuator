<?php

if(!defined('ABSPATH')){exit;}

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class Translate_SEO_Titles_And_Descriptions extends WP_CLI_Command
    {

        private $original_lang;
        private $translated_langs = array();
        private $specific_post_id = null;

        /**
         * @synopsis [<post_id>]
         */
        public function __invoke($args = array(), $assoc_args = array())
        {
            WP_CLI::log('🌐 Почнемо переклад SEO тайтлів та описів...');

            if (!empty($args) && is_numeric($args[0])) {
                $this->specific_post_id = (int)$args[0];
                WP_CLI::log(sprintf('📌 Обробка конкретного посту: ID %d', $this->specific_post_id));
            }

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

            if ($this->specific_post_id) {
                $args['p'] = $this->specific_post_id;
            }

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
            WP_CLI::success('✅ Переклад завершено!');
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
            $yoast_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
            $yoast_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);

            if (empty($yoast_title) && empty($yoast_desc)) {
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
                if ($lang === $this->original_lang || $translated_post_id === $post_id) {
                    continue;
                }

                if ($yoast_title) {
                    $translated_title = ai_translate_content($yoast_title, $lang);
                    $this->update_post_meta_direct($translated_post_id, '_yoast_wpseo_title', $translated_title);
                }

                if ($yoast_desc) {
                    $translated_desc = ai_translate_content($yoast_desc, $lang);
                    $this->update_post_meta_direct($translated_post_id, '_yoast_wpseo_metadesc', $translated_desc);
                }

                WP_CLI::log(sprintf('✓ ID %d → ID %d (%s): SEO переведено', $post_id, $translated_post_id, $lang));
            }
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

        private function update_post_meta_direct($post_id, $meta_key, $meta_value)
        {
            global $wpdb;

            $existing = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
                    $post_id,
                    $meta_key
                )
            );

            if ($existing) {
                $wpdb->update(
                    $wpdb->postmeta,
                    array('meta_value' => $meta_value),
                    array('post_id' => $post_id, 'meta_key' => $meta_key)
                );
            } else {
                $wpdb->insert(
                    $wpdb->postmeta,
                    array(
                        'post_id' => $post_id,
                        'meta_key' => $meta_key,
                        'meta_value' => $meta_value
                    )
                );
            }
        }
    }

    WP_CLI::add_command('translate-seo-titles-and-descriptions', 'Translate_SEO_Titles_And_Descriptions');

}
