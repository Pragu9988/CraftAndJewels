<?php
/**
 * Homepage - Best Selling WooCommerce Products
 */
?>

<div class="kl-home kl-product">
           <div class="kl-container">
                      <div class="row">
                                 <div class="col-xs-12">


                                            <header class="kl-product__headline mb-5 md:mb-8 lg:mb-10">
                                                       <small class="strapline text-center">Our Best Sellers</small>
                                                       <h2 class="h3 section-title leading-tight mb-3 text-center">
                                                                  Explore Our Top Selling Products
                                                       </h2>
                                            </header>


                                            <section class="kl-product__result" aria-label="Product Grid">
                                                       <div class="kl-product__grid ">
                                                                  <?php
                                                                  // Use do_shortcode to execute the WooCommerce Best Selling logic
                                                                  echo do_shortcode('[products best_selling="true" columns="4" limit="8"]');
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