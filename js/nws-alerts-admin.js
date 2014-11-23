/*
Plugin Name: National Weather Service Alerts

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
        }
    }

    $('#nws-alerts-build-tables-submit').click(function (event) {
        event.preventDefault();

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
