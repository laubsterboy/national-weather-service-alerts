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
        //check_ajax_referer($this->nonce_string, 'security');


        $s_zip = isset($_POST['zip']) ? sanitize_text_field($_POST['zip']) : '';
		$s_display = isset($_POST['display']) ? sanitize_text_field($_POST['display']) : '';
		$s_scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : '';
        if (empty($s_zip) || empty($s_display) || empty($s_scope)) {
            echo 0;
            die();
        }

        $nws_alerts_data = new NWS_Alerts(array('zip' => $s_zip, 'scope' => $s_scope));

        if ($s_display == NWS_ALERTS_DISPLAY_BASIC) {
            echo $nws_alerts_data->get_output_html(false);
        } else {
            echo $nws_alerts_data->get_output_html(true);
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
}
