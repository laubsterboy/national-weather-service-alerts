/*
Plugin Name: National Weather Service Alerts
Plugin URI: http://www.laubsterboy.com/blog/nws-alerts/
Author: John Russell
Author URI: http://www.laubsterboy.com
*/

/*global jQuery,ajaxurl*/

(function ($) {
    "use strict";

    function setup() {
        var zip = $(this).data('zip'),
            display = $(this).data('display'),
            scope = $(this).data('scope'),
            classes = $(this).attr('class'),
            refresh_rate = parseInt($(this).data('refresh_rate')) * 60000,
            element = this;

        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'nws_alert_refresh',
                    zip: zip,
                    display: display,
                    scope: scope,
                    classes: classes
                },
                success: function (html, textStatus, jqXHR) {
                    if (html != 0) update(html, element);
                }
            });
        }, refresh_rate);
    }

    function update(html, originalElement) {
        $(html).addClass('nws-alerts-updated').insertBefore(originalElement).each(setup);
        $(originalElement).remove();
    }

    // Initialize each nws-alert
    $('.nws-alerts').each(setup);
}(jQuery));
