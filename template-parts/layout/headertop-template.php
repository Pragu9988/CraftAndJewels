<?php
/**
 * Footer Bottom / Header Layout
 *
 * @package Octoways-theme
 */

$cart_count = 0;
if (function_exists('WC') && isset(WC()->cart) && WC()->cart) {
           $cart_count = WC()->cart->get_cart_contents_count();
}
?>

<div class="ht-header-top-bar">
           <div class="kl-container">
                      <div class="ht-header-top-bar__wrapper">
                                 <p class="ht-header-top-bar__text">
                                            An ISO 9001:2015 Certified Company
                                 </p>
                                 <?php if (class_exists('WooCommerce')) : ?>
                                            <?php
                                            $current_currency = \OCTOWAYS_THEME\Inc\Currency_Context::instance()->get_display_currency();
                                            $usd_available    = ( new \OCTOWAYS_THEME\Inc\Currency_Converter() )->is_usd_available();
                                            ?>
                                            <div class="ht-header-top-bar__currency">
                                                       <label class="screen-reader-text" for="ht-currency-switcher"><?php esc_html_e('Currency', 'octoways'); ?></label>
                                                       <select
                                                                  id="ht-currency-switcher"
                                                                  class="ht-currency-switcher"
                                                                  data-ht-currency-switcher
                                                                  <?php echo $usd_available ? '' : 'disabled'; ?>
                                                       >
                                                                  <option value="NPR" <?php selected($current_currency, 'NPR'); ?>>🇳🇵 NPR</option>
                                                                  <option value="USD" <?php selected($current_currency, 'USD'); ?> <?php disabled(!$usd_available); ?>>🇺🇸 USD</option>
                                                       </select>
                                            </div>
                                 <?php endif; ?>
                      </div>
           </div>
</div>