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
        global $wp_version;

        // Check for minimum WordPress and PHP versions
        if(version_compare($wp_version, NWS_ALERTS_MIN_WP_VERSION, '>=') && version_compare(phpversion(), NWS_ALERTS_MIN_PHP_VERSION, '>=')) {
            // Save the plugin version to the database so it can be compared against for future updates
            update_site_option('nws_alerts_version', NWS_ALERTS_VERSION);
        } else {
            deactivate_plugins(array('national-weather-service-alerts/nws-alerts.php'), false, is_network_admin());
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



    /*
    * build_tables_admin_notice
    *
    * If the NWS Alerts database tables have not been built then an admin notice will be displayed until they are.
    */
    public static function build_tables_admin_notice() {
        echo '<div class="update-nag"><p>The National Weather Service Alerts plugin isn\'t quite ready to be used. Go to the <a href="' . admin_url('options-general.php?page=nws-alerts') . '">NWS Alerts</a> settings page to finish the install.</p></div>';
    }



    /*
    * build_tables
    */
    public static function build_tables($file = false, $part = false) {
        $return_value = array('populate_tables' => false, 'status' => 0);

        if (DOING_AJAX) {
            global $wpdb;
            $sql;
            $table_name = NWS_ALERTS_TABLE_NAME_LOCATIONS;
            $table_locations_created = false;
            $table_codes_created = false;

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

                $table_locations_created = true;
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

                $table_codes_created = true;
            }

            if ((NWS_ALERTS_TABLES_BUILT !== true && $table_locations_created === true && $table_codes_created === true) || get_site_transient('nws_alerts_populate_tables_args') !== false) {
                //set transients with all necessary info for tracking populating the tables
                if (get_site_transient('nws_alerts_populate_tables_args') === false) {
                    set_site_transient('nws_alerts_populate_tables_args',
                        array(
                            array(
                                'file_name_base' => 'zip-codes',
                                'file_extention' => 'txt',
                                'file_parts' => 9,
                                'table_name' => NWS_ALERTS_TABLE_NAME_LOCATIONS
                            ),
                            array(
                                'file_name_base' => 'ansi-codes',
                                'file_extention' => 'txt',
                                'file_parts' => 3,
                                'table_name' => NWS_ALERTS_TABLE_NAME_CODES
                            )
                        ), 0);
                }
                if (get_site_transient('nws_alerts_populate_tables_current_file') === false) set_site_transient('nws_alerts_populate_tables_current_file', 0, 0);
                if (get_site_transient('nws_alerts_populate_tables_current_part') === false) set_site_transient('nws_alerts_populate_tables_current_part', 1, 0);

                $return_value['populate_tables'] = true;
            } else {
                update_site_option('nws_alerts_tables_built', true);
            }
        }

        header('Content-Type: application/json');
        echo json_encode($return_value);
        die();
    }




    public static function populate_tables() {
        global $wpdb;
        $return_value = array('populate_tables' => false);

        $args = get_site_transient('nws_alerts_populate_tables_args');
        $current_file = (int) get_site_transient('nws_alerts_populate_tables_current_file');
        $current_part = (int) get_site_transient('nws_alerts_populate_tables_current_part');

        if (DOING_AJAX && NWS_ALERTS_TABLES_BUILT !== true && $args !== false && $current_file !== false && $current_part !== false) {

            $file_name = $args[$current_file]['file_name_base'] . $current_part . '.' . $args[$current_file]['file_extention'];

            // Loop through file and insert into database
            $opened_file = fopen(NWS_ALERTS_ABSPATH . 'data/' . $file_name, 'r');

            if ($args[$current_file]['file_name_base'] === 'zip-codes') {
                while ($line = fgets($opened_file)) {
                    list($zip, $latitude, $longitude, $city, $state, $county, $zipclass) = explode(',', str_replace('"', '', strtolower($line)));
                    $rows_affected = $wpdb->insert($args[$current_file]['table_name'], array('zip' => $zip, 'latitude' => $latitude, 'longitude' => $longitude, 'city' => $city, 'state' => $state, 'county' => $county, 'zipclass' => $zipclass));
                }
            } else if ($args[$current_file]['file_name_base'] === 'ansi-codes') {
                while ($line = fgets($opened_file)) {
                    list($state, $stateansi, $countyansi, $county, $ansiclass) = explode(',', strtolower($line));
                    $rows_affected = $wpdb->insert($args[$current_file]['table_name'], array('state' => $state, 'stateansi' => $stateansi, 'countyansi' => $countyansi, 'county' => $county));
                }
            }

            fclose($opened_file);

            // Update file and part
            if ($current_part < (int) $args[$current_file]['file_parts']) {
                $current_part += 1;
            } else {
                $current_file += 1;
                $current_part = 1;
            }

            // Get status
            $status_parts = 0;
            $status_parts_total = 0;

            foreach ($args as $key => $files) {
                $status_parts_total += $files['file_parts'];
                if ($key < $current_file) {
                    $status_parts += $files['file_parts'];
                } else if ($key === $current_file) {
                    $status_parts += $current_part;
                }
            }

            $status = ceil(($status_parts / $status_parts_total) * 100);

            if ($current_file === count($args)) {
                delete_site_transient('nws_alerts_populate_tables_args');
                delete_site_transient('nws_alerts_populate_tables_current_file');
                delete_site_transient('nws_alerts_populate_tables_current_part');

                update_site_option('nws_alerts_tables_built', true);

                $return_value['status'] = $status;
            } else {
                set_site_transient('nws_alerts_populate_tables_current_file', $current_file, 0);
                set_site_transient('nws_alerts_populate_tables_current_part', $current_part, 0);

                $return_value['populate_tables'] = true;
                $return_value['status'] = $status;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($return_value);
        die();
    }




    public static function add_settings_menu() {
        add_submenu_page('options-general.php', 'National Weather Service Alerts', 'NWS Alerts', 'manage_options', 'nws-alerts', 'NWS_Alerts_Admin::add_settings_page');
    }




    public static function add_settings_page() {
        $controls = array();

        if (!empty($_POST)) {
            if (isset($_POST['nws_alerts_alerts_bar_action']) && $_POST['nws_alerts_alerts_bar_action'] === 'update' && check_admin_referer('update', 'nws_alerts_alerts_bar_nonce')) {
                $prefix = 'nws_alerts_alerts_bar_';

                $control = 'error';
                $key = $prefix . $control;
                $controls[$control] = false;

                $control = 'zip';
                $key = $prefix . $control;
                if (isset($_POST[$key])) {
                    update_option($key, $_POST[$key]);
                    $controls[$control] = $_POST[$key];
                }

                $control = 'city';
                $key = $prefix . $control;
                if (isset($_POST[$key])) {
                    update_option($key, $_POST[$key]);
                    $controls[$control] = $_POST[$key];
                }

                $control = 'state';
                $key = $prefix . $control;
                if (isset($_POST[$key])) {
                    update_option($key, $_POST[$key]);
                    $controls[$control] = $_POST[$key];
                }

                $control = 'county';
                $key = $prefix . $control;
                if (isset($_POST[$key])) {
                    update_option($key, $_POST[$key]);
                    $controls[$control] = $_POST[$key];
                }

                $control = 'scope';
                $key = $prefix . $control;
                if (isset($_POST[$key])) {
                    update_option($key, $_POST[$key]);
                    $controls[$control] = $_POST[$key];
                }

                $control = 'fix';
                $key = $prefix . $control;
                if (isset($_POST[$key]) && $_POST[$key] == 'on') {
                    update_option($key, 1);
                    $controls[$control] = true;
                } else {
                    update_option($key, 0);
                    $controls[$control] = false;
                }

                $control = 'enabled';
                $key = $prefix . $control;
                $allow_enabled = false;

                if (isset($controls['zip']) && !empty($controls['zip'])) $allow_enabled = true;
                if (isset($controls['city']) && !empty($controls['city']) && isset($controls['state']) && !empty($controls['state'])) $allow_enabled = true;
                if (isset($controls['state']) && !empty($controls['state']) && isset($controls['county']) && !empty($controls['county'])) $allow_enabled = true;

                if ($allow_enabled && isset($_POST[$key]) && $_POST[$key] == 'on') {
                    update_option($key, 1);
                    $controls[$control] = true;
                } else {
                    if (isset($_POST[$key]) && $_POST[$key] == 'on') {
                        $controls['error'] = 'Not enough location information was provided to enable the Alerts Bar';
                    }

                    update_option($key, 0);
                    $controls[$control] = false;
                }
            }
        }

        echo '<div class="wrap">';

        echo '<h2>National Weather Service Alerts</h2>';

        echo self::get_module('build-tables', $controls);
        echo self::get_module('alerts-bar', $controls);

        echo '</div>';
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
        if (NWS_ALERTS_TABLES_BUILT !== true) wp_enqueue_script('nws-alerts-admin-js', NWS_ALERTS_URL . 'js/nws-alerts-admin.js', array(), false, true);
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
        array_push($buttons, 'nws_alerts_shortcode');

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
        echo self::get_modal('shortcode');
    }




    /**
    * get_modal
    *
    * The html markup for the NWS Alerts TinyMCE Plugin - Modals
    *
    * @return   void
    * @access   public
    */
    public static function get_modal($modal) {
        if ($modal === 'shortcode') {
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

                            $return_value .= self::get_control('zip', $control_id_prefix);
                            $return_value .= self::get_control('city', $control_id_prefix);
                            $return_value .= self::get_control('state', $control_id_prefix);
                            $return_value .= self::get_control('county', $control_id_prefix);
                            $return_value .= self::get_control('display', $control_id_prefix);
                            $return_value .= self::get_control('scope', $control_id_prefix);


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
    * get_module
    *
    * The html markup for the NWS Alerts Form Input - Module
    *
    * @return   void
    * @access   public
    */
    public static function get_module($module = '', $controls = array()) {
        if ($module === 'alerts-bar') {
            $defaults = array('error' => false,
                              'enabled' => NWS_ALERTS_BAR_ENABLED,
                              'zip' => NWS_ALERTS_BAR_ZIP,
                              'city' => NWS_ALERTS_BAR_CITY,
                              'state' => NWS_ALERTS_BAR_STATE,
                              'county' => NWS_ALERTS_BAR_COUNTY,
                              'scope' => NWS_ALERTS_BAR_SCOPE,
                              'fix' => NWS_ALERTS_BAR_FIX);
            $controls = wp_parse_args($controls, $defaults);
            $return_value = '';
            $module_id_prefix = 'nws-alerts';
            $control_id_prefix = $module_id_prefix . '-' . $module;

            $return_value .= '<div class="metabox-holder"><div class="meta-box-sortables ui-sortable"><div class="postbox"><h3 class="hndle"><span>Alerts Bar</span></h3>';
            $return_value .= '<div class="inside">';
                $return_value .= '<p class="description">If the alerts bar is enabled, current alerts for the specified location will be added immediately following the &lt;body&gt; tag using a horizontal bar display style. If there are no current alerts then the alerts bar will be added to allow for AJAX auto-refreshing, but nothing will display.</p>';
                $return_value .= '<form id="' . $control_id_prefix . '" method="post" action="">';

                    $return_value .= self::get_control('action', $control_id_prefix);
                    $return_value .= self::get_control('nonce', $control_id_prefix);

                    $return_value .= '<table>';
                        $return_value .= '<tbody>';

                            foreach($controls as $control => $default) {
                                $return_value .= self::get_control($control, $control_id_prefix, $default);
                            }

                        $return_value .= '</tbody>'; 
                    $return_value .= '</table>';  

                    $return_value .= '<input type="submit" value="Save Changes" class="button button-primary" id="' . $control_id_prefix . '-submit" name="' . str_replace('-', '_', $control_id_prefix) . '-submit">';

                $return_value .= '</form>'; 
            $return_value .= '</div>'; 
            $return_value .= '</div>'; 
        } else if ($module === 'build-tables') {
            $return_value = '';
            $module_id_prefix = 'nws-alerts';
            $control_id_prefix = $module_id_prefix . '-' . $module;

            $return_value .= '<div class="metabox-holder"><div class="meta-box-sortables ui-sortable"><div class="postbox"><h3 class="hndle"><span>Build Database Tables</span></h3>';
            $return_value .= '<div class="inside">';
                if (NWS_ALERTS_TABLES_BUILT !== true) {
                    $return_value .= '<p class="description">The NWS Alerts plugin relies on custom database tables to look up locations by zip code, city, state, and/or county. These tables must be built before the NWS Alerts plugin can be used. Due to the size of the tables being built the process has been broken up into small steps, and separated from the activation process, in order to accomodate most web hosts.</p>';
                    $return_value .= '<form id="' . $control_id_prefix . '" method="post" action="">';

                        $return_value .= '<div id="' . $control_id_prefix . '-status-bar-container"><div id="' . $control_id_prefix . '-status-bar"></div></div>';
                        $return_value .= '<input type="submit" value="Build Database Tables" class="button button-primary" id="' . $control_id_prefix . '-submit" name="' . str_replace('-', '_', $control_id_prefix) . '-submit">';

                    $return_value .= '</form>';
                } else {
                    $return_value .= '<p class="description">The NWS Alerts plugin database tables are fully setup and the plugin is ready to be used.</p>';
                }
            $return_value .= '</div>';
            $return_value .= '</div>';
        }

        return $return_value;
    }




    /**
    * get_control
    *
    * The html markup for the NWS Alerts TinyMCE Plugin and Admin page - Controls
    *
    * @return   void
    * @access   public
    */
    public static function get_control($control, $control_id_prefix, $default = false) {
    	$return_value = '';

        if ($control === 'action') {
            if ($default) { $default = ' value="' . $default . '"'; } else { $default = ' value="update"'; }
            $return_value .= '<input id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="hidden"' . $default . ' />';
        } else if ($control === 'nonce') {
            if ($default === false) { $default = 'update'; }
            $return_value .= wp_nonce_field($default, str_replace('-', '_', $control_id_prefix . '_' . $control), true, false);
        } else if ($control === 'error') {
            if ($default === false) return;
            $return_value .= '<tr>';
				$return_value .= '<td><h4>' . $default . '</h4></td>';
			$return_value .= '</tr>';
        } else if ($control === 'enabled') {
            if ($default) { $default = ' checked="checked"'; } else { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Enable</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-checkbox-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="checkbox"' . $default . ' />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'fix') {
            if ($default) { $default = ' checked="checked"'; } else { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Fixed Header</h4><p class="howto">Check this box if the main header/navigation bar is set to a fixed position. This will position the Alerts Bar below the header rather than above. This is an experimental feature and may not be compatible with all themes. If this does not work with your theme you may need to add the Alerts Bar manually using the <a href="https://github.com/laubsterboy/national-weather-service-alerts" target="_blank">shortcode</a>.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-checkbox-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="checkbox"' . $default . ' />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'zip') {
            if ($default) { $default = ' value="' . $default . '"'; } else { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Zipcode</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="text" size="5" maxlength="5"' . $default . ' />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'city') {
            if ($default) { $default = ' value="' . $default . '"'; } else { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>City</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="text"' . $default . ' />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'state') {
            if ($default === false) { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>State</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '">';
                            $return_value .= '<option value=""' . selected($default, '', false) . '></option>';
                            foreach (NWS_Alerts_Utils::get_states() as $state) {
                                $return_value .= '<option value="' . $state['abbrev'] . '"' . selected($default, $state['abbrev'], false) . '>' . $state['name'] . '</option>';
                            }
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'county') {
            if ($default) { $default = ' value="' . $default . '"'; } else { $default = ''; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>County</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '" type="text"' . $default . ' />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'display') {
            if ($default === false) { $default = NWS_ALERTS_DISPLAY_FULL; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Display</h4><p class="howto">Bar: Graphic, Scope, Location, and Alert Type for the most severe current alert, in a horizontal layout.</p><p class="howto">Basic: Graphic, Scope, Location, and Alert Type for the most severe current alert, but no mouse over details.</p><p class="howto">Full: Graphic, Scope, Location, and Alert Type for the most severe current alert, and mouse over for all other alerts (including Alert Descriptions and Google Map) within the scope of the designated location.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '">';
                            $return_value .= '<option value="' . NWS_ALERTS_DISPLAY_BAR . '"' . selected($default, NWS_ALERTS_DISPLAY_BAR, false) . '>Bar</option>';
                            $return_value .= '<option value="' . NWS_ALERTS_DISPLAY_BASIC . '"' . selected($default, NWS_ALERTS_DISPLAY_BASIC, false) . '>Basic</option>';
                            $return_value .= '<option value="' . NWS_ALERTS_DISPLAY_FULL . '"' . selected($default, NWS_ALERTS_DISPLAY_FULL, false) . '>Full</option>';
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'scope') {
            if ($default === false) { $default = NWS_ALERTS_SCOPE_COUNTY; }
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Scope</h4><p class="howto">Show alerts at only the selected county, state, or national level.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alerts-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . str_replace('-', '_', $control_id_prefix . '_' . $control) . '">';
                            $return_value .= '<option value="' . NWS_ALERTS_SCOPE_COUNTY . '"' . selected($default, NWS_ALERTS_SCOPE_COUNTY, false) . '>County</option>';
                            $return_value .= '<option value="' . NWS_ALERTS_SCOPE_STATE . '"' . selected($default, NWS_ALERTS_SCOPE_STATE, false) . '>State</option>';
                            $return_value .= '<option value="' . NWS_ALERTS_SCOPE_NATIONAL . '"' . selected($default, NWS_ALERTS_SCOPE_NATIONAL, false) . '>National</option>';
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
