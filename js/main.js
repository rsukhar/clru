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
				console.log(response.data);
				if (!response.success) {
					$form.showErrors(response.data);
					return;
				}
				// Login successful
				var data = response.data;

				// TODO Event-driven logins should not reload page
				location.reload();
			}, 'json');
		};
	$overlay.on('click', hideForm);
	$formCloser.on('click', hideForm);
	$user.find('.i-login').on('click', showForm);
	$form.on('submit', function(event){
		event.preventDefault();
		var data = $form.serialize();
		login(data);
	});
})(jQuery);

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

/**
 * Salary calculator
 */
!function($){
	$('.cl-calculator').each(function(index, container){
		var $container = $(container),
			basicRate = parseInt($container.find('.cl-calculator-list-header-rate').data('rate')),
			advancedRates = [],
			$hourly = $container.find('.cl-calculator-result-hourly span'),
			$monthly = $container.find('.cl-calculator-result-monthly span'),
			recount = function(instantly){
				var oldHourly = parseInt($hourly.html().replace(/[^0-9]+/g, '')),
					newHourly = basicRate;
				$container.find('input[type="checkbox"]').each(function(index){
					if (this.checked) newHourly += advancedRates[index];
				});
				var oldMonthly = oldHourly * 168,
					newMonthly = newHourly * 168;
				if (instantly === true) {
					$hourly.html((newHourly + '').replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
					$monthly.html((newMonthly + '').replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
				} else {
					$hourly.css('step', 0).animate({step: 1}, {
						duration: 500,
						step: function(now){
							$hourly.html((parseInt((1 - now) * oldHourly + newHourly * now) + '').replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
							$monthly.html((parseInt((1 - now) * oldMonthly + newMonthly * now) + '').replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
						}
					});
				}
			};
		$container.find('.cl-calculator-list-item-rate').each(function(index, rate){
			advancedRates.push(parseInt($(rate).data('rate')));
		});
		$container.find('input[type="checkbox"]').change(recount);
		recount(true);
	});
}(jQuery);
