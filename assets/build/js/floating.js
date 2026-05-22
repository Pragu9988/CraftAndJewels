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
;// CONCATENATED MODULE: ./src/js/floating.js

external_jQuery_default()(document).ready(function () {
  const $btn = external_jQuery_default()('#ht-floating-btn');
  const $modal = external_jQuery_default()('#ht-floating-modal');
  const $closeBtns = external_jQuery_default()('[data-action="close"]');
  const $backBtn = external_jQuery_default()('#ht-floating-back');
  const $restartBtn = external_jQuery_default()('#ht-floating-restart');
  const $stepText = external_jQuery_default()('#ht-floating-step-text');
  const $title = external_jQuery_default()('#ht-floating-title');
  const $progressBar = external_jQuery_default()('#ht-floating-progress');
  const $step1 = external_jQuery_default()('#ht-floating-step-1');
  const $step2 = external_jQuery_default()('#ht-floating-step-2');
  const $step3 = external_jQuery_default()('#ht-floating-step-3');
  const $taxonomiesList = external_jQuery_default()('#ht-floating-taxonomies-list');
  const $termsList = external_jQuery_default()('#ht-floating-terms-list');
  const $productsList = external_jQuery_default()('#ht-floating-products-list');
  let currentStep = 1;
  let selectedTaxonomy = '';
  let selectedTaxonomyName = '';
  let selectedTerm = '';
  const AJAX_URL = typeof heritageFloating !== 'undefined' ? heritageFloating.ajaxUrl : '';
  const NONCE = typeof heritageFloating !== 'undefined' ? heritageFloating.nonce : '';
  if (!$btn.length || !AJAX_URL) return;

  // Open Modal
  $btn.on('click', function () {
    $modal.addClass('is-open');
    $modal.attr('aria-hidden', 'false');
    external_jQuery_default()('body').css('overflow', 'hidden'); // Prevent background scrolling

    // Initial load if empty
    if (currentStep === 1 && $taxonomiesList.find('button.ht-floating-list-item').length === 0) {
      fetchTaxonomies();
    }
  });

  // Close Modal
  $closeBtns.on('click', function () {
    closeModal();
  });
  function closeModal() {
    $modal.removeClass('is-open');
    $modal.attr('aria-hidden', 'true');
    external_jQuery_default()('body').css('overflow', '');
  }

  // Back Button
  $backBtn.on('click', function () {
    if (currentStep > 1) {
      goToStep(currentStep - 1);
    }
  });

  // Restart Button
  $restartBtn.on('click', function () {
    selectedTaxonomy = '';
    selectedTaxonomyName = '';
    selectedTerm = '';
    goToStep(1);
  });
  function updateHeaderUI() {
    // Progress bar (3 steps)
    const progressPercentage = currentStep / 3 * 100;
    $progressBar.css('width', `${progressPercentage}%`);
    if (currentStep === 1) {
      $stepText.text('Step 1 of 3');
      $title.text("Signature Jewellery Collection ?");
      $backBtn.hide();
    } else if (currentStep === 2) {
      $stepText.text('Step 2 of 3');
      $title.text(`Preferred ${selectedTaxonomyName.toLowerCase()}?`);
      $backBtn.show();
    } else if (currentStep === 3) {
      $stepText.text('Your Recommendations');
      $title.text('We found these for you ✦');
      $backBtn.show();
    }
  }
  function goToStep(step) {
    $step1.removeClass('is-active');
    $step2.removeClass('is-active');
    $step3.removeClass('is-active');
    currentStep = step;
    if (step === 1) {
      $step1.addClass('is-active');
    } else if (step === 2) {
      $step2.addClass('is-active');
    } else if (step === 3) {
      $step3.addClass('is-active');
    }
    updateHeaderUI();
  }
  function fetchTaxonomies() {
    $taxonomiesList.html('<div class="ht-floating-loader">Loading...</div>');
    external_jQuery_default().ajax({
      url: AJAX_URL,
      type: 'POST',
      data: {
        action: 'heritage_floating_get_taxonomies',
        nonce: NONCE
      },
      success: function (response) {
        if (response.success) {
          renderTaxonomies(response.data);
        } else {
          $taxonomiesList.html('<p>Error loading options.</p>');
        }
      },
      error: function () {
        $taxonomiesList.html('<p>Error connecting to server.</p>');
      }
    });
  }
  function renderTaxonomies(taxonomies) {
    $taxonomiesList.empty();
    taxonomies.forEach(tax => {
      const btn = external_jQuery_default()(`
                <button class="ht-floating-list-item" data-slug="${tax.slug}" data-name="${tax.name}">
                    <span class="ht-floating-list-item__text">${tax.name}</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `);
      btn.on('click', function () {
        selectedTaxonomy = external_jQuery_default()(this).data('slug');
        selectedTaxonomyName = external_jQuery_default()(this).data('name');
        goToStep(2);
        fetchTerms(selectedTaxonomy);
      });
      $taxonomiesList.append(btn);
    });
  }
  function fetchTerms(taxonomySlug) {
    $termsList.html('<div class="ht-floating-loader">Loading options...</div>');
    external_jQuery_default().ajax({
      url: AJAX_URL,
      type: 'POST',
      data: {
        action: 'heritage_floating_get_terms',
        nonce: NONCE,
        taxonomy: taxonomySlug
      },
      success: function (response) {
        if (response.success && response.data.length > 0) {
          renderTerms(response.data);
        } else {
          $termsList.html('<p class="ht-floating-no-results">No specific options found for this category. <br><button class="ht-floating-text-btn" data-action="back">Go Back</button></p>');
          $termsList.find('[data-action="back"]').on('click', () => goToStep(1));
        }
      },
      error: function () {
        $termsList.html('<p>Error connecting to server.</p>');
      }
    });
  }
  function renderTerms(terms) {
    $termsList.empty();
    terms.forEach(term => {
      const btn = external_jQuery_default()(`
                <button class="ht-floating-list-item" data-slug="${term.slug}">
                    <span class="ht-floating-list-item__text">${term.name}</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            `);
      btn.on('click', function () {
        selectedTerm = external_jQuery_default()(this).data('slug');
        goToStep(3);
        fetchProducts(selectedTaxonomy, selectedTerm);
      });
      $termsList.append(btn);
    });
  }
  function fetchProducts(taxonomySlug, termSlug) {
    $productsList.html('<div class="ht-floating-loader">Finding perfect matches...</div>');
    external_jQuery_default().ajax({
      url: AJAX_URL,
      type: 'POST',
      data: {
        action: 'heritage_floating_get_products',
        nonce: NONCE,
        taxonomy: taxonomySlug,
        term_slug: termSlug
      },
      success: function (response) {
        if (response.success) {
          $productsList.html(response.data.html);
        } else {
          $productsList.html('<p>Could not load products.</p>');
        }
      },
      error: function () {
        $productsList.html('<p>Error connecting to server.</p>');
      }
    });
  }
});
/******/ })()
;
//# sourceMappingURL=floating.js.map