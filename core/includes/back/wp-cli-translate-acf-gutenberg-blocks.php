<?php

if(!defined('ABSPATH')){exit;}

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class Translate_ACF_Gutenberg_Blocks extends WP_CLI_Command {

        private $original_lang;
        private $translated_langs = array();
        private $acf_fields_map = array();

        public function __invoke($args = array(), $assoc_args = array()) {
            WP_CLI::log('🔄 Почнемо переклад ACF блоків...');

            $this->get_original_language();
            $this->get_translated_languages();

            if (empty($this->original_lang)) {
                WP_CLI::error('Не визначена оригінальна мова');
            }

            if (empty($this->translated_langs)) {
                WP_CLI::error('Не знайдено мов для перекладу');
            }

            $this->load_acf_fields_map();

            if (empty($this->acf_fields_map)) {
                WP_CLI::error('Не вдалося завантажити ACF поля');
            }

            WP_CLI::log(sprintf('🌍 Оригінальна мова: %s', $this->original_lang));
            WP_CLI::log(sprintf('📝 Мови перекладу: %s', implode(', ', $this->translated_langs)));

            $post_ids = array();

            if (!empty($args[0])) {
                $post_ids = array((int)$args[0]);
                WP_CLI::log(sprintf('📄 Обробка сторінки ID: %d', $post_ids[0]));
            } else {
                $post_args = array(
                    'post_type' => 'page',
                    'numberposts' => -1,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                );

                $post_ids = get_posts($post_args);

                if (empty($post_ids)) {
                    WP_CLI::warning('Не знайдено постів');
                    return;
                }

                WP_CLI::log(sprintf('📋 Знайдено %d постів', count($post_ids)));
            }

            $progress = WP_CLI\Utils\make_progress_bar('Обробка постів', count($post_ids));

            foreach ($post_ids as $post_id) {
                $this->process_post($post_id);
                $progress->tick();
            }

            $progress->finish();
            WP_CLI::success('✅ Переклад завершено!');
        }

        private function get_original_language() {
            if (function_exists('icl_get_default_language')) {
                $this->original_lang = icl_get_default_language();
            }
        }

        private function get_translated_languages() {
            if (function_exists('icl_get_languages')) {
                $languages = icl_get_languages();
                foreach ($languages as $lang_code => $lang_data) {
                    if ($lang_code !== $this->original_lang) {
                        $this->translated_langs[] = $lang_code;
                    }
                }
            }
        }

        private function load_acf_fields_map() {
            $acf_json_path = THEME_PATH . DS . 'core' . DS . 'acf-json';

            if (!is_dir($acf_json_path)) {
                return;
            }

            $files = glob($acf_json_path . '/*.json');

            foreach ($files as $file) {
                $data = json_decode(file_get_contents($file), true);
                if (isset($data['fields'])) {
                    $this->map_acf_fields($data['fields']);
                }
            }
        }

        private function map_acf_fields($fields) {
            foreach ($fields as $field) {
                if (isset($field['name']) && !empty($field['name'])) {
                    $this->acf_fields_map[$field['name']] = $field;
                }

                if (isset($field['key'])) {
                    $this->acf_fields_map[$field['key']] = $field;
                }

                if (in_array($field['type'], array('repeater', 'flexible_content', 'group'))) {
                    if (isset($field['sub_fields'])) {
                        $this->map_acf_fields($field['sub_fields']);
                    }
                }
            }
        }

        private function process_post($post_id) {
            $post_lang = $this->get_post_language($post_id);

            if ($post_lang !== $this->original_lang) {
                return;
            }

            $post = get_post($post_id);
            $blocks = parse_blocks($post->post_content);

            if (empty($blocks)) {
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
                if ($lang === $this->original_lang) {
                    continue;
                }

                $this->translate_blocks_for_post($translated_post_id, $blocks, $lang);
            }

            WP_CLI::log(sprintf('✓ ID %d: ACF блоки оброблені', $post_id));
        }

        private function get_post_language($post_id) {
            global $wpdb;

            $lang = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type IN ('post_page', 'page')",
                    $post_id
                )
            );

            return $lang;
        }

        private function get_trid($post_id) {
            global $wpdb;

            $trid = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type IN ('post_page', 'page')",
                    $post_id
                )
            );

            return $trid;
        }

        private function get_translations_by_trid($trid) {
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

        private function translate_blocks_for_post($post_id, $blocks, $target_lang) {
            $translated_blocks = array();

            foreach ($blocks as $block) {
                if (!empty($block['blockName']) && strpos($block['blockName'], 'acf/') === 0) {
                    $block = $this->translate_acf_block($block, $target_lang);
                }

                $translated_blocks[] = $block;
            }

            $serialized_blocks = '';
            foreach ($translated_blocks as $block) {
                $serialized_blocks .= serialize_block($block);
            }

            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array('post_content' => $serialized_blocks),
                array('ID' => $post_id),
                array('%s'),
                array('%d')
            );
        }

        private function translate_acf_block($block, $target_lang) {
            if (!isset($block['attrs']['data'])) {
                return $block;
            }

            $data = $block['attrs']['data'];

            foreach ($data as $field_key => &$value) {
                if ($this->is_translatable_field($field_key)) {
                    $field_type = $this->get_field_type($field_key);

                    if ($field_type === 'repeater' && is_array($value)) {
                        //error_log('Found repeater: ' . $field_key);
                        $value = $this->translate_repeater($value, $target_lang, $field_key);
                    } elseif ($field_type === 'link' && is_array($value)) {
                        if (isset($value['title']) && !empty($value['title'])) {
                            $value['title'] = $this->safe_translate($value['title'], $target_lang);
                        }
                    } elseif (is_string($value) && !empty($value)) {
                        $value = $this->safe_translate($value, $target_lang);
                    } elseif (is_array($value)) {
                        $value = $this->translate_array_recursively($value, $target_lang, $field_key);
                    }
                }
            }

            $block['attrs']['data'] = $data;

            return $block;
        }

        private function translate_repeater($items, $target_lang, $repeater_field_key) {
            foreach ($items as &$item) {
                if (!is_array($item)) {
                    continue;
                }

                foreach ($item as $sub_field_key => &$sub_value) {
                    if ($this->is_translatable_field($sub_field_key)) {
                        $sub_field_type = $this->get_field_type($sub_field_key);

                        if ($sub_field_type === 'link' && is_array($sub_value)) {
                            if (isset($sub_value['title']) && !empty($sub_value['title'])) {
                                $sub_value['title'] = $this->safe_translate($sub_value['title'], $target_lang);
                            }
                            if (isset($sub_value['url']) && !empty($sub_value['url'])) {
                                $sub_value['url'] = $this->localize_url($sub_value['url'], $target_lang);
                            }
                        } elseif (is_string($sub_value) && !empty($sub_value)) {
                            $sub_value = $this->safe_translate($sub_value, $target_lang);
                        } elseif (is_array($sub_value)) {
                            $sub_value = $this->translate_array_recursively($sub_value, $target_lang, $sub_field_key);
                        }
                    }
                }
            }

            return $items;
        }

        private function localize_url($url, $target_lang) {
            if (!function_exists('url_to_postid')) {
                return $url;
            }

            $post_id = url_to_postid($url);

            if (!$post_id) {
                return $url;
            }

            $trid = $this->get_trid($post_id);

            if (!$trid) {
                return $url;
            }

            $translations = $this->get_translations_by_trid($trid);

            if (!isset($translations[$target_lang])) {
                return $url;
            }

            $translated_post_id = $translations[$target_lang];
            $translated_post = get_post($translated_post_id);

            if (!$translated_post) {
                return $url;
            }

            return get_permalink($translated_post_id);
        }

        private function translate_array_recursively($array, $target_lang, $parent_field_key = null) {
            //error_log('translate_array_recursively: parent_field_key=' . ($parent_field_key ?? 'null'));
            //error_log('array_keys: ' . json_encode(array_keys($array)));

            foreach ($array as &$item) {
                if (is_string($item) && !empty($item)) {
                    $item = $this->safe_translate($item, $target_lang);
                } elseif (is_array($item)) {
                    //error_log('Processing array item, keys: ' . json_encode(array_keys($item)));

                    foreach ($item as $sub_key => &$sub_value) {
                        //error_log('Sub-key: ' . $sub_key . ', type: ' . gettype($sub_value));

                        $sub_field_type = $this->get_field_type($sub_key);
                        //error_log('Sub-field type for ' . $sub_key . ': ' . ($sub_field_type ?? 'null'));

                        if ($sub_field_type === 'link' && is_array($sub_value)) {
                            //error_log('Found link field: ' . $sub_key);
                            if (isset($sub_value['title']) && !empty($sub_value['title'])) {
                                //error_log('Translating link title: ' . substr($sub_value['title'], 0, 50));
                                $sub_value['title'] = $this->safe_translate($sub_value['title'], $target_lang);
                            }
                        } elseif (is_string($sub_value) && !empty($sub_value)) {
                            $sub_value = $this->safe_translate($sub_value, $target_lang);
                        } elseif (is_array($sub_value)) {
                            $sub_value = $this->translate_array_recursively($sub_value, $target_lang, $sub_key);
                        }
                    }
                }
            }

            return $array;
        }

        private function safe_translate($text, $target_lang) {
            if (!is_string($text) || strlen($text) < 11) {
                return $text;
            }

            $result = @ai_translate_content($text, $target_lang);

            if (is_wp_error($result)) {
                return $text;
            }

            return (is_string($result) && !empty($result)) ? $result : $text;
        }

        private function is_translatable_field($field_key) {
            if (!isset($this->acf_fields_map[$field_key])) {
                $base_field_key = $this->extract_base_field_name($field_key);
                if ($base_field_key !== $field_key && isset($this->acf_fields_map[$base_field_key])) {
                    //error_log('Field check (from base): ' . $field_key . ' -> ' . $base_field_key);
                    $field = $this->acf_fields_map[$base_field_key];
                    $is_translatable = isset($field['wpml_cf_preferences']) && $field['wpml_cf_preferences'] == 2;
                    //error_log('Result: ' . ($is_translatable ? 'YES' : 'NO'));
                    return $is_translatable;
                }
                //error_log('Field NOT in map: ' . $field_key);
                return false;
            }

            $field = $this->acf_fields_map[$field_key];
            $is_translatable = isset($field['wpml_cf_preferences']) && $field['wpml_cf_preferences'] == 2;
            //error_log('Field check: ' . $field_key . ' (type=' . $field['type'] . ', wpml=' . ($field['wpml_cf_preferences'] ?? 'none') . ') = ' . ($is_translatable ? 'YES' : 'NO'));

            return $is_translatable;
        }

        private function extract_base_field_name($field_key) {
            if (preg_match('/^(\w+)_\d+_(.+)$/', $field_key, $matches)) {
                return $matches[2];
            }
            return $field_key;
        }

        private function get_field_type($field_key) {
            if (!isset($this->acf_fields_map[$field_key])) {
                return null;
            }

            return $this->acf_fields_map[$field_key]['type'];
        }
    }

    WP_CLI::add_command('translate-acf-gutenberg-blocks', 'Translate_ACF_Gutenberg_Blocks');

}
