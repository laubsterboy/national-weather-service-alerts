=== Plugin Name ===

Contributors: laubsterboy
Tags: National Weather Service, NWS, Storm Prediction Center, SPC, Alert, Weather, Storm, Severe, Tornado, Thunder, Flood
Requires at least: 3.1
Tested up to: 4.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add official National Weather Service alerts to your website.




== Description ==

The National Weather Service Alerts plugin allows you to easily display weather alerts, such as tornado warnings, 
severe thunderstorm warnings, or flash flood warnings, on your website. The alerts are pulled directly from the
National Weather Service (http://alerts.weather.gov) based on the location that you specify and are then parsed,
sorted, and output to your website. The alerts are then automatically updated using
AJAX, based on the severity of the alerts for the specified location. The location can be set by using zipcode, 
city and state, or state and county. There is also the option to choose the scope of what alerts to include, 
such as alerts only for your county, alerts only for your state, or alerts for the entire United States.

If applicable, a Google Map will be included with polygon overlays to show the affected regions of certain alert
types, such as tornado warnings or flash flood warnings.

*Currently the National Weather Service Alerts plugin only works for areas within United States. However, the
plugin expects Atom feeds that use the Common Alerting Protocol (CAP) format so in theory any CAP feed could be
used.*

**Features**

* Shortcode
* Widget
* NWS Alerts settings page for adding the Alerts Bar 
* Clean html5 markup
* CSS classes that make it easy to override default styles
* Developer API (filters)





== Installation ==

1. Go to Plugins > Add New in the admin area, and search for National Weather Service Alerts.
1. Click install.
1. Once installed, activate and you're done.

**Note that the first time the plugin is activated the database tables used for location searching are built and
this process can take up to a minute to complete, so please be patient. These tables are deleted from the database
when the plugin is deactivated, and then deleted, in the WordPress admin Plugins area.**

Once the plugin is installed and activated you can easily add weather alerts to your website by using the included
NWS Alerts widget or by using the [nws_alert] shortcode. The plugin adds a "National Weather Service Alerts" button
to the WordPress editor that can be used to build properly formatted nws_alert shortcodes.

For further documentation and developer reference check out the GitHub repository: https://github.com/laubsterboy/national-weather-service-alerts




== Frequently Asked Questions ==

= I'm only seeing the following message: The specified location could not be found. Try specifying a county and state instead. =
The plugin is letting your know that there was an error when attempting to retrieve additional location information
about the specified location. Check for spelling errors in the city or county name. On rare occasion the locations
database table may not include the specified city and is thus unable to retrieve additional information necessary
for the plugin to function properly and the only workaround is to instead use the zipcode.

= I'm seeing the following message: Data Error =
The plugin will show this message when it is unable to retrieve the Atom feed from the National Weather Service.
It is rare for this to happen and when it does it's generally because the Atom feed is temporarily unavailable. 
Simply refreshing the page should fix the problem.




== Screenshots ==

1. *Full display example - with no Google map*
1. *Full display example - with Google map*
1. *Shortcode builder in the page/post editor*
1. *Widget*
1. *Alerts Bar example - with Google map*




== Changelog ==

= 1.1.0 =
* Added: NWS Alerts settings page to add the NWS Alerts Bar.
* Added: Bar display option, which displays in a horizontal layout and only displays when there are active alerts.
* Improvement: Style and layout compatibility with themes. Also added additional classes to nws alerts markup to allow for more specific adjustments.

= 1.0.1 =
* Improvement: Typos
* Change: Updated readme

= 1.0.0 =
* Initial release of the National Weather Service Alerts plugin.




== Upgrade Notice ==

= 1.1.0 =
* Added features, including an alerts bar, and improved layout compatibility across across themes.

= 1.0.1 =
* Updated reference.

= 1.0.0 =
* Initial release of the National Weather Service Alerts plugin.