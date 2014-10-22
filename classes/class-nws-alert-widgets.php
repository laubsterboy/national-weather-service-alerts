<?php
/*
* Widget for the NWS Alert plugin
*/

class NWS_Alert_Widget extends WP_Widget {

    private $defaults;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct('nws_alert_widget', 'NWS Alert', array('description' => NWS_ALERT_DESCRIPTION));

        $this->defaults = array('zip' => null,
                                'city' => null,
                                'state' => null,
                                'county' => null,
                                'display' => NWS_ALERT_DISPLAY_FULL,
                                'scope' => NWS_ALERT_SCOPE_COUNTY);
	}




    /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
        $instance = array_merge($this->defaults, $instance);

        if ($instance['scope'] !== NWS_ALERT_SCOPE_NATIONAL && $instance['scope'] !== NWS_ALERT_SCOPE_STATE && $instance['scope'] !== NWS_ALERT_SCOPE_COUNTY) $instance['scope'] = NWS_ALERT_SCOPE_COUNTY;

        $nws_alert_data = new NWS_Alert($instance['zip'], $instance['city'], $instance['state'], $instance['county'], $instance['scope']);

        if ($instance['display'] == NWS_ALERT_DISPLAY_BASIC) {
            echo $nws_alert_data->get_output_html(false);
        } else {
            echo $nws_alert_data->get_output_html(true);
        }

        unset($nws_alert_data);
	}




    /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance = array_merge($this->defaults, $new_instance);

		return $instance;
	}




    /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$instance = array_merge($this->defaults, $instance);
		?>
		<p>
            <label for="<?php echo $this->get_field_id('zip'); ?>"><?php _e('Zipcode:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('zip'); ?>" name="<?php echo $this->get_field_name('zip'); ?>" type="text" value="<?php echo esc_attr($instance['zip']); ?>">
		</p>
		<?php
	}
}

?>
