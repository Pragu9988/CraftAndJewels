<?php
/**
 * Homepage Template
 * 
 * 
 */
get_header();

?>
<main id="primary" class="site-main">
           <?php
           get_template_part('template-parts/pages/homepage/homepage', 'slider');
           // feture product
           // heritage assurance
          	get_template_part('template-parts/pages/homepage/heritage', 'assurance');
			get_template_part('template-parts/pages/homepage/shop', 'catslider');
           //get_template_part('template-parts/pages/homepage/shop', 'byheritage');
           // shp by luxuary
           //get_template_part('template-parts/pages/homepage/shopby', 'luxuary');

           get_template_part('template-parts/pages/homepage/shop', 'bond');
           //top selling
           // get_template_part('template-parts/pages/homepage/top', 'selling');
           


           get_template_part('template-parts/pages/homepage/shopby', 'recipe');

           ?>
</main>
<?php
get_footer();