/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external "jQuery"
var external_jQuery_namespaceObject = jQuery;
var external_jQuery_default = /*#__PURE__*/__webpack_require__.n(external_jQuery_namespaceObject);
;// CONCATENATED MODULE: ./src/js/metal-pricing-breakdown.js

external_jQuery_default()(document).ready(function () {
  const $modal = external_jQuery_default()("#ht-metal-pricing-modal");
  if (!$modal.length) {
    return;
  }
  const $trigger = external_jQuery_default()("[data-ht-open-metal-pricing]");
  let $lastFocus = null;
  function openModal() {
    $lastFocus = external_jQuery_default()(document.activeElement);
    $modal.addClass("is-open");
    $modal.attr("aria-hidden", "false");
    external_jQuery_default()("body").css("overflow", "hidden");
    $modal.find(".ht-floating-modal__close").trigger("focus");
  }
  function closeModal() {
    $modal.removeClass("is-open");
    $modal.attr("aria-hidden", "true");
    external_jQuery_default()("body").css("overflow", "");
    if ($lastFocus && $lastFocus.length) {
      $lastFocus.trigger("focus");
    }
  }
  external_jQuery_default()(document.body).off("click.htMetalPricingOpen", "[data-ht-open-metal-pricing]").on("click.htMetalPricingOpen", "[data-ht-open-metal-pricing]", function (e) {
    e.preventDefault();
    openModal();
  });
  external_jQuery_default()(document.body).off("click.htMetalPricingClose", "[data-ht-close-metal-pricing]").on("click.htMetalPricingClose", "[data-ht-close-metal-pricing]", function (e) {
    e.preventDefault();
    closeModal();
  });
  external_jQuery_default()(document).off("keydown.htMetalPricing").on("keydown.htMetalPricing", function (e) {
    if (!$modal.hasClass("is-open")) {
      return;
    }
    if (e.key === "Escape") {
      e.preventDefault();
      closeModal();
    }
  });
});
/******/ })()
;
//# sourceMappingURL=metalPricingBreakdown.js.map