<?php
/**
 * Homepage – Shop by Bond Section
 * Displays products filtered by 'shop_by_bond' taxonomy in a static tabbed layout.
 */

$bond_terms = get_terms([
           'taxonomy' => 'shop_by_bond',
           'hide_empty' => true,
           'parent' => 0,
]);

if (empty($bond_terms) || is_wp_error($bond_terms)) {
           return;
}
?>

<section class="ht-shop-by-bond">
           <div class="kl-container">

                      <header class="ht-shop-by-bond__headline">
                                 <!-- <small class="strapline text-center">Gifts that speak from the heart</small> -->
                                 <small class="strapline text-center">Gifts from the Heart</small>
                                 <h2 class="ht-shop-by-bond__title section-title text-center divider pb-1">Shop by Bond
                                 </h2>
                      </header>

                      <div class="ht-shop-by-bond__tabs-wrapper">
                                 <ul class="ht-shop-by-bond__tabs">
                                            <?php foreach ($bond_terms as $index => $term): ?>
                                                       <li class="ht-shop-by-bond__tab-item">
                                                                  <button class="ht-shop-by-bond__tab <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                                                             data-target="bond-tab-<?php echo esc_attr($term->slug); ?>">
                                                                             <?php echo esc_html($term->name); ?>
                                                                  </button>
                                                       </li>
                                            <?php endforeach; ?>
                                 </ul>
                      </div>

                      <div class="ht-shop-by-bond__content-wrapper">
                                 <?php foreach ($bond_terms as $index => $term): ?>
                                            <div id="bond-tab-<?php echo esc_attr($term->slug); ?>"
                                                       class="ht-shop-by-bond__tab-panel <?php echo $index === 0 ? 'is-active' : ''; ?>">

                                                       <?php
                                                       $args = [
                                                                  'post_type' => 'product',
                                                                  'posts_per_page' => 3,
                                                                  'post_status' => 'publish',
                                                                  'tax_query' => [
                                                                             [
                                                                                        'taxonomy' => 'shop_by_bond',
                                                                                        'field' => 'slug',
                                                                                        'terms' => $term->slug,
                                                                             ],
                                                                  ],
                                                       ];

                                                       $query = new WP_Query($args);

                                                       if ($query->have_posts()):
                                                                  woocommerce_product_loop_start();
                                                                  while ($query->have_posts()):
                                                                             $query->the_post();
                                                                             wc_get_template_part('content', 'product');
                                                                  endwhile;
                                                                  woocommerce_product_loop_end();
                                                                  wp_reset_postdata();
                                                       else:
                                                                  echo '<p class="text-center">' . esc_html__('No products found for this bond.', 'woocommerce') . '</p>';
                                                       endif;
                                                       ?>
                                            </div>
                                 <?php endforeach; ?>
                      </div>

           </div>
</section>