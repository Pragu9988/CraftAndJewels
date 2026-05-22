<?php

/**
 * Homepage - Blogs
 */

$featured_args = [
  'post_type'         => 'post',
  'post_status'       => 'publish',
  'posts_per_page'    => 5,
  'post__in'          => get_option('sticky_posts'), // Get the sticky posts
  'ignore_sticky_posts' => 1
];

$featured_query = new WP_Query($featured_args);

?>
<section class="kl-home kl-blogs">
  <div class="kl-container">
    <div class="">
      <div class="col-xs-12 splide blog-splide">
        <div class="kl-blogs__top">
          <div class="kl-blogs__headline">
            <small class="strapline">Blog Insights</small>
            <h2 class="text-section-title font-medium  text-text-400">Daily Dose of Education</h2>
          </div>
          <div class="kl-splider__nav">
            <div class="splide__arrows arrows-wrapper"></div>
          </div>
        </div>
        <?php if ($featured_query->have_posts()) : ?>
          <div class="kl-blogs__posts">
            <div class="splide__track">
              <div class="splide__list">
                <?php while ($featured_query->have_posts()) :
                  $featured_query->the_post();
                  $thumbnail = get_the_post_thumbnail_url(get_the_ID()); ?>
                  <div class="splide__slide">
                    <div class="kl-people__card kl-card group">
                      <a class="blog-link" href="<?php the_permalink() ?>">link</a>
                      <div class="card-img overflow-hidden rounded-xl">
                        <img src="<?= esc_url($thumbnail) ?>" alt="" class="transition-transform duration-300 group-hover:scale-105">
                      </div>
                      <div class="kl-card__content">
                        <h3 class="mt-4 mb-4 text-post-title font-medium text-text-400 transition-colors duration-300 group-hover:text-primary-400">
                          <?php the_title() ?>

                        </h3> 
                        <p class="  line-clamp-3 text-normal-text text-gray-700 "><?php echo wp_trim_words(get_the_excerpt(), 100); ?></p>
                        
                        <!-- <a href="<?php the_permalink() ?>" class="cta kl-btn" data-reverse="true">Read More</a> -->
                      </div>
                    </div>
                  </div>
                <?php endwhile ?>
              </div>
            </div>
          </div>
        <?php endif;
        wp_reset_postdata(); ?>
      </div>
    </div>
  </div>
</section>