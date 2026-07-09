/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
/**
 * Header currency switcher (NPR / USD).
 */
(function () {
  'use strict';

  function initCurrencySwitcher() {
    var select = document.querySelector('[data-ht-currency-switcher]');
    if (!select || typeof htCurrencySwitcher === 'undefined') {
      return;
    }
    if (htCurrencySwitcher.currentCurrency) {
      select.value = htCurrencySwitcher.currentCurrency;
    }
    select.addEventListener('change', function () {
      var currency = select.value;
      var url = new URL(window.location.href);
      url.searchParams.set('ht_currency', currency);
      url.searchParams.set('_wpnonce', htCurrencySwitcher.nonce);
      window.location.replace(url.toString());
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCurrencySwitcher);
  } else {
    initCurrencySwitcher();
  }
})();
/******/ })()
;
//# sourceMappingURL=currencySwitcher.js.map