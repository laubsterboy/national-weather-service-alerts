<?php
/**
* NWS_Alert Shortcodes
*
* @since 1.0.0
*/

class NWS_Alert_Shortcodes {
    /**
    * NWS_Alert_Shortcodes constructor
    */
    public function __construct() {
        if (!is_admin()) add_shortcode('nws_alert', array($this, 'shortcode_handler'));
    }




    /**
    *
    *
    * The NWS_Alert_Plugin shortcode handler
    *
    * @param array $atts an array containing the attributes specified in the shortcode_handler
    *
    * @return string
    */
    public function shortcode_handler($atts) {
        extract(shortcode_atts(array('zip' => null, 'city' => null, 'state' => null, 'county' => null, 'display' => null, 'scope' => 'county'), $atts));

        // Sanitize user input
        $zip = $zip === null ? null : sanitize_text_field($zip);
        $city = $city === null ? null : sanitize_text_field($city);
        $state = $state === null ? null : sanitize_text_field($state);
        $county = $county === null ? null : sanitize_text_field($county);
        $display = $display === null ? null : sanitize_text_field($display);
        $scope = (string) sanitize_text_field($scope);

        if ($scope !== 'national' && $scope !== 'state' && $scope !== 'county') $scope = 'county';

        $nws_alert_data = new NWS_Alert($zip, $city, $state, $county, $scope);

        if ($display == 'basic') {
            return $nws_alert_data->get_output_html_basic($nws_alert_data);
        } else {
            return $nws_alert_data->get_output_html_full($nws_alert_data);
        }

        unset($nws_alert_data);
    }
}
