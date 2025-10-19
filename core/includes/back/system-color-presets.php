<?php

if(!defined('ABSPATH')){exit;}

if(is_admin() && is_user_logged_in()){

    /**
     * Get system color presets from SCSS variables and cache them.
     *
     * @return array Array of hex color codes.
     */
    function get_system_color_presets(){
        $cached_colors = get_transient('system_color_presets');
        if (false !== $cached_colors) {
            return $cached_colors;
        }

        $colors = [];
        $scss_file_path = get_theme_file_path('/assets/scss/_variables.scss');

        if (file_exists($scss_file_path)) {
            $scss_content = file_get_contents($scss_file_path);
            // Regex to find variables starting with --color-
            preg_match_all('/--color-[\w-]+:\s*(.*?);/i', $scss_content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $value) {
                    $value = trim($value);
                    if (strpos($value, '#') === 0) {
                        // It's a hex color
                        $colors[] = strtoupper($value);
                    } elseif (strpos($value, 'rgba') === 0) {
                        // It's an rgba color, e.g., rgba(255,255,255,0.8)
                        preg_match('/rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $value, $rgb_matches);
                        if (count($rgb_matches) === 4) {
                            $r = intval($rgb_matches[1]);
                            $g = intval($rgb_matches[2]);
                            $b = intval($rgb_matches[3]);
                            $colors[] = sprintf('#%02X%02X%02X', $r, $g, $b);
                        }
                    }
                }
            }
        }

        $colors = array_values(array_unique($colors));
        sort($colors);

        set_transient('system_color_presets', $colors, DAY_IN_SECONDS);

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

//pr(get_system_color_presets());