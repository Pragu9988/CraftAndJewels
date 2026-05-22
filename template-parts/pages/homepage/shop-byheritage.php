<?php
/**
 * Homepage – Shop by Bond Section
 * Displays taxonomy terms from 'shop_by_bond' in a responsive CSS Grid.
 */

$bond_terms = get_terms([
           'taxonomy' => 'nepali_heritage',
           'hide_empty' => false,
           'parent' => 0,
]);
?>

<section class="ht-heritage">
           <div class="kl-container">
                      <header class="ht-heritage__headline">
                                 <!-- <small class="strapline text-center">
                                            Traditional jewellery rooted in Nepal's diverse communities
                                 </small> -->
                                 <small class="strapline text-center">
                                            Heritage Nepal Jewellery
                                 </small>
                                 <h2 class="ht-heritage__title section-title text-center divider pb-1">
                                            Shop by Nepali Ethnic Heritage
                                 </h2>
                      </header>

                      <?php if (!empty($bond_terms) && !is_wp_error($bond_terms)): ?>
                                 <div class="ht-heritage__grid">
                                            <?php foreach ($bond_terms as $term):
                                                       $image_id = get_term_meta($term->term_id, 'taxonomy_image', true);
                                                       $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
                                                       $term_link = get_term_link($term);
                                                       ?>
                                                       <a href="<?php echo esc_url($term_link); ?>" class="ht-heritage__card"
                                                                  title="<?php echo esc_attr($term->name); ?>">

                                                                  <div class="ht-heritage__img-wrapper">
                                                                             <?php if ($image_url): ?>
                                                                                        <img src="<?php echo esc_url($image_url); ?>"
                                                                                                   alt="<?php echo esc_attr($term->name); ?>"
                                                                                                   class="ht-heritage__img" loading="lazy" />
                                                                             <?php else: ?>
                                                                                        <div class="ht-heritage__placeholder">
                                                                                                   <?php echo esc_html($term->name); ?>
                                                                                        </div>
                                                                             <?php endif; ?>
                                                                  </div>

                                                                  <span class="ht-heritage__label">
                                                                             <?php echo esc_html($term->name); ?>
                                                                  </span>

                                                       </a>
                                            <?php endforeach; ?>
                                 </div>
                      <?php else: ?>
                                 <p class="ht-heritage__empty text-center">No bond categories found.</p>
                      <?php endif; ?>

           </div>
</section>