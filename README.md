<h1>National Weather Service Alerts</h1>
<p>The official repository for the National Weather Service Alerts WordPress plugin. This repository contains the most recent changes, including new features and bug fixes, however to download the latest stable release use the WordPress repository.</p>
<hr />
<ul>
    <li>Readme: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/readme.txt">readme.txt</a></li>
    <li>WordPress Repository: <a href="https://wordpress.org/plugins/national-weather-service-alerts/">https://wordpress.org/plugins/national-weather-service-alerts/</a></a></li>
</ul>
<hr />

<h2>Usage</h2>
<h3>Widget</h3>
<p>An <em>NWS Alerts</em> widget is included as part of the plugin and can be added to any sidebar theme location by going to your themes Appearance > Widgets, adding the widget to the desired sidebar, and then filling out the location information.</p>
<h3>Shortcode</h3>
<p>If the plugin is installed on WordPress 3.9 or greater then a new button will be added to the WordPress editor that will open a modal window which is used to build a properly formatted shortcode based on user input.</p>
<p>Regardless of WordPress version, the shortcode can still be typed out manually or added to a theme template file.</p>
<h4>Attributes</h4>
<ul>
    <li><strong>zip</strong>
        <ul>
            <li>Value: Any valid U.S. Zipcode</li>
            <li>Required: Only if city, state, and county are not set.</li>
        </ul>
    </li>
    <li><strong>city</strong>
        <ul>
            <li>Value: Any valid U.S. City</li>
            <li>Required: Only if zip and county are not set.</li>
        </ul>
    </li>
    <li><strong>state</strong>
        <ul>
            <li>Value: Any valid U.S. State</li>
            <li>Required: Only if zip and county are not set.</li>
        </ul>
    </li>
    <li><strong>county</strong>
        <ul>
            <li>Value: Any valid U.S. City</li>
            <li>Required: Only if zip and city are not set.</li>
        </ul>
    </li>
    <li><strong>location_title</strong>
        <ul>
            <li>Value: Any string of text</li>
            <li>Optional</li>
        </ul>
    </li>
    <li><strong>display</strong>
        <ul>
            <li>Value:</li>
            <li>
                <ul>
                    <li>Option: bar</li>
                    <li>Option: basic</li>
                    <li>Option: full</li>
                    <li>Option: list</</li>
                </ul>
            </li>
            <li>Default: full</li>
            <li>Required: No</li>
        </ul>
    </li>
    <li><strong>scope</strong>
        <ul>
            <li>Value:</li>
            <li>
                <ul>
                    <li>Option: county</li>
                    <li>Option: state</li>
                    <li>Option: national</li>
                </ul>
            </li>
            <li>Default: county</li>
            <li>Required: No</li>
        </ul>
    </li>
    <li><strong>limit</strong>
        <ul>
            <li>Value: Any valid integer, though 0 results in no limit</li>
            <li>Default: empty</li>
            <li>Required: No</li>
        </ul>
    </li>
</ul>
<h4>Example</h4>
<pre>[nws_alerts zip="90001" city="Los Angeles" state="California" county="Los Angeles" display="full" scope="county" limit="3"]</pre>
<h4>Example for a theme page template</h4>
<pre>echo do_shortcode('[nws_alerts zip="90001" city="Los Angeles" state="California" county="Los Angeles" display="full" scope="county" limit="3"]');</pre>

<h2>Reference</h2>
<h3>Filters</h3>
<strong>nws_alerts_allowed_alert_types</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts.php">classes/class-nws-alerts.php</a></li>
    <li>Parameters:
        <ul>
            <li>$allowed_alert_types
                <ul>
                    <li><em>(array)</em> Alerts with these alert types are allowed to display.</li>
                </ul>
            </li>
            <li>$args
                <ul>
                    <li><em>(array)</em> Arguments used to create the NWS_Alert instance.</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
<strong>nws_alerts_allowed_msg_types</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts.php">classes/class-nws-alerts.php</a></li>
    <li>Parameters:
        <ul>
            <li>$allowed_msg_types
                <ul>
                    <li><em>(array)</em> Alerts with these message types are allowed to display.</li>
                </ul>
            </li>
            <li>$args
                <ul>
                    <li><em>(array)</em> Arguments used to create the NWS_Alert instance.</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
<strong>nws_alerts_allowed_status_types</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts.php">classes/class-nws-alerts.php</a></li>
    <li>Parameters:
        <ul>
            <li>$allowed_status_types
                <ul>
                    <li><em>(array)</em> Alerts with these status types are allowed to display.</li>
                </ul>
            </li>
            <li>$args
                <ul>
                    <li><em>(array)</em> Arguments used to create the NWS_Alert instance.</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
<strong>nws_alerts_sort_alert_types</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts.php">classes/class-nws-alerts.php</a></li>
    <li>Parameters:
        <ul>
            <li>$alert_types
                <ul>
                    <li><em>(array)</em> The order of the alert types in the array is the order in which alert entries will be displayed. The default order places the most severe and life threatening alerts first.</li>
                </ul>
            </li>
            <li>$args
                <ul>
                    <li><em>(array)</em> Arguments used to create the NWS_Alert instance.</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>
<strong>nws_alerts_template_path</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts-utils.php">classes/class-nws-alerts-utils.php</a></li>
    <li>Use: Can be used to specify an alternative location for display templates, by modifying the $paths array, to be used with a plugin or a custom location within a theme.</li>
    <li>Parameters:
        <ul>
            <li>$paths
                <ul>
                    <li><em>(array)</em> The default array of paths used to search for a display template. The first path is the child theme, second is the parent theme, and the last is the NWS Alerts plugin directory. For theme directories the paths include 'plugins/national-weather-service-alerts/templates/'.</li>
                </ul>
            </li>
            <li>$template_filename
                <ul>
                    <li><em>(string)</em> The filename of the current display template being searched for.</li>
                </ul>
            </li>
        </ul>
    </li>
</ul>

<h3>Functions</h3>
<strong>NWS_Alerts_Utils::register_display_template</strong>
<ul>
    <li>Source File: <a href="https://github.com/laubsterboy/national-weather-service-alerts/blob/master/classes/class-nws-alerts-utils.php">classes/class-nws-alerts-utils.php</a></li>
    <li>Parameters:
        <ul>
            <li>$args = array('display' => 'identifier', 'name' => 'Title')</li>
        </ul>
    </li>
</ul>