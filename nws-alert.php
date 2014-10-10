<?php
/*
Plugin Name: National Weather Service Alert
Plugin URI: http://www.laubsterboy.com/blog/nws-alert/
Description:    National Weather Service Alert is a plugin to provide an
                easy method of placing official local weather alerts on
                your website. This plugin uses the official NWS CAP
                messages.
Version: 1.0.0
Author: John Russell
Author URI: http://www.laubsterboy.com
License: GPL2

**************************************************************************
Copyright 2013 John Russell  (email : laubsterboy@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
**************************************************************************/
/*
// Class: nws_alert
// Purpose: To provide local weather alerts (specifically Buffalo County, Nebraska for the time being)
// Structure:
//		1: Retrieve XML (atom) feed from NWS for Buffalo County - http://alerts.weather.gov
//		2: Parse through the file to retrieve TITLE, and ENTRYs - Documentation for CAP can be foun here: http://alerts.weather.gov/cap/pdf/CAP%20v12%20guide%20web%2006052013.pdf
//		3: Parse ENTRYs CAP namespace information:
//		3a: To determine the type: special statements, advisory, watch, or warning
//		3b: Check to be sure cap:status is equal to 'actual'
//		3c: msgType will likely be 'Alert', 'Update', or 'Cancel'
//		3d: category:
//				"Geo" Geophysical (inc. landslide)
//				"Met" Meteorological (inc. flood)
//				"Safety" General emergency and public safety
//				"Security" Law enforcement, military, homeland and local/private security
//				"Rescue" Rescue and recovery
//				"Fire" Fire suppression and rescue
//				"Health" Medical and public health
//				"Env" Pollution and other environmental
//				"Transport" Public and private transportation
//				"Infra" Utility, telecommunication, other non-transport infrastructure
//				"CBRNE" Chemical, Biological, Radiological, Nuclear or High-Yield Explosive threat or attack
//		3e: cap:event - There are other event types, however these are meteorilogically relevant for Buffalo county
//				"Blizzard Warning"
//				"Dust Storm Warning"
//				"Flash Flood Watch"
//				"Flash Flood Warning"
//				"Flash Flood Statement"
//				"Flood Watch"
//				"Flood Warning"
//				"Flood Statement"
//				"High Wind Watch"
//				"High Wind Warning"
//				"Severe Thunderstorm Watch"
//				"Severe Thunderstorm Warning"
//				"Severe Weather Statement"
//				"Tornado Watch"
//				"Tornado Warning"
//				"Winter Storm Watch"
//				"Winter Storm Warning"
//              "Avalanche Watch"
//
//				NON-WEATHER-RELATED-EVENTS
//				"Child Abduction Emergency"
//				"Civil Danger Warning"
//				"Civil Emergency Message"
//				"Evacuation Immediate"
//				"Fire Warning"
//				"Hazardous Materials Warning"
//				"Law Enforcement Warning"
//				"Local Area Emergency"
//				"911 Telephone Outage Emergency"
//				"Nuclear Power Plant Warning"
//				"Radiological Hazard Warning"
//				"Shelter in Place Warning"
//		4: Store weather alert data in private array for returning (for custom parsing) or returning of pre-formatted HTML
//		5: Sort the array by severity: Warning, Watch, Advisory, Special Statement
*/
class NWS_Alert_Plugin {

    public $utils = null;
    public $shortcodes = null;

    /*
    * constructor
    *
    * @return void
    * @access public
    */
    public function __construct() {
        require_once('nws-alert-globals.php');
        require_once('nws-alert-utils.php');
        require_once('class-nws-alert.php');
        require_once('class-nws-alert-entry.php');
        require_once('class-nws-alert-shortcodes.php');

        // Shortcodes
        $this->shortcodes = new NWS_Alert_Shortcodes();

        // Cannot use __FILE__ for the first parameter because the plugin is using a symlink which resolves to a directory outside the WP plugins directory
        register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), array($this, 'activation'));
        register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), array($this, 'deactivation'));

        // Scripts and Styles
        add_action('wp_enqueue_scripts', array($this, 'scripts_styles'));

        // WordPress Editor Buttons - TinyMCE Plugins
        add_action('admin_head', array($this, 'admin_head_action'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_action'));

        // Initialize NWS_Alert_Plugin object parameters
        $this->utils = new NWS_Alert_Utils();
    }




    /*
    * activation
    *
    * Is called when the NWS_Alert plugin is activated and creates necessary database tables and populates them with data
    *
    * @return void
    * @access public
    */
    public function activation() {
        global $wpdb;
        $file;
        $sql;
        $table_name = NWS_ALERT_TABLE_NAME_LOCATIONS;

        // Only create the table and populate it on the first activation - or if the table_name has changed or been deleted
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

            $zip_codes_file = fopen(dirname(__FILE__) . '/data/zip-codes.txt', 'r');

            while ($line = fgets($zip_codes_file)) {
                list($zip, $latitude, $longitude, $city, $state, $county, $zipclass) = explode(',', str_replace('"', '', strtolower($line)));
                $rows_affected = $wpdb->insert($table_name, array('zip' => $zip, 'latitude' => $latitude, 'longitude' => $longitude, 'city' => $city, 'state' => $state, 'county' => $county, 'zipclass' => $zipclass));
            }
            fclose($zip_codes_file);
        }

        $table_name = NWS_ALERT_TABLE_NAME_CODES;

        // Only create the table and populate it on the first activation - or if the table_name has changed or been deleted
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

            $ansi_codes_file = fopen(dirname(__FILE__) . '/data/ansi-codes.txt', 'r');

            while ($line = fgets($ansi_codes_file)) {
                list($state, $stateansi, $countyansi, $county, $ansiclass) = explode(',', strtolower($line));
                $rows_affected = $wpdb->insert($table_name, array('state' => $state, 'stateansi' => $stateansi, 'countyansi' => $countyansi, 'county' => $county));
            }
            fclose($ansi_codes_file);
        }
    }




    /*
    * deactivation
    *
    * Is called when the NWS_Alert plugin is deactivated
    *
    * @return void
    * @access public
    */
    public function deactivation() {

    }




    /*
    * scripts_styles
    *
    * Enqueues necessary JavaScript and Stylesheet files
    *
    * @return void
    * @access public
    */
    public function scripts_styles() {
        // Stylesheets
        wp_enqueue_style('nws-alert-css', plugins_url('nws-alert/css/nws-alert.css'));

        /* JavaScript */
        wp_enqueue_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=weather&sensor=false', false, null, false);
    }




    /**
    * admin_head_action
    *
    * Checks to see if the current admin screen is a valid post type
    *
    * @return void
    */
    public function admin_head_action() {
        global $typenow;

        if (empty($typenow)) return;

        if (NWS_ALERT_TINYMCE_4) {
            add_filter('mce_external_plugins', array($this, 'mce_external_plugins_filter'));
            add_filter('mce_buttons', array($this, 'mce_buttons_filter'));
            add_action('after_wp_tiny_mce', array($this, 'mce_markup'));
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
    public function admin_enqueue_scripts_action() {
	    wp_enqueue_style('nws-alert-admin-css', plugins_url('/css/nws-alert-admin.css', basename(dirname(__FILE__)).'/'.basename(__FILE__)));
    }




    /**
    * mce_external_plugins_filter
    *
    * Adds the custom NWS Alert TinyMCE plugin
    *
    * @return void
    */
    public function mce_external_plugins_filter($plugins) {
        $plugins['nws_alert'] = plugins_url('/js/nws-alert-mce-plugin.js', basename(dirname(__FILE__)).'/'.basename(__FILE__));

        return $plugins;
    }




    /**
    * mce_buttons_filter
    *
    * Adds the custom NWS Alert TinyMCE plugin
    *
    * @return void
    */
    public function mce_buttons_filter($buttons) {
        array_push($buttons, 'nws_alert_shortcodes');

        return $buttons;
    }




    /**
    * mce_markup
    *
    * The html markup for the NWS Alert TinyMCE Plugin
    *
    * @return   void
    * @access   public
    */
    public function mce_markup() {
        echo $this->get_mce_modal('shortcodes');
    }




    /**
    * get_mce_modal
    *
    * The html markup for the NWS Alert TinyMCE Plugin - Modals
    *
    * @return   void
    * @access   public
    */
    public function get_mce_modal($modal) {
        if ($modal === 'shortcodes') {
            $return_value = '';
            $modal_id_prefix = 'nws-alert';
            $control_id_prefix = $modal_id_prefix . '-' . $modal;

            $return_value .= '<div id="' . $control_id_prefix . '-backdrop" style="display: none"></div>';
            $return_value .= '<div id="' . $control_id_prefix . '-wrap" class="wp-core-ui" style="display: none">';
            $return_value .= '<form id="' . $control_id_prefix . '" tabindex="-1">';
                $return_value .= '<div id="' . $control_id_prefix . '-modal-title" class="' . $modal_id_prefix . '-modal-title">Insert NWS Alert Shortcode<div id="' . $control_id_prefix . '-close" class="' . $modal_id_prefix . '-close" tabindex="0"></div></div>';

                $return_value .= '<div class="' . $modal_id_prefix . '-options">';
                    $return_value .= '<div id="' . $control_id_prefix . '-errors" class="' . $modal_id_prefix . '-errors"></div>';
                    $return_value .= '<table>';
                        $return_value .= '<tbody>';

                            $return_value .= $this->get_mce_control('zip', $control_id_prefix);
                            $return_value .= $this->get_mce_control('city', $control_id_prefix);
                            $return_value .= $this->get_mce_control('state', $control_id_prefix);
                            $return_value .= $this->get_mce_control('county', $control_id_prefix);
                            $return_value .= $this->get_mce_control('display', $control_id_prefix);
                            $return_value .= $this->get_mce_control('scope', $control_id_prefix);


                        $return_value .= '</tbody>';
                    $return_value .= '</table>';
                $return_value .= '</div>';

                $return_value .= '<div class="submitbox ' . $modal_id_prefix . '-submitbox">';
                    $return_value .= '<div id="' . $control_id_prefix . '-update" class="' . $modal_id_prefix . '-update">';
                        $return_value .= '<input type="submit" value="Insert NWS Alert Shortcode" class="button button-primary" id="' . $control_id_prefix . '-submit" name="' . $control_id_prefix . '-submit">';
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
    * The html markup for the NWS Alert TinyMCE Plugin - Controls
    *
    * @return   void
    * @access   public
    */
    public function get_mce_control($control, $control_id_prefix) {
    	$return_value = '';

        if ($control === 'zip') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Zipcode</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'city') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>City</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'state') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>State</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            foreach ($this->utils->get_states() as $state) {
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
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'display') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Display</h4><p class="howto">Full: Graphic, Scope, Location, and Alert Type for the most severe current alert, and mouse over for all other alerts (including Alert Descriptions and Google Map) within the scope of the designated location.</p><p class="howto">Basic: Graphic, Scope, Location, and Alert Type for the most severe current alert, and no mouse over.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-select-container">';
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
					$return_value .= '<div class="nws-alert-control-select-container">';
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




    public function __destruct() {
        unset($this);
    }
}

global $nws_alert_plugin;

$nws_alert_plugin = new NWS_Alert_Plugin();
?>
