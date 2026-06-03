import $ from "jquery";

$(document).ready(function () {
  const $modal = $("#ht-metal-pricing-modal");

  if (!$modal.length) {
    return;
  }

  const $trigger = $("[data-ht-open-metal-pricing]");
  let $lastFocus = null;

  function openModal() {
    $lastFocus = $(document.activeElement);
    $modal.addClass("is-open");
    $modal.attr("aria-hidden", "false");
    $("body").css("overflow", "hidden");
    $modal.find(".ht-floating-modal__close").trigger("focus");
  }

  function closeModal() {
    $modal.removeClass("is-open");
    $modal.attr("aria-hidden", "true");
    $("body").css("overflow", "");

    if ($lastFocus && $lastFocus.length) {
      $lastFocus.trigger("focus");
    }
  }

  $(document.body)
    .off("click.htMetalPricingOpen", "[data-ht-open-metal-pricing]")
    .on("click.htMetalPricingOpen", "[data-ht-open-metal-pricing]", function (e) {
      e.preventDefault();
      openModal();
    });

  $(document.body)
    .off("click.htMetalPricingClose", "[data-ht-close-metal-pricing]")
    .on("click.htMetalPricingClose", "[data-ht-close-metal-pricing]", function (e) {
      e.preventDefault();
      closeModal();
    });

  $(document)
    .off("keydown.htMetalPricing")
    .on("keydown.htMetalPricing", function (e) {
      if (!$modal.hasClass("is-open")) {
        return;
      }

      if (e.key === "Escape") {
        e.preventDefault();
        closeModal();
      }
    });
});
