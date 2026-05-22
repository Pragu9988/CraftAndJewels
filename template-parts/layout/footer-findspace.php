<?php
/**
 * Footer Floating Widget
 *
 * @package OCTOWAYS_THEME
 */
?>
<div class="ht-floating-widget-wrapper">
           <!-- Floating Button -->
           <button id="ht-floating-btn" class="ht-floating-btn" aria-label="Open Product Finder">
                      <span class="ht-floating-btn__icon">✦</span>
                      <span class="ht-floating-btn__text">Find Your Piece</span>
           </button>

           <!-- Modal Popup -->
           <div id="ht-floating-modal" class="ht-floating-modal" aria-hidden="true" role="dialog">
                      <div class="ht-floating-modal__overlay" data-action="close"></div>
                      <div class="ht-floating-modal__dialog">

                                 <button class="ht-floating-modal__close" data-action="close" aria-label="Close modal">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                       xmlns="http://www.w3.org/2000/svg">
                                                       <path d="M18 6L6 18M6 6L18 18" stroke="currentColor"
                                                                  stroke-width="2" stroke-linecap="round"
                                                                  stroke-linejoin="round" />
                                            </svg>
                                 </button>

                                 <div class="ht-floating-modal__header">
                                            <div class="ht-floating-modal__progress">
                                                       <div class="ht-floating-modal__progress-bar"
                                                                  id="ht-floating-progress"></div>
                                            </div>
                                            <div class="ht-floating-modal__step-text" id="ht-floating-step-text">Step 1
                                                       of 3</div>
                                            <h3 class="ht-floating-modal__title" id="ht-floating-title">Signature
                                                       Jewellery Collection</h3>
                                            <button class="ht-floating-modal__back" id="ht-floating-back"
                                                       style="display: none;">
                                                       <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                                  xmlns="http://www.w3.org/2000/svg">
                                                                  <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor"
                                                                             stroke-width="1.5" stroke-linecap="round"
                                                                             stroke-linejoin="round" />
                                                       </svg>
                                                       Back
                                            </button>
                                 </div>

                                 <div class="ht-floating-modal__body">
                                            <!-- Step containers -->
                                            <div class="ht-floating-step is-active" id="ht-floating-step-1">
                                                       <div class="ht-floating-list" id="ht-floating-taxonomies-list">
                                                                  <!-- Loaded via AJAX -->
                                                                  <div class="ht-floating-loader">Loading...</div>
                                                       </div>
                                            </div>

                                            <div class="ht-floating-step" id="ht-floating-step-2">
                                                       <div class="ht-floating-list" id="ht-floating-terms-list">
                                                                  <!-- Loaded via AJAX -->
                                                       </div>
                                            </div>

                                            <div class="ht-floating-step" id="ht-floating-step-3">
                                                       <div class="ht-floating-grid" id="ht-floating-products-list">
                                                                  <!-- Loaded via AJAX -->
                                                       </div>
                                                       <div class="ht-floating-actions">
                                                                  <button class="ht-floating-btn ht-floating-btn--outline"
                                                                             id="ht-floating-restart">Take Quiz
                                                                             Again</button>
                                                       </div>
                                            </div>
                                 </div>

                      </div>
           </div>
</div>