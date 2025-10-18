<?php

if(!defined('ABSPATH')){exit;}

function add_redirect_rules_metabox() {
    add_meta_box(
        'redirect_rules_metabox',
        __('Redirect Rules Options', TEXTDOMAIN),
        'render_redirect_rules_metabox',
        'redirect-rules',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_redirect_rules_metabox');

function render_redirect_rules_metabox($post) {
    wp_nonce_field('redirect_rules_metabox', 'redirect_rules_nonce');
    $context = Timber::context();
    $context = array_merge($context, array(
        'old_url' => esc_url(get_post_meta($post->ID, 'old_url', true)),
        'new_url' => esc_url(get_post_meta($post->ID, 'new_url', true)),
        'code' => get_post_meta($post->ID, 'code', true),
        'protocol' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
        'BLOGINFO_JUST_DOMAIN' => BLOGINFO_JUST_DOMAIN,
    ));
    Timber::render( 'dashboard/redirects-meta.twig', $context);
}

function clear_redirect_rules_cache() {
    delete_transient('redirect_rules' . LANG_SUFFIX);
}

add_action('save_post', 'save_redirect_rules');
function save_redirect_rules($post_id) {
    if (!isset($_POST['redirect_rules_nonce']) || !wp_verify_nonce($_POST['redirect_rules_nonce'], 'redirect_rules_metabox')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $old_url = esc_url_raw($_POST['old_url']);
    // fix current protocol in old_url link
    $old_url = str_replace(['https://', 'http://'], $protocol.'://', $old_url);
    $new_url = esc_url_raw($_POST['new_url']);
    // fix current protocol in new_url link
    $new_url_fixed = str_replace(['https://', 'http://'], $protocol.'://', $new_url);
    $code = absint($_POST['code']);

    if (!$old_url || !$new_url || $old_url === $new_url_fixed) {
        if (get_post($post_id)) {
            wp_delete_post($post_id, true);
        }
        wp_die(__('Invalid redirect rule: Old URL and New URL must be different and not empty.', TEXTDOMAIN));
    }

    $existing_rules = new WP_Query([
        'post_type' => 'redirect-rules',
        'post__not_in' => [$post_id],
        'meta_query' => [
            [
                'key' => 'old_url',
                'value' => $old_url,
                'compare' => '='
            ]
        ]
    ]);

    if ($existing_rules->found_posts > 0) {
        if (get_post($post_id)) {
            wp_delete_post($post_id, true);
        }
        wp_die(__('Conflict detected: A redirect rule with this Old URL already exists.', TEXTDOMAIN));
    }

    // check if old url contains current wordpress domain, if not - wp_die
    if (strpos($old_url, BLOGINFO_JUST_DOMAIN) === false) {
        if (get_post($post_id)) {
            wp_delete_post($post_id, true);
        }
        wp_die(__('Invalid redirect rule: Old URL must contain the current WordPress domain.', TEXTDOMAIN));
    }

    update_post_meta($post_id, 'old_url', $old_url);
    update_post_meta($post_id, 'new_url', $new_url);
    update_post_meta($post_id, 'code', $code);

    // Тимчасово відключаємо хук, щоб уникнути рекурсії
    remove_action('save_post', 'save_redirect_rules');

    $post_update = array(
        'ID'         => $post_id,
        'post_title' => str_replace($protocol.'://', '', $old_url) . ' -> ' . str_replace($protocol.'://', '', $new_url)
    );
    wp_update_post( $post_update );

    // Знову підключаємо хук
    add_action('save_post', 'save_redirect_rules');

    // Очищаємо кеш
    clear_redirect_rules_cache();

}

// Очищення кешу при зміні статусу поста
add_action('transition_post_status', 'clear_redirect_rules_cache_on_status_change', 10, 3);
function clear_redirect_rules_cache_on_status_change($new_status, $old_status, $post) {
    if ($post->post_type === 'redirect-rules') {
        clear_redirect_rules_cache();
    }
}

// Очищення кешу при видаленні поста в кошик
add_action('wp_trash_post', 'clear_redirect_rules_cache', 10, 1);

// Очищення кешу при відновленні поста з кошика
add_action('untrash_post', 'clear_redirect_rules_cache', 10, 1);

// Очищення кешу при видаленні поста назавжди
add_action('before_delete_post', 'clear_redirect_rules_cache', 10, 1);

function get_redirect_rules(){
    if($redirect_rules = get_transient( 'redirect_rules'.LANG_SUFFIX )){
        return $redirect_rules;
    } else {
        $redirect_posts = get_posts([
            'post_type' => 'redirect-rules',
            'numberposts' => -1
        ]);
        $redirect_rules = [];
        if(!empty($redirect_posts)){
            foreach ($redirect_posts as $post) {
                $redirect_rule['old_url'] = get_post_meta($post->ID, 'old_url', true);
                $redirect_rule['new_url'] = get_post_meta($post->ID, 'new_url', true);
                $redirect_rule['code'] = get_post_meta($post->ID, 'code', true);
                $redirect_rules[] = $redirect_rule;
            }
        } else {
            $redirect_rules = ['empty' => true];
        }
        set_transient( 'redirect_rules'.LANG_SUFFIX, $redirect_rules, TRANSIENTS_TIME );
        return $redirect_rules;
    }
}

add_action('template_redirect', function () {
    $redirect_rules = get_redirect_rules();
    if (!empty($redirect_rules) && !isset($redirect_rules['empty'])) {
        $REQUEST_URI = isset($_SERVER['REQUEST_URI']) ? rtrim($_SERVER['REQUEST_URI'], '/') . '/' : '/';
        foreach ($redirect_rules as $rule) {
            if (!$rule['old_url'] || !$rule['new_url'] || !$rule['code']) {
                continue; // Пропускаємо цей запис, якщо якесь з полів відсутнє
            }
            // Видаляємо протокол і домен з old_url
            $old = str_replace(["https://", "http://", parse_url(get_bloginfo('url'), PHP_URL_HOST)], "", rtrim($rule['old_url'], '/') . '/');
            if ($REQUEST_URI === $old) {
                wp_redirect($rule['new_url'], $rule['code']);
                exit;
            }
        }
    }
});
