<?php
/*
* NWS Alerts Plugin Global constants
*/

global $wpdb, $tinymce_version;

// NWS Alerts Version Information
define('NWS_ALERTS_VERSION', '1.1.1');
define('NWS_ALERTS_MIN_WP_VERSION', '3.1');
define('NWS_ALERTS_MIN_PHP_VERSION', '5');
if (intval(substr($tinymce_version, 0, 1)) === 4) {
    define('NWS_ALERTS_TINYMCE_4', true);
} else {
    define('NWS_ALERTS_TINYMCE_4', false);
}

define('NWS_ALERTS_DESCRIPTION', 'Easily add official National Weather Service alerts to your website.');
define('NWS_ALERTS_PATH', trailingslashit(basename(dirname(__FILE__))));
define('NWS_ALERTS_ABSPATH', plugin_dir_path(__FILE__));
define('NWS_ALERTS_URL', plugins_url('/', __FILE__));

define('NWS_ALERTS_BAR_ENABLED', filter_var(get_option('nws_alerts_alerts_bar_enabled'), FILTER_VALIDATE_BOOLEAN));
define('NWS_ALERTS_BAR_ZIP', get_option('nws_alerts_alerts_bar_zip'));
define('NWS_ALERTS_BAR_CITY', get_option('nws_alerts_alerts_bar_city'));
define('NWS_ALERTS_BAR_STATE', get_option('nws_alerts_alerts_bar_state'));
define('NWS_ALERTS_BAR_COUNTY', get_option('nws_alerts_alerts_bar_county'));
define('NWS_ALERTS_BAR_SCOPE', get_option('nws_alerts_alerts_bar_scope'));
define('NWS_ALERTS_BAR_FIX', get_option('nws_alerts_alerts_bar_fix'));

define('NWS_ALERTS_TABLES_BUILT', filter_var(get_site_option('nws_alerts_tables_built'), FILTER_VALIDATE_BOOLEAN));
define('NWS_ALERTS_TABLE_NAME_CODES', $wpdb->prefix . 'nws_alerts_codes');
define('NWS_ALERTS_TABLE_NAME_LOCATIONS', $wpdb->prefix . 'nws_alerts_locations');

// Location Scope
define('NWS_ALERTS_SCOPE_COUNTY', 'county');
define('NWS_ALERTS_SCOPE_STATE', 'state');
define('NWS_ALERTS_SCOPE_NATIONAL', 'national');

// Display
define('NWS_ALERTS_DISPLAY_BAR', 'bar');
define('NWS_ALERTS_DISPLAY_BASIC', 'basic');
define('NWS_ALERTS_DISPLAY_FULL', 'full');

// NWS Alerts Error messages
define('NWS_ALERTS_ERROR_NO_ACTIVATION', 'The <strong>National Weather Service Alerts</strong> plugin requires WordPress version ' . NWS_ALERTS_MIN_WP_VERSION . ' or greater and PHP version ' . NWS_ALERTS_MIN_PHP_VERSION . ' or greater. Please update WordPress before activating the plugin.');
define('NWS_ALERTS_ERROR_NO_ENTRIES', 'There are currently no active weather alerts.');
define('NWS_ALERTS_ERROR_NO_LOCATION', 'The specified location could not be found. Try specifying a county and state instead.');
define('NWS_ALERTS_ERROR_NO_XML', 'There was an error retrieving the National Weather Service alert data.');
define('NWS_ALERTS_ERROR_NO_XML_SHORT', 'Data Error');

// NWS body class
function nws_alerts_body_class($classes) {

    if (NWS_ALERTS_BAR_ENABLED) {
        $classes[] = 'nws-alerts-bar-enabled';
        define('NWS_ALERTS_BODY_CLASS_SUPPORT', true);
    } else {
        define('NWS_ALERTS_BODY_CLASS_SUPPORT', false);
    }

    return $classes;
}
add_filter('body_class', 'nws_alerts_body_class');

?>
