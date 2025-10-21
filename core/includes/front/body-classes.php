<?php

if(!defined('ABSPATH')){exit;}

function custom_body_classes($classes) {

    global $post;

    $classes[] = 'preload';

    if ( !empty($post) and post_password_required( $post->ID ) ) {
        $classes[] = 'password-protected';
    }

    $classes[] = 'headroom--top';

    if(is_page()){
        $custom_fields = cache_fields($post->ID);
        if(!empty($custom_fields['header_padding'])){
            $classes[] = 'header-padding';
        }
    }

    return $classes;
}

add_filter('body_class', 'custom_body_classes');
