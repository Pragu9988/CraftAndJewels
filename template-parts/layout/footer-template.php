<?php

/**
 * Footer Bottom
 *
 * @package Octoways-theme
 */
?>
<section class="ht-footer-wrapper">
           <div class="news-letter-section">

                      <!-- Newsletter Section -->
                      <div class="ht-footer__newsletter">
                                 <h2 class="ht-footer__newsletter-title">
                                            Join
                                            the Heritage Circle</h2>
                                 <p class="ht-footer__newsletter-subtext">Unlock exclusive access to secret deals and pre-launch offers.</p>
                                 <form class="ht-footer__newsletter-form">
                                            <input type="email" placeholder="Your email address" required>
                                            <button type="submit" class="ht-btn ht-btn--gold">CLAIM OFFER</button>
                                 </form>
                      </div>
           </div>
           <div class="footer-main-wrapper">
                      <div class="kl-container">
                                 <div class="ht-footer__grid">
                                            <!-- Column 1: Brand -->
                                            <div class="ht-footer__column ht-footer__column--brand">
                                                       <div class="ht-footer__logo">
                                                                  <?php
                                                                  if (has_custom_logo()) {
                                                                             the_custom_logo();
                                                                  } else {
                                                                             bloginfo('name');
                                                                  }
                                                                  ?>
                                                       </div>
                                                       <p class="ht-footer__tagline">Crafted for generation in Himalayan</p>
                                                       <div class="ht-footer__social">
                                                                  <?php
                                                                  if (function_exists('octoways_render_social_links')) {
                                                                             octoways_render_social_links();
                                                                  }
                                                                  ?>
                                                       </div>
                                            </div>

                                            <!-- Column 2: Quick Links -->
                                            <div class="ht-footer__column">
                                                       <h3 class="ht-footer__title">Quick Links</h3>
                                                       <ul class="ht-footer__list">
                                                                  <li><a href="<?php echo esc_url(home_url('/about-us')); ?>">About
                                                                                        Us</a></li>
                                                                  <li><a href="<?php echo esc_url(home_url('/faq')); ?>">FAQ</a>
                                                                  </li>

                                                       </ul>
                                            </div>

                                            <!-- Column 3: Customer Care -->
                                            <div class="ht-footer__column">
                                                       <h3 class="ht-footer__title">Customer Care</h3>
                                                       <ul class="ht-footer__list">
                                                                  <li><a href="<?php echo esc_url(home_url('/shipping-info')); ?>">Shipping
                                                                                        Info</a></li>
                                                                  <li><a href="<?php echo esc_url(home_url('/returns-exchange')); ?>">Returns
                                                                                        & Exchange</a></li>
                                                                  <li><a href="<?php echo esc_url(home_url('/size-guide')); ?>">Size
                                                                                        Guide</a></li>
                                                                  <li><a href="<?php echo esc_url(home_url('/contact-us')); ?>">Contact
                                                                                        Us</a></li>
                                                                  <li><a href="<?php echo esc_url(home_url('/whatsapp-support')); ?>">WhatsApp
                                                                                        Support</a></li>
                                                       </ul>
                                            </div>
                                 </div>
                      </div>
                      <div class="kl-container">



                                 <div class="ht-footer__bottom">
                                            <div class="ht-footer__copyright">
                                                       <p>&copy; <?php echo esc_html(date('Y')); ?> Heritage Craft &
                                                                  Jewels · <a target="_blank"
                                                                             href="<?php echo esc_url(home_url('/wp-content/uploads/2026/04/iso.pdf')); ?>">An
                                                                             ISO 9001:2015 Certified Company</a>

                                                                  · Made in Nepal</p>
                                            </div>
                                            <div class="ht-footer__iso">
                                                       <div class="ht-footer__wrapper">
                                                                  <img src="<?php echo OCTOWAYS_SRC_IMG_URI . '/footer/footer.webp'; ?>"
                                                                             alt="Footer Image">


                                                       </div>

                                            </div>
                                 </div>
                      </div>
           </div>

</section>