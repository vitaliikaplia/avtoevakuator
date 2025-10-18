<?php

if(!defined('ABSPATH')){exit;}

function get_custom_language_selector_flags($show_current = false, $for_menu = false) {
    $languages = icl_get_languages('skip_missing=0&orderby=KEY');
    $output = '';

    if (!empty($languages)) {
        foreach ($languages as $l) {
            if ($show_current || !$l['active']) {
                $url = $l['url'];
                $native_name = $l['native_name'];
                $output .= '<li ';
                if ($l['active']){
                    $output .= 'class="active"';
                }
                $output .= '><a href="' . $url . '" title="' . $native_name . '">';
                if($for_menu){
                    $output .= '<span class="itemWrapper withoutIcon"><span class="title">';
                }
                $output .= $native_name;
                if($for_menu){
                    $output .= '</span></span>';
                }
                $output .= '</a></li>';
            }
        }
    }
    return $output;
}

function get_current_language_native_name() {
    $languages = icl_get_languages('skip_missing=0&orderby=KEY');
    if (!empty($languages)) {
        foreach ($languages as $l) {
            if ($l['active']) {
                return $l['native_name'];
            }
        }
    }
}
