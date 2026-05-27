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
;// CONCATENATED MODULE: ./src/js/checkout-payment-proof.js

external_jQuery_default()(document).ready(function () {
  const config = typeof htPaymentProof !== "undefined" ? htPaymentProof : null;
  if (!config) {
    return;
  }
  const gatewayId = config.gatewayId || "ht_upload_proof";
  const $modal = external_jQuery_default()("#ht-payment-proof-modal");
  const $success = $modal.find(".ht-payment-proof-modal__success");
  if (!$modal.length) {
    return;
  }
  let thankYouOpened = false;
  function openModal() {
    $modal.addClass("is-open");
    $modal.attr("aria-hidden", "false");
    external_jQuery_default()("body").css("overflow", "hidden");
  }
  function closeModal() {
    $modal.removeClass("is-open");
    $modal.attr("aria-hidden", "true");
    external_jQuery_default()("body").css("overflow", "");
  }
  function isUploadProofSelected() {
    const $selected = external_jQuery_default()('input[name="payment_method"]:checked');
    return $selected.length && $selected.val() === gatewayId;
  }
  function onPaymentMethodChange() {
    if (isUploadProofSelected()) {
      openModal();
    } else {
      closeModal();
    }
  }
  function bindPaymentMethodListeners() {
    external_jQuery_default()(document.body).off("change.htPaymentProof", 'input[name="payment_method"]').on("change.htPaymentProof", 'input[name="payment_method"]', onPaymentMethodChange);
    external_jQuery_default()(document.body).off("click.htPaymentProofOpen", "[data-ht-open-payment-proof]").on("click.htPaymentProofOpen", "[data-ht-open-payment-proof]", function (e) {
      e.preventDefault();
      openModal();
    });
    external_jQuery_default()(document.body).off("click.htPaymentProofClose", "[data-ht-close-payment-proof]").on("click.htPaymentProofClose", "[data-ht-close-payment-proof]", function (e) {
      e.preventDefault();
      closeModal();
    });
  }
  bindPaymentMethodListeners();
  external_jQuery_default()(document.body).on("updated_checkout", function () {
    bindPaymentMethodListeners();
    if (isUploadProofSelected()) {
      openModal();
    }
  });
  if (config.autoOpenOnThankYou && !thankYouOpened) {
    thankYouOpened = true;
    openModal();
  } else if (external_jQuery_default()('input[name="payment_method"]:checked').length && isUploadProofSelected()) {
    openModal();
  }
  document.addEventListener("wpcf7mailsent", function (event) {
    const $form = $modal.find(".wpcf7");
    if (!$form.length || !event.target || !$form[0].contains(event.target)) {
      return;
    }
    const message = config.i18n && config.i18n.uploadSuccess || "Thank you — your payment proof was submitted.";
    $success.text(message).prop("hidden", false);
    $modal.find(".ht-payment-proof-modal__form").attr("hidden", "hidden");
    setTimeout(function () {
      closeModal();
    }, 2500);
  }, false);
});
/******/ })()
;
//# sourceMappingURL=checkoutPaymentProof.js.map