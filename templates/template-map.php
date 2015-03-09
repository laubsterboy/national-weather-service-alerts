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

$return_value = '
    <script type="text/javascript">
        function initialize' . $this->zip . $this->scope . '() {
            var mapOptions = {
                zoom: ' . ($this->scope === NWS_ALERTS_SCOPE_COUNTY ? '8' : '6') . ',
                center: new google.maps.LatLng(' . $this->latitude . ', ' . $this->longitude . '),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{"stylers": [{"hue": "#ff1a00"},{"invert_lightness": true},{"saturation": -100},{"lightness": 33},{"gamma": 0.5}]},{"featureType": "water","elementType": "geometry","stylers": [{"color": "#2D333C"}]}]
            };

            var nwsAlertTriangle;

            var map = new google.maps.Map(document.getElementById("nws-alerts-map-' . $this->zip . $this->scope . '"), mapOptions);

            ' . $google_map_polys . '
        }

        ' . ((defined('DOING_AJAX') && DOING_AJAX) ? ('initialize' . $this->zip . $this->scope . '()') : ('google.maps.event.addDomListener(window, "load", initialize' . $this->zip . $this->scope . ');')) . '

    </script>
    <section id="nws-alerts-map-' . $this->zip . $this->scope . '" class="nws-alerts-map"></section>';

?>
