<?php
/**
 * Homepage - Shop By Occasion (Category)
 */
?>

<div class="ht-cat-slider">
           <div class="kl-container">
                      <div class="row">
                                 <div class="col-xs-12">

                                            <header class="ht-cat-slider__headline mb-5 md:mb-7 lg:mb-9">
                                                       <small data-aos="fade-up" class="strapline">Jewellery
                                                                  Type
                                                       </small>
                                                       <h2 data-aos="fade-up" data-aos-anchor-placement="center-bottom"
                                                                  data-aos-delay="150"
                                                                  class="h3 section-title divider leading-tight mb-2 pb-1">
                                                                  By Jewellery Type
                                                       </h2>

                                            </header>

                                            <section data-aos="fade-up" data-aos-delay="150"
                                                       class="ht-cat-slider__content splide"
                                                       aria-label="Shop By Categories Slider">
                                                       <div class="splide__track">
                                                                  <ul class="splide__list">
                                                                             <?php
                                                                             $jewellery_terms = get_terms([
                                                                                        'taxonomy' => 'jewellery',
                                                                                        'hide_empty' => false,
                                                                                        'parent' => 0,
                                                                             ]);

                                                                             if (!is_wp_error($jewellery_terms) && !empty($jewellery_terms)):
                                                                                        foreach ($jewellery_terms as $term):
                                                                                                   $image_id = get_term_meta($term->term_id, 'taxonomy_image', true);
                                                                                                   $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
                                                                                                   $term_link = get_term_link($term);
                                                                                                   ?>
                                                                                                   <li
                                                                                                              class="splide__slide ht-cat-slider__slide">
                                                                                                              <a href="<?php echo esc_url($term_link); ?>"
                                                                                                                         class="ht-cat-slider__item">
                                                                                                                         <div
                                                                                                                                    class="ht-cat-slider__img-wrapper">
                                                                                                                                    <div
                                                                                                                                               class="inner">
                                                                                                                                               <?php if ($image_url): ?>
                                                                                                                                                          <img src="<?php echo esc_url($image_url); ?>"
                                                                                                                                                                     alt="<?php echo esc_attr($term->name); ?>"
                                                                                                                                                                     class="ht-cat-slider__img">
                                                                                                                                               <?php else: ?>
                                                                                                                                                          <span
                                                                                                                                                                     class="ht-cat-slider__placeholder"><?php echo esc_html($term->name); ?></span>
                                                                                                                                               <?php endif; ?>
                                                                                                                                    </div>
                                                                                                                         </div>
                                                                                                                         <div
                                                                                                                                    class="ht-cat-slider__label-wrapper">
                                                                                                                                    <span
                                                                                                                                               class="ht-cat-slider__label">
                                                                                                                                               <?php echo esc_html($term->name); ?>
                                                                                                                                    </span>
                                                                                                                         </div>
                                                                                                              </a>
                                                                                                   </li>
                                                                                        <?php endforeach;
                                                                             endif; ?>
                                                                  </ul>
                                                       </div>
                                            </section>

                                 </div>
                      </div>
           </div>
</div>