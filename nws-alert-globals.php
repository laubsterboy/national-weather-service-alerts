<?php
// NWS Alert Plugin Global Variables
global $wpdb, $tinymce_version;

define('NWS_ALERT_TABLE_NAME_CODES', $wpdb->prefix . 'nws_alert_codes');
define('NWS_ALERT_TABLE_NAME_LOCATIONS', $wpdb->prefix . 'nws_alert_locations');

// NWS Alert Version Information
define('NWS_ALERT_VERSION', '1.0.0');
if (intval(substr($tinymce_version, 0, 1)) === 4) {
    define('NWS_ALERT_TINYMCE_4', true);
} else {
    define('NWS_ALERT_TINYMCE_4', false);
}

// NWS Alert Error messages
define('NWS_ALERT_ERROR_NO_ALERTS', 'NWS Alert: There are currently no active alerts for the specified location.');
define('NWS_ALERT_ERROR_NO_LOCATION', 'NWS Alert: The specified location could not be found. Try specifying a county and state instead.');
?>
