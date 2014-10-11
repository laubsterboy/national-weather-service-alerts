/*
* NWS Alert TinyMCE Plugin
*
* Adds a button to the first row of TinyMCE buttons which
* allows for form input to generate a nws_alert shortcode
* for WordPress.
*/

/*global jQuery*/

(function ($) {
    "use strict";

    var nwsAlertShortcodes, controls = {}, editor, controlValues = {}, controlValuesDefaults;

    nwsAlertShortcodes = {
        init: function () {
            controlValues.zip = '';
            controlValues.city = '';
            controlValues.state = 'AL';
            controlValues.county = '';
            controlValues.display = 'full';
            controlValues.scope = 'county';

            controlValuesDefaults = $.extend({}, controlValues);

			controls.backdrop = $('#nws-alert-shortcodes-backdrop');
			controls.wrap = $('#nws-alert-shortcodes-wrap');
			controls.dialog = $('#nws-alert-shortcodes');
            controls.errors = $('#nws-alert-shortcodes-errors');
            controls.errors.hide();
			controls.submit = $('#nws-alert-shortcodes-submit');
			controls.close = $('#nws-alert-shortcodes-close');

			// Bind event handlers
			$('.nws-alert-control-multi-container input').click(function (event) {
				nwsAlertShortcodes.controlListenerMulti(this);
			});
			$('.nws-alert-control-boolean-container input').click(function (event) {
				nwsAlertShortcodes.controlListenerBoolean(this);
			});
			$('.nws-alert-control-select-container select').change(function (event) {
				nwsAlertShortcodes.controlListenerSelect(this);
			});
			$('.nws-alert-control-text-container input').keyup(function (event) {
				nwsAlertShortcodes.controlListenerText(this);
			});
			controls.submit.click(function (event) {
				event.preventDefault();
				nwsAlertShortcodes.update();
			});
			controls.close.add(controls.backdrop).add('#nws-alert-shortcodes-cancel a').click(function (event) {
				event.preventDefault();
				nwsAlertShortcodes.close();
			});
        },
        open: function (editorId) {
            var ed;

			nwsAlertShortcodes.range = null;

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
                shortcode = '[nws_alert';

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
            if (controlValues.zip === controlValuesDefaults.zip && match === false) {
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
                nwsAlertShortcodes.close();
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

    $(document).ready(nwsAlertShortcodes.init);

    window.tinymce.create('tinymce.plugins.nws_alert', {
        init: function (editor, url) {
            editor.addButton('nws_alert_shortcodes', {
                //text: 'NWS Alert',
                title: 'National Weather Service Alerts Shortcode',
                icon: true,
                image: url + '/../images/nws-alert-mce-icon.png',
                onclick: function () { window.tinyMCE.activeEditor.execCommand('NWS_Alert_Shortcodes_Listener'); }
            });

            editor.addCommand('NWS_Alert_Shortcodes_Listener', function () {
                if (typeof nwsAlertShortcodes !== 'undefined') {
                    nwsAlertShortcodes.open(editor.id);
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

    window.tinymce.PluginManager.add('nws_alert', window.tinymce.plugins.nws_alert);
}(jQuery));
