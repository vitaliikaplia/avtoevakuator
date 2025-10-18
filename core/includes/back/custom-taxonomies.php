<?php

if(!defined('ABSPATH')){exit;}

register_taxonomy(
    'pattern_categories',
    'patterns',
    array(
        'hierarchical' => true,
        'label' => __('Pattern categories', TEXTDOMAIN),
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => false, 'with_front' => false ),
        'public' => false,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'labels' => array(
            'name' => __('Pattern categories', TEXTDOMAIN),
            'singular_name' => __('Pattern category', TEXTDOMAIN),
            'search_items' => __('Search categories', TEXTDOMAIN),
            'all_items' => __('All categories', TEXTDOMAIN),
            'edit_item' => __('Edit category', TEXTDOMAIN),
            'update_item' => __('Update category', TEXTDOMAIN),
            'add_new_item' => __('Add new category', TEXTDOMAIN),
            'new_item_name' => __('New category name', TEXTDOMAIN),
            'menu_name' => __('Categories', TEXTDOMAIN),
        ),
    )
);
