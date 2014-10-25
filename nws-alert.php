<?php
/*
* Plugin Name: National Weather Service Alerts
* Plugin URI: http://www.laubsterboy.com/blog/nws-alert/
* Description: Easily add official National Weather Service alerts to your website.
* Version: 0.1
* Author: John Russell
* Author URI: http://www.laubsterboy.com
* Copyright: (c) 2014 John Russell
* License: GNU General Public License v2.0
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once('nws-alert-globals.php');
require_once('classes/class-nws-alert-utils.php');
require_once('classes/class-nws-alert.php');
require_once('classes/class-nws-alert-entry.php');
require_once('classes/class-nws-alert-shortcodes.php');
require_once('classes/class-nws-alert-widgets.php');
require_once('classes/class-nws-alert-client.php');
require_once('classes/class-nws-alert-admin.php');

// Cannot use __FILE__ for the first parameter because the plugin is using a symlink which resolves to a directory outside the WP plugins directory
register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::activation');
register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::deactivation');

// Shortcodes
add_shortcode('nws_alert', 'NWS_Alert_Shortcodes::shortcode_handler');

// Client - Set JavaScript ajaxurl global variable
add_action('wp_head','NWS_Alert_Client::set_ajaxurl');

// Client - Scripts and Styles
add_action('wp_enqueue_scripts', 'NWS_Alert_Client::scripts_styles');

// Client - AJAX listeners
if(is_admin()) add_action('wp_ajax_nopriv_nws_alert_refresh', 'NWS_Alert_Client::refresh');
if(is_admin()) add_action('wp_ajax_nws_alert_refresh', 'NWS_Alert_Client::refresh');

// Admin - WordPress Editor Buttons - TinyMCE Plugins
add_action('admin_head', 'NWS_Alert_Admin::admin_head_action');
add_action('admin_enqueue_scripts', 'NWS_Alert_Admin::admin_enqueue_scripts_action');

// Admin/Client - WordPress Widget
add_action('widgets_init', 'NWS_Alert_Admin::register_widget');

?>
