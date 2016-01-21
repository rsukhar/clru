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
