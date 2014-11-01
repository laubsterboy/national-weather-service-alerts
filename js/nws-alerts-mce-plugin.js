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

    var nwsAlertsShortcodes, controls = {}, editor, controlValues = {}, controlValuesDefaults;

    nwsAlertsShortcodes = {
        init: function () {
            controlValues.zip = '';
            controlValues.city = '';
            controlValues.state = 'AL';
            controlValues.county = '';
            controlValues.display = 'full';
            controlValues.scope = 'county';

            controlValuesDefaults = $.extend({}, controlValues);

			controls.backdrop = $('#nws-alerts-shortcodes-backdrop');
			controls.wrap = $('#nws-alerts-shortcodes-wrap');
			controls.dialog = $('#nws-alerts-shortcodes');
            controls.errors = $('#nws-alerts-shortcodes-errors');
            controls.errors.hide();
			controls.submit = $('#nws-alerts-shortcodes-submit');
			controls.close = $('#nws-alerts-shortcodes-close');

			// Bind event handlers
			$('.nws-alerts-control-multi-container input').click(function (event) {
				nwsAlertsShortcodes.controlListenerMulti(this);
			});
			$('.nws-alerts-control-boolean-container input').click(function (event) {
				nwsAlertsShortcodes.controlListenerBoolean(this);
			});
			$('.nws-alerts-control-select-container select').change(function (event) {
				nwsAlertsShortcodes.controlListenerSelect(this);
			});
			$('.nws-alerts-control-text-container input').keyup(function (event) {
				nwsAlertsShortcodes.controlListenerText(this);
			});
			controls.submit.click(function (event) {
				event.preventDefault();
				nwsAlertsShortcodes.update();
			});
			controls.close.add(controls.backdrop).add('#nws-alerts-shortcodes-cancel a').click(function (event) {
				event.preventDefault();
				nwsAlertsShortcodes.close();
			});
        },
        open: function (editorId) {
            var ed;

			nwsAlertsShortcodes.range = null;

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
            if (controlValues.state !== controlValuesDefaults.state || (controlValues.city !== controlValuesDefaults.city || controlValues.county !== controlValuesDefaults.county)) { shortcode += ' state="' + controlValues.state + '"'; }
            if (controlValues.county !== controlValuesDefaults.county) { shortcode += ' county="' + controlValues.county + '"'; }
            if (controlValues.display !== controlValuesDefaults.display) { shortcode += ' display="' + controlValues.display + '"'; }
            if (controlValues.scope !== controlValuesDefaults.scope) { shortcode += ' scope="' + controlValues.scope + '"'; }

            shortcode += ']';

            // validate input
            if (controlValues.state === controlValuesDefaults.state && controlValues.county === controlValuesDefaults.county && match === false) {
                errors = "Please enter a state and county";
            } else {
                match = true;
                errors = false;
            }
            if (controlValues.city === controlValuesDefaults.city && controlValues.state === controlValuesDefaults.state && match === false) {
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
                nwsAlertsShortcodes.close();
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

    $(document).ready(nwsAlertsShortcodes.init);

    window.tinymce.create('tinymce.plugins.nws_alerts', {
        init: function (editor, url) {
            editor.addButton('nws_alerts_shortcodes', {
                //text: 'NWS Alert',
                title: 'National Weather Service Alerts Shortcode',
                icon: true,
                image: url + '/../images/nws-alerts-mce-icon.png',
                onclick: function () { window.tinyMCE.activeEditor.execCommand('NWS_Alert_Shortcodes_Listener'); }
            });

            editor.addCommand('NWS_Alert_Shortcodes_Listener', function () {
                if (typeof nwsAlertsShortcodes !== 'undefined') {
                    nwsAlertsShortcodes.open(editor.id);
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname : 'NWS Alert Plugin',
                author : 'laubsterboy',
                authorurl : 'http://laubsterboy.com',
                infourl : 'http://laubsterboy.com',
                version : "1.0"
            };
        }
    });

    window.tinymce.PluginManager.add('nws_alerts', window.tinymce.plugins.nws_alerts);
}(jQuery));
