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


require_once('nws-alert-globals.php');
require_once('nws-alert-utils.php');
require_once('class-nws-alert.php');
require_once('class-nws-alert-entry.php');
require_once('class-nws-alert-shortcodes.php');
require_once('class-nws-alert-admin.php');

// Shortcodes
add_shortcode('nws_alert', 'NWS_Alert_Shortcodes::shortcode_handler');

// Scripts and Styles
add_action('wp_enqueue_scripts', 'NWS_Alert_Shortcodes::scripts_styles');

// Admin WordPress Editor Buttons - TinyMCE Plugins
add_action('admin_head', 'NWS_Alert_Admin::admin_head_action');
add_action('admin_enqueue_scripts', 'NWS_Alert_Admin::admin_enqueue_scripts_action');

// Cannot use __FILE__ for the first parameter because the plugin is using a symlink which resolves to a directory outside the WP plugins directory
register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::activation');
register_deactivation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), 'NWS_Alert_Admin::deactivation');

?>
