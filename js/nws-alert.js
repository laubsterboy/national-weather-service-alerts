/*
Plugin Name: National Weather Service Alert
Plugin URI: http://www.laubsterboy.com/blog/nws-alert-plugin/
Author: John Russell
Author URI: http://www.laubsterboy.com
*/

(function(window, undefined) {
    var document = window.document,
		location = window.location,
		navigator = window.navigator;

	//=====================================================================================
	// POLYFILLS
	//=====================================================================================
    (function() {
        // requestAnimationFrame - a browser-synchronized "update" method
        if(!window.requestAnimationFrame) {
            window.requestAnimationFrame = window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || window.oRequestAnimationFrame || function(callback) {
                window.setTimeout(callback, 1000 / 60);
            };
        }
    })();

    jQuery(document).ready(init);

    function init() {

    }

	function delegate(fnc, obj) { var that = obj; return function() { fnc.call(that, arguments[0]); } }

})(window);
