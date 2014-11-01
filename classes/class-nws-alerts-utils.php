<?php
// NWS Alerts Plugin Utility Functions

class NWS_Alerts_Utils {
    /*
    * @return array
    * @access public
    */
    public static function get_states() {
        $states = array(
            array('name'=>'Alabama', 'abbrev'=>'AL'),
            array('name'=>'Alaska', 'abbrev'=>'AK'),
            array('name'=>'Arizona', 'abbrev'=>'AZ'),
            array('name'=>'Arkansas', 'abbrev'=>'AR'),
            array('name'=>'California', 'abbrev'=>'CA'),
            array('name'=>'Colorado', 'abbrev'=>'CO'),
            array('name'=>'Connecticut', 'abbrev'=>'CT'),
            array('name'=>'Delaware', 'abbrev'=>'DE'),
            array('name'=>'District of Columbia', 'abbrev'=>'DC'),
            array('name'=>'Florida', 'abbrev'=>'FL'),
            array('name'=>'Georgia', 'abbrev'=>'GA'),
            array('name'=>'Hawaii', 'abbrev'=>'HI'),
            array('name'=>'Idaho', 'abbrev'=>'ID'),
            array('name'=>'Illinois', 'abbrev'=>'IL'),
            array('name'=>'Indiana', 'abbrev'=>'IN'),
            array('name'=>'Iowa', 'abbrev'=>'IA'),
            array('name'=>'Kansas', 'abbrev'=>'KS'),
            array('name'=>'Kentucky', 'abbrev'=>'KY'),
            array('name'=>'Louisiana', 'abbrev'=>'LA'),
            array('name'=>'Maine', 'abbrev'=>'ME'),
            array('name'=>'Maryland', 'abbrev'=>'MD'),
            array('name'=>'Massachusetts', 'abbrev'=>'MA'),
            array('name'=>'Michigan', 'abbrev'=>'MI'),
            array('name'=>'Minnesota', 'abbrev'=>'MN'),
            array('name'=>'Mississippi', 'abbrev'=>'MS'),
            array('name'=>'Missouri', 'abbrev'=>'MO'),
            array('name'=>'Montana', 'abbrev'=>'MT'),
            array('name'=>'Nebraska', 'abbrev'=>'NE'),
            array('name'=>'Nevada', 'abbrev'=>'NV'),
            array('name'=>'New Hampshire', 'abbrev'=>'NH'),
            array('name'=>'New Jersey', 'abbrev'=>'NJ'),
            array('name'=>'New Mexico', 'abbrev'=>'NM'),
            array('name'=>'New York', 'abbrev'=>'NY'),
            array('name'=>'North Carolina', 'abbrev'=>'NC'),
            array('name'=>'North Dakota', 'abbrev'=>'ND'),
            array('name'=>'Ohio', 'abbrev'=>'OH'),
            array('name'=>'Oklahoma', 'abbrev'=>'OK'),
            array('name'=>'Oregon', 'abbrev'=>'OR'),
            array('name'=>'Pennsylvania', 'abbrev'=>'PA'),
            array('name'=>'Rhode Island', 'abbrev'=>'RI'),
            array('name'=>'South Carolina', 'abbrev'=>'SC'),
            array('name'=>'South Dakota', 'abbrev'=>'SD'),
            array('name'=>'Tennessee', 'abbrev'=>'TN'),
            array('name'=>'Texas', 'abbrev'=>'TX'),
            array('name'=>'Utah', 'abbrev'=>'UT'),
            array('name'=>'Vermont', 'abbrev'=>'VT'),
            array('name'=>'Virginia', 'abbrev'=>'VA'),
            array('name'=>'Washington', 'abbrev'=>'WA'),
            array('name'=>'West Virginia', 'abbrev'=>'WV'),
            array('name'=>'Wisconsin', 'abbrev'=>'WI'),
            array('name'=>'Wyoming', 'abbrev'=>'WY')
        );

        return $states;
    }

    /*
    * @return string
    * @access public
    */
    public static function convert_state_format($state, $search_key = 'name') {
        $states = self::get_states();
        $return_value = false;
        $return_key = 'abbrev';

        if ($search_key === 'abbrev') {
            $state = strtoupper((string)$state);
            $return_key = 'name';
        } else {
            $state = ucwords(strtolower((string)$state));
            $state = str_replace('Of', 'of', $state);
        }

        foreach ($states as $_state) {
            if ($state === $_state[$search_key]) {
                $return_value = strtolower($_state[$return_key]);
            }
        }

        return $return_value;
    }



    /*
    * @return string
    * @access public
    */
    public static function str_lreplace($search, $replace, $string) {
        $position = strrpos($string, $search);

        if ($position !== false) {
            $string = substr_replace($string, $replace, $position, strlen($search));
        }

        return $string;
    }



    /*
    * @return string
    * @access public
    */
    public static function str_freplace($search, $replace, $string) {
        $position = strpos($string, $search);

        if ($position !== false) {
            $string = substr_replace($string, $replace, $position, strlen($search));
        }

        return $string;
    }




    public static function adjust_timezone_offset($date_time) {
        // The timestamp from NWS comes in as GMT and must be adjusted for UTC
        $offset = get_option('gmt_offset');
        $date_time->modify("$offset hours");
        $return_value = $date_time->format('F j, Y \a\t g:ia');
        return $return_value;
    }



    /*
    * @return string
    * @access public
    */
    public static function array_merge_by_order($associative_array = array(), $order_array = array()) {
        if (!empty($associative_array)) {
            $return_array = array();

            foreach($order_array as $order) {
                if (isset($associative_array[$order])) $return_array = array_merge($return_array, $associative_array[$order]);
            }
            $associative_array = $return_array;
        }

        return $associative_array;
    }
}

?>
