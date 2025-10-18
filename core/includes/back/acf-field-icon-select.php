<?php

if(!defined('ABSPATH')){exit;}

if (class_exists('ACF')) {

    /**
     * ACF_Field_Icon_Select_V5 Class
     *
     * A simplified field for selecting an SVG icon from the theme's sprite.
     */
    class ACF_Field_Icon_Select_V5 extends acf_field {

        /**
         * Sets up default values for the field.
         */
        public function __construct() {
            $this->name     = 'icon_select';
            $this->label    = __( 'Icon Select', TEXTDOMAIN );
            $this->category = 'choice';
            $this->defaults = array();

            parent::__construct();
        }

        /**
         * Parse SVG sprite to extract icon IDs, with caching.
         *
         * @return array Array of icon IDs.
         */
        private function get_icons() {
            // Use transient cache to avoid parsing the file on every load.
            $cached_icons = get_transient('acf_icon_select_icons');
            if ($cached_icons !== false) {
                return $cached_icons;
            }

            $svg_file = get_template_directory() . '/assets/svg/sprite.svg';

            if (!file_exists($svg_file)) {
                return array();
            }

            $content = file_get_contents($svg_file);
            $icons = array();

            // Extract all IDs matching the "icon-*" pattern.
            preg_match_all('/id="(icon-[^"]+)"/i', $content, $icon_matches);

            if (!empty($icon_matches[1])) {
                foreach ($icon_matches[1] as $icon) {
                    $label = str_replace(array('icon-', '-'), array('', ' '), $icon);
                    $icons[$icon] = ucfirst($label);
                }
            }

            // Store the parsed icons in a transient for performance.
            set_transient('acf_icon_select_icons', $icons, TRANSIENTS_TIME);

            return $icons;
        }

        /**
         * Renders the Icon Select Field.
         *
         * @param array $field The array representation of the current field.
         */
        public function render_field( $field ) {
            $icons = $this->get_icons();
            $current_value = esc_attr($field['value']);
            ?>
            <div class="acf-icon-select-field">
                <select name="<?php echo esc_attr($field['name']); ?>" style="width: 100%;">
                    <option value=""><?php _e('- Select Icon -', TEXTDOMAIN); ?></option>
                    <?php foreach ($icons as $icon_key => $icon_name) : ?>
                        <option value="<?php echo esc_attr($icon_key); ?>" <?php selected($current_value, $icon_key); ?>>
                            <?php echo esc_html($icon_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php
        }

        /**
         * Formats the value for use on the front-end.
         *
         * @param mixed $value The raw field value.
         * @param int $post_id The Post ID.
         * @param array $field The field object.
         * @return mixed The formatted value.
         */
        public function format_value( $value, $post_id, $field ) {
            if (empty($value)) {
                return false;
            }
            return $value;
        }

        /**
         * Sanitize the value before saving to the database.
         *
         * @param mixed $value The raw field value.
         * @param int $post_id The Post ID.
         * @param array $field The field object.
         * @return string The sanitized value.
         */
        public function update_value( $value, $post_id, $field ) {
            return sanitize_text_field($value);
        }
    }

    new ACF_Field_Icon_Select_V5();

}
