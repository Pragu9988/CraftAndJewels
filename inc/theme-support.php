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

function woocommerce_setup()
{
           /*
            * Make theme available for translation.
            * Translations can be filed in the /languages/ directory.
            * If you're building a theme based on Heritage-website, use a find and replace
            * to change 'woocommerce' to the name of your theme in all the template files.
            */
           load_theme_textdomain('woocommerce', get_template_directory() . '/languages');

           // Add default posts and comments RSS feed links to head.
           add_theme_support('automatic-feed-links');

           /*
            * Let WordPress manage the document title.
            * By adding theme support, we declare that this theme does not use a
            * hard-coded <title> tag in the document head, and expect WordPress to
            * provide it for us.
            */
           add_theme_support('title-tag');

           /*
            * Enable support for Post Thumbnails on posts and pages.
            *
            * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
            */
           add_theme_support('post-thumbnails');

           // This theme uses wp_nav_menu() in one location.
           // register_nav_menus(
           // 	array(
           // 		'menu-1' => esc_html__('Primary', 'woocommerce'),
           // 	)
           // );

           /*
            * Switch default core markup for search form, comment form, and comments
            * to output valid HTML5.
            */
           add_theme_support(
                      'html5',
                      array(
                                 'search-form',
                                 'comment-form',
                                 'comment-list',
                                 'gallery',
                                 'caption',
                                 'style',
                                 'script',
                      )
           );

           // Set up the WordPress core custom background feature.
           add_theme_support(
                      'custom-background',
                      apply_filters(
                                 'woocommerce_custom_background_args',
                                 array(
                                            'default-color' => 'ffffff',
                                            'default-image' => '',
                                 )
                      )
           );

           // Add theme support for selective refresh for widgets.
           add_theme_support('customize-selective-refresh-widgets');

           /**
            * Add support for core custom logo.
            *
            * @link https://codex.wordpress.org/Theme_Logo
            */
           add_theme_support(
                      'custom-logo',
                      array(
                                 'height' => 250,
                                 'width' => 250,
                                 'flex-width' => true,
                                 'flex-height' => true,
                      )
           );
}
add_action('after_setup_theme', 'woocommerce_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function woocommerce_content_width()
{
           $GLOBALS['content_width'] = apply_filters('woocommerce_content_width', 640);
}
add_action('after_setup_theme', 'woocommerce_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function woocommerce_widgets_init()
{
           register_sidebar(
                      array(
                                 'name' => esc_html__('Sidebar', 'woocommerce'),
                                 'id' => 'sidebar-1',
                                 'description' => esc_html__('Add widgets here.', 'woocommerce'),
                                 'before_widget' => '<section id="%1$s" class="widget %2$s">',
                                 'after_widget' => '</section>',
                                 'before_title' => '<h2 class="widget-title">',
                                 'after_title' => '</h2>',
                      )
           );
}
add_action('widgets_init', 'woocommerce_widgets_init');


// Change the gallery thumbnail size to 'full'
add_filter('woocommerce_get_image_size_gallery_thumbnail', function ($size) {
           return array(
                      'width' => 800, // Set to your preferred width
                      'height' => 800,  // Set to 0 for auto-height (full image)
                      'crop' => 0,   // 0 means no cropping
           );
});

// Force the single product main image to be the full upload
add_filter('single_product_archive_thumbnail_size', function () {
           return 'full';
});