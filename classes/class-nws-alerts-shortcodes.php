<?php
/**
* NWS_Alerts Shortcodes
*
* @since 1.0.0
*/

class NWS_Alerts_Shortcodes {
    /**
    *
    *
    * The NWS_Alerts_Plugin shortcode handler
    *
    * @param array $atts an array containing the attributes specified in the shortcode_handler
    *
    * @return string
    */
    public static function shortcode_handler($atts) {
        extract(shortcode_atts(array('zip' => null, 'city' => null, 'state' => null, 'county' => null, 'display' => NWS_ALERTS_DISPLAY_FULL, 'scope' => NWS_ALERTS_SCOPE_COUNTY), $atts));

        if ($scope !== NWS_ALERTS_SCOPE_NATIONAL && $scope !== NWS_ALERTS_SCOPE_STATE && $scope !== NWS_ALERTS_SCOPE_COUNTY) $scope = NWS_ALERTS_SCOPE_COUNTY;

        $nws_alerts_data = new NWS_Alerts(array('zip' => $zip, 'city' => $city, 'state' => $state, 'county' => $county, 'scope' => $scope));

        if ($display == NWS_ALERTS_DISPLAY_BASIC) {
            return $nws_alerts_data->get_output_html(false);
        } else {
            return $nws_alerts_data->get_output_html(true);
        }

        unset($nws_alerts_data);
    }
}