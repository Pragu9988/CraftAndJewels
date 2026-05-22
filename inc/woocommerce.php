<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Heritage-website
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function woocommerce_woocommerce_setup()
{
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 150,
			'single_image_width' => 300,
			'product_grid' => array(
				'default_rows' => 3,
				'min_rows' => 1,
				'default_columns' => 4,
				'min_columns' => 1,
				'max_columns' => 6,
			),
		)
	);
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'woocommerce_woocommerce_setup');

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function woocommerce_woocommerce_scripts()
{
	wp_enqueue_style('woocommerce-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), _S_VERSION);

	$font_path = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style('woocommerce-woocommerce-style', $inline_font);
}
add_action('wp_enqueue_scripts', 'woocommerce_woocommerce_scripts');

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
// add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function woocommerce_woocommerce_active_body_class($classes)
{
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter('body_class', 'woocommerce_woocommerce_active_body_class');

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function woocommerce_woocommerce_related_products_args($args)
{
	$defaults = array(
		'posts_per_page' => 3,
		'columns' => 3,
	);

	$args = wp_parse_args($defaults, $args);

	return $args;
}
add_filter('woocommerce_output_related_products_args', 'woocommerce_woocommerce_related_products_args');

/**
 * Remove default WooCommerce wrapper.
 */
// remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
// remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

if (!function_exists('woocommerce_woocommerce_wrapper_before')) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function woocommerce_woocommerce_wrapper_before()
	{
		?>
		<main id="primary" class="site-main">
			<?php
	}
}
// add_action('woocommerce_before_main_content', 'woocommerce_woocommerce_wrapper_before');

if (!function_exists('woocommerce_woocommerce_wrapper_after')) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function woocommerce_woocommerce_wrapper_after()
	{
		?>
		</main><!-- #main -->
		<?php
	}
}
// add_action('woocommerce_after_main_content', 'woocommerce_woocommerce_wrapper_after');

/**
 * Sample implementation of the WooCommerce Mini Cart.
 *
 * You can add the WooCommerce Mini Cart to header.php like so ...
 *
	<?php
		if ( function_exists( 'woocommerce_woocommerce_header_cart' ) ) {
			woocommerce_woocommerce_header_cart();
		}
	?>
 */

if (!function_exists('woocommerce_woocommerce_cart_link_fragment')) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function woocommerce_woocommerce_cart_link_fragment($fragments)
	{
		ob_start();
		woocommerce_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_woocommerce_cart_link_fragment');

if (!function_exists('woocommerce_woocommerce_cart_link')) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function woocommerce_woocommerce_cart_link()
	{
		?>
		<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>"
			title="<?php esc_attr_e('View your shopping cart', 'woocommerce'); ?>">
			<?php
			$item_count_text = sprintf(
				/* translators: number of items in the mini cart. */
				_n('%d item', '%d items', WC()->cart->get_cart_contents_count(), 'woocommerce'),
				WC()->cart->get_cart_contents_count()
			);
			?>
			<span class="amount"><?php echo wp_kses_data(WC()->cart->get_cart_subtotal()); ?></span> <span
				class="count"><?php echo esc_html($item_count_text); ?></span>
		</a>
		<?php
	}
}

if (!function_exists('woocommerce_woocommerce_header_cart')) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function woocommerce_woocommerce_header_cart()
	{
		if (is_cart()) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr($class); ?>">
				<?php woocommerce_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
				$instance = array(
					'title' => '',
				);

				the_widget('WC_Widget_Cart', $instance);
				?>
			</li>
		</ul>
		<?php
	}
}

/**
 * Customize Single Product Page Layout
 */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// Add custom Brand/Taxonomy display
add_action('woocommerce_single_product_summary', 'ht_woocommerce_template_single_brand', 2);
function ht_woocommerce_template_single_brand()
{
	global $product;

	$taxonomies = get_object_taxonomies('product', 'names');

	foreach ($taxonomies as $taxonomy) {

		$terms = get_the_terms($product->get_id(), $taxonomy);

		if (!empty($terms) && !is_wp_error($terms)) {

			$term = reset($terms); // first term
			echo '<div class="ht-product-brand">' . esc_html(strtoupper($term->name)) . '</div>';

			break; // stop after first term found
		}
	}
}
// Re-add Title
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);

// Add custom Attributes (e.g. 24K Gold)
// add_action('woocommerce_single_product_summary', 'ht_woocommerce_template_single_attributes', 7);
// function ht_woocommerce_template_single_attributes()
// {
// 	global $product;
// 	// For now, let's look for a 'material' attribute or similar, or just show a fallback if empty
// 	$material = $product->get_attribute('material');
// 	if (!$material) {
// 		// Fallback for demo/dev purposes if no attribute is set
// 		$material = '22K Gold';
// 	}
// 	echo '<div class="ht-product-attributes"><span class="ht-attribute-badge">' . esc_html($material) . '</span></div>';
// }

// Re-add Price
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

// Re-add Excerpt (Short description)
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// Add to cart
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

// Add Shipping Info
add_action('woocommerce_single_product_summary', 'ht_woocommerce_template_single_shipping_info', 35);
function ht_woocommerce_template_single_shipping_info()
{
	?>
	<div class="ht-product-shipping-info">
		<span class="ht-shipping-item">
			<i class="fas fa-truck"></i> Ships in 3–5 business days
		</span>
		<span class="ht-shipping-divider">·</span>
		<span class="ht-shipping-item">
			<i class="fas fa-undo"></i> Free worldwide
		</span>
	</div>
	<hr class="ht-summary-divider">
	<?php
}

// Move Tabs to Summary as Accordions
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 40);



