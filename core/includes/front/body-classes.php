<?php

if(!defined('ABSPATH')){exit;}

function custom_body_classes($classes) {

    global $post;

    $classes[] = 'preload';

    if ( !empty($post) and post_password_required( $post->ID ) ) {
        $classes[] = 'password-protected';
    }

    $classes[] = 'headroom--top';

    return $classes;
}

add_filter('body_class', 'custom_body_classes');
