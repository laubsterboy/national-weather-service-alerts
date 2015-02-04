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
        $return_value .= '<span class="nws-alerts-heading-location">' . $heading_args['location'] . '</span><span class="nws-alerts-heading-scope">' . $heading_args['scope'] . '</span>';

        // Heading event entry
        $return_value .= $heading_args['alert'];
    $return_value .= '</section>';

    // Details
    $return_value .= '<section class="nws-alerts-details">';
        // Details entries
        $return_value .= '<section class="nws-alerts-entries">';
        if ($this->error) {
            // Entries error
            $return_value .= $this->error;
        } else if (!empty($this->entries)) {
            // Entries
            foreach ($this->entries as $entry) {
                $return_value .= $entry->get_output_entry();
            }
        } else {
            // Entries empty
            $return_value .= NWS_ALERTS_ERROR_NO_ENTRIES;
        }
        $return_value .= '</section>';

        // Details map
        $return_value .= $this->get_output_google_map();
    $return_value .= '</section>';
$return_value .= '</article>';

?>
