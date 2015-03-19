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
        var settings = JSON.stringify($(this).data('settings')),
            refreshRate = parseInt($(this).data('refresh-rate')) * 1000,
            element = this;

        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'nws_alerts_refresh',
                    settings: settings
                },
                success: function (html, textStatus, jqXHR) {
                    if (html != 0) update(html, element);
                }
            });
        }, refreshRate);
    }

    function update(html, originalElement) {
        $(html).addClass('nws-alerts-updated').insertBefore(originalElement).each(setup);
        $(originalElement).remove();
    }

    // Initialize each nws-alert
    $('.nws-alerts').each(setup);
}(jQuery));
