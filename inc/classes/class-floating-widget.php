<?php
/**
 * Floating Product Finder Widget
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

class Floating_Widget
{
    private $taxonomies = [
        'jewellery' => 'Jewellery',
        'by_religious' => 'By Religious',
        'nepali_heritage' => 'Nepali Heritage',
        'shop_by_bond' => 'Shop by Bond',
        'luxury_within_reach' => 'Luxury within Reach',
        'for_whom' => 'For Whom',
    ];

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'localize_ajax_script']);

        // AJAX endpoints
        $actions = ['get_taxonomies', 'get_terms', 'get_products'];
        foreach ($actions as $action) {
            add_action("wp_ajax_nopriv_heritage_floating_{$action}", [$this, "ajax_{$action}"]);
            add_action("wp_ajax_heritage_floating_{$action}", [$this, "ajax_{$action}"]);
        }
    }

    public function localize_ajax_script()
    {
        wp_localize_script('main-js', 'heritageFloating', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('heritage_floating_nonce'),
        ]);
    }

    public function ajax_get_taxonomies()
    {
        check_ajax_referer('heritage_floating_nonce', 'nonce');

        $tax_objects = [];
        foreach ($this->taxonomies as $slug => $name) {
            $tax_objects[] = [
                'slug' => $slug,
                'name' => $name,
            ];
        }

        wp_send_json_success($tax_objects);
    }

    public function ajax_get_terms()
    {
        check_ajax_referer('heritage_floating_nonce', 'nonce');

        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';

        if (!array_key_exists($taxonomy, $this->taxonomies)) {
            wp_send_json_error('Invalid taxonomy');
        }

        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
            'parent' => 0, // Request says "Display only parent taxonomy terms"
        ]);

        if (is_wp_error($terms)) {
            wp_send_json_error($terms->get_error_message());
        }

        $term_objects = [];
        foreach ($terms as $term) {
            $term_objects[] = [
                'term_id' => $term->term_id,
                'slug' => $term->slug,
                'name' => $term->name,
                'count' => $term->count
            ];
        }

        wp_send_json_success($term_objects);
    }

    public function ajax_get_products()
    {
        check_ajax_referer('heritage_floating_nonce', 'nonce');

        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
        $term_slug = isset($_POST['term_slug']) ? sanitize_text_field($_POST['term_slug']) : '';

        if (empty($taxonomy) || empty($term_slug)) {
            wp_send_json_error('Missing parameters');
        }

        $args = [
            'post_type' => 'product',
            'posts_per_page' => 12, // Arbitrary limit for recommended products
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $term_slug,
                ]
            ]
        ];

        $query = new \WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $product;
                
                $price_html = $product->get_price_html();
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'thumbnail');
                $image_url = $image ? $image[0] : wc_placeholder_img_src('thumbnail');
                
                // Get one primary category or the term we just filtered by to display as meta
                $meta_text = '';
                $terms = get_the_terms($product->get_id(), $taxonomy);
                if ($terms && !is_wp_error($terms)) {
                    $meta_text = esc_html($terms[0]->name);
                }

                ?>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="ht-floating-product">
                    <div class="ht-floating-product__img">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                    </div>
                    <div class="ht-floating-product__info">
                        <h4 class="ht-floating-product__title"><?php echo get_the_title(); ?></h4>
                        <?php if ($meta_text): ?>
                            <span class="ht-floating-product__meta"><?php echo $meta_text; ?></span>
                        <?php endif; ?>
                        <div class="ht-floating-product__price"><?php echo $price_html; ?></div>
                    </div>
                </a>
                <?php
            }
        } else {
            echo '<p class="ht-floating-no-results">No products found.</p>';
        }

        $html = ob_get_clean();
        wp_reset_postdata();

        wp_send_json_success([
            'html' => $html
        ]);
    }
}
