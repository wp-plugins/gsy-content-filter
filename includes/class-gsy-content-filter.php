<?php
if (!class_exists('GSY_Content_Filter')) {

    class GSY_Content_Filter {

        /**
         * Holds the values to be used in the fields callbacks
         */
        private $_options;

        /**
         * Holds all possible filters to be added
         */
        private $_filters = array(
            'the_title' => 'title',
            'the_content' => 'content',
            'the_excerpt' => 'excerpt',
            'the_tags' => 'tags',
        );

        /**
         * Holds the number of form fields for each type
         */
        private $_count = 20;

        /**
         * Start up
         */
        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'gsy_content_filter_add_styles'));
            add_action('admin_enqueue_scripts', array($this, 'gsy_content_filter_add_scripts'));
            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));
            $this->add_filters();
        }

        /**
         * Adding styles for admin page
         */
        public function gsy_content_filter_add_styles($hook) {
            // Load styles only for the plugin's option page
            if ($hook === 'settings_page_gsy-content-filter') {
                $style_src = '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css';
                wp_enqueue_style('gsy-content-filter-bootstrap', $style_src);

                $style_src = '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css';
                wp_enqueue_style('gsy-content-filter-bootstrap-theme', $style_src);

                $style_src = plugins_url('../css/style.css', __FILE__);
                wp_enqueue_style('gsy-content-filter-style', $style_src);
            }
        }

        /**
         * Adding scripts for admin page
         */
        public function gsy_content_filter_add_scripts() {
            $script_src = plugins_url('../js/script.js', __FILE__);
            wp_enqueue_script('gsy-content-filter-script', $script_src, array('jquery'));
        }

        /**
         * Add options page
         */
        public function add_plugin_page() {
            // This page will be under "Settings"
            add_options_page(__('GSY Content Filter', 'gsy-content-filter'), __('GSY Content Filter', 'gsy-content-filter'), 'manage_options', 'gsy-content-filter', array($this, 'create_admin_page'));
        }

        /**
         * Options page callback
         */
        public function create_admin_page() {
            // Set class property
            $this->_options = get_option('gsy_content_filter_options');
            ?>
            <div id="gsy-content-filter" class="wrap">
                <h2><?php _e('GSY Content Filter', 'gsy-content-filter'); ?></h2>           
                <form method="post" action="options.php" role="form">
                    <div class="form-inline">
                        <?php
                        // This prints out all hidden setting fields
                        settings_fields('gsy_content_filter_group');
                        do_settings_sections('gsy-content-filter');
                        ?>
                        <p>
                            <button class="add-filter btn btn-success btn-sm">
                                <?php _e('add more', 'gsy-content-filter'); ?>
                            </button>                        
                        </p>
                        <?php
                        submit_button();
                        ?>
                    </div>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        public function page_init() {
            register_setting(
                    'gsy_content_filter_group', // Option group
                    'gsy_content_filter_options', // Option name
                    array($this, 'sanitize') // Sanitize
            );

            add_settings_section(
                    'gsy_content_filter_section', // ID
                    __('Filters', 'gsy-content-filter'), // Title
                    array($this, 'print_section_info'), // Callback
                    'gsy-content-filter' // Page
            );

            for ($i = 1; $i <= $this->_count; $i++) {
                add_settings_field(
                        'old_word_' . $i, // ID
                        __('Search for word:', 'gsy-content-filter'), // Title 
                        array($this, 'old_word_callback'), // Callback
                        'gsy-content-filter', // Page
                        'gsy_content_filter_section', // Section
                        $i // Additional argument
                );

                add_settings_field(
                        'new_word_' . $i, // ID
                        __('Replace with:', 'gsy-content-filter'), // Title
                        array($this, 'new_word_callback'), // Callback
                        'gsy-content-filter', // Page 
                        'gsy_content_filter_section', // Section
                        $i // Additional argument
                );

                add_settings_field(
                        'filter_type_' . $i, // ID
                        __('Search in:', 'gsy-content-filter'), // Title
                        array($this, 'filter_type_callback'), // Callback
                        'gsy-content-filter', // Page 
                        'gsy_content_filter_section', // Section
                        $i // Additional argument
                );

                add_settings_field(
                        'case_sensitive_' . $i, // ID
                        '', // Title
                        array($this, 'case_sensitive_callback'), // Callback
                        'gsy-content-filter', // Page 
                        'gsy_content_filter_section', // Section
                        $i // Additional argument
                );
            }
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize($input) {
            $sanitized_input = array();

            for ($i = 1; $i <= $this->_count; $i++) {

                if (isset($input['old_word_' . $i])) {
                    $sanitized_input['old_word_' . $i] = sanitize_text_field($input['old_word_' . $i]);
                }

                if (isset($input['new_word_' . $i])) {
                    $sanitized_input['new_word_' . $i] = sanitize_text_field($input['new_word_' . $i]);
                }

                if (isset($input['filter_type_' . $i])) {
                    $sanitized_input['filter_type_' . $i] = $input['filter_type_' . $i];

                    foreach ($input['filter_type_' . $i] as $key => $value) {
                        if (!array_key_exists($value, $this->_filters)) {
                            $sanitized_input['filter_type_' . $i] = array('the_title'); // default value if not a correct filter type                      
                            break;
                        }
                    }
                } else {
                    if (isset($input['old_word_' . $i])) {
                        $sanitized_input['filter_type_' . $i] = array('the_title'); // default value if non selected
                    }
                }

                if (isset($input['case_sensitive_' . $i])) {
                    if ($input['case_sensitive_' . $i] === 'on' || $input['case_sensitive_' . $i] === '') {
                        $sanitized_input['case_sensitive_' . $i] = $input['case_sensitive_' . $i];
                    }
                }
            }

            return $sanitized_input;
        }

        /**
         * Print the Section text
         */
        public function print_section_info() {
            ?>
            <p>
                <button class="remove-all-filters btn btn-danger btn-sm" disabled="disabled">
                    <?php _e('remove all', 'gsy-content-filter'); ?>
                </button>
            </p>
            <?php
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function old_word_callback() {
            $arg_list = func_get_args();
            $field_id = 'old_word_' . $arg_list[0];

            echo '<p>';
            printf(
                    '<input type="text" required="required" class="old-word form-control" disabled="disabled" id="%1$s" name="gsy_content_filter_options[%1$s]" value="%2$s" />', $field_id, isset($this->_options[$field_id]) ? esc_attr($this->_options[$field_id]) : ''
            );
            echo '<button class="delete-this-filter btn btn-danger btn-sm">' . __('delete', 'gsy-content-filter') . '</button>';
            echo '</p>';
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function new_word_callback() {
            $arg_list = func_get_args();
            $field_id = 'new_word_' . $arg_list[0];

            printf(
                    '<input type="text" required="required" class="new-word form-control" disabled="disabled" id="%1$s" name="gsy_content_filter_options[%1$s]" value="%2$s" />', $field_id, isset($this->_options[$field_id]) ? esc_attr($this->_options[$field_id]) : ''
            );
        }

        public function filter_type_callback() {
            $arg_list = func_get_args();
            $field_id = 'filter_type_' . $arg_list[0];

            $html = '<select required="required" class="filter-type form-control" name="gsy_content_filter_options[' . $field_id . '][]" id="' . $field_id . '" disabled="disabled" multiple="multiple">';
            foreach ($this->_filters as $k => $v) {
                $selected = false;

                if (isset($this->_options[$field_id]) && in_array($k, $this->_options[$field_id])) {
                    $selected = true;
                }
                $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
            }
            $html .= '</select> ';

            echo $html;
        }

        public function case_sensitive_callback() {
            $arg_list = func_get_args();
            $field_id = 'case_sensitive_' . $arg_list[0];

            if (isset($this->_options[$field_id])) {
                $checked = checked($this->_options[$field_id], 'on', false);
            } else {
                $checked = '';
            }

            $html = '<div class="checkbox">';
            $html .= '<label>';
            $html .= '<input type="checkbox" class="case-sensitive" name="gsy_content_filter_options[' . $field_id . ']" id="' . $field_id . '" ' . $checked . ' disabled="disabled"  />';
            $html .= '  ' . __('case sensitive', 'gsy-content-filter');
            $html .= '</label>';
            $html .= '</div>';

            echo $html;
        }

        /**
         * Add filter tagas where to search for
         */
        public function add_filters() {
            $this->_options = get_option('gsy_content_filter_options');

            add_filter('the_title', array($this, 'the_title_callback'));
            add_filter('the_content', array($this, 'the_content_callback'));
            add_filter('the_excerpt', array($this, 'the_excerpt_callback'));
            add_filter('the_tags', array($this, 'the_tags_callback'));
        }

        public function the_title_callback($content) {
            for ($i = 1; $i <= $this->_count; $i++) {
                if (!empty($this->_options['old_word_' . $i]) && in_array('the_title', $this->_options['filter_type_' . $i])) {

                    if (isset($this->_options['case_sensitive_' . $i])) {
                        $replace = 'str_replace'; // for case-sensitive replace
                    } else {
                        $replace = 'str_ireplace'; // for case-insensitive replace
                    }

                    $content = $replace($this->_options['old_word_' . $i], $this->_options['new_word_' . $i], $content);
                }
            }

            return $content;
        }

        public function the_content_callback($content) {
            for ($i = 1; $i <= $this->_count; $i++) {
                if (!empty($this->_options['old_word_' . $i]) && in_array('the_content', $this->_options['filter_type_' . $i])) {

                    if (isset($this->_options['case_sensitive_' . $i])) {
                        $replace = 'str_replace'; // for case-sensitive replace
                    } else {
                        $replace = 'str_ireplace'; // for case-insensitive replace
                    }

                    $content = $replace($this->_options['old_word_' . $i], $this->_options['new_word_' . $i], $content);
                }
            }

            return $content;
        }

        public function the_excerpt_callback($content) {
            for ($i = 1; $i <= $this->_count; $i++) {
                if (!empty($this->_options['old_word_' . $i]) && in_array('the_excerpt', $this->_options['filter_type_' . $i])) {

                    if (isset($this->_options['case_sensitive_' . $i])) {
                        $replace = 'str_replace'; // for case-sensitive replace
                    } else {
                        $replace = 'str_ireplace'; // for case-insensitive replace
                    }

                    $content = $replace($this->_options['old_word_' . $i], $this->_options['new_word_' . $i], $content);
                }
            }

            return $content;
        }

        public function the_tags_callback($content) {
            for ($i = 1; $i <= $this->_count; $i++) {
                if (!empty($this->_options['old_word_' . $i]) && in_array('the_tags', $this->_options['filter_type_' . $i])) {

                    if (isset($this->_options['case_sensitive_' . $i])) {
                        $replace = 'str_replace'; // for case-sensitive replace
                    } else {
                        $replace = 'str_ireplace'; // for case-insensitive replace
                    }

                    $content = $replace($this->_options['old_word_' . $i], $this->_options['new_word_' . $i], $content);
                }
            }

            return $content;
        }

    }

}