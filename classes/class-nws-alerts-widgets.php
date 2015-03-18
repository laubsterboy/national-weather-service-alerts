<?php
/*
* Widget for the NWS Alerts plugin
*/

class NWS_Alerts_Widget extends WP_Widget {

    private $defaults;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct('nws_alert_widget', 'NWS Alerts', array('description' => NWS_ALERTS_DESCRIPTION));

        $this->defaults = array('zip' => false,
                                'city' => false,
                                'state' => false,
                                'county' => false,
                                'location_title' => false,
                                'display' => NWS_ALERTS_DISPLAY_DEFAULT,
                                'scope' => NWS_ALERTS_SCOPE_COUNTY,
                                'limit' => 0);
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
        $instance = wp_parse_args($instance, $this->defaults);

        if ($instance['scope'] !== NWS_ALERTS_SCOPE_NATIONAL && $instance['scope'] !== NWS_ALERTS_SCOPE_STATE && $instance['scope'] !== NWS_ALERTS_SCOPE_COUNTY) $instance['scope'] = NWS_ALERTS_SCOPE_COUNTY;

        if (!empty($instance['zip']) || (!empty($instance['city']) && !empty($instance['state'])) || (!empty($instance['state']) && !empty($instance['county']))) {
            $nws_alert_data = new NWS_Alerts(array('zip' => $instance['zip'], 'city' => $instance['city'], 'state' => $instance['state'], 'county' => $instance['county'], 'scope' => $instance['scope'], 'limit' => $instance['limit']));

            echo $args['before_widget'];
            echo $nws_alert_data->get_output_html($instance['display'], 'nws-alerts-widget', array('location_title' => $instance['location_title'], 'widget' => true, 'widget_before_title' => $args['before_title'], 'widget_after_title' => $args['after_title']));
            echo $args['after_widget'];

            unset($nws_alert_data);
        }
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
		$instance = wp_parse_args($new_instance, $this->defaults);

        if ($new_instance['location_title'] === '') $instance['location_title'] = false;

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
		$instance = wp_parse_args($instance, $this->defaults);
		?>
		<p>
            <label for="<?php echo $this->get_field_id('zip'); ?>">Zipcode:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('zip'); ?>" name="<?php echo $this->get_field_name('zip'); ?>" type="text" value="<?php echo esc_attr($instance['zip']); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('city'); ?>">City:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" value="<?php echo esc_attr($instance['city']); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('state'); ?>">State:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('state'); ?>" name="<?php echo $this->get_field_name('state'); ?>">
            <?php
                foreach (NWS_Alerts_Utils::get_states() as $state) {
                    echo '<option value="' . $state['abbrev'] . '"' . selected($instance['state'], $state['abbrev'], false) . '>' . $state['name'] . '</option>';
                }
            ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('county'); ?>">County:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('county'); ?>" name="<?php echo $this->get_field_name('county'); ?>" type="text" value="<?php echo esc_attr($instance['county']); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('location_title'); ?>">Location Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('location_title'); ?>" name="<?php echo $this->get_field_name('location_title'); ?>" type="text" value="<?php echo esc_attr($instance['location_title']); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('display'); ?>">Display:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <?php foreach (NWS_Alerts_Utils::$displays as $display => $name) { ?>
                    <option value="<?php echo $display; ?>"<?php selected($instance['display'], $display) ?>><?php echo $name ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('scope'); ?>">Scope:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('scope'); ?>" name="<?php echo $this->get_field_name('scope'); ?>">
                <option value="<?php echo NWS_ALERTS_SCOPE_COUNTY ?>"<?php selected($instance['scope'], NWS_ALERTS_SCOPE_COUNTY) ?>><?php echo NWS_ALERTS_SCOPE_COUNTY ?></option>
                <option value="<?php echo NWS_ALERTS_SCOPE_STATE ?>"<?php selected($instance['scope'], NWS_ALERTS_SCOPE_STATE) ?>><?php echo NWS_ALERTS_SCOPE_STATE ?></option>
                <option value="<?php echo NWS_ALERTS_SCOPE_NATIONAL ?>"<?php selected($instance['scope'], NWS_ALERTS_SCOPE_NATIONAL) ?>><?php echo NWS_ALERTS_SCOPE_NATIONAL ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>">Limit:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($instance['limit']); ?>">
		</p>
		<?php
	}
}

?>
