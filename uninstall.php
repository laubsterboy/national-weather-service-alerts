<?php

// If uninstall not called from WordPress then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Remove the tables created by the NWS Alerts plugin
//require_once('nws-alert-globals.php');

global $wpdb;

$table_name = NWS_ALERT_TABLE_NAME_LOCATIONS;
$wpdb->query("DROP TABLE IF EXISTS $table_name");
$table_name = NWS_ALERT_TABLE_NAME_CODES;
$wpdb->query("DROP TABLE IF EXISTS $table_name");
