<?php
/**
* NWS_Alert Shortcodes
*
* @since 1.0.0
*/

class NWS_Alert_Shortcodes {
    /**
    *
    *
    * The NWS_Alert_Plugin shortcode handler
    *
    * @param array $atts an array containing the attributes specified in the shortcode_handler
    *
    * @return string
    */
    public static function shortcode_handler($atts) {
        extract(shortcode_atts(array('zip' => null, 'city' => null, 'state' => null, 'county' => null, 'display' => NWS_ALERT_DISPLAY_FULL, 'scope' => NWS_ALERT_SCOPE_COUNTY), $atts));

        if ($scope !== NWS_ALERT_SCOPE_NATIONAL && $scope !== NWS_ALERT_SCOPE_STATE && $scope !== NWS_ALERT_SCOPE_COUNTY) $scope = NWS_ALERT_SCOPE_COUNTY;

        $nws_alert_data = new NWS_Alert($zip, $city, $state, $county, $scope);

        if ($display == NWS_ALERT_DISPLAY_BASIC) {
            return $nws_alert_data->get_output_html(false);
        } else {
            return $nws_alert_data->get_output_html(true);
        }

        unset($nws_alert_data);
    }
}
