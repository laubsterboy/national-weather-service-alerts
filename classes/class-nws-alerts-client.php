<?php
/**
* NWS_Alerts Client
*
* @since 1.0.0
*/

class NWS_Alerts_Client {

    var $nonce_string = 'nws_alerts_nonce';
    var $nonce;

    /*
    * set_ajaxurl
    *
    * Is called when the NWS_Alerts plugin is activated and creates necessary database tables and populates them with data
    *
    * @return void
    * @access public
    */
    public static function set_ajaxurl() {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }




    /*
    * refresh
    *
    * Is called from the front-end to update the alert data.
    *
    * @return void
    * @access public
    */
    public static function refresh() {

        $s_zip = isset($_POST['zip']) ? sanitize_text_field($_POST['zip']) : '';
		$s_display = isset($_POST['display']) ? sanitize_text_field($_POST['display']) : '';
		$s_scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : '';
        $s_classes = isset($_POST['classes']) ? sanitize_text_field($_POST['classes']) : array();
        if (empty($s_zip) || empty($s_display) || empty($s_scope)) {
            echo 0;
            die();
        }

        $nws_alerts_data = new NWS_Alerts(array('zip' => $s_zip, 'scope' => $s_scope));

        if ($s_display == NWS_ALERTS_DISPLAY_BASIC) {
            echo $nws_alerts_data->get_output_html(false, $s_classes);
        } else {
            echo $nws_alerts_data->get_output_html(true, $s_classes);
        }

        die();
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
        wp_enqueue_style('nws-alerts-css', NWS_ALERTS_URL . 'css/nws-alerts.css');

        /* JavaScript */
        wp_enqueue_script('nws-alerts-js', NWS_ALERTS_URL . 'js/nws-alerts.js', array('jquery'), null, true);
        wp_enqueue_script('google-map-api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=weather&sensor=false', false, null, false);
    }




    /*
    *
    */
    public static function buffer_callback($buffer) {
        if (NWS_ALERTS_BODY_CLASS_SUPPORT) {
            $nws_alerts_data = new NWS_Alerts(array('zip' => NWS_ALERTS_BAR_ZIP,
                                                    'city' => NWS_ALERTS_BAR_CITY,
                                                    'state' => NWS_ALERTS_BAR_STATE,
                                                    'county' => NWS_ALERTS_BAR_COUNTY,
                                                    'scope' => NWS_ALERTS_BAR_SCOPE));
            $classes = '';
            if (NWS_ALERTS_BAR_FIX) $classes .= 'nws-alerts-bar-fix';

            $body_tag_start_pos = stripos($buffer, '<body');
            $body_tag_end_pos = stripos($buffer, '>', $body_tag_start_pos) + 1;
            $buffer = substr_replace($buffer, $nws_alerts_data->get_output_html(NWS_ALERTS_DISPLAY_BAR, $classes), $body_tag_end_pos, 0);
        }

        return $buffer;
    }

    public static function buffer_start() {
        if (NWS_ALERTS_BAR_ENABLED) {
            ob_start("NWS_Alerts_Client::buffer_callback");
        }
    }

    public static function buffer_end() {
        if (NWS_ALERTS_BAR_ENABLED) {
            ob_end_flush();
        }
    }
}
