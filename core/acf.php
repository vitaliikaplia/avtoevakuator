<?php

if(!defined('ABSPATH')){exit;}

if (class_exists('ACF')) {
    if(get_option('hide_acf')){
        add_filter('acf/settings/show_admin', '__return_false');
    }
    add_filter('acf/settings/save_json', 'my_acf_json_save_point');
    function my_acf_json_save_point( $path ) {
        // update path
        $path = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $path;
    }
    add_filter('acf/settings/load_json', 'my_acf_json_load_point');
    function my_acf_json_load_point( $paths ) {
        // remove original path (optional)
        unset($paths[0]);
        // append path
        $paths[] = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $paths;
    }

    // Options pages for ACF
    acf_add_options_sub_page(array(
        'page_title'  => __('Header', TEXTDOMAIN),
        'menu_title'  => __('Header', TEXTDOMAIN),
        'slug' => 'header',
        'parent_slug' => 'themes.php',
        'updated_message' => __('Header options updated', TEXTDOMAIN),
        'update_button' => __('Update', TEXTDOMAIN),
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Footer', TEXTDOMAIN),
        'menu_title'  => __('Footer', TEXTDOMAIN),
        'slug' => 'footer',
        'parent_slug' => 'themes.php',
        'updated_message' => __('Footer options updated', TEXTDOMAIN),
        'update_button' => __('Update', TEXTDOMAIN),
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Other', TEXTDOMAIN),
        'menu_title'  => __('Other', TEXTDOMAIN),
        'slug' => 'other',
        'parent_slug' => 'themes.php',
        'updated_message' => __('Other options updated', TEXTDOMAIN),
        'update_button' => __('Update', TEXTDOMAIN)
    ));
}
