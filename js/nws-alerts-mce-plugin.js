/*
* NWS Alert TinyMCE Plugin
*
* Adds a button to the first row of TinyMCE buttons which
* allows for form input to generate a nws_alerts shortcode
* for WordPress.
*/

/*global jQuery*/

(function ($) {
    "use strict";

    var nwsAlertsShortcode, controls = {}, editor, controlValues = {}, controlValuesDefaults;

    nwsAlertsShortcode = {
        init: function () {
            controlValues.zip = '';
            controlValues.city = '';
            controlValues.state = '';
            controlValues.county = '';
            controlValues.location_title = '';
            controlValues.display = 'full';
            controlValues.scope = 'county';
            controlValues.limit = 0;

            controlValuesDefaults = $.extend({}, controlValues);

			controls.backdrop = $('#nws-alerts-shortcode-backdrop');
			controls.wrap = $('#nws-alerts-shortcode-wrap');
			controls.dialog = $('#nws-alerts-shortcode');
            controls.errors = $('#nws-alerts-shortcode-errors');
            controls.errors.hide();
			controls.submit = $('#nws-alerts-shortcode-submit');
			controls.close = $('#nws-alerts-shortcode-close');

			// Bind event handlers
			$('.nws-alerts-control-multi-container input').click(function (event) {
				nwsAlertsShortcode.controlListenerMulti(this);
			});
			$('.nws-alerts-control-boolean-container input').click(function (event) {
				nwsAlertsShortcode.controlListenerBoolean(this);
			});
			$('.nws-alerts-control-select-container select').change(function (event) {
				nwsAlertsShortcode.controlListenerSelect(this);
			});
			$('.nws-alerts-control-text-container input').keyup(function (event) {
				nwsAlertsShortcode.controlListenerText(this);
			});
			controls.submit.click(function (event) {
				event.preventDefault();
				nwsAlertsShortcode.update();
			});
			controls.close.add(controls.backdrop).add('#nws-alerts-shortcode-cancel a').click(function (event) {
				event.preventDefault();
				nwsAlertsShortcode.close();
			});
        },
        open: function (editorId) {
            var ed;

			nwsAlertsShortcode.range = null;

			if (editorId) {
				window.wpActiveEditor = editorId;
			}

			if (!window.wpActiveEditor) {
				return;
			}

			this.textarea = $('#' + window.wpActiveEditor).get(0);

			if (typeof window.tinymce !== 'undefined') {
				ed = window.tinymce.get(window.wpActiveEditor);

				if (ed && !ed.isHidden()) {
					editor = ed;
				} else {
					editor = null;
				}

				if (editor && window.tinymce.isIE) {
					editor.windowManager.bookmark = editor.selection.getBookmark();
				}
			}

			controls.backdrop.show();
			controls.wrap.show();
        },
        close: function () {
            editor.focus();

            controls.errors.empty().hide();
			controls.backdrop.hide();
			controls.wrap.hide();
        },
        update: function () {
            var errors = false,
                match = false,
                shortcode = '[nws_alerts';

            if (controlValues.zip !== controlValuesDefaults.zip) { shortcode += ' zip="' + controlValues.zip + '"'; }
            if (controlValues.city !== controlValuesDefaults.city) { shortcode += ' city="' + controlValues.city + '"'; }
            if (controlValues.state !== controlValuesDefaults.state) { shortcode += ' state="' + controlValues.state + '"'; }
            if (controlValues.county !== controlValuesDefaults.county) { shortcode += ' county="' + controlValues.county + '"'; }
            if (controlValues.location_title !== controlValuesDefaults.location_title) { shortcode += ' location_title="' + controlValues.location_title + '"'; }
            if (controlValues.display !== controlValuesDefaults.display) { shortcode += ' display="' + controlValues.display + '"'; }
            if (controlValues.scope !== controlValuesDefaults.scope) { shortcode += ' scope="' + controlValues.scope + '"'; }
            if (controlValues.limit !== controlValuesDefaults.limit) { shortcode += ' limit="' + controlValues.limit + '"'; }

            shortcode += ']';

            // validate input
            if ((controlValues.state === controlValuesDefaults.state || controlValues.county === controlValuesDefaults.county) && match === false) {
                errors = "Please enter a state and county";
            } else {
                match = true;
                errors = false;
            }
            if ((controlValues.city === controlValuesDefaults.city || controlValues.state === controlValuesDefaults.state) && match === false) {
                errors = "Please enter a city and state";
            } else {
                match = true;
                errors = false;
            }
            if (isNaN(controlValues.zip) && match === false) {
                errors = "Please enter a valid zipcode.";
            } else if (controlValues.zip === controlValuesDefaults.zip && match === false) {
                errors = "Please enter a zipcode, city and state, or state and county";
            } else {
                match = true;
                errors = false;
            }

            // Output
            if (errors !== false) {
                controls.errors.show().text(errors);
            } else {
                editor.execCommand('mceInsertContent', false, shortcode);
                nwsAlertsShortcode.close();
            }
        },
        controlListenerMulti: function (checkbox) {
            var control = checkbox.getAttribute("data-control"),
                controlParent = checkbox.getAttribute("data-control-parent"),
                values = null,
                valuesText = "",
                valuesUpdated = [],
                firstValueAdded = false,
                i = 0;
            if (controlValues[controlParent] === "") {
                values = [];
            } else if (controlValues[controlParent] !== "" && controlValues[controlParent].indexOf(",") !== -1) {
                values = controlValues[controlParent].split(",");
            } else {
                values = [controlValues[controlParent]];
            }

            if (checkbox.checked) {
                // Checked - add to controlValues
                values.push(control);
            } else {
                // Unchecked - remove from controlValues
                for (i = 0; i < values.length; i += 1) {
                    if (values[i] !== control) {
                        valuesUpdated.push(values[i]);
                    }
                }
                values = valuesUpdated;
            }

            for (i = 0; i < values.length; i += 1) {
                if (firstValueAdded) {
                    valuesText += "," + values[i];
                } else {
                    firstValueAdded = true;
                    valuesText += values[i];
                }
            }
            controlValues[controlParent] = valuesText;
        },
        controlListenerBoolean: function (checkbox) {
            var control = checkbox.getAttribute("data-control"),
                controlParent = checkbox.getAttribute("data-control-parent"),
                valueText = "";

            if (checkbox.checked) {
                // Checked
                valueText = "true";
            } else {
                // Unchecked
                valueText = "false";
            }
            controlValues[controlParent] = valueText;
        },
        controlListenerSelect: function (select) {
            var control = select.getAttribute("data-control"),
                controlParent = select.getAttribute("data-control-parent"),
                valueText = $(select).val();
            controlValues[controlParent] = valueText;
        },
        controlListenerText: function (textbox) {
            var control = textbox.getAttribute("data-control"),
                controlParent = textbox.getAttribute("data-control-parent"),
                valueText = textbox.value;
            controlValues[controlParent] = valueText;
        }
    };

    $(document).ready(nwsAlertsShortcode.init);

    window.tinymce.create('tinymce.plugins.nws_alerts', {
        init: function (editor, url) {
            editor.addButton('nws_alerts_shortcode', {
                title: 'National Weather Service Alerts Shortcode',
                icon: true,
                image: url + '/../images/nws-alerts-mce-icon.png',
                onclick: function () { window.tinyMCE.activeEditor.execCommand('NWS_Alerts_Shortcode_Listener'); }
            });

            editor.addCommand('NWS_Alerts_Shortcode_Listener', function () {
                if (typeof nwsAlertsShortcode !== 'undefined') {
                    nwsAlertsShortcode.open(editor.id);
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname : 'NWS Alerts Plugin',
                author : 'laubsterboy',
                authorurl : 'http://laubsterboy.com',
                infourl : 'http://laubsterboy.com',
                version : "1.0"
            };
        }
    });

    window.tinymce.PluginManager.add('nws_alerts', window.tinymce.plugins.nws_alerts);
}(jQuery));
