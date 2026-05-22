<?php
/**
 * about us page template Template
 * 
 * 
 */
get_header();

?>
<main id="primary" class="site-main">
           <?php
           //about-introduction.php get this form pages/about/about-introduction.php get
           //get_template_part('template-parts/pages/homepage/about', 'us');
           //get_template_part('template-parts/pages/about/about', 'introduction');
           //get_template_part('template-parts/pages/homepage/message', 'ceo');
           get_template_part('template-parts/pages/about/vision', 'trust');

           ?>
</main>
<?php
get_footer();