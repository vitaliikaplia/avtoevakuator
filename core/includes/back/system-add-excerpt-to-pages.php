<?php

if(!defined('ABSPATH')){exit;}

function vitaliikaplia_add_excerpt_to_pages() {
    add_post_type_support('page', 'excerpt');
}
add_action('init', 'vitaliikaplia_add_excerpt_to_pages');

function vitaliikaplia_enable_page_excerpt_editor() {
    if (get_current_screen()->id === 'page') {
        remove_post_type_support('page', 'editor');
        add_post_type_support('page', 'editor');
    }
}
add_action('current_screen', 'vitaliikaplia_enable_page_excerpt_editor');
