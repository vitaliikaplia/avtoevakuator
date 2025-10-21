<?php

if(!defined('ABSPATH')){exit;}

/** custom editor css styles */
function my_theme_add_editor_styles() {
    add_editor_style( TEMPLATE_DIRECTORY_URL . '/assets/css/tinymce.min.css?' . ASSETS_VERSION );
}
add_action( 'after_setup_theme', 'my_theme_add_editor_styles' );

/** custom editor buttons lines */
function my_mce_buttons( $buttons ) {
    $option = 'bold,italic,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,wp_adv,undo,redo,spellchecker,fullscreen';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons', 'my_mce_buttons' );

function my_mce_buttons_2( $buttons ) {
    $option = 'outdent,indent,blockquote,removeformat,charmap,forecolor,backcolor,table,code';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_buttons_3( $buttons ) {
    $option = 'formatselect,styleselect';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons_3', 'my_mce_buttons_3' );

/** adding custom style formats and fix important mce configs */
function my_mce_before_init_insert_formats( $init_array ) {
    $mce_style_prefix = 'mce-';
    $style_formats = array(
        array(
            'title' => __("Fonts", TEXTDOMAIN),
            'items' => array(
                array( 'title' => 'Montserrat', 'inline' => 'span', 'classes' => $mce_style_prefix.'font-montserrat' ),
                array( 'title' => 'Ubuntu', 'inline' => 'span', 'classes' => $mce_style_prefix.'font-ubuntu' ),
            )
        ),
        array(
            'title' => __("Text sizes", TEXTDOMAIN),
            'items' => array(
                array( 'title' => '10px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-10' ),
                array( 'title' => '11px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-11' ),
                array( 'title' => '12px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-12' ),
                array( 'title' => '13px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-13' ),
                array( 'title' => '14px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-14' ),
                array( 'title' => '15px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-15' ),
                array( 'title' => '16px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-16' ),
                array( 'title' => '17px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-17' ),
                array( 'title' => '18px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-18' ),
                array( 'title' => '19px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-19' ),
                array( 'title' => '20px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-20' ),
                array( 'title' => '22px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-22' ),
                array( 'title' => '24px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-24' ),
                array( 'title' => '26px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-26' ),
                array( 'title' => '28px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-28' ),
                array( 'title' => '32px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-32' ),
                array( 'title' => '36px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-36' ),
                array( 'title' => '42px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-42' ),
                array( 'title' => '48px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-48' ),
                array( 'title' => '50px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-50' ),
                array( 'title' => '52px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-52' ),
                array( 'title' => '54px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-54' ),
                array( 'title' => '56px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-56' ),
                array( 'title' => '58px', 'inline' => 'span', 'classes' => $mce_style_prefix.'text-size ' . $mce_style_prefix.'text-size-58' ),
            )
        ),
        array(
            'title' => __("Text colors", TEXTDOMAIN),
            'items' => array(
                array( 'title' => __('Accent Teal', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-accent-teal' ),
                array( 'title' => __('Accent Orange', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-accent-orange' ),
                array( 'title' => __('Accent Yellow', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-accent-yellow' ),
                array( 'title' => __('Text White', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-text-white' ),
                array( 'title' => __('White 80%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-white-80' ),
                array( 'title' => __('White 60%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-white-60' ),
                array( 'title' => __('White 40%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-white-40' ),
                array( 'title' => __('BG Main', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-bg-main' ),
                array( 'title' => __('BG Secondary', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-bg-secondary' ),
                array( 'title' => __('BG Darkest', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'text-color-bg-darkest' ),
            )
        ),
        array(
            'title' => __("Background text colors", TEXTDOMAIN),
            'items' => array(
                array( 'title' => __('Accent Teal', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-accent-teal' ),
                array( 'title' => __('Accent Orange', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-accent-orange' ),
                array( 'title' => __('Accent Yellow', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-accent-yellow' ),
                array( 'title' => __('Text White', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-text-white' ),
                array( 'title' => __('White 80%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-white-80' ),
                array( 'title' => __('White 60%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-white-60' ),
                array( 'title' => __('White 40%', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-white-40' ),
                array( 'title' => __('BG Main', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-bg-main' ),
                array( 'title' => __('BG Secondary', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-bg-secondary' ),
                array( 'title' => __('BG Darkest', TEXTDOMAIN), 'inline' => 'span', 'classes' => $mce_style_prefix.'background-text-color ' . $mce_style_prefix.'background-text-color-bg-darkest' ),
            )
        ),
    );
    $init_array['wpautop'] = false;
    $init_array['tadv_noautop'] = true;
    $init_array['style_formats'] = json_encode( $style_formats );
    return $init_array;
}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

/** editor plugins */
function mce_register_plugins( $plugin_array ) {
    $plugin_array['table'] = TEMPLATE_DIRECTORY_URL . 'assets/js/mce/table.min.js';
    $plugin_array['code'] = TEMPLATE_DIRECTORY_URL . 'assets/js/mce/code.min.js';
    return $plugin_array;
}
add_filter( 'mce_external_plugins', 'mce_register_plugins' );
