/*
Plugin Name: National Weather Service Alert
Plugin URI: http://www.laubsterboy.com/blog/nws-alert-plugin/
Author: John Russell
Author URI: http://www.laubsterboy.com
*/

/*global jQuery*/

(function ($) {
    "use strict";

    var nonce = '';

    function update (data, textStatus, jqXHR) {
        console.log('refresh update');
        console.log(data);
        console.log(textStatus);
        console.log(jqXHR);
    }

    function setup () {
        var zip = $(this).data('zip'),
            display = $(this).data('display'),
            scope = $(this).data('scope'),
            refresh_rate = parseInt($(this).data('refresh_rate')) * 60000;
            refresh_rate = 3000;

        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'nws_alert_refresh',
                    security: nonce,
                    zip: zip,
                    display: display,
                    scope: scope
                },
                success: update
            });
        }, refresh_rate);
    }

    $('.nws-alert').each(setup);
}(jQuery));
