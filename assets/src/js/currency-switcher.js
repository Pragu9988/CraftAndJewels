/**
 * Header currency switcher (NPR / USD).
 */
(function ($) {
	'use strict';

	function initCurrencySwitcher() {
		var $select = $('[data-ht-currency-switcher]');

		if (!$select.length || typeof htCurrencySwitcher === 'undefined') {
			return;
		}

		if (htCurrencySwitcher.currentCurrency) {
			$select.val(htCurrencySwitcher.currentCurrency);
		}

		$select.on('change', function () {
			var $el = $(this);
			var currency = $el.val();
			var previous = htCurrencySwitcher.currentCurrency || 'NPR';

			$el.prop('disabled', true);

			$.post(htCurrencySwitcher.ajaxUrl, {
				action: 'ht_set_display_currency',
				nonce: htCurrencySwitcher.nonce,
				currency: currency
			})
				.done(function (response) {
					if (!response || !response.success) {
						$el.val(previous);
						$el.prop('disabled', false);
						return;
					}

					window.location.reload();
				})
				.fail(function () {
					$el.val(previous);
					$el.prop('disabled', false);
				});
		});
	}

	$(initCurrencySwitcher);
})(jQuery);
