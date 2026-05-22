<?php
/**
 * Template Name: Custom Wrapper
 */

get_header(); ?>

<div class="heritage-wrapper">
    <div class="kl-container kl-space-wrapper">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</div>

<?php get_footer(); ?>