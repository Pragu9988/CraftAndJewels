<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Heritage-website
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ht-search-item'); ?>>
	<div class="ht-image-wrapper">
		<a href="<?php the_permalink(); ?>">
			<?php if (has_post_thumbnail()): ?>
				<?php the_post_thumbnail('full'); ?>
			<?php else: ?>
				<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/placeholder.png'); ?>"
					alt="<?php the_title_attribute(); ?>">
			<?php endif; ?>
		</a>
	</div>
	<div class="ht-search-content">
		<h2 class="search-title"><a
				href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
		</h2>
		<?php if (class_exists('WooCommerce') && 'product' === get_post_type()):
			global $product;
			$product = wc_get_product(get_the_ID());
			if ($product): ?>
				<div class="price"><?php echo $product->get_price_html(); ?></div>
			<?php endif;
		endif; ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->