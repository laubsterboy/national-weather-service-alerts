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
    * refresh
    *
    * Is called from the front-end to update the alert data.
    *
    * @return void
    * @access public
    */
    public static function refresh() {
        $s_zip = isset($_POST['zip']) ? sanitize_text_field($_POST['zip']) : '';
        $s_scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : NWS_ALERTS_SCOPE_COUNTY;
        $s_limit = isset($_POST['limit']) ? sanitize_text_field($_POST['limit']) : '';
        $s_display = isset($_POST['display']) ? sanitize_text_field($_POST['display']) : NWS_ALERTS_DISPLAY_DEFAULT;
        $s_classes = isset($_POST['classes']) ? sanitize_text_field($_POST['classes']) : array();
        $s_location_title = isset($_POST['location_title']) ? sanitize_text_field($_POST['location_title']) : false;

        if (empty($s_zip) || empty($s_display) || empty($s_scope)) {
            echo 0;
            die();
        }

        $nws_alerts_data = new NWS_Alerts(array('zip' => $s_zip, 'scope' => $s_scope, 'limit' => $s_limit));

        echo $nws_alerts_data->get_output_html($s_display, $s_classes, array('location_title' => $s_location_title));

        die();
    }



    /*
    * register_display_templates
    *
    * Registers the default display templates
    *
    * @return void
    * @access public
    */
    public static function register_display_templates() {
        NWS_Alerts_Utils::register_display_template(array('display' => 'bar', 'name' => 'Bar'));
        NWS_Alerts_Utils::register_display_template(array('display' => 'basic', 'name' => 'Basic'));
        NWS_Alerts_Utils::register_display_template(array('display' => 'full', 'name' => 'Full'));
        NWS_Alerts_Utils::register_display_template(array('display' => 'list', 'name' => 'List'));
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
    * buffer_start
    *
    * Is called by wp_head and starts an output buffer so that the NWS Alerts Bar can be added immediately after the <body> tag.
    *
    * @return void
    * @access public
    */
    public static function buffer_start() {
        if (NWS_ALERTS_BAR_ENABLED) {
            ob_start();
        }
    }



    /*
    * buffer_end
    *
    * Is called by wp_footer and clears the previously started output buffer and adds the NWS Alerts Bar immediately after the <body> tag.
    *
    * @return void
    * @access public
    */
    public static function buffer_end() {
        if (NWS_ALERTS_BAR_ENABLED) {
            $buffer = ob_get_clean();

            if (NWS_ALERTS_BODY_CLASS_SUPPORT) {
                $nws_alerts_data = new NWS_Alerts(array('zip' => NWS_ALERTS_BAR_ZIP,
                                                        'city' => NWS_ALERTS_BAR_CITY,
                                                        'state' => NWS_ALERTS_BAR_STATE,
                                                        'county' => NWS_ALERTS_BAR_COUNTY,
                                                        'scope' => NWS_ALERTS_BAR_SCOPE));
                $classes = '';
                if (NWS_ALERTS_BAR_FIX) $classes .= 'nws-alerts-bar-fix';
                $location_title = false;
                if (NWS_ALERTS_BAR_LOCATION_TITLE !== false && NWS_ALERTS_BAR_LOCATION_TITLE !== '') $location_title = NWS_ALERTS_BAR_LOCATION_TITLE;

                $body_tag_start_pos = stripos($buffer, '<body');
                $body_tag_end_pos = stripos($buffer, '>', $body_tag_start_pos) + 1;
                $buffer = substr_replace($buffer, $nws_alerts_data->get_output_html(NWS_ALERTS_DISPLAY_BAR, $classes, array('location_title' => $location_title)), $body_tag_end_pos, 0);
            }

            echo $buffer;
        }
    }
}
