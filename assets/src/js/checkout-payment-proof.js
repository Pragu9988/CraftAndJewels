import $ from "jquery";

$(document).ready(function () {
  const config =
    typeof htPaymentProof !== "undefined" ? htPaymentProof : null;

  if (!config) {
    return;
  }

  const gatewayId = config.gatewayId || "ht_upload_proof";
  const $modal = $("#ht-payment-proof-modal");
  const $success = $modal.find(".ht-payment-proof-modal__success");

  if (!$modal.length) {
    return;
  }

  let thankYouOpened = false;

  function openModal() {
    $modal.addClass("is-open");
    $modal.attr("aria-hidden", "false");
    $("body").css("overflow", "hidden");
  }

  function closeModal() {
    $modal.removeClass("is-open");
    $modal.attr("aria-hidden", "true");
    $("body").css("overflow", "");
  }

  function isUploadProofSelected() {
    const $selected = $('input[name="payment_method"]:checked');
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
    $(document.body)
      .off("change.htPaymentProof", 'input[name="payment_method"]')
      .on("change.htPaymentProof", 'input[name="payment_method"]', onPaymentMethodChange);

    $(document.body)
      .off("click.htPaymentProofOpen", "[data-ht-open-payment-proof]")
      .on("click.htPaymentProofOpen", "[data-ht-open-payment-proof]", function (e) {
        e.preventDefault();
        openModal();
      });

    $(document.body)
      .off("click.htPaymentProofClose", "[data-ht-close-payment-proof]")
      .on("click.htPaymentProofClose", "[data-ht-close-payment-proof]", function (e) {
        e.preventDefault();
        closeModal();
      });
  }

  bindPaymentMethodListeners();

  $(document.body).on("updated_checkout", function () {
    bindPaymentMethodListeners();
    if (isUploadProofSelected()) {
      openModal();
    }
  });

  if (config.autoOpenOnThankYou && !thankYouOpened) {
    thankYouOpened = true;
    openModal();
  } else if ($('input[name="payment_method"]:checked').length && isUploadProofSelected()) {
    openModal();
  }

  document.addEventListener(
    "wpcf7mailsent",
    function (event) {
      const $form = $modal.find(".wpcf7");
      if (!$form.length || !event.target || !$form[0].contains(event.target)) {
        return;
      }

      const message =
        (config.i18n && config.i18n.uploadSuccess) ||
        "Thank you — your payment proof was submitted.";

      $success.text(message).prop("hidden", false);
      $modal.find(".ht-payment-proof-modal__form").attr("hidden", "hidden");

      setTimeout(function () {
        closeModal();
      }, 2500);
    },
    false
  );
});
