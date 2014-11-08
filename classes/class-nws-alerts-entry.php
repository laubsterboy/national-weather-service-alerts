<?php
/**
* NWS_Alerts_Entry functions and utility function.
*
* @since 1.0.0
*/

class NWS_Alerts_Entry {
    /**
    * The ID of the NWS_Alerts entry - a string URL pointing to the XML file for the specific alert entry.
    *
    * @var string
    */
    public $ID = '';

    /**
    * The date when the NWS_Alerts entry was last updated.
    *
    * @var string
    */
    public $updated = '0000-00-00T00:00:00-00:00';

    /**
    * The date when the NWS_Alerts entry was published.
    *
    * @var string
    */
    public $published = '0000-00-00T00:00:00-00:00';

    /**
    * The title of the NWS_Alerts entry.
    *
    * @var string
    */
    public $title = '';

    /**
    * The summary of the NWS_Alerts entry.
    *
    * @var string
    */
    public $summary = '';

    /**
    * CAP data: The event of the NWS_Alerts entry - example is 'Severe Thunderstorm Warning', 'Tornado Warning', 'Flash Flood Warning', and several variations of watches.
    *
    * @var string
    */
    public $cap_event = '';

    /**
    * CAP data: Same as cap_event, but all lowercase and spaces are replaced with hyphens.
    *
    * @var string
    */
    public $cap_event_slug = '';

    /**
    * CAP data: The effective date of the NWS_Alerts entry.
    *
    * @var string
    */
    public $cap_effective = '0000-00-00T00:00:00-00:00';

    /**
    * CAP data: The expiration date of the NWS_Alerts entry.
    *
    * @var string
    */
    public $cap_expires = '0000-00-00T00:00:00-00:00';

    /**
    * CAP data: The status of the NWS_Alerts entry - example is 'Actual'.
    *
    * @var string
    */
    public $cap_status = '';

    /**
    * CAP data: The message type of the NWS_Alerts entry - example is 'Alert'.
    *
    * @var string
    */
    public $cap_msg_type = '';

    /**
    * CAP data: The category type of the NWS_Alerts entry - example is 'Met'.
    *
    * @var string
    */
    public $cap_category = '';

    /**
    * CAP data: The urgency of the NWS_Alerts entry - example is 'Immediate'.
    *
    * @var string
    */
    public $cap_urgency = '';

    /**
    * CAP data: The severity of the NWS_Alerts entry - example is 'Severe', or 'Moderate'.
    *
    * @var string
    */
    public $cap_severity = '';

    /**
    * CAP data: The certainty of the NWS_Alerts entry - example is 'Likely', or 'Observed'.
    *
    * @var string
    */
    public $cap_certainty = '';

    /**
    * CAP data: The area description of the NWS_Alerts entry - example is 'Clay; Fountain; Hendricks; Montgomery; Owen; Parke; Putnam; Vermillion; Vigo'.
    *
    * @var array
    */
    public $cap_area_desc = array();

    /**
    * CAP data: Latitude and longitude coordinates that create a polygon of the NWS_Alerts entry - example is '39.89,-87.53 39.84,-86.85 39.47,-86.99 39.65,-87.54 39.87,-87.54 39.88,-87.54 39.89,-87.53'.
    *
    * @var array
    */
    public $cap_polygon = array();




    /**
    * NWS_Alerts_Entry constructor
    *
    * @return void
    */
    public function __construct($entry) {
        foreach ($entry as $key => $value) {
            if (!empty($value)) $this->$key = $value;
        }

        // cap_event_slug for use in CSS classes
        $this->cap_event_slug = str_replace(' ', '-', strtolower($this->cap_event));
    }

    /**
    * get_output_google_map_points
    *
    * @return string/boolean
    */
    public function get_output_google_map_points() {
        if (!empty($this->cap_polygon)) {
            $google_map_points = '';
            $polygon_points = explode(' ', $this->cap_polygon);

            foreach($polygon_points as $polygon_point) {
                $google_map_points .= 'new google.maps.LatLng(' . $polygon_point . '),';
            }

            $google_map_points = rtrim($google_map_points, ',');

            return $google_map_points;
        } else {
            return false;
        }
    }




    /**
    * get_output_google_map_polys
    *
    * @return string/boolean
    */
    public function get_output_google_map_polys() {
        $google_map_points = $this->get_output_google_map_points();

        if ($google_map_points === false) return false;

        if (strpos($this->cap_event, 'Tornado') !== false) { $google_map_point_color = 'strokeColor:"#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor:"#FF0000", fillOpacity: 0.2';
        } else if (strpos($this->cap_event, 'Thunderstorm') !== false) { $google_map_point_color = 'strokeColor:"#ffdf00", strokeOpacity: 1.0, strokeWeight: 2, fillColor:"#ffdf00", fillOpacity: 0.2';
        } else if (strpos($this->cap_event, 'Flood') !== false) { $google_map_point_color = 'strokeColor:"#00AA00", strokeOpacity: 0.8, strokeWeight: 2, fillColor:"#00AA00", fillOpacity: 0.2';
        } else if (strpos($this->cap_event, 'Blizzard') !== false || strpos($this->cap_event, 'Winter') !== false || strpos($this->cap_event, 'Freeze') !== false) { $google_map_point_color = 'strokeColor:"#FFFFFF", strokeOpacity: 0.8, strokeWeight: 2, fillColor:"#FFFFFF", fillOpacity: 0.2';
        } else { return false; }

        $google_map_polys = '

            var nwsAlertsTriangleCoords' . ' = [' . $google_map_points . '];

            nwsAlertsTriangle' . ' = new google.maps.Polygon({
                paths: nwsAlertsTriangleCoords,
                ' . $google_map_point_color . '
            });

            nwsAlertsTriangle' . '.setMap(map);';

        return $google_map_polys;
    }




    /**
    * get_output_graphic
    *
    * @return string/boolean
    */
    public function get_output_graphic($size = 1, $class = '') {
        if ($size === false) return;

        // Size
		if ($size == 3) {
			$_size = 'nws-alerts-size-large';
		} else if ($size == 2) {
			$_size = 'nws-alerts-size-medium';
		} else {
			$_size = 'nws-alerts-size-small';
		}

        $return_value = ($size === 3 ? '<span class="nws-alerts-graphic-container">' : '') . '<span class="nws-alerts-' . $this->cap_event_slug . '-graphic ' . $_size . ($class === '' ? '' : ' ' . $class) . '"></span>' . ($size === 3 ? '</span>' : '');

        return $return_value;
    }




    /**
    * get_output_text
    *
    * @return string/boolean
    */
    public function get_output_text($details = true) {

        // Size
		if ($details) {
            $return_value = '<span class="nws-alerts-event">' . $this->cap_event . '</span><br /> ' . ucwords(strtolower($this->summary));
        } else {
            $return_value = '<span class="nws-alerts-event">' . $this->cap_event . '</span>';
        }

        return $return_value;
    }



    /**
    * get_output_entry
    *
    * @param array $args Contains the options for what to output
    *
    * @return string/boolean
    */
    public function get_output_entry($args = array()) {
        $defaults = array('details' => true,
                          'graphic' => 1,
                          'prefix' => '<p>',
                          'suffix' => '</p>');
        $args = wp_parse_args($args, $defaults);

        $args['prefix'] = NWS_Alerts_Utils::str_lreplace('>', ' class="nws-alerts-entry">', $args['prefix']);

        $return_value = $args['prefix'] . $this->get_output_graphic($args['graphic'], 'nws-alerts-entry-graphic') . $this->get_output_text($args['details']) . $args['suffix'];

        /* add_feature - add filter to allow entry output to be filtered prior to being output as markup */

        return $return_value;
    }
}

?>
