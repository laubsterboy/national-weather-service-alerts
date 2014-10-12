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
        extract(shortcode_atts(array('zip' => null, 'city' => null, 'state' => null, 'county' => null, 'display' => null, 'scope' => 'county'), $atts));

        // Sanitize user input
        $zip = $zip === null ? null : sanitize_text_field($zip);
        $city = $city === null ? null : sanitize_text_field($city);
        $state = $state === null ? null : sanitize_text_field($state);
        $county = $county === null ? null : sanitize_text_field($county);
        $display = $display === null ? null : sanitize_text_field($display);
        $scope = (string) sanitize_text_field($scope);

        if ($scope !== NWS_ALERT_SCOPE_NATIONAL && $scope !== NWS_ALERT_SCOPE_STATE && $scope !== NWS_ALERT_SCOPE_COUNTY) $scope = NWS_ALERT_SCOPE_COUNTY;

        $nws_alert_data = new NWS_Alert($zip, $city, $state, $county, $scope);

        if ($display == 'basic') {
            return $nws_alert_data->get_output_html(false);
        } else {
            return $nws_alert_data->get_output_html(true);
        }

        unset($nws_alert_data);
    }




    /*
    * scripts_styles
    *
    * Enqueues necessary JavaScript and Stylesheet files
    *
    * @return void
    * @access public
    */
    public static function scripts_styles() {
        // Stylesheets
        wp_enqueue_style('nws-alert-css', NWS_ALERT_URL . '/css/nws-alert.css');

        /* JavaScript */
        wp_enqueue_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=weather&sensor=false', false, null, false);
    }
}
