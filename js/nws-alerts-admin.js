/*
Plugin Name: National Weather Service Alerts
*/

/*global jQuery,ajaxurl*/

(function ($) {
    "use strict";

    function populate_tables(action, textStatus, jqXHR) {
        $('#nws-alerts-build-tables-status-bar').css('width', action['status'] + '%');

        if (action['populate_tables'] == true) {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'nws_alerts_populate_tables'
                },
                success: populate_tables
            });
        } else {
            $('#nws-alerts-build-tables').hide();
            location.reload();
        }
    }

    $('#nws-alerts-build-tables').submit(function (event) {
        event.preventDefault();

        $('#nws-alerts-build-tables-submit').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'nws_alerts_build_tables'
            },
            success: populate_tables
        });
    });
}(jQuery));
