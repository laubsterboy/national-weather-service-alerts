<?php
/*
Plugin Name: National Weather Service Alerts
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
        register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::activation');
        register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::deactivation');

        // Scripts and Styles
        add_action('wp_enqueue_scripts', array($this, 'scripts_styles'));

        // Initialize NWS_Alert_Plugin object parameters
        $this->utils = new NWS_Alert_Utils();
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
