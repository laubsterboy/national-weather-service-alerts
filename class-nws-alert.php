<?php
/**
* NWS_Alert functions and utility function.
*
* @since 1.0.0
*/

class NWS_Alert {
    /**
    * The ID of the NWS_Alert XML file - a string URL pointing to the XML file.
    *
    * @var string
    */
    public $ID = '';

    /**
    * The generator of the NWS_Alert XML file - likely 'NWS CAP Server'.
    *
    * @var string
    */
    public $generator = '';

    /**
    * The date when the NWS_Alert XML file was last updated.
    *
    * @var string
    */
    public $updated = '0000-00-00T00:00:00-00:00';

    /**
    * The title of the NWS_Alert.
    *
    * @var string
    */
    public $title = '';

    /**
    * A URL pointing to the NWS_Alert XML file.
    *
    * @var string
    */
    public $link = '';

    /**
    * An array of each of the NWS_Alert_Entry objects.
    *
    * @var string
    */
    public $entries = array();

    /**
    * A string containing any error encountered while retrieving the NWS_Alert data.
    *
    * @var boolean|string
    */
    public $error = false;

    /**
    * The average latitude of all NWS_Alert_Entries. Intended to be used with Google Maps to center around each of the polygons.
    *
    * @var string
    */
    public $latitude;

    /**
    * The average longitude of all NWS_Alert_Entries. Intended to be used with Google Maps to center around each of the polygons.
    *
    * @var string
    */
    public $longitude;

    /**
    * The zip code associated with the NWS_Alert, or the exact zip code entered by the user.
    *
    * @var string
    */
    public $zip;

    /**
    * The nearest city associated with the NWS_Alert, or the exact city (or town) entered by the user.
    *
    * @var string
    */
    public $city;

    /**
    * The state associated with the NWS_Alert, or the exact state entered by the user.
    *
    * @var string
    */
    public $state;

    /**
    * The county, within the state, associated with the NWS_Alert, or the exact county entered by the user.
    *
    * @var string
    */
    public $county;

    /**
    * The county code associated with the NWS_Alert.
    *
    * @var string
    */
    public $county_code;

    /**
    * The scope of which to limit alert events to: county, state, national.
    *
    * @var string
    */
    public $scope = 'county';

    /**
    * NWS_Alert constructor $args, $nws_alert_data
    */
    public function __construct($zip = null, $city = null, $state = null, $county = null, $scope = 'county') {
        global $wpdb;
        $nws_alert_xml;
        $nws_alert_xml_url;
        $nws_alert_data;
        $entry_cap_data;
        $locations_query;
        $county_code;
        $table_name_codes = NWS_ALERT_TABLE_NAME_CODES;
        $table_name_locations = NWS_ALERT_TABLE_NAME_LOCATIONS;

        // Based on available attributes, search the nws_alert_locations database table for a match
        if ($zip !== null) {
            $locations_query = $wpdb->get_row("SELECT * FROM $table_name_locations WHERE zip = $zip", ARRAY_A);
        } else if ($city !== null && $state !== null) {
            $city = strtolower($city);
            $state = strlen($state) > 2 ? NWS_Alert_Utils::convert_state_format($state) : $state;
            $locations_query = $wpdb->get_row("SELECT * FROM $table_name_locations WHERE city LIKE '$city' AND state LIKE '$state'", ARRAY_A);
        } else if ($state !== null && $county !== null) {
            $state = NWS_Alert_Utils::convert_state_format($state);
            $county = strtolower($county);
            $locations_query = $wpdb->get_row("SELECT * FROM $table_name_locations WHERE state LIKE '$state' AND county LIKE '$county'", ARRAY_A);
        } else {
            // Not enough information to determine the location and get an ANSI County code
            return false;
        }

        // Location could not be found - return 'empty' NWS_Alert
        if ($locations_query === null) return new NWS_Alert(array('zip' => $zip, 'city' => $city, 'state' => $state, 'county' => $county, 'error' => NWS_ALERT_ERROR_NO_LOCATION), array());

        // Individual locations_query variables
        $latitude = $locations_query['latitude'];
        $longitude = $locations_query['longitude'];
        $zip = $locations_query['zip'];
        $city = $locations_query['city'];
        $state = $locations_query['state'];
        $county = $locations_query['county'];

        $county_code = $wpdb->get_var("SELECT countyansi FROM $table_name_codes WHERE state LIKE '{$state}' AND county LIKE '%{$county}%'");
        $county_code = str_pad($county_code, 3, '0', STR_PAD_LEFT);

        if (strlen($county_code) < 3) { $county_code = '0' . $county_code; }

        // Make the city and state more legible
        $city = ucwords($city);
        $state_abbrev = $state;
        $state = ucwords(NWS_Alert_Utils::convert_state_format($state, 'abbrev'));


















        $nws_alert_xml = simplexml_load_string('<?xml version = "1.0" encoding = "UTF-8" standalone = "yes"?>

<!--
This atom/xml feed is an index to active advisories, watches and warnings
issued by the National Weather Service.  This index file is not the complete
Common Alerting Protocol (CAP) alert message.  To obtain the complete CAP
alert, please follow the links for each entry in this index.  Also note the
CAP message uses a style sheet to convey the information in a human readable
format.  Please view the source of the CAP message to see the complete data
set.  Not all information in the CAP message is contained in this index of
active alerts.
-->

<feed
xmlns = "http://www.w3.org/2005/Atom"
xmlns:cap = "urn:oasis:names:tc:emergency:cap:1.1"
xmlns:ha = "http://www.alerting.net/namespace/index_1.0"
>

<!-- TZN = <EDT> -->
<!-- TZO = <-4> -->
<!-- http-date = Thu, 22 May 2014 02:10:00 GMT -->

<id>http://alerts.weather.gov/cap/wwaatmget.php?x=INC121&amp;y=0</id>
<generator>NWS CAP Server</generator>
<updated>2014-05-21T22:10:00-04:00</updated>
<author>
    <name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Current Watches, Warnings and Advisories for Parke (INC121) Indiana Issued by the National Weather Service</title>
<link href="http://alerts.weather.gov/cap/wwaatmget.php?x=INC121&amp;y=0"/>

<entry>
<id>http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A92088.SevereWeatherStatement.125154A9227CIN.INDSVSIND.2faa803e2f00caa7e0f7bf548b60d2b5</id>
<updated>2014-05-21T22:10:00-04:00</updated>
<published>2014-05-21T22:10:00-04:00</published>
<author>
<name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Severe Weather Statement issued May 21 at 10:10PM EDT until May 21 at 10:15PM EDT by NWS</title>
<link href="http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A92088.SevereWeatherStatement.125154A9227CIN.INDSVSIND.2faa803e2f00caa7e0f7bf548b60d2b5"/>
<summary>...THE SEVERE THUNDERSTORM WARNING FOR FOUNTAIN...WESTERN MONTGOMERY...NORTHERN PARKE...NORTHERN VERMILLION AND EXTREME SOUTHWESTERN WARREN COUNTIES WILL EXPIRE AT 1015 PM EDT/915 PM CDT/... THE STORM WHICH PROMPTED THE WARNING HAS WEAKENED BELOW SEVERE LIMITS...AND NO LONGER POSES AN IMMEDIATE THREAT TO LIFE OR PROPERTY.</summary>
<cap:event>Severe Weather Statement</cap:event>
<cap:effective>2014-05-21T22:10:00-04:00</cap:effective>
<cap:expires>2014-05-21T22:15:00-04:00</cap:expires>
<cap:status>Actual</cap:status>
<cap:msgType>Alert</cap:msgType>
<cap:category>Met</cap:category>
<cap:urgency>Immediate</cap:urgency>
<cap:severity>Severe</cap:severity>
<cap:certainty>Observed</cap:certainty>
<cap:areaDesc>Fountain; Montgomery; Parke; Vermillion; Warren</cap:areaDesc>
<cap:polygon>40.18,-87.54 40.22,-87.09 40.21,-87.09 40.21,-87.01 39.83,-87.01 39.88,-87.54 40.18,-87.54</cap:polygon>
<cap:geocode>
<valueName>FIPS6</valueName>
<value>018045 018107 018121 018165 018171</value>
<valueName>UGC</valueName>
<value>INC045 INC107 INC121 INC165 INC171</value>
</cap:geocode>
<cap:parameter>
<valueName>VTEC</valueName>
<value>/O.EXP.KIND.SV.W.0067.000000T0000Z-140522T0215Z/</value>
</cap:parameter>
</entry>

<entry>
<id>http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A91E94.SevereThunderstormWarning.125154A92858IN.INDSVSIND.ffef3d015d27de648ac23e8bf90131a0</id>
<updated>2014-05-21T22:05:00-04:00</updated>
<published>2014-05-21T22:05:00-04:00</published>
<author>
<name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Severe Thunderstorm Warning issued May 21 at 10:05PM EDT until May 21 at 10:30PM EDT by NWS</title>
<link href="http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A91E94.SevereThunderstormWarning.125154A92858IN.INDSVSIND.ffef3d015d27de648ac23e8bf90131a0"/>
<summary>...A SEVERE THUNDERSTORM WARNING REMAINS IN EFFECT FOR NORTH CENTRAL CLAY...PARKE...WESTERN PUTNAM...SOUTHERN VERMILLION AND NORTHEASTERN VIGO COUNTIES UNTIL 1030 PM EDT... AT 1003 PM EDT...A SEVERE THUNDERSTORM WAS LOCATED 6 MILES SOUTHWEST OF ROCKVILLE...AND MOVING SOUTHEAST AT 35 MPH. HAZARD...PING PONG BALL SIZE HAIL AND 60 MPH WIND GUSTS.</summary>
<cap:event>Severe Thunderstorm Warning</cap:event>
<cap:effective>2014-05-21T22:05:00-04:00</cap:effective>
<cap:expires>2014-05-21T22:30:00-04:00</cap:expires>
<cap:status>Actual</cap:status>
<cap:msgType>Alert</cap:msgType>
<cap:category>Met</cap:category>
<cap:urgency>Immediate</cap:urgency>
<cap:severity>Severe</cap:severity>
<cap:certainty>Observed</cap:certainty>
<cap:areaDesc>Clay; Parke; Putnam; Vermillion; Vigo</cap:areaDesc>
<cap:polygon>39.89,-87.53 39.84,-86.85 39.47,-86.99 39.65,-87.54 39.87,-87.54 39.88,-87.54 39.89,-87.53</cap:polygon>
<cap:geocode>
<valueName>FIPS6</valueName>
<value>018021 018121 018133 018165 018167</value>
<valueName>UGC</valueName>
<value>INC021 INC121 INC133 INC165 INC167</value>
</cap:geocode>
<cap:parameter>
<valueName>VTEC</valueName>
<value>/O.CON.KIND.SV.W.0070.000000T0000Z-140522T0230Z/</value>
</cap:parameter>
</entry>

<entry>
<id>http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A90594.FloodWarning.125154AA39C8IN.INDFLWIND.14fac176c5042330485acec45ee4b42e</id>
<updated>2014-05-21T21:41:00-04:00</updated>
<published>2014-05-21T21:41:00-04:00</published>
<author>
<name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Flood Warning issued May 21 at 9:41PM EDT until May 22 at 5:30AM EDT by NWS</title>
<link href="http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A90594.FloodWarning.125154AA39C8IN.INDFLWIND.14fac176c5042330485acec45ee4b42e"/>
<summary>THE NATIONAL WEATHER SERVICE IN INDIANAPOLIS HAS ISSUED A * FLOOD WARNING FOR... CLAY COUNTY IN WEST CENTRAL INDIANA... MONTGOMERY COUNTY IN WEST CENTRAL INDIANA... NORTHERN OWEN COUNTY IN WEST CENTRAL INDIANA... PARKE COUNTY IN WEST CENTRAL INDIANA...</summary>
<cap:event>Flood Warning</cap:event>
<cap:effective>2014-05-21T21:41:00-04:00</cap:effective>
<cap:expires>2014-05-22T05:30:00-04:00</cap:expires>
<cap:status>Actual</cap:status>
<cap:msgType>Alert</cap:msgType>
<cap:category>Met</cap:category>
<cap:urgency>Expected</cap:urgency>
<cap:severity>Moderate</cap:severity>
<cap:certainty>Likely</cap:certainty>
<cap:areaDesc>Clay; Fountain; Hendricks; Montgomery; Owen; Parke; Putnam; Vermillion; Vigo</cap:areaDesc>
<cap:polygon>39.33,-87.6 39.36,-87.54 39.93,-87.54 40.15,-87.52 40.15,-87.49 40.13,-87.49 40.14,-87.4 40.15,-87.44 40.16,-87.43 40.15,-86.69 39.54,-86.66 39.52,-86.69 39.47,-86.68 39.47,-86.65 39.36,-86.65 39.35,-86.66 39.28,-87.17 39.28,-87.6 39.33,-87.6</cap:polygon>
<cap:geocode>
<valueName>FIPS6</valueName>
<value>018021 018045 018063 018107 018119 018121 018133 018165 018167</value>
<valueName>UGC</valueName>
<value>INC021 INC045 INC063 INC107 INC119 INC121 INC133 INC165 INC167</value>
</cap:geocode>
<cap:parameter>
<valueName>VTEC</valueName>
<value>/O.NEW.KIND.FA.W.0048.140522T0141Z-140522T0930Z/
/00000.0.ER.000000T0000Z.000000T0000Z.000000T0000Z.OO/</value>
</cap:parameter>
</entry>

<entry>
<id>http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A8FD60.SevereThunderstormWatch.125154A9DFF0IN.INDWCNIND.353726cb6078128fbf4296d733f2a5ff</id>
<updated>2014-05-21T21:20:00-04:00</updated>
<published>2014-05-21T21:20:00-04:00</published>
<author>
<name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Severe Thunderstorm Watch issued May 21 at 9:20PM EDT until May 22 at 3:00AM EDT by NWS</title>
<link href="http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A8FD60.SevereThunderstormWatch.125154A9DFF0IN.INDWCNIND.353726cb6078128fbf4296d733f2a5ff"/>
<summary>SEVERE THUNDERSTORM WATCH 171 REMAINS VALID UNTIL 3 AM EDT THURSDAY FOR THE FOLLOWING AREAS IN INDIANA THIS WATCH INCLUDES 13 COUNTIES IN SOUTH CENTRAL INDIANA LAWRENCE MONROE IN SOUTHWEST INDIANA</summary>
<cap:event>Severe Thunderstorm Watch</cap:event>
<cap:effective>2014-05-21T21:20:00-04:00</cap:effective>
<cap:expires>2014-05-22T03:00:00-04:00</cap:expires>
<cap:status>Actual</cap:status>
<cap:msgType>Alert</cap:msgType>
<cap:category>Met</cap:category>
<cap:urgency>Expected</cap:urgency>
<cap:severity>Severe</cap:severity>
<cap:certainty>Likely</cap:certainty>
<cap:areaDesc>Clay; Daviess; Greene; Knox; Lawrence; Martin; Monroe; Owen; Parke; Putnam; Sullivan; Vermillion; Vigo</cap:areaDesc>
<cap:polygon></cap:polygon>
<cap:geocode>
    <valueName>FIPS6</valueName>
    <value>018021 018027 018055 018083 018093 018101 018105 018119 018121 018133 018153 018165 018167</value>
    <valueName>UGC</valueName>
    <value>INC021 INC027 INC055 INC083 INC093 INC101 INC105 INC119 INC121 INC133 INC153 INC165 INC167</value>
</cap:geocode>
<cap:parameter>
    <valueName>VTEC</valueName>
    <value>/O.CON.KIND.SV.A.0171.000000T0000Z-140522T0700Z/</value>
</cap:parameter>
</entry>

<entry>
<id>http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A8D718.FlashFloodWarning.125154A92858IN.INDFFSIND.152ac553b8b81f2834c19bcc17b510f0</id>
<updated>2014-05-21T20:22:00-04:00</updated>
<published>2014-05-21T20:22:00-04:00</published>
<author>
<name>w-nws.webmaster@noaa.gov</name>
</author>
<title>Flash Flood Warning issued May 21 at 8:22PM EDT until May 21 at 10:30PM EDT by NWS</title>
<link href="http://alerts.weather.gov/cap/wwacapget.php?x=IN125154A8D718.FlashFloodWarning.125154A92858IN.INDFFSIND.152ac553b8b81f2834c19bcc17b510f0"/>
<summary>...FLASH FLOOD WARNING REMAINS IN EFFECT FOR SOUTHERN FOUNTAIN... MONTGOMERY...NORTHERN PARKE...NORTHERN PUTNAM AND NORTHERN VERMILLION COUNTIES UNTIL 1030 PM EDT... AT 822 PM EDT...NATIONAL WEATHER SERVICE DOPPLER RADAR INDICATED THAT FLASH FLOODING WAS OCCURRING. SOME LOCATIONS THAT WILL EXPERIENCE FLASH FLOODING INCLUDE...</summary>
<cap:event>Flash Flood Warning</cap:event>
<cap:effective>2014-05-21T20:22:00-04:00</cap:effective>
<cap:expires>2014-05-21T22:30:00-04:00</cap:expires>
<cap:status>Actual</cap:status>
<cap:msgType>Alert</cap:msgType>
<cap:category>Met</cap:category>
<cap:urgency>Immediate</cap:urgency>
<cap:severity>Severe</cap:severity>
<cap:certainty>Likely</cap:certainty>
<cap:areaDesc>Fountain; Montgomery; Parke; Putnam; Vermillion</cap:areaDesc>
<cap:polygon>40.15,-87.54 40.15,-87.49 40.13,-87.49 40.13,-87.4 40.16,-87.43 40.06,-87.1 40.16,-87.11 40.17,-86.69 39.65,-86.69 39.95,-87.53 40.15,-87.54</cap:polygon>
<cap:geocode>
<valueName>FIPS6</valueName>
<value>018045 018107 018121 018133 018165</value>
<valueName>UGC</valueName>
<value>INC045 INC107 INC121 INC133 INC165</value>
</cap:geocode>
<cap:parameter>
<valueName>VTEC</valueName>
<value>/O.CON.KIND.FF.W.0041.000000T0000Z-140522T0230Z/
/00000.0.ER.000000T0000Z.000000T0000Z.000000T0000Z.OO/</value>
</cap:parameter>
</entry>
</feed>');













        // Set the XML (atom) feed URL to be loaded
        if ($scope === 'national') {
            // National
            $nws_alert_xml_url = 'http://alerts.weather.gov/cap/us.php?x=0';
        } else if ($scope === 'state') {
            // State
            $nws_alert_xml_url = 'http://alerts.weather.gov/cap/' . $state_abbrev . '.php?x=0';
        } else {
            // Users requested location
            $nws_alert_xml_url = 'http://alerts.weather.gov/cap/wwaatmget.php?x=' . strtoupper($state_abbrev) . 'C' . $county_code . '&y=0';
        }

        $nws_alert_xml = simplexml_load_file($nws_alert_xml_url);

        $nws_alert_data = array();
        $nws_alert_data['ID'] = isset($nws_alert_xml->id) ? (string)$nws_alert_xml->id : null;
        $nws_alert_data['generator'] = isset($nws_alert_xml->generator) ? (string)$nws_alert_xml->generator : null;
        $nws_alert_data['updated'] = isset($nws_alert_xml->updated) ? (string)$nws_alert_xml->updated : null;
        $nws_alert_data['title'] = isset($nws_alert_xml->title) ? (string)$nws_alert_xml->title : null;
        $nws_alert_data['link'] = isset($nws_alert_xml->link['href']) ? (string)$nws_alert_xml->link['href'] : null;
        $nws_alert_data['entries'] = array();


        // parse through and load into $nws_alert_data array
        foreach($nws_alert_xml->entry as $entry) {
            // load 'cap' namespaced data into $cap_data
            $entry_cap_data = $entry->children('urn:oasis:names:tc:emergency:cap:1.1');

            $_entry = array(
                'ID' => isset($entry->id) ? (string)$entry->id : null,
                'updated' => isset($entry->updated) ? NWS_Alert_Utils::adjust_timezone_offset(new DateTime((string)$entry->updated)) : null, // convert to date object '2013-08-30T21:31:26+00:00'
                'published' => isset($entry->published) ? NWS_Alert_Utils::adjust_timezone_offset(new DateTime((string)$entry->published)) : null, // convert to date object '2013-08-30T11:33:00-05:00'
                'title' => isset($entry->title) ? (string)$entry->title : null,
                'link' => isset($entry->link['href']) ? (string)$entry->link['href'] : null,
                'summary' => isset($entry->summary) ? (string)$entry->summary : null,
                'cap_event' => isset($entry_cap_data->event) ? (string)$entry_cap_data->event : null, // list of cap:event above
                'cap_effective' => isset($entry_cap_data->effective) ? NWS_Alert_Utils::adjust_timezone_offset(new DateTime((string)$entry_cap_data->effective)) : null, // convert to date object '2013-08-30T11:33:00-05:00'
                'cap_expires' => isset($entry_cap_data->expires) ? NWS_Alert_Utils::adjust_timezone_offset(new DateTime((string)$entry_cap_data->expires)) : null, // convert to date object '2013-08-30T19:00:00-05:00'
                'cap_status' => isset($entry_cap_data->status) ? (string)$entry_cap_data->status : null,
                'cap_msg_type' => isset($entry_cap_data->msgType) ? (string)$entry_cap_data->msgType : null,
                'cap_category' => isset($entry_cap_data->category) ? (string)$entry_cap_data->category : null,
                'cap_urgency' => isset($entry_cap_data->urgency) ? (string)$entry_cap_data->urgency : null,
                'cap_severity' => isset($entry_cap_data->severity) ? (string)$entry_cap_data->severity : null,
                'cap_certainty' => isset($entry_cap_data->certainty) ? (string)$entry_cap_data->certainty : null,
                'cap_area_desc' => isset($entry_cap_data->areaDesc) ? (string)$entry_cap_data->areaDesc : null,
                'cap_polygon' => isset($entry_cap_data->polygon) ? (string)$entry_cap_data->polygon : null
            );

            $nws_alert_data['entries'][] = $_entry;
        }





















        /*
        * Possible CAP Event types that can be used when filtering $alert_types
        *
        * "Blizzard Warning"
        * "Dust Storm Warning"
        * "Flash Flood Watch"
        * "Flash Flood Warning"
        * "Flash Flood Statement"
        * "Flood Watch"
        * "Flood Warning"
        * "Flood Statement"
        * "High Wind Watch"
        * "High Wind Warning"
        * "Severe Thunderstorm Watch"
        * "Severe Thunderstorm Warning"
        * "Severe Weather Statement"
        * "Tornado Watch"
        * "Tornado Warning"
        * "Winter Storm Watch"
        * "Winter Storm Warning"
        * "Avalanche Watch"
        *
        * NON-WEATHER-RELATED-EVENTS
        * "Child Abduction Emergency"
        * "Civil Danger Warning"
        * "Civil Emergency Message"
        * "Evacuation Immediate"
        * "Fire Warning"
        * "Hazardous Materials Warning"
        * "Law Enforcement Warning"
        * "Local Area Emergency"
        * "911 Telephone Outage Emergency"
        * "Nuclear Power Plant Warning"
        * "Radiological Hazard Warning"
        * "Shelter in Place Warning"
        */
        /* add_feature - add filter to allow alerts to be added or removed */
        $alert_types = array('Tornado Warning',
                             'Severe Thunderstorm Warning',
                             'Flash Flood Warning',
                             'Flood Warning',
                             'Blizzard Warning',
                             'Winter Storm Warning',
                             'Freeze Warning',
                             'Dust Storm Warning',
                             'High Wind Warning'
                            );

        /*
        * msg types
        *
        * “Alert” - Initial information requiring attention by targeted recipients
        * “Update” - Updates and supercedes the earlier message(s) identified in <references>
        * “Cancel” - Cancels the earlier message(s) identified in <references>
        * “Ack” - Acknowledges receipt and acceptance of the message(s) identified in <references>
        * “Error” - Indicates rejection of the message(s) identified in <references>; explanation SHOULD appear in <note>
        */
        /* add_feature - add filter to allow msgTypes to be added or removed */
        $msg_types = array('Alert', 'Update');

        /*
        * Status types
        *
        * “Actual” - Actionable by all targeted recipients
        * “Exercise” - Actionable only by designated exercise participants; exercise identifier SHOULD appear in <note>
        * “System” - For messages that support alert network internal functions
        * “Test” - Technical testing only, all recipients disregard
        * “Draft” – A preliminary template or draft, not actionable in its current form
        */
        /* add_feature - add filter to allow msgTypes to be added or removed */
        $status_types = array('Actual');

        // Store args in class attributes
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zip = $zip;
        $this->city = $city;
        $this->state = $state;
        $this->county = $county;
        $this->county_code = $county_code;
        $this->scope = $scope;

        // Store first level $nws_alert_data values in class attributes
        $this->ID = $nws_alert_data['ID'];
        $this->generator = $nws_alert_data['generator'];
        $this->updated = $nws_alert_data['updated'];
        $this->title = $nws_alert_data['title'];
        $this->link = $nws_alert_data['link'];

        // Create NWS_Alert_Entry objects for each $nws_alert_data['entries'] and save in class attribute $entries, only if cap_event is a warning (flood, thunderstorm, or tornado)
        if (!empty($nws_alert_data['entries'])) {
            foreach ($nws_alert_data['entries'] as $key => $entry) {
                // Only add entries of allowed alert types
                if (in_array($entry['cap_event'], $alert_types, true) !== false && in_array($entry['cap_msg_type'], $msg_types, true) !== false && in_array($entry['cap_status'], $status_types, true) !== false) {
                    $entry['ID'] = (int)$key + 1;
                    $this->entries[] = new NWS_Alert_Entry($entry);
                }
            }
        }

        // Sort by cap_event, urgency, severity, certainty
        $this->entries = $this->sort_entries($this->entries);

        // Set class attributes $latitude and $longitude to average of all NWS_Alert_Entry objects
        $this->set_latitude_and_longitude();
    }




    /**
    * Sort Entries by alert type, urgency, and then severity
    *
    * @return array
    */
    public function sort_entries($entries) {
        if (empty($entries)) return $entries;

        $_entries = array();

        /* add_feature - add filter to allow alert_type_sort_order to be rearranged */
        $alert_types = array('Tornado Warning',
                             'Severe Thunderstorm Warning',
                             'Flash Flood Warning',
                             'Flood Warning',
                             'Blizzard Warning',
                             'Winter Storm Warning',
                             'Freeze Warning',
                             'Dust Storm Warning',
                             'High Wind Warning',
                             'Tornado Watch',
                             'Severe Thunderstorm Watch',
                             'Flash Flood Watch',
                             'Flood Watch',
                             'Winter Storm Watch',
                             'Avalanche Watch',
                             'High Wind Watch',
                             'Fire Weather Watch',
                             'Severe Weather Statement',
                             'Flash Flood Statement',
                             'Flood Statement',
                             'Frost Advisory',
                             'Heat Advisory'
                            );
        /*
        * Urgency types
        *
        * Immediate - Responsive action SHOULD be taken immediately
        * Expected  - Responsive action SHOULD be taken soon (within next hour)
        * Future    - Responsive action SHOULD be taken in the near future
        * Past      - Responsive action is no longer required
        * Unknown   - Urgency not known
        */
        $urgency_types = array('Immediate', 'Expected', 'Future', 'Past', 'Unknown');

        /*
        * Severity types
        *
        * Extreme   - Extraordinary threat to life or property
        * Severe    - Significant threat to life or property
        * Moderate  - Possible threat to life or property
        * Minor     – Minimal to no known threat to life or property
        * Unknown   - Severity unknown
        */
        $severity_types = array('Extreme', 'Severe', 'Moderate', 'Minor', 'Unknown');

        /*
        * Certainty types
        *
        * Observed      – Determined to have occurred or to be ongoing
        * Very Likely   - Deprecated and should be treated the same as "Likely"
        * Likely        - Likely (p > ~50%)
        * Possible      - Possible but not likely (p <= ~50%)
        * Unlikely      - Not expected to occur (p ~ 0)
        * Unknown       - Certainty unknown
        */
        $certainty_types = array('Observed', 'Very Likely', 'Likely', 'Possible', 'Unlikely', 'Unknown');

        foreach ($alert_types as $alert_type) {
            $entries_by_alert_type = array();
            $entries_by_urgency = array();
            $entries_by_severity = array();
            $entries_by_certainty = array();

            // Sort by Alert Type
            foreach ($entries as $entry) {
                if ($entry->cap_event === $alert_type) {
                    $entries_by_alert_type[] = $entry;
                }
            }

            if (!empty($entries_by_alert_type)) {
                // Sort by Urgency
                foreach ($entries_by_alert_type as $entry) {
                    if (!isset($entries_by_urgency[$entry->cap_urgency])) $entries_by_urgency[$entry->cap_urgency] = array();
                    $entries_by_urgency[$entry->cap_urgency][] = $entry;
                }
                $entries_by_urgency = NWS_Alert_Utils::array_merge_by_order($entries_by_urgency, $urgency_types);

                // Sort by Severity
                foreach ($entries_by_urgency as $entry) {
                    if (!isset($entries_by_severity[$entry->cap_severity])) $entries_by_severity[$entry->cap_severity] = array();
                    $entries_by_severity[$entry->cap_severity][] = $entry;
                }
                $entries_by_severity = NWS_Alert_Utils::array_merge_by_order($entries_by_severity, $severity_types);

                // Sort by Certainty
                foreach ($entries_by_severity as $entry) {
                    if (!isset($entries_by_certainty[$entry->cap_certainty])) $entries_by_certainty[$entry->cap_certainty] = array();
                    $entries_by_certainty[$entry->cap_certainty][] = $entry;
                }
                $entries_by_certainty = NWS_Alert_Utils::array_merge_by_order($entries_by_certainty, $certainty_types);

                // Merge into entries
                $_entries = array_merge($_entries, $entries_by_certainty);
            }
        }

        return $_entries;
    }




    /**
    * Builds the necessary JavaScript to output a Google map using the NWS_Alert_Entry objects and their cap_polygon
    *
    * @return string|boolean
    */
    public function get_output_google_map() {
        $return_value = false;

        if (!empty($this->entries)) {
            $google_map_polys = '';

            foreach ($this->entries as $entry) {
                $google_map_polys .= $entry->get_output_google_map_polys();
            }

            if (!empty($google_map_polys)) {
            $return_value = '
                <script type="text/javascript">
                    function initialize() {
                        var mapOptions = {
                            zoom: ' . ($this->scope === 'county' ? '8' : '6') . ',
                            center: new google.maps.LatLng(' . $this->latitude . ', ' . $this->longitude . '),
                            mapTypeId: google.maps.MapTypeId.ROADMAP,
                            styles: [{"stylers": [{"hue": "#ff1a00"},{"invert_lightness": true},{"saturation": -100},{"lightness": 33},{"gamma": 0.5}]},{"featureType": "water","elementType": "geometry","stylers": [{"color": "#2D333C"}]}]
                        };

                        var nwsAlertTriangle;

                        var map = new google.maps.Map(document.getElementById("nws-alert-map-' . $this->zip . '"), mapOptions);

                        ' . $google_map_polys . '
                    }

                    google.maps.event.addDomListener(window, "load", initialize);

                </script>
                <section id="nws-alert-map-' . $this->zip . '" class="nws-alert-map"></section>';
            }
        }

        return $return_value;
    }




    /**
    * Returns the html markup for the city, state of the NWS_Alert
    *
    * @return string
    */
    public function get_output_heading($args = array()) {
        $defaults = array('graphic' => 2,
                          'prefix' => '<section>',
                          'suffix' => '</section>',
                          'current_alert' => true);
        $args = wp_parse_args($args, $defaults);

        if ($args['graphic'] !== false && !empty($this->entries)) {
            $args['prefix'] = NWS_Alert_Utils::str_lreplace('>', ' class="nws-alert-heading">', $args['prefix']);
        } else {
            $args['prefix'] = NWS_Alert_Utils::str_lreplace('>', ' class="nws-alert-heading nws-alert-heading-no-graphic">', $args['prefix']);
        }

        $return_value = $args['prefix'];

        if ($args['graphic'] !== false && !empty($this->entries)) {
            $return_value .= $this->entries[0]->get_output_graphic($args['graphic'], 'nws-alert-heading-graphic');
        }

        if ($this->scope === 'national') {
            $return_value .= '<span class="nws-alert-heading-scope">National Weather Alerts</span><h2 class="nws-alert-heading-location">United States</h2>';
        } else if ($this->scope === 'state') {
            $return_value .= '<span class="nws-alert-heading-scope">State Weather Alerts</span><h2 class="nws-alert-heading-location">' . $this->state . '</h2>';
        } else {
            $return_value .= '<span class="nws-alert-heading-scope">Local Weather Alerts</span><h2 class="nws-alert-heading-location">' . $this->city . ', ' . $this->state . '</h2>';
        }

        if ($args['current_alert'] && !empty($this->entries)) $return_value .= $this->entries[0]->get_output_text(false);

        return $return_value . $args['suffix'];
    }




    /**
    * Returns the html markup for each NWS_Alert_Entry cap_event of the NWS_Alert
    *
    * @return string
    */
    public function get_output_entries($args = array()) {
        $defaults = array('graphic' => 2,
                          'prefix' => '<section>',
                          'suffix' => '</section>');
        $args = wp_parse_args($args, $defaults);

        $args['prefix'] = NWS_Alert_Utils::str_lreplace('>', ' class="nws-alert-entries">', $args['prefix']);

        if (!empty($this->entries)) {
            $return_value = $args['prefix'];

            foreach ($this->entries as $entry) {
                $return_value .= $entry->get_output_entry(array('graphic' => $args['graphic']));
            }

            return $return_value . $args['suffix'];
        } else {
            return $args['prefix'] . NWS_ALERT_ERROR_NO_ALERTS . $args['suffix'];
        }
    }




    /*
    * get_nws_alert_html_full
    *
    * Returns a string with html including full information about the alert(s).
    *
    * @param NWS_Alert $nws_alert a full populated NWS_Alert object
    * @return string
    */
    public function get_output_html_full($nws_alert = false) {
        if (!$nws_alert) return '';

        if (!$nws_alert->error) {
            $return_value = '';

            $return_value .= '<article class="nws-alert">';
            $return_value .= $nws_alert->get_output_heading();
            $return_value .= '<section class="nws-alert-details">';
            $return_value .= $nws_alert->get_output_entries(array('graphic' => 2));
            $return_value .= $nws_alert->get_output_google_map();
            $return_value .= '</section>';
            $return_value .= '</article>';

            return $return_value;
        } else {
            return $nws_alert->error;
        }
    }




    /**
    * get_nws_alert_html_basic
    *
    * Returns a string with html markup for basic alert information
    *
    * @param NWS_Alert $nws_alert a full populated NWS_Alert object
    * @return string
    */
    public function get_output_html_basic($nws_alert = false) {
        // return html string with minimal information about the alert(s)
        if (!$nws_alert) return '';

        if (!$nws_alert->error) {
            $return_value = '';

            $return_value .= '<article class="nws-alert">';
            $return_value .= $nws_alert->get_output_heading();
            $return_value .= '</article>';

            return $return_value;
        } else {
            return $nws_alert->error;
        }
    }




    /**
    * Loops through all of the NWS_Alert_Entry objects to find cap_polygon values and sets NWS_Alert latitude and longitude to the average of them all
    *
    * @return boolean
    */
    private function set_latitude_and_longitude() {
        $return_value = false;

        if (!empty($this->entries)) {
            $latitudes = array();
            $longitudes = array();

            foreach ($this->entries as $entry) {
                if (empty($entry->cap_polygon)) continue;

                $polygon_points = explode(' ', $entry->cap_polygon);
                foreach($polygon_points as $polygon_point) {
                    $split = explode(',', $polygon_point);
                    $latitudes[] = $split[0];
                    $longitudes[] = $split[1];
                }
            }

            if (count($latitudes) > 0 && count($longitudes) > 0) {
                $this->latitude = array_sum($latitudes) / count($latitudes);
                $this->longitude = array_sum($longitudes) / count($longitudes);

                $return_value = true;
            }
        }

        return $return_value;
    }
}

?>
