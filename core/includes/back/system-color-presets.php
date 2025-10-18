<?php

if(!defined('ABSPATH')){exit;}

if(is_admin() && is_user_logged_in()){

    function get_system_color_presets(){
        $favorite_colors = get_field('favorite_colors', 'options');
        $colors = [];
        if(!empty($favorite_colors)){
            foreach ($favorite_colors as $color) {
                if(!empty($color['color']) && !empty($color['enable_color'])){
                    $colors[] = $color['color'];
                }
            }
        }
        return $colors;
    }

    // acf
    function change_acf_color_picker() {
        $colors_for_acf = array();
        foreach(get_system_color_presets() as $c){
            array_push($colors_for_acf, $c);
        }
        echo Timber::compile( 'dashboard/acf-colors.twig', array(
            'colors'=>json_encode($colors_for_acf)
        ));
    }
    add_action( 'acf/input/admin_head', 'change_acf_color_picker' );

    // tinymce
    function my_tiny_mce_custom_colors($mceInit) {
        $colors_for_tiny_mce = "";
        foreach(get_system_color_presets() as $k => $v){
            $colors_for_tiny_mce .= '"'.str_replace('#','',$v).'", "Custom color '.($k+1).'", ';
        }
        // build colour grid default+custom colors
        $mceInit['textcolor_map'] = '['.$colors_for_tiny_mce.']';
        // enable 6th row for custom colours in grid
        $mceInit['textcolor_rows'] = 6;
        return $mceInit;
    }
    add_filter('tiny_mce_before_init', 'my_tiny_mce_custom_colors');

}
