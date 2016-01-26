/**
 * Material design animations and related helpers
 */
(function($){
	"use strict";
	var checkInputEmptiness = function(){
		var $this = jQuery(this);
		if ($this.attr('type') == 'hidden') return;
		$this.toggleClass('not-empty', $this.val() != '');
	};
	jQuery('.g-form input, .g-form textarea').each(checkInputEmptiness);
	jQuery('body').on('input', '.g-form input, .g-form textarea', checkInputEmptiness);

	// Applying alternate color to the whole page
	var $section = $('.l-section');
	if ($section.length == 1 && $section.hasClass('color_alternate')) $('.l-canvas').addClass('color_alternate');
})(jQuery);

/**
 * Auth functions
 */
(function($){
	"use strict";
	var $body = $('body'),
		$form = $('.g-form.for_login').detach().css('display', ''),
		$formCloser = $form.find('.g-closer'),
		$overlay = $('<div class="g-overlay">&nbsp;</div>'),
		$user = $('#user-block'),
		$userNav = $('#user-nav'),
	// Show login form
		showForm = function(){
			$overlay.appendTo($body);
			$form.appendTo($body);
			$form.find('input[type="text"]').first().focus();
		},
	// Hide login form
		hideForm = function(){
			$form.detach();
			$overlay.detach();
		},
	// Attempt to login using the provided credentials
		login = function(data){
			$.post(clruAjax.ajaxurl, data, function(response){
				// Clearing error messages
				$form.find('.g-form-row.check_wrong').removeClass('check_wrong');
				$form.find('.g-form-row-state').html('');
				// Login failed
				if (!response.success) {
					$form.showErrors(response.errors);
					return;
				}
				// Login successful
				var data = response.data,
					message = response.message;

				// TODO Event-driven logins should not reload page
				location.reload();
			}, 'json');
		},
	// Attempt to logout
		logout = function(){
			var data = {
				action: 'do_logout'
			};
			$.post(clruAjax.ajaxurl, data, function(response){
				// Logout successful
				location.reload();
			}, 'json');
		};
	$overlay.on('click', hideForm);
	$formCloser.on('click', hideForm);
	$user.find('.i-login').on('click', showForm);
	$user.find('.i-logout').on('click', logout);
	$form.on('submit', function(event){
		event.preventDefault();
		var data = $form.serialize();
		login(data);
	});
})(jQuery);

/**
 * Getting/setting/removing modifier css classes
 * @param {String} mod Modifier namespace (the part before "_")
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
jQuery.fn.mod = function(mod, value){
	if (this.length == 0) return this;
	// Deleting modifier
	if (value === false) {
		this.get(0).className = this.get(0).className.replace(new RegExp('(^| )' + mod + '\_[a-z0-9]+( |$)'), '$2');
		return this;
	}
	var pcre = new RegExp('^.*?' + mod + '\_([a-z0-9]+).*?$'),
		arr;
	// Getting modifier
	if (value === undefined) {
		return (arr = pcre.exec(this.get(0).className)) ? arr[1] : false;
	}
	// Setting modifier
	else {
		this.mod(mod, false).get(0).className += ' ' + mod + '_' + value;
		return this;
	}
};

/**
 * Show errors from API request in some particular form
 * @param errors
 */
jQuery.fn.showErrors = function(errors){
	// Cleaning previous errors at first
	this.find('.g-form-row.check_wrong .g-form-row-state').html('');
	this.find('.g-form-row.check_wrong').removeClass('check_wrong');
	for (var key in errors) {
		if (!errors.hasOwnProperty(key)) continue;
		var $input = this.find('[name="' + key + '"]');
		if ($input.length == 0) continue;
		$input.parents('.g-form-row').addClass('check_wrong').find('.g-form-row-state').html(errors[key]);
	}
};

// Fixing hovers for devices with both mouse and touch screen
jQuery.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
jQuery('html').toggleClass('no-touch', !jQuery.isMobile);
