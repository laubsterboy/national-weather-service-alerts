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
        var settings = $(this).data('settings'),
            zip = settings.zip,
            scope = settings.scope,
            limit = settings.limit,
            display = settings.display,
            classes = settings.classes,
            locationTitle = settings.location_title,
            refreshRate = parseInt(settings.refresh_rate) * 60000,
            element = this;

        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'nws_alerts_refresh',
                    zip: zip,
                    scope: scope,
                    limit: limit,
                    display: display,
                    classes: classes,
                    location_title: locationTitle,
                    refresh_rate: refreshRate
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
