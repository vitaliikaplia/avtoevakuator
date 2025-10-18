<?php

if(!defined('ABSPATH')){exit;}

/** enable thumbnails */
add_theme_support('post-thumbnails', ['post', 'page']);

function AddThumbColumn($cols) {
    $cols['thumbnail'] = __('Thumbnail', TEXTDOMAIN);
    return $cols;
}

function AddThumbValue($column_name, $post_id) {
    $width = 80;
    $height = 80;
    if ($column_name === 'thumbnail') {
        $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
        if ($thumbnail_id) {
            $thumb = wp_get_attachment_image($thumbnail_id, [$width, $height], true);
            echo $thumb ?: __('None', TEXTDOMAIN);
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
}

add_filter('manage_post_posts_columns', 'AddThumbColumn');
add_action('manage_post_posts_custom_column', 'AddThumbValue', 10, 2);

add_filter('manage_page_posts_columns', 'AddThumbColumn');
add_action('manage_page_posts_custom_column', 'AddThumbValue', 10, 2);
