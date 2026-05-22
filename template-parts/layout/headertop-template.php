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
                      </div>
           </div>
</div>