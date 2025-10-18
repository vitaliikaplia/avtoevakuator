<?php

if(!defined('ABSPATH')){exit;}

if (class_exists('ACF')) {

    /**
     * ACF_Field_Button_Type_V5 Class
     *
     * This class contains all the custom workings for the Button Type Field for ACF v5
     */
    class ACF_Field_Button_Type_V5 extends acf_field {

        /**
         * Sets up some default values and delegates work to the parent constructor.
         */
        public function __construct() {
            $this->name     = 'button_type';
            $this->label    = __( 'Button Type', TEXTDOMAIN );
            $this->category = 'choice';
            $this->defaults = array(
                'style'         => '',
                'icon'          => false,
                'icon_position' => 'left',
                'options'       => array(),
            );

            parent::__construct();
        }

        /**
         * Parse SCSS file to extract button styles
         *
         * @return array Array of style names
         */
        private function get_button_styles() {
            // Check transient cache first
            $cached_styles = get_transient('acf_button_type_styles');
            if ($cached_styles !== false) {
                return $cached_styles;
            }

            $scss_file = get_template_directory() . '/assets/scss/_extend.scss';

            if (!file_exists($scss_file)) {
                return array();
            }

            $content = file_get_contents($scss_file);
            $styles = array();

            // Find .overallButton{ block
            if (preg_match('/\.overallButton\s*\{(.*?)\n\}/s', $content, $matches)) {
                $button_block = $matches[1];

                // Extract all &.style-* patterns
                preg_match_all('/&\.style-([a-zA-Z0-9\-]+)\s*\{/i', $button_block, $style_matches);

                if (!empty($style_matches[1])) {
                    foreach ($style_matches[1] as $style) {
                        $label = str_replace('-', ' ', $style);
                        $styles['style-' . $style] = ucfirst($label);
                    }
                }
            }

            // Store in transient cache
            set_transient('acf_button_type_styles', $styles, TRANSIENTS_TIME);

            return $styles;
        }

        /**
         * Parse SCSS file to extract optional classes
         *
         * @return array Array of optional class names
         */
        private function get_button_options() {
            // Check transient cache first
            $cached_options = get_transient('acf_button_type_options');
            if ($cached_options !== false) {
                return $cached_options;
            }

            $scss_file = get_template_directory() . '/assets/scss/_extend.scss';

            if (!file_exists($scss_file)) {
                return array();
            }

            $content = file_get_contents($scss_file);
            $options = array();

            // Find .overallButton{ block
            if (preg_match('/\.overallButton\s*\{(.*?)\n\}/s', $content, $matches)) {
                $button_block = $matches[1];

                // Extract all &.optional-* patterns
                preg_match_all('/&\.optional-([a-zA-Z0-9\-]+)\s*\{/i', $button_block, $option_matches);

                if (!empty($option_matches[1])) {
                    foreach ($option_matches[1] as $option) {
                        $label = str_replace('-', ' ', $option);
                        $options['optional-' . $option] = ucfirst($label);
                    }
                }
            }

            // Store in transient cache
            set_transient('acf_button_type_options', $options, TRANSIENTS_TIME);

            return $options;
        }

        /**
         * Parse SVG sprite to extract icon IDs
         *
         * @return array Array of icon IDs
         */
        private function get_button_icons() {
            // Check transient cache first
            $cached_icons = get_transient('acf_button_type_icons');
            if ($cached_icons !== false) {
                return $cached_icons;
            }

            $svg_file = get_template_directory() . '/assets/svg/sprite.svg';

            if (!file_exists($svg_file)) {
                return array();
            }

            $content = file_get_contents($svg_file);
            $icons = array();

            // Extract only id="icon-* patterns
            preg_match_all('/id="(icon-[^"]+)"/i', $content, $icon_matches);

            if (!empty($icon_matches[1])) {
                foreach ($icon_matches[1] as $icon) {
                    $label = str_replace(array('icon-', '-'), array('', ' '), $icon);
                    $icons[$icon] = ucfirst($label);
                }
            }

            // Store in transient cache
            set_transient('acf_button_type_icons', $icons, TRANSIENTS_TIME);

            return $icons;
        }

        /**
         * Renders the Button Type Field.
         *
         * @param array $field The array representation of the current Button Type Field.
         */
        public function render_field( $field ) {
            $styles = $this->get_button_styles();
            $icons = $this->get_button_icons();
            $options = $this->get_button_options();

            // Parse current value
            $current_value = $field['value'];
            $selected_style = '';
            $selected_icon = false;
            $selected_icon_position = 'left';
            $selected_options = array();

            if (!empty($current_value) && is_array($current_value)) {
                $selected_style = isset($current_value['style']) ? $current_value['style'] : '';
                $selected_icon = isset($current_value['icon']) ? $current_value['icon'] : false;
                $selected_icon_position = isset($current_value['icon_position']) ? $current_value['icon_position'] : 'left';
                $selected_options = isset($current_value['options']) && is_array($current_value['options']) ? $current_value['options'] : array();
            }

            ?>
            <div class="acf-button-type-field" style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;">

                <!-- Style Select -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <?php _e('Button Style', TEXTDOMAIN); ?>
                    </label>
                    <select
                        name="<?php echo esc_attr($field['name']); ?>[style]"
                        style="width: 100%;"
                    >
                        <option value=""><?php _e('- Select Style -', TEXTDOMAIN); ?></option>
                        <?php foreach ($styles as $style_key => $style_name) : ?>
                            <option value="<?php echo esc_attr($style_key); ?>" <?php selected($selected_style, $style_key); ?>>
                                <?php echo esc_html($style_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Icon Select -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <?php _e('Icon', TEXTDOMAIN); ?>
                    </label>
                    <select
                        name="<?php echo esc_attr($field['name']); ?>[icon]"
                        class="acf-button-type-icon-select"
                        style="width: 100%;"
                        data-field-name="<?php echo esc_attr($field['name']); ?>"
                    >
                        <option value=""><?php _e('- No Icon -', TEXTDOMAIN); ?></option>
                        <?php foreach ($icons as $icon_key => $icon_name) : ?>
                            <option value="<?php echo esc_attr($icon_key); ?>" <?php selected($selected_icon, $icon_key); ?>>
                                <?php echo esc_html($icon_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Icon Position Select -->
                <div class="acf-button-type-icon-position" style="flex: 1; min-width: 200px; <?php echo empty($selected_icon) ? 'display: none;' : ''; ?>">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <?php _e('Icon Position', TEXTDOMAIN); ?>
                    </label>
                    <select
                        name="<?php echo esc_attr($field['name']); ?>[icon_position]"
                        style="width: 100%;"
                    >
                        <option value="left" <?php selected($selected_icon_position, 'left'); ?>><?php _e('Left', TEXTDOMAIN); ?></option>
                        <option value="right" <?php selected($selected_icon_position, 'right'); ?>><?php _e('Right', TEXTDOMAIN); ?></option>
                    </select>
                </div>

                <!-- Options Checkboxes -->
                <?php if (!empty($options)) : ?>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <?php _e('Options', TEXTDOMAIN); ?>
                    </label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($options as $option_key => $option_name) : ?>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input
                                    type="checkbox"
                                    name="<?php echo esc_attr($field['name']); ?>[options][]"
                                    value="<?php echo esc_attr($option_key); ?>"
                                    <?php checked(in_array($option_key, $selected_options)); ?>
                                    style="margin: 0;"
                                />
                                <span><?php echo esc_html($option_name); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <script type="text/javascript">
            (function($) {
                $(document).ready(function() {
                    // Handle icon select change
                    $(document).on('change', '.acf-button-type-icon-select', function() {
                        var $this = $(this);
                        var $wrapper = $this.closest('.acf-button-type-field');
                        var $iconPosition = $wrapper.find('.acf-button-type-icon-position');

                        if ($this.val()) {
                            $iconPosition.show();
                        } else {
                            $iconPosition.hide();
                        }
                    });
                });
            })(jQuery);
            </script>
            <?php
        }

        /**
         * Formats the value for the Button Type Field.
         *
         * @param mixed $value   The field value
         * @param int   $post_id The Post ID this $value is associated with
         * @param array $field   The array representation of the current Button Type Field
         *
         * @return array|false The formatted array with style, icon, and options, or false
         */
        public function format_value( $value, $post_id, $field ) {
            // Bail early if no value
            if (empty($value)) {
                return false;
            }

            // Ensure value is an array
            if (!is_array($value)) {
                return false;
            }

            // Build the return array
            $return = array(
                'style'         => isset($value['style']) && !empty($value['style']) ? $value['style'] : '',
                'icon'          => isset($value['icon']) && !empty($value['icon']) ? $value['icon'] : false,
                'icon_position' => isset($value['icon_position']) ? $value['icon_position'] : 'left',
                'options'       => isset($value['options']) && is_array($value['options']) ? $value['options'] : array(),
            );

            // Return false if no style is selected
            if (empty($return['style'])) {
                return false;
            }

            return $return;
        }

        /**
         * Update the value before saving to database
         *
         * @param mixed $value   The field value
         * @param int   $post_id The Post ID this $value is associated with
         * @param array $field   The array representation of the current Button Type Field
         *
         * @return array The formatted array
         */
        public function update_value( $value, $post_id, $field ) {
            // Ensure value is an array
            if (!is_array($value)) {
                $value = array(
                    'style'   => '',
                    'icon'    => false,
                    'options' => array(),
                );
            }

            // Sanitize values
            $value['style'] = isset($value['style']) ? sanitize_text_field($value['style']) : '';
            $value['icon'] = isset($value['icon']) && !empty($value['icon']) ? sanitize_text_field($value['icon']) : false;
            $value['icon_position'] = isset($value['icon_position']) && in_array($value['icon_position'], array('left', 'right')) ? $value['icon_position'] : 'left';
            $value['options'] = isset($value['options']) && is_array($value['options']) ? array_map('sanitize_text_field', $value['options']) : array();

            return $value;
        }
    }

    new ACF_Field_Button_Type_V5();

}
