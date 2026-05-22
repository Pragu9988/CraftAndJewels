<?php
/**
 * Homepage - Best Selling WooCommerce Products
 */
?>

<div class="kl-home kl-product">
           <div class="kl-container">
                      <div class="row">
                                 <div class="col-xs-12">

                                            <header class="kl-product__headline mb-5 md:mb-6 lg:mb-8">
                                                       <small data-aos="fade-up"
                                                                  data-aos-anchor-placement="center-bottom" class="
                                                                  strapline text-center">Featured
                                                                  Collection</small>
                                                       <h2 data-aos="fade-up" data-aos-anchor-placement="center-bottom"
                                                                  data-aos-delay="200"
                                                                  class="h3 section-title leading-tight mb-3 text-center">
                                                                  Discover Our Featured Products
                                                       </h2>
                                            </header>


                                            <section data-aos="fade-up" data-aos-anchor-placement="center-bottom"
                                                       data-aos-delay="400" class="kl-product__result"
                                                       aria-label="Product Grid">
                                                       <div class="kl-product__grid ">
                                                                  <?php
                                                                  // Use do_shortcode to execute the WooCommerce Best Selling logic
                                                                  echo do_shortcode('[featured_products columns="4" limit="4"]');
                                                                  ?>
                                                       </div>
                                                       <!-- 
                                                       <?php if (!is_page('product')): ?>
                                                                  <div class="kl-product__cta mt-8 text-center">
                                                                             <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
                                                                                        class="oct-btn"
                                                                                        aria-label="View all products">
                                                                                        View All Products
                                                                             </a>
                                                                  </div>
                                                       <?php endif; ?> -->
                                            </section>

                                 </div>
                      </div>
           </div>
</div>