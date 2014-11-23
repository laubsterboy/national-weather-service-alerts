<?php

// If uninstall not called from WordPress then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Remove the tables created by the NWS Alerts plugin
require_once('nws-alerts-globals.php');

global $wpdb;

$table_name = NWS_ALERTS_TABLE_NAME_LOCATIONS;
$wpdb->query("DROP TABLE IF EXISTS $table_name");
$table_name = NWS_ALERTS_TABLE_NAME_CODES;
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove options
remove_option('nws_alerts_alerts_bar_enabled');
remove_option('nws_alerts_alerts_bar_zip');
remove_option('nws_alerts_alerts_bar_city');
remove_option('nws_alerts_alerts_bar_state');
remove_option('nws_alerts_alerts_bar_county');
remove_option('nws_alerts_alerts_bar_scope');
remove_option('nws_alerts_alerts_bar_fix');

remove_site_option('nws_alerts_tables_built');

// Remove any lingering transients - should never exist if activation and setup ran properly.
delete_site_transient('nws_alerts_populate_tables_args');
delete_site_transient('nws_alerts_populate_tables_current_file');
delete_site_transient('nws_alerts_populate_tables_current_part');
