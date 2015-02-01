<?php

/**
* National Weather Service Alerts uses template files to manage html output.
* Each template in the "template" plugin directory can be overridden and
* customized by copying it to your theme directory, such as the paths below:
*
*   /themes/{child-theme-name}/plugins/national-weather-service-alerts/templates/{template-name.php}
*   /themes/{parent-theme-name}/plugins/national-weather-service-alerts/templates/{template-name.php}
*
*/

$return_value .= '<article class="nws-alerts ' . trim(implode(' ', $classes)) . '" data-zip="' . $this->zip . '" data-display="' . $display . '" data-scope="' . $this->scope . '" data-refresh_rate="' . $this->refresh_rate . '">';
    // Heading
    $return_value .= '<section class="' . trim(implode(' ', $heading_args['classes'])) . '">';
        // Heading graphic
        if ($heading_args['graphic'] !== false && !empty($this->entries)) {
            $return_value .= $this->entries[0]->get_output_graphic($heading_args['graphic'], 'nws-alerts-heading-graphic');
        }

        // Heading location and scope
        if ($heading_args['location_title'] !== false) {
            $return_value .= '<span class="nws-alerts-heading-scope">Local Weather Alerts</span><h2 class="nws-alerts-heading-location">' . $heading_args['location_title'] . '</h2>';
        } else if ($this->scope === NWS_ALERTS_SCOPE_NATIONAL) {
            $return_value .= '<span class="nws-alerts-heading-scope">National Weather Alerts</span><h2 class="nws-alerts-heading-location">United States</h2>';
        } else if ($this->scope === NWS_ALERTS_SCOPE_STATE) {
            $return_value .= '<span class="nws-alerts-heading-scope">State Weather Alerts</span><h2 class="nws-alerts-heading-location">' . $this->state . '</h2>';
        } else {
            $return_value .= '<span class="nws-alerts-heading-scope">Local Weather Alerts</span><h2 class="nws-alerts-heading-location">' . $this->city . ', ' . $this->state . '</h2>';
        }

        // Heading entry event
        if ($heading_args['current_alert'] && !empty($this->entries)) {
            $return_value .= $this->entries[0]->get_output_text(false);
        } else if ($this->error) {
            $return_value .= NWS_ALERTS_ERROR_NO_XML_SHORT;
        }
    $return_value .= '</section>';
$return_value .= '</article>';

?>