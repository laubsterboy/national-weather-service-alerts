<?php
/**
* NWS_Alerts_Admin
*
* @since 0.1
*/

class NWS_Alerts_Admin {

    /*
    * activation
    *
    * Is called when the NWS_Alerts plugin is activated and creates necessary database tables and populates them with data
    *
    * @return void
    * @access public
    */
    public static function activation() {
        global $wpdb, $wp_version;
        $sql;
        $table_name = NWS_ALERTS_TABLE_NAME_LOCATIONS;

        // Check for WordPress 3.5 and above.
        if(version_compare($wp_version, NWS_ALERTS_MIN_WP_VERSION, '>=') && version_compare(phpversion(), NWS_ALERTS_MIN_PHP_VERSION, '>=')) {

            // Only create the table and populate it on the first activation - or if the table_name has changed or been dropped
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                // Create the database table
                $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    zip int NOT NULL,
                    latitude text NOT NULL,
                    longitude text NOT NULL,
                    city text NOT NULL,
                    state text NOT NULL,
                    county text NOT NULL,
                    zipclass text NOT NULL,
                    PRIMARY KEY (id)
                );";

                dbDelta($sql);

                $zip_codes_file = fopen(NWS_ALERTS_ABSPATH . 'data/zip-codes.txt', 'r');

                while ($line = fgets($zip_codes_file)) {
                    list($zip, $latitude, $longitude, $city, $state, $county, $zipclass) = explode(',', str_replace('"', '', strtolower($line)));
                    $rows_affected = $wpdb->insert($table_name, array('zip' => $zip, 'latitude' => $latitude, 'longitude' => $longitude, 'city' => $city, 'state' => $state, 'county' => $county, 'zipclass' => $zipclass));
                }
                fclose($zip_codes_file);
            }

            $table_name = NWS_ALERTS_TABLE_NAME_CODES;

            // Only create the table and populate it on the first activation - or if the table_name has changed or been dropped
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                // Create the database table
                $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    state text NOT NULL,
                    stateansi int NOT NULL,
                    countyansi int NOT NULL,
                    county text NOT NULL,
                    PRIMARY KEY (id)
                );";

                dbDelta($sql);

                $ansi_codes_file = fopen(NWS_ALERTS_ABSPATH . 'data/ansi-codes.txt', 'r');

                while ($line = fgets($ansi_codes_file)) {
                    list($state, $stateansi, $countyansi, $county, $ansiclass) = explode(',', strtolower($line));
                    $rows_affected = $wpdb->insert($table_name, array('state' => $state, 'stateansi' => $stateansi, 'countyansi' => $countyansi, 'county' => $county));
                }
                fclose($ansi_codes_file);
            }

            /* add_feature - add check to see if database tables were created successfully - if not then do not activate */
        } else {
            deactivate_plugins(array('nws-alerts/nws-alerts.php'), false, is_network_admin());
            die(NWS_ALERTS_ERROR_NO_ACTIVATION);
        }
    }




    /*
    * deactivation
    *
    * Is called when the NWS_Alerts plugin is deactivated
    *
    * @return void
    * @access public
    */
    public static function deactivation() {

    }




    /**
    * admin_head_action
    *
    * Checks to see if the current admin screen is a valid post type
    *
    * @return void
    */
    public static function admin_head_action() {
        global $typenow;

        if (empty($typenow)) return;

        if (NWS_ALERTS_TINYMCE_4) {
            add_filter('mce_external_plugins', 'NWS_Alerts_Admin::mce_external_plugins_filter');
            add_filter('mce_buttons', 'NWS_Alerts_Admin::mce_buttons_filter');
            add_action('after_wp_tiny_mce', 'NWS_Alerts_Admin::mce_markup');
        }
    }




    /**
    * admin_enqueue_scripts_action
    *
    * Admin styles for editor
    *
    * @return   void
    * @access   public
    */
    public static function admin_enqueue_scripts_action() {
	    wp_enqueue_style('nws-alerts-admin-css', NWS_ALERTS_URL . 'css/nws-alerts-admin.css');
    }




/**
    * mce_external_plugins_filter
    *
    * Adds the custom NWS Alerts TinyMCE plugin
    *
    * @return void
    */
    public static function mce_external_plugins_filter($plugins) {
        $plugins['nws_alerts'] = NWS_ALERTS_URL . 'js/nws-alerts-mce-plugin.js';

        return $plugins;
    }




    /**
    * mce_buttons_filter
    *
    * Adds the custom NWS Alerts TinyMCE plugin
    *
    * @return void
    */
    public static function mce_buttons_filter($buttons) {
        array_push($buttons, 'nws_alerts_shortcodes');

        return $buttons;
    }




    /**
    * mce_markup
    *
    * The html markup for the NWS Alerts TinyMCE Plugin
    *
    * @return   void
    * @access   public
    */
    public static function mce_markup() {
        echo self::get_mce_modal('shortcodes');
    }




    /**
    * get_mce_modal
    *
    * The html markup for the NWS Alerts TinyMCE Plugin - Modals
    *
    * @return   void
    * @access   public
    */
    public static function get_mce_modal($modal) {
        if ($modal === 'shortcodes') {
            $return_value = '';
            $modal_id_prefix = 'nws-alerts';
            $control_id_prefix = $modal_id_prefix . '-' . $modal;

            $return_value .= '<div id="' . $control_id_prefix . '-backdrop" style="display: none"></div>';
            $return_value .= '<div id="' . $control_id_prefix . '-wrap" class="wp-core-ui" style="display: none">';
            $return_value .= '<form id="' . $control_id_prefix . '" tabindex="-1">';
                $return_value .= '<div id="' . $control_id_prefix . '-modal-title" class="' . $modal_id_prefix . '-modal-title">Insert NWS Alerts Shortcode<div id="' . $control_id_prefix . '-close" class="' . $modal_id_prefix . '-close" tabindex="0"></div></div>';

                $return_value .= '<div class="' . $modal_id_prefix . '-options">';
                    $return_value .= '<div id="' . $control_id_prefix . '-errors" class="' . $modal_id_prefix . '-errors"></div>';
                    $return_value .= '<table>';
                        $return_value .= '<tbody>';

                            $return_value .= self::get_mce_control('zip', $control_id_prefix);
                            $return_value .= self::get_mce_control('city', $control_id_prefix);
                            $return_value .= self::get_mce_control('state', $control_id_prefix);
                            $return_value .= self::get_mce_control('county', $control_id_prefix);
                            $return_value .= self::get_mce_control('display', $control_id_prefix);
                            $return_value .= self::get_mce_control('scope', $control_id_prefix);


                        $return_value .= '</tbody>';
                    $return_value .= '</table>';
                $return_value .= '</div>';

                $return_value .= '<div class="submitbox ' . $modal_id_prefix . '-submitbox">';
                    $return_value .= '<div id="' . $control_id_prefix . '-update" class="' . $modal_id_prefix . '-update">';
                        $return_value .= '<input type="submit" value="Insert NWS Alerts Shortcode" class="button button-primary" id="' . $control_id_prefix . '-submit" name="' . $control_id_prefix . '-submit">';
                    $return_value .= '</div>';
                    $return_value .= '<div id="' . $control_id_prefix . '-cancel" class="' . $modal_id_prefix . '-cancel">';
                        $return_value .= '<a class="submitdelete deletion" href="#">Cancel</a>';
                    $return_value .= '</div>';
                $return_value .= '</div>';
            $return_value .= '</form>';
            $return_value .= '</div>';
        }

        return $return_value;
    }




    /**
    * get_mce_control
    *
    * The html markup for the NWS Alerts TinyMCE Plugin - Controls
    *
    * @return   void
    * @access   public
    */
    public static function get_mce_control($control, $control_id_prefix) {
    	$return_value = '';

        if ($control === 'zip') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Zipcode</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" size="5" maxlength="5" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'city') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>City</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'state') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>State</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            foreach (NWS_Alerts_Utils::get_states() as $state) {
                                if ($state['abbrev'] === 'AL') {
                                    $return_value .= '<option value="' . $state['abbrev'] . '" selected="selected">' . $state['name'] . '</option>';
                                } else {
                                    $return_value .= '<option value="' . $state['abbrev'] . '">' . $state['name'] . '</option>';
                                }
                            }
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'county') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>County</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'display') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Display</h4><p class="howto">Full: Graphic, Scope, Location, and Alert Type for the most severe current alert, and mouse over for all other alerts (including Alert Descriptions and Google Map) within the scope of the designated location.</p><p class="howto">Basic: Graphic, Scope, Location, and Alert Type for the most severe current alert, but no mouse over details.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            $return_value .= '<option value="full" selected="selected">Full</option>';
                            $return_value .= '<option value="basic">Basic</option>';
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'scope') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Scope</h4><p class="howto">Show alerts at only the selected county, state, or national level.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            $return_value .= '<option value="county" selected="selected">County</option>';
                            $return_value .= '<option value="state">State</option>';
                            $return_value .= '<option value="national">National</option>';
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        }

        return $return_value;
    }




    public static function register_widget() {
        register_widget('NWS_Alerts_Widget');
    }
}
