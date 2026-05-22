<?php
/**
 * Homepage Template
 */
get_header();
?>

<main id="primary" class="site-main">
           <div class="ht-wishlist">
                      <div class="kl-container">

                                 <?php
                                 if (have_posts()):
                                            while (have_posts()):
                                                       the_post();
                                                       ?>


                                                       <div class="page-content">
                                                                  <?php the_content(); ?>
                                                       </div>

                                                       <?php
                                            endwhile;
                                 endif;
                                 ?>

                      </div>
           </div>
</main>

<?php
get_footer();