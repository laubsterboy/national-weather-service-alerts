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

class NWS_Alert_Plugin {

    public $utils = null;
    public $shortcodes = null;
    public $admin = null;

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
        require_once('class-nws-alert-admin.php');

        // Shortcodes
        $this->shortcodes = new NWS_Alert_Shortcodes();

        // Admin
        $this->admin = new NWS_Alert_Admin();

        // Cannot use __FILE__ for the first parameter because the plugin is using a symlink which resolves to a directory outside the WP plugins directory
        register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), array($this, 'activation'));
        register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), array($this, 'deactivation'));

        // Scripts and Styles
        add_action('wp_enqueue_scripts', array($this, 'scripts_styles'));

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

        /* add_feature - add check to see if database tables were created successfully - if not then do not activate */
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




    public function __destruct() {
        unset($this);
    }
}

global $nws_alert_plugin;

$nws_alert_plugin = new NWS_Alert_Plugin();
?>
