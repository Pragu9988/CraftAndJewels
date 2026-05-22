<?php
/**
 * Homepage - Shop By Occasion (Category)
 */
?>

<div class="ht-shop-by-cat">
      <div class="kl-container">
            <div class="row">
                  <div class="col-xs-12">


                        <header class="ht-shop-by-cat__headline mb-5 md:mb-7 lg:mb-9">
                              <small class="strapline text-center">SShop by Recipient
                              </small>
                              <h2 class="h2 section-title leading-tight mb-3 text-center divider">
                                    By Recipient / For Whom
                              </h2>
                        </header>



                        <section class="ht-shop-by-cat__content" aria-label="Shop By Occasion Grid">
                              <div class="ht-shop-by-cat__grid">
                                    <?php
                                    $product_categories = get_terms(array(
                                          'taxonomy' => 'for_whom',
                                          'hide_empty' => false,
                                          'parent' => 0,
                                    ));

                                    if (!empty($product_categories) && !is_wp_error($product_categories)):
                                          foreach ($product_categories as $category):
                                                // Skip the default "Uncategorized" category
                                                if ($category->slug === 'uncategorized') {
                                                      continue;
                                                }

                                                // Get category link
                                                $category_link = get_term_link($category);

                                                // Get category image
                                                $taxonomy_image_id = get_term_meta($category->term_id, 'taxonomy_image', true);
                                                $image_url = '';

                                                if ($taxonomy_image_id) {
                                                      $image_url = wp_get_attachment_url($taxonomy_image_id);
                                                }
                                                ?>
                                                <a href="<?php echo esc_url($category_link); ?>"
                                                      class="ht-shop-by-cat__card <?php echo !$image_url ? 'ht-shop-by-cat__card--no-image' : ''; ?>">
                                                      <div class="ht-shop-by-cat__img-wrapper">
                                                            <?php if ($image_url): ?>
                                                                  <img src="<?php echo esc_url($image_url); ?>"
                                                                        alt="<?php echo esc_attr($category->name); ?>">
                                                            <?php else: ?>
                                                                  <div class="ht-shop-by-cat__placeholder-text">
                                                                        <?php echo esc_html($category->name); ?>
                                                                  </div>
                                                            <?php endif; ?>
                                                      </div>
                                                      <div class="ht-shop-by-cat__label-wrapper">
                                                            <span
                                                                  class="ht-shop-by-cat__label"><?php echo esc_html($category->name); ?></span>
                                                            <span class="ht-shop-by-cat__explore">
                                                                  Explore
                                                                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                                        <polyline points="12 5 19 12 12 19"></polyline>
                                                                  </svg>
                                                            </span>
                                                      </div>
                                                </a>
                                                <?php
                                          endforeach;
                                    endif;
                                    ?>
                              </div>
                        </section>

                  </div>
            </div>
      </div>
</div>