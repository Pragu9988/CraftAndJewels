<?php
/**
 * Octoways theme functions and definitions
 *
 * @package octoways
 */

if (!class_exists('OCTOWAYS_PORTFOLIO_THEME')) {

	class OCTOWAYS_PORTFOLIO_THEME
	{

		/**
		 * Constructor
		 * Runs immediately when the class is instantiated.
		 */
		public function __construct()
		{
			$this->define_constants();
			$this->include_files();

			// Hook the assets method into WordPress
			// add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

		}

		/**
		 * Define constants
		 * Sets up paths and versioning for the theme.
		 */
		public function define_constants()
		{
			// Versioning for cache busting
			defined('_S_VERSION') || define('_S_VERSION', '1.0.0');
			defined('OCTOWAYS_DEFAULT_VERSION') || define('OCTOWAYS_DEFAULT_VERSION', '1.0.0');

			// Core Paths
			defined('OCTOWAYS_DIR_PATH') || define('OCTOWAYS_DIR_PATH', untrailingslashit(get_template_directory()));
			defined('OCTOWAYS_DIR_URI') || define('OCTOWAYS_DIR_URI', untrailingslashit(get_template_directory_uri()));

			// Asset Build Paths
			defined('OCTOWAYS_BUILD_PATH') || define('OCTOWAYS_BUILD_PATH', OCTOWAYS_DIR_PATH . '/assets/build');
			defined('OCTOWAYS_BUILD_URI') || define('OCTOWAYS_BUILD_URI', OCTOWAYS_DIR_URI . '/assets/build');

			// JS and CSS URIs
			defined('OCTOWAYS_BUILD_JS_URI') || define('OCTOWAYS_BUILD_JS_URI', OCTOWAYS_BUILD_URI . '/js');
			defined('OCTOWAYS_BUILD_CSS_URI') || define('OCTOWAYS_BUILD_CSS_URI', OCTOWAYS_BUILD_URI . '/css');
			defined('OCTOWAYS_BUILD_IMG_URI') || define('OCTOWAYS_BUILD_IMG_URI', OCTOWAYS_BUILD_URI . '/img');
			// Source and Library URIs
			defined('OCTOWAYS_SRC_IMG_URI') || define('OCTOWAYS_SRC_IMG_URI', OCTOWAYS_DIR_URI . '/assets/src/img');
			defined('OCTOWAYS_BUILD_LIB_URI') || define('OCTOWAYS_BUILD_LIB_URI', OCTOWAYS_DIR_URI . '/assets/src/library');
		}

		/**
		 * Enqueue scripts and styles.
		 */
		// public function enqueue_assets()
		// {
		// 	// Register and Enqueue Main Stylesheet
		// 	wp_enqueue_style('octoways-style', get_stylesheet_uri(), array(), _S_VERSION);

		// 	wp_enqueue_style('main-css', OCTOWAYS_BUILD_CSS_URI . '/main.css', [], file_exists(OCTOWAYS_BUILD_PATH . '/css/main.css') ? filemtime(OCTOWAYS_BUILD_PATH . '/css/main.css') : '1.0.0', 'all');

		// 	// Support for RTL (Right to Left) languages
		// 	wp_style_add_data('octoways-style', 'rtl', 'replace');

		// 	// Enqueue Main Navigation Script
		// 	wp_enqueue_script(
		// 		'octoways-navigation',
		// 		OCTOWAYS_DIR_URI . '/js/navigation.js',
		// 		array(),
		// 		_S_VERSION,
		// 		true
		// 	);

		// 	// Enqueue threaded comments script if applicable
		// 	if (is_singular() && comments_open() && get_option('thread_comments')) {
		// 		wp_enqueue_script('comment-reply');
		// 	}
		// }

		/**
		 * Include required theme files
		 */
		public function include_files()
		{
			// Load Autoloader
			if (file_exists(OCTOWAYS_DIR_PATH . '/inc/helpers/autoloader.php')) {
				require_once OCTOWAYS_DIR_PATH . '/inc/helpers/autoloader.php';
			}

			$inc_path = OCTOWAYS_DIR_PATH . '/inc/';
			// Manually requiring the Assets class
			if (file_exists($inc_path . 'classes/class-assets.php')) {
				require_once $inc_path . 'classes/class-assets.php';
				new \OCTOWAYS_THEME\Inc\Assets();
			}

			// Manually requiring the Taxonomy class
			if (file_exists($inc_path . 'classes/class-taxonomy.php')) {
				require_once $inc_path . 'classes/class-taxonomy.php';
				new \OCTOWAYS_THEME\Inc\Taxonomy();
			}

			// Manually requiring the Shop Filters class
			if (file_exists($inc_path . 'classes/class-shop-filters.php')) {
				require_once $inc_path . 'classes/class-shop-filters.php';
				new \OCTOWAYS_THEME\Inc\Shop_Filters();
			}

			// Manually requiring the Floating Widget class
			if (file_exists($inc_path . 'classes/class-floating-widget.php')) {
				require_once $inc_path . 'classes/class-floating-widget.php';
				new \OCTOWAYS_THEME\Inc\Floating_Widget();
			}

			if (file_exists($inc_path . 'acf-contact-page-fields.php')) {
				require_once $inc_path . 'acf-contact-page-fields.php';
			}

			require_once $inc_path . 'custom-header.php';
			require_once $inc_path . 'template-tags.php';
			require_once $inc_path . 'template-functions.php';
			require_once $inc_path . 'customizer.php';
			require_once $inc_path . 'theme-support.php';

			// Load Jetpack compatibility
			if (defined('JETPACK__VERSION')) {
				require_once $inc_path . 'jetpack.php';
			}

			// Load WooCommerce compatibility
			if (class_exists('WooCommerce')) {
				require_once $inc_path . 'woocommerce.php';
			}
		}

		/**
		 * Get theme instance from the main namespaced theme class
		 */
	}

	/**
	 * Initialize the Theme Bootstrapper
	 */
	new OCTOWAYS_PORTFOLIO_THEME();
}

function octoways_custom_admin_styles()
{
	echo '<style>
		table.fixed {
			table-layout: unset !important;
		}
	</style>';
}
add_action('admin_head', 'octoways_custom_admin_styles');


