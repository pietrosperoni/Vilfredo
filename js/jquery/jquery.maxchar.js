/**
* maxChar jQuery plugin
* @author Mitch Wilson
* @version 0.2.0
* @requires jQuery 1.3.2 (only version tested)
* @see http://mitchwilson.net/2009/08/03/new-jquery-plugin-maxchar/
* @param {Boolean}	debug				Specify whether to send message updates, at the current rate, to the Firebug console.
* @param {String}	indicator 			Specify alternate indicator element by id. The indicator displays any available messages.
* @param {String}	label				Specify a default label displayed when input element is not in focus.
* @param {String}	pluralMessage 		Set the plural form of the remaining count message, eg, ' characters remaining'.
* @param {Number}	rate 				Set the update rate in milliseconds.
* @param {String}	singularMessage 	Set the singular form of the remaining count message, eg, ' character remaining'.
* @param {String}	spaceBeforeMessage 	Set the space, if any, in front of (to the left of) the indicator message. Sometimes necessary for layout.
* @description Enforces max character limit on any input or textarea HTML element and provides user feedback and many options. 
* New features added in 0.2.0 are: 
* 1) Optional default label displayed when input element is not in focus.
* 2) Optional display of only count when singularMessage and/or pluralMessage are set to empty.
* 3) Optional updates, at the current rate, to the Firebug console.
* 4) Refactored, improved code.
* 5) More code comments and documentation.
*/

(function($){
	$.fn.maxChar = function(limit, options) {
		
		// Define default settings and override w/ options.	
		settings = jQuery.extend({
			debug: false,
			indicator: 'indicator',
			label: '',
			pluralMessage:' remaining',
			rate: 200,
			singularMessage: ' remaining',
			spaceBeforeMessage: ' '
		}, options);
		
		// Get maxChar target element.
		var target = $(this); // Must get target first, since it is used in setting other local variables.
		
		// Get settings.
		var debug = settings.debug;
		var indicatorId = settings.indicator;
		var label = settings.label;
		var pluralMessage = settings.pluralMessage;
		var rate = settings.rate;
		var singularMessage = settings.singularMessage;
		var spaceBeforeMessage = settings.spaceBeforeMessage;
		
		// Set additional local variables.
		var currentMessage = ''; // Current message to display to the user.
		var indicator = getIndicator(indicatorId); // Element to display count, messages and label.
		var limit = limit; // Character limit.
		var remaining = limit; // Characters remaining.
		var timer = null; // Timer to run update.
		
		// Initialize on page ready.
		if(label) {
			indicator.text(label);
		} else {
			// Call update once on code initialization to update view if text is already in textarea,
			// eg, if user relaoads page or hits back button and form textarea retains previoulsy entered text.
			update(limit);
		}
		
		// When user focuses on the target element, do the following.
		$(this).change(function(){
			if(timer == null) {
				if(label) {
					indicator.fadeOut(function(){indicator.text('')}).fadeIn(function(){start()});					
				} else {
					start();
				}
			}
		});
		
		// When user removes focus from the target element, do the following.
		$(this).blur(function() {
			// Stop timer that updates count and the indicator message.
			stop();
			// Update view.
			if(label) {
				indicator.fadeOut(function(){indicator.text(label)}).fadeIn();
			}
		});
		
		function getIndicator(id){
			// Get indicator element in the dom.
			var indicator = $('#'+id);
			// If indicator element does not already exist in the dom, create it.
			if(indicator.length == 0) {
				target.after(spaceBeforeMessage + '<span id="'+id+'"></span>');
				indicator = $('#'+id)
			}
			// Return reference to indicator element.
			return indicator;
		}

		// Create helper functions.
		function log(message) {
			// Display 
			if(debug) {
				try {
					if(console) {
						console.log(message);
					}
				} catch(e) {
					// Do nothing on error.
				}
			}
		}
		
		// Start the timer that updates indicator.
		function start() {
			timer = setInterval(function(){update(limit)}, rate);
		}
		
		// Stop the timer that updates the indicator.
		function stop() {
			if(timer != null) {
				clearInterval(timer);
				timer = null;
			}
		}
		
		// Update the indicator.
		function update(limit){
			var remaining = limit - target.val().length;
			if(remaining < 1) {
				target.val(target.val().slice(0,limit));
				remaining = 0; // Prevents flashing negative remaining character amounts, such as -1, before update overwrites.
			}
			// Update remaining count and message.
			if(remaining == 1) {
				currentMessage = remaining + singularMessage;
			} else {
				currentMessage = remaining + pluralMessage;
			}
			// Update indicator.
			indicator.text(currentMessage);
			log(currentMessage);
		}
	};
})(jQuery);