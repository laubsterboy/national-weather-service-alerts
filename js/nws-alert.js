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

    function update (html, originalElement) {
        $(html).addClass('nws-alert-updated').insertBefore(originalElement).each(setup);
        $(originalElement).remove();
    }

    function setup () {
        var zip = $(this).data('zip'),
            display = $(this).data('display'),
            scope = $(this).data('scope'),
            refresh_rate = parseInt($(this).data('refresh_rate')) * 60000,
            element = this;

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
                success: function (html, textStatus, jqXHR) {
                    if (html != 0) update(html, element);
                }
            });
        }, refresh_rate);
    }

    // Initialize each nws-alert
    $('.nws-alert').each(setup);
}(jQuery));
