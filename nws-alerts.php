<?php
/*
* Plugin Name: National Weather Service Alerts
* Plugin URI: https://github.com/laubsterboy/nws-alerts
* Description: Easily add official National Weather Service alerts to your website.
* Version: 1.1.1
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

if (NWS_ALERTS_TABLES_BUILT !== true) {
    // Admin - Notices
    add_action('admin_notices', 'NWS_Alerts_Admin::build_tables_admin_notice');

    // Admin - AJAX listeners
    if (is_admin()) add_action('wp_ajax_nws_alerts_build_tables', 'NWS_Alerts_Admin::build_tables');
    if (is_admin()) add_action('wp_ajax_nws_alerts_populate_tables', 'NWS_Alerts_Admin::populate_tables');
} else {
    // Shortcodes
    add_shortcode('nws_alerts', 'NWS_Alerts_Shortcodes::shortcode_handler');

    // Client - Set JavaScript ajaxurl global variable
    add_action('wp_head','NWS_Alerts_Client::set_ajaxurl');

    // Client - Scripts and Styles
    add_action('wp_enqueue_scripts', 'NWS_Alerts_Client::scripts_styles');

    // Client - AJAX listeners
    if (is_admin()) add_action('wp_ajax_nopriv_nws_alerts_refresh', 'NWS_Alerts_Client::refresh');
    if (is_admin()) add_action('wp_ajax_nws_alerts_refresh', 'NWS_Alerts_Client::refresh');

    // Client - WordPress output buffer
    add_action('wp_head', 'NWS_Alerts_Client::buffer_start');
    add_action('wp_footer', 'NWS_Alerts_Client::buffer_end');

    // Admin/Client - WordPress Widget
    add_action('widgets_init', 'NWS_Alerts_Admin::register_widget');

    // Admin - WordPress Editor Buttons - TinyMCE Plugins
    add_action('admin_head', 'NWS_Alerts_Admin::admin_head_action');
}

// Admin - Scripts and Styles
add_action('admin_enqueue_scripts', 'NWS_Alerts_Admin::admin_enqueue_scripts_action');

// Admin - WordPress Settings Page
add_action('admin_menu', 'NWS_Alerts_Admin::add_settings_menu');

?>
