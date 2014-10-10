<?php
/**
* NWS_Alert Admin
*
* @since 1.0.0
*/

class NWS_Alert_Admin {

    public $utils = null;

    /**
    * NWS_Alert_Admin constructor
    */
    public function __construct() {
        if (is_admin()) {
            require_once('nws-alert-utils.php');

            // Initialize NWS_Alert_Plugin object parameters
            $this->utils = new NWS_Alert_Utils();

            // WordPress Editor Buttons - TinyMCE Plugins
            add_action('admin_head', array($this, 'admin_head_action'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_action'));
        }
    }




    /**
    * admin_head_action
    *
    * Checks to see if the current admin screen is a valid post type
    *
    * @return void
    */
    public function admin_head_action() {
        global $typenow;

        if (empty($typenow)) return;

        if (NWS_ALERT_TINYMCE_4) {
            add_filter('mce_external_plugins', array($this, 'mce_external_plugins_filter'));
            add_filter('mce_buttons', array($this, 'mce_buttons_filter'));
            add_action('after_wp_tiny_mce', array($this, 'mce_markup'));
        }
    }




    /**
    * admin_enqueue_scripts_action
    *
    * Admin styles for editor
    *
    * @return   void
    * @access   public
    */
    public function admin_enqueue_scripts_action() {
	    wp_enqueue_style('nws-alert-admin-css', plugins_url('/css/nws-alert-admin.css', basename(dirname(__FILE__)).'/'.basename(__FILE__)));
    }




/**
    * mce_external_plugins_filter
    *
    * Adds the custom NWS Alert TinyMCE plugin
    *
    * @return void
    */
    public function mce_external_plugins_filter($plugins) {
        $plugins['nws_alert'] = plugins_url('/js/nws-alert-mce-plugin.js', basename(dirname(__FILE__)).'/'.basename(__FILE__));

        return $plugins;
    }




    /**
    * mce_buttons_filter
    *
    * Adds the custom NWS Alert TinyMCE plugin
    *
    * @return void
    */
    public function mce_buttons_filter($buttons) {
        array_push($buttons, 'nws_alert_shortcodes');

        return $buttons;
    }




    /**
    * mce_markup
    *
    * The html markup for the NWS Alert TinyMCE Plugin
    *
    * @return   void
    * @access   public
    */
    public function mce_markup() {
        echo $this->get_mce_modal('shortcodes');
    }




    /**
    * get_mce_modal
    *
    * The html markup for the NWS Alert TinyMCE Plugin - Modals
    *
    * @return   void
    * @access   public
    */
    public function get_mce_modal($modal) {
        if ($modal === 'shortcodes') {
            $return_value = '';
            $modal_id_prefix = 'nws-alert';
            $control_id_prefix = $modal_id_prefix . '-' . $modal;

            $return_value .= '<div id="' . $control_id_prefix . '-backdrop" style="display: none"></div>';
            $return_value .= '<div id="' . $control_id_prefix . '-wrap" class="wp-core-ui" style="display: none">';
            $return_value .= '<form id="' . $control_id_prefix . '" tabindex="-1">';
                $return_value .= '<div id="' . $control_id_prefix . '-modal-title" class="' . $modal_id_prefix . '-modal-title">Insert NWS Alert Shortcode<div id="' . $control_id_prefix . '-close" class="' . $modal_id_prefix . '-close" tabindex="0"></div></div>';

                $return_value .= '<div class="' . $modal_id_prefix . '-options">';
                    $return_value .= '<div id="' . $control_id_prefix . '-errors" class="' . $modal_id_prefix . '-errors"></div>';
                    $return_value .= '<table>';
                        $return_value .= '<tbody>';

                            $return_value .= $this->get_mce_control('zip', $control_id_prefix);
                            $return_value .= $this->get_mce_control('city', $control_id_prefix);
                            $return_value .= $this->get_mce_control('state', $control_id_prefix);
                            $return_value .= $this->get_mce_control('county', $control_id_prefix);
                            $return_value .= $this->get_mce_control('display', $control_id_prefix);
                            $return_value .= $this->get_mce_control('scope', $control_id_prefix);


                        $return_value .= '</tbody>';
                    $return_value .= '</table>';
                $return_value .= '</div>';

                $return_value .= '<div class="submitbox ' . $modal_id_prefix . '-submitbox">';
                    $return_value .= '<div id="' . $control_id_prefix . '-update" class="' . $modal_id_prefix . '-update">';
                        $return_value .= '<input type="submit" value="Insert NWS Alert Shortcode" class="button button-primary" id="' . $control_id_prefix . '-submit" name="' . $control_id_prefix . '-submit">';
                    $return_value .= '</div>';
                    $return_value .= '<div id="' . $control_id_prefix . '-cancel" class="' . $modal_id_prefix . '-cancel">';
                        $return_value .= '<a class="submitdelete deletion" href="#">Cancel</a>';
                    $return_value .= '</div>';
                $return_value .= '</div>';
            $return_value .= '</form>';
            $return_value .= '</div>';
        }

        return $return_value;
    }




    /**
    * get_mce_control
    *
    * The html markup for the NWS Alert TinyMCE Plugin - Controls
    *
    * @return   void
    * @access   public
    */
    public function get_mce_control($control, $control_id_prefix) {
    	$return_value = '';

        if ($control === 'zip') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Zipcode</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'city') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>City</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'state') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>State</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            foreach ($this->utils->get_states() as $state) {
                                if ($state['abbrev'] === 'AL') {
                                    $return_value .= '<option value="' . $state['abbrev'] . '" selected="selected">' . $state['name'] . '</option>';
                                } else {
                                    $return_value .= '<option value="' . $state['abbrev'] . '">' . $state['name'] . '</option>';
                                }
                            }
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'county') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>County</h4></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-text-container">';
				        $return_value .= '<input data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '" type="text" />';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'display') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Display</h4><p class="howto">Full: Graphic, Scope, Location, and Alert Type for the most severe current alert, and mouse over for all other alerts (including Alert Descriptions and Google Map) within the scope of the designated location.</p><p class="howto">Basic: Graphic, Scope, Location, and Alert Type for the most severe current alert, but no mouse over details.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            $return_value .= '<option value="full" selected="selected">Full</option>';
                            $return_value .= '<option value="basic">Basic</option>';
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        } else if ($control === 'scope') {
            $return_value .= '<tr>';
				$return_value .= '<td><h4>Scope</h4><p class="howto">Show alerts at only the selected county, state, or national level.</p></td>';
				$return_value .= '<td>';
					$return_value .= '<div class="nws-alert-control-select-container">';
                        $return_value .= '<select data-control-parent="' . $control . '" data-control="' . $control . '" id="' . $control_id_prefix . '-' . $control . '" name="' . $control_id_prefix . '-' . $control . '">';
                            $return_value .= '<option value="county" selected="selected">County</option>';
                            $return_value .= '<option value="state">State</option>';
                            $return_value .= '<option value="national">National</option>';
                        $return_value .= '</select>';
					$return_value .= '</div>';
				$return_value .= '</td>';
			$return_value .= '</tr>';
        }

        return $return_value;
    }
}
