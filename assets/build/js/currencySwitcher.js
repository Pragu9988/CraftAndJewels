/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
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
    $select.on('change', function () {
      var currency = $(this).val();
      $.post(htCurrencySwitcher.ajaxUrl, {
        action: 'ht_set_display_currency',
        nonce: htCurrencySwitcher.nonce,
        currency: currency
      }).done(function () {
        window.location.reload();
      }).fail(function () {
        window.location.reload();
      });
    });
  }
  $(initCurrencySwitcher);
})(jQuery);
/******/ })()
;
//# sourceMappingURL=currencySwitcher.js.map