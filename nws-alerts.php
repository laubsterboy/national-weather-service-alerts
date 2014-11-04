<?php
/*
* Plugin Name: National Weather Service Alerts
* Plugin URI: https://github.com/laubsterboy/nws-alerts
* Description: Easily add official National Weather Service alerts to your website.
* Version: 1.0.1
* Author: John Russell
* Author URI: http://www.laubsterboy.com
* Copyright: (c) 2014 John Russell
* License: GNU General Public License v2.0
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once('nws-alerts-globals.php');
require_once('classes/class-nws-alerts-utils.php');
require_once('classes/class-nws-alerts.php');
require_once('classes/class-nws-alerts-entry.php');
require_once('classes/class-nws-alerts-shortcodes.php');
require_once('classes/class-nws-alerts-widgets.php');
require_once('classes/class-nws-alerts-client.php');
require_once('classes/class-nws-alerts-admin.php');

register_activation_hook(NWS_ALERTS_ABSPATH . 'nws-alerts.php', 'NWS_Alerts_Admin::activation');
register_deactivation_hook(NWS_ALERTS_ABSPATH . 'nws-alerts.php', 'NWS_Alerts_Admin::deactivation');

// Shortcodes
add_shortcode('nws_alerts', 'NWS_Alerts_Shortcodes::shortcode_handler');

// Client - Set JavaScript ajaxurl global variable
add_action('wp_head','NWS_Alerts_Client::set_ajaxurl');

// Client - Scripts and Styles
add_action('wp_enqueue_scripts', 'NWS_Alerts_Client::scripts_styles');

// Client - AJAX listeners
if(is_admin()) add_action('wp_ajax_nopriv_nws_alerts_refresh', 'NWS_Alerts_Client::refresh');
if(is_admin()) add_action('wp_ajax_nws_alerts_refresh', 'NWS_Alerts_Client::refresh');

// Admin - WordPress Editor Buttons - TinyMCE Plugins
add_action('admin_head', 'NWS_Alerts_Admin::admin_head_action');
add_action('admin_enqueue_scripts', 'NWS_Alerts_Admin::admin_enqueue_scripts_action');

// Admin/Client - WordPress Widget
add_action('widgets_init', 'NWS_Alerts_Admin::register_widget');

?>
