<h1>National Weather Service Alerts</h1>
<p>The official repository for the National Weather Service Alerts WordPress plugin.</p>
<hr />
<ul>
    <li>Readme: <a href="https://github.com/laubsterboy/nws-alerts/blob/master/readme.txt">https://github.com/laubsterboy/nws-alerts/blob/master/readme.txt</a></li>
    <li>WordPress Repository: <a href="https://wordpress.org/plugins/national-weather-service-alerts/">https://wordpress.org/plugins/national-weather-service-alerts/</a></a></li>
</ul>
<hr />
<h2>Usage</h2>
<h3>Widget</h3>
<p>An <em>NWS Alerts</em> widget is included as part of the plugin and can be added to any sidebar theme location by going to your themes Appearance > Widgets, adding the widget to the desired sidebar, and then filling out the location information.</p>
<h3>Shortcode</h3>
<p>If the plugin is installed on WordPress 3.9 or greater then a new button will be added to the WordPress editor that will open a modal window which is used to build a properly formatted shortcode based on user input.</p>
<p>Regardless of WordPress version, the shortcode can still be typed out manually or added to a theme template file</p>
<h4>Base Shortcode</h4>
<pre>[nws_alerts]</pre>
<p>The following shortcode attributes are accepted:</p>
<ul>
    <li>zip</li>
    <li>
        <ul>
            <li>Value: Any valid U.S. Zipcode</li>
            <li>Required: Only if city, state, and county are not set.</li>
        </ul>
    </li>
    <li>city</li>
    <li>
        <ul>
            <li>Value: Any valid U.S. City</li>
            <li>Required: Only if zip and county are not set.</li>
        </ul>
    </li>
    <li>state</li>
    <li>
        <ul>
            <li>Value: Any valid U.S. State</li>
            <li>Required: Only if zip and county are not set.</li>
        </ul>
    </li>
    <li>county</li>
    <li>
        <ul>
            <li>Value: Any valid U.S. City</li>
            <li>Required: Only if zip and city are not set.</li>
        </ul>
    </li>
    <li>display</li>
    <li>
        <ul>
            <li>Value:</li>
            <li>
                <ul>
                    <li>Option: full</li>
                    <li>Option: basic</li>
                </ul>
            </li>
            <li>Default: full</li>
            <li>Required: No</li>
        </ul>
    </li>
    <li>display</li>
    <li>
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
</ul>