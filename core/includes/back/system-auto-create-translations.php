<?php

if(!defined('ABSPATH')){exit;}

function is_default_language($language_code) {
    return $language_code === apply_filters('wpml_default_language', null);
}

function is_new_post($post) {
    // Перевіряємо чи є переклади
    $translations = apply_filters('wpml_get_element_translations', null,
        apply_filters('wpml_element_trid', null, $post->ID, 'post_' . $post->post_type)
    );
    return empty($translations) || count((array)$translations) <= 1;
}

function auto_create_wpml_translations($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    if( ! get_option('create_translation_duplicates_while_adding_posts') ) return;

    static $creating_translation = false;
    if ($creating_translation) return;

    // Перевіряємо чи це публікація
    if ($post->post_status !== 'publish') return;

    // Перевіряємо чи це дозволений тип поста
    $allowed_post_types = ['post', 'page', 'patterns'];
    if (!in_array($post->post_type, $allowed_post_types)) return;

    // Перевіряємо чи встановлений WPML
    $languages = apply_filters('wpml_active_languages', null);
    if (!$languages) return;

    // Перевіряємо чи це оригінальна мова
    $current_language = apply_filters('wpml_post_language_details', null, $post_id);
    if (!$current_language || !isset($current_language['language_code'])) return;

    // Пропускаємо якщо це не оригінальна мова
    if (!is_default_language($current_language['language_code'])) return;

    // Перевіряємо чи це новий пост
    if (!is_new_post($post)) return;

    $creating_translation = true;

    foreach ($languages as $language) {
        if ($language['code'] === $current_language['language_code']) continue;
        create_post_translation($post_id, $language['code']);
    }

    $creating_translation = false;
}

function create_post_translation($post_id, $language_code) {
    $post = get_post($post_id);

    $args = array(
        'post_type' => $post->post_type,
        'post_status' => $post->post_status,
        'post_title' => $post->post_title . ' (' . $language_code . ')',
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_parent' => $post->post_parent,
        'menu_order' => $post->menu_order
    );

    $translated_post_id = wp_insert_post($args);

    if ($translated_post_id) {
        copy_post_meta($post_id, $translated_post_id);

        do_action('wpml_set_element_language_details', [
            'element_id' => $translated_post_id,
            'element_type' => 'post_' . $post->post_type,
            'trid' => apply_filters('wpml_element_trid', null, $post_id, 'post_' . $post->post_type),
            'language_code' => $language_code,
            'source_language_code' => apply_filters('wpml_post_language_details', null, $post_id)['language_code']
        ]);

        copy_post_taxonomies($post_id, $translated_post_id, $language_code);

        return $translated_post_id;
    }

    return false;
}

function copy_post_taxonomies($source_id, $target_id, $language_code) {
    $taxonomies = get_object_taxonomies(get_post_type($source_id));

    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($source_id, $taxonomy);

        if (!empty($terms) && !is_wp_error($terms)) {
            $translated_term_ids = [];

            foreach ($terms as $term) {
                $translated_term_id = apply_filters('wpml_object_id', $term->term_id, $taxonomy, false, $language_code);
                if ($translated_term_id) {
                    $translated_term_ids[] = (int)$translated_term_id;
                }
            }

            if (!empty($translated_term_ids)) {
                wp_set_object_terms($target_id, $translated_term_ids, $taxonomy);
            }
        }
    }
}

function copy_post_meta($source_id, $target_id) {
    global $wpdb;

    $post_meta = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
        $source_id
    ));

    if ($post_meta) {
        $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES ";
        $values = array();

        foreach ($post_meta as $meta) {
            if ($meta->meta_key === '_wp_old_slug') continue;
            if (strpos($meta->meta_key, 'wpml') === 0) continue;
            if (strpos($meta->meta_key, '_icl_') === 0) continue;

            $values[] = $wpdb->prepare(
                "(%d, %s, %s)",
                $target_id,
                $meta->meta_key,
                $meta->meta_value
            );
        }

        if ($values) {
            $wpdb->query($sql_query . implode(',', $values));
        }
    }
}

add_action('wp_insert_post', 'auto_create_wpml_translations', 10, 2);
