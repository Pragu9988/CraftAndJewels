<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Heritage-website
 */

?>

<footer id="colophon" class="site-footer">
	<div class="ht-footer">
		<div class="ht-footer__wrapper">
			<?php
			get_template_part('template-parts/layout/footer', 'template');
			?>
		</div>
	</div>


</footer><!-- #colophon -->
</div><!-- #page -->


<?php wp_footer(); ?>
<?php
get_template_part('template-parts/layout/footer', 'findspace');
?>
</body>


</html>