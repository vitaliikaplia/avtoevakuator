<?php

if(!defined('ABSPATH')){exit;}

function get_custom_options(){
    return array(
        'images'   =>  Array(
            'label' => __('Images', TEXTDOMAIN),
            'title' => __('Resize and optimize media while upload', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'checkbox',
                    'name'          => 'enable_resize_at_upload',
                    'label'         => __("Enable", TEXTDOMAIN),
                    'description'   => __("Enable resizing media while upload", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'select-multiple',
                    'options'       => array (
                        'image/gif' => 'GIF',
                        'image/png' => 'PNG',
                        'image/jpeg' => 'JPEG',
                        'image/jpg' => 'JPG',
                        'image/webp' => 'WEBP',
                    ),
                    'name'         => 'resize_at_upload_formats',
                    'label'         => __("Formats", TEXTDOMAIN),
                    'description'   => __("Resize at upload formats", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_resize_at_upload',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'          => 'range',
                    'name'          => 'resize_upload_width',
                    'tweaks'        => array(
                        'min' => '0',
                        'max' => '4096',
                        'step' => '2',
                        'suffix' => 'px',
                    ),
                    'label'         => __("Width", TEXTDOMAIN),
                    'description'   => __("Resize upload width", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_resize_at_upload',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'          => 'range',
                    'name'          => 'resize_upload_height',
                    'tweaks'        => array(
                        'min' => '0',
                        'max' => '4096',
                        'step' => '2',
                        'suffix' => 'px',
                    ),
                    'label'         => __("Height", TEXTDOMAIN),
                    'description'   => __("Resize upload height", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_resize_at_upload',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'          => 'range',
                    'name'          => 'resize_upload_quality',
                    'tweaks'        => array(
                        'min' => '2',
                        'max' => '100',
                        'step' => '2',
                        'suffix' => '%',
                    ),
                    'label'         => __("Quality", TEXTDOMAIN),
                    'description'   => __("Resize upload quality", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_resize_at_upload',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'          => 'checkbox',
                    'name'         => 'enable_webp_convert',
                    'label'         => __("Enable", TEXTDOMAIN),
                    'description'   => __("Enable WEBP convert", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'range',
                    'name'          => 'webp_convert_quality',
                    'tweaks'        => array(
                        'min' => '2',
                        'max' => '100',
                        'step' => '2',
                        'suffix' => '%',
                    ),
                    'label'         => __("Webp convert quality", TEXTDOMAIN),
                    'description'   => __("Webp convert quality", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_webp_convert',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'smtp'   =>  Array(
            'label' => __('SMTP', TEXTDOMAIN),
            'title' => __('Configure custom SMTP server', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'checkbox',
                    'name'          => 'enable_custom_smtp_server',
                    'label'         => __("Enable", TEXTDOMAIN),
                    'description'   => __("Enable custom SMTP server", TEXTDOMAIN),
                ),
                array (
                    'type'              => 'text',
                    'name'              => 'smtp_host',
                    'label'             => __("SMTP host", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'              => 'number',
                    'name'              => 'smtp_port',
                    'label'             => __("SMTP port", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'              => 'text',
                    'name'              => 'smtp_username',
                    'label'             => __("SMTP username", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'              => 'password',
                    'name'              => 'smtp_password',
                    'label'             => __("SMTP password", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'              => 'text',
                    'name'              => 'smtp_from_name',
                    'label'             => __("SMTP from name", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'              => 'checkbox',
                    'name'              => 'smtp_secure',
                    'label'             => __("Secure SMTP connection", TEXTDOMAIN),
                    'description'       => __("Use SSL for SMTP connection", TEXTDOMAIN),
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_custom_smtp_server',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'custom_code'   =>  Array(
            'label' => __('Custom code', TEXTDOMAIN),
            'title' => __('Custom HTML code for header and footer', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'code',
                    'name'          => 'header_custom_code',
                    'label'         => __("Header custom code", TEXTDOMAIN),
                    'description'   => __("The custom code will be placed inside the header tag", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'code',
                    'name'          => 'after_body_custom_code',
                    'label'         => __("After &#x3C;body&#x3E; custom code", TEXTDOMAIN),
                    'description'   => __("The special code will be placed after the start of the body tag", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'code',
                    'name'          => 'footer_custom_code',
                    'label'         => __("Footer custom code", TEXTDOMAIN),
                    'description'   => __("The special code will be placed before the end of the body tag", TEXTDOMAIN)
                ),
            ),
        ),
        'maintenance'   =>  Array(
            'label' => __('Maintenance', TEXTDOMAIN),
            'title' => __('Maintenance mode for anonymous users', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'checkbox',
                    'name'          => 'enable_maintenance_mode',
                    'label'         => __('Enable', TEXTDOMAIN),
                    'description'   => __('Enable maintenance mode for anonymous users', TEXTDOMAIN),
                ),
                array (
                    'type'          => 'text',
                    'name'          => 'maintenance_mode_title',
                    'label'         => __('Title', TEXTDOMAIN),
                    'description'   => __('Maintenance mode title for anonymous users', TEXTDOMAIN),
                    'localize'      => true,
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_maintenance_mode',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array (
                    'type'          => 'mce',
                    'name'          => 'maintenance_mode_text',
                    'label'         => __('Text', TEXTDOMAIN),
                    'description'   => __('Maintenance mode text for anonymous users', TEXTDOMAIN),
                    'localize'      => true,
                    'conditional_logic' => array(
                        'action' => 'show',
                        'rules' => array(
                            array(
                                'field' => 'enable_maintenance_mode',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'integrations'   =>  Array(
            'label' => __('Integrations', TEXTDOMAIN),
            'title' => __('Integrations with third-party services options', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'password',
                    'name'          => 'google_maps_api_key',
                    'label'         => __("Google Maps API key", TEXTDOMAIN),
                    'description'   => '<a href="https://console.cloud.google.com/apis/credentials" target="_blank">'.__('Google Cloud Console', TEXTDOMAIN).'</a>',
                ),
                array (
                    'type'          => 'password',
                    'name'          => 'telegram_token',
                    'label'         => __("Telegram token", TEXTDOMAIN),
                    'description'   => __("Telegram token to integrate with Telegram bot", TEXTDOMAIN) . ', <a href="https://core.telegram.org/bots#6-botfather" target="_blank">'.__('link', TEXTDOMAIN).'</a>',
                ),
                array (
                    'type'          => 'text',
                    'name'          => 'telegram_chat_id',
                    'label'         => __("Telegram chat ID", TEXTDOMAIN),
                    'description'   => __("Telegram chat ID to integrate with Telegram bot", TEXTDOMAIN) . ', <a href="https://core.telegram.org/bots#6-botfather" target="_blank">'.__('link', TEXTDOMAIN).'</a>',
                ),
            ),
        ),
        'various'   =>  Array(
            'label' => __('Other options', TEXTDOMAIN),
            'title' => __('All other various options', TEXTDOMAIN),
            'fields' => Array(
                array (
                    'type'          => 'checkbox',
                    'name'          => 'disable_all_updates',
                    'label'         => __("Disable all updates", TEXTDOMAIN),
                    'description'   => __("Disable plugins and WordPress core updates", TEXTDOMAIN),
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'delete_child_media',
                    'label'         => __("Delete child media", TEXTDOMAIN),
                    'description'   => __("Delete child media when parent post is deleted", TEXTDOMAIN),
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'enable_html_cache',
                    'label'         => __("Enable HTML cache", TEXTDOMAIN),
                    'description'   => __("Enable HTML page cache for anonymous users", TEXTDOMAIN),
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'enable_minify',
                    'label'         => __("Enable minify", TEXTDOMAIN),
                    'description'   => __("Enable HTML minifier on frontend", TEXTDOMAIN),
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'hide_acf',
                    'label'         => __("Hide ACF", TEXTDOMAIN),
                    'description'   => __("Hide Advanced Custom Fields from Dashboard", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'parse_all_pages_blocks_as_gutenberg_patterns',
                    'label'         => __("Pages to patterns", TEXTDOMAIN),
                    'description'   => __("Parse all pages blocks as Gutenberg patterns", TEXTDOMAIN)
                ),
                array (
                    'type'          => 'checkbox',
                    'name'          => 'create_translation_duplicates_while_adding_posts',
                    'label'         => __("Translation duplicates", TEXTDOMAIN),
                    'description'   => __("Create translation duplicates while adding posts", TEXTDOMAIN)
                ),
            ),
        ),
    );
}

global $pagenow;
if(is_admin() && $pagenow == "options-general.php" && !empty($_GET['page'])){
    require_once ABSPATH . "wp-includes/class-wp-editor.php";
}

add_action('admin_menu', function() {
    foreach (get_custom_options() as $key=>$value) {
        add_submenu_page(
            'options-general.php', // вказуємо null, щоб сторінка не зʼявлялась у підменю
            $value['label'],
            $value['label'],
            'manage_options',
            $key,
            function() use ($value, $key) {
                echo '<div class="wrap">';
                echo '<h1>' . (!empty($value['title']) ? $value['title'] : $value['label']).'</h1>';
                echo '<form method="post" action="options.php" class="custom-options-form">';
                if(!empty($value['description'])){
                    echo '<p>'.$value['description'].'</p>';
                }
                settings_fields($key.'_settings');
                echo Timber::compile( 'dashboard/options.twig', array(
                    'options' => $value['fields'],
                ));
                submit_button();
                echo '</form>';
                echo '</div>';
            }
        );
    }
});

add_action('admin_init', function() {
    foreach (get_custom_options() as $key=>$value) {
        foreach ($value['fields'] as $field) {
            register_setting($key.'_settings', $field['name']);
        }
    }
});

if( defined('ICL_LANGUAGE_CODE' ) ){
    add_action( 'init', function() {
        foreach (get_custom_options() as $key=>$value) {
            foreach ($value['fields'] as $field) {
                if (isset($field['localize']) && $field['localize']) {
                    do_action( 'wpml_multilingual_options', $field['name'] );
                }
            }
        }
    });
    do_action( 'wpml_multilingual_options', 'blogname' );
    do_action( 'wpml_multilingual_options', 'blogdescription' );
    add_filter('pre_option', function($pre_option, $option, $default) {
        if (is_admin() || $pre_option !== false) {
            return $pre_option;
        }

        global $sitepress, $wpdb;

        if (!$sitepress) {
            return $pre_option;
        }

        $current_lang = $sitepress->get_current_language();
        $default_lang = $sitepress->get_default_language();

        if ($current_lang !== $default_lang) {
            $localized_option = $option . '_' . $current_lang;
            $localized_value = $wpdb->get_var($wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
                $localized_option
            ));

            if ($localized_value !== null) {
                return maybe_unserialize($localized_value);
            }
        }

        return $pre_option;
    }, 10, 3);
}
