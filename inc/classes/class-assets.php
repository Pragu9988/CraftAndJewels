<?php
/**
 * Theme Assets Class
 * * @package OCTOWAYS-octoways-theme
 */

namespace OCTOWAYS_THEME\Inc;

class Assets
{

           public function __construct()
           {
                      $this->setup_hooks();
           }

           protected function setup_hooks()
           {
                      /**
                       * Use the array syntax for namespaced class callbacks
                       */
                      add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
                      add_action('wp_head', [$this, 'add_google_fonts_preconnect'], 5);
           }

           public function add_google_fonts_preconnect()
           {
                      echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . PHP_EOL;
                      echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . PHP_EOL;
           }

           public function enqueue_assets()
           {
                      // Enqueue Google Fonts first so it loads before main css
                      wp_enqueue_style('google-font-playfair', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap', [], null);
                      wp_enqueue_style('google-font-cormorant', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&display=swap', [], null);
                      wp_enqueue_style('google-font-inter', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap', [], null);

                      // Register and Enqueue Main Stylesheet
                      wp_enqueue_style('octoways-style', get_stylesheet_uri(), array(), file_exists(get_stylesheet_directory() . '/style.css') ? filemtime(get_stylesheet_directory() . '/style.css') : _S_VERSION);

                      wp_enqueue_style('main-css', OCTOWAYS_BUILD_CSS_URI . '/main.css', [], file_exists(OCTOWAYS_BUILD_PATH . '/css/main.css') ? filemtime(OCTOWAYS_BUILD_PATH . '/css/main.css') : '1.0.0', 'all');

                      // Support for RTL (Right to Left) languages
                      wp_style_add_data('octoways-style', 'rtl', 'replace');

                      // Enqueue Main Navigation Script
                      wp_enqueue_script(
                                 'octoways-navigation',
                                 OCTOWAYS_DIR_URI . '/js/navigation.js',
                                 array(),
                                 file_exists(OCTOWAYS_DIR_PATH . '/js/navigation.js') ? filemtime(OCTOWAYS_DIR_PATH . '/js/navigation.js') : _S_VERSION,
                                 true
                      );

                      // Enqueue threaded comments script if applicable
                      if (is_singular() && comments_open() && get_option('thread_comments')) {
                                 wp_enqueue_script('comment-reply');
                      }

                      // AOS Configuration - CSS and JS File Enqueues
                      wp_enqueue_style('aos-css', 'https://unpkg.com/aos@2.3.1/dist/aos.css', [], '2.3.1');
                      wp_enqueue_script('aos-js', 'https://unpkg.com/aos@2.3.1/dist/aos.js', [], '2.3.1', true);

                      // auto scroll slider import
                      wp_enqueue_script('auto-scroll-slider', OCTOWAYS_BUILD_JS_URI . '/autoScroll.js', [], file_exists(OCTOWAYS_BUILD_PATH . '/js/autoScroll.js') ? filemtime(OCTOWAYS_BUILD_PATH . '/js/autoScroll.js') : '1.0.0', true);
                      // auto scroll slider import
                      wp_enqueue_script('floating', OCTOWAYS_BUILD_JS_URI . '/floating.js', [], file_exists(OCTOWAYS_BUILD_PATH . '/js/floating.js') ? filemtime(OCTOWAYS_BUILD_PATH . '/js/floating.js') : '1.0.0', true);

                      // main.js - Depends on AOS and auto-scroll-slider to ensure they run first
                      wp_enqueue_script('main-js', OCTOWAYS_BUILD_JS_URI . '/main.js', ['aos-js', 'auto-scroll-slider', 'floating'], file_exists(OCTOWAYS_BUILD_PATH . '/js/main.js') ? filemtime(OCTOWAYS_BUILD_PATH . '/js/main.js') : '1.0.0', true);
           }

}