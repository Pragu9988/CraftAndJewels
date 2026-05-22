<?php
/**
 * Shop Taxonomy Filters
 *
 * Renders a horizontal filter bar with dropdown panels for custom product taxonomies.
 * Handles AJAX filtering with tax_query.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

class Shop_Filters
{

    /**
     * Custom product taxonomies for filtering.
     */
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
        add_action('wp_ajax_heritage_filter_products', [$this, 'ajax_filter_products']);
        add_action('wp_ajax_nopriv_heritage_filter_products', [$this, 'ajax_filter_products']);
        add_action('wp_enqueue_scripts', [$this, 'localize_ajax']);
    }

    /**
     * Localize AJAX URL for frontend.
     */
    public function localize_ajax()
    {
        if (is_shop() || is_product_taxonomy()) {
            wp_localize_script('main-js', 'heritageFilters', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('heritage_filter_nonce'),
            ]);
        }
    }

    /**
     * Get visible taxonomies for the filter bar.
     * - Shop page: show all taxonomy filters.
     * - Taxonomy archive pages: show only the current taxonomy filter.
     */
    public function get_visible_taxonomies()
    {
        $taxonomies = $this->taxonomies;

        // On taxonomy archive pages, show ONLY the current taxonomy filter
        if (is_tax() || is_product_taxonomy()) {
            $queried = get_queried_object();
            if ($queried && isset($queried->taxonomy) && array_key_exists($queried->taxonomy, $taxonomies)) {
                return [$queried->taxonomy => $taxonomies[$queried->taxonomy]];
            }
        }

        // Shop page: return all
        return $taxonomies;
    }

    /**
     * Render the horizontal filter bar.
     */
    public function render_filter_bar()
    {
        $taxonomies = $this->get_visible_taxonomies();

        if (empty($taxonomies)) {
            return;
        }

        // Determine currently active filters from URL
        $active_filters = [];
        foreach ($taxonomies as $slug => $label) {
            if (!empty($_GET['filter_' . $slug])) {
                $active_filters[$slug] = array_map('sanitize_text_field', explode(',', $_GET['filter_' . $slug]));
            }
        }

        // Get current taxonomy term for archive pages
        $current_tax_slug = '';
        $current_term_slug = '';
        if (is_tax()) {
            $queried = get_queried_object();
            $current_tax_slug = $queried->taxonomy;
            $current_term_slug = $queried->slug;
        }
        ?>
        <div class="ht-filter-bar" data-current-taxonomy="<?php echo esc_attr($current_tax_slug); ?>"
            data-current-term="<?php echo esc_attr($current_term_slug); ?>">
            <div class="ht-filter-bar__items">
                <?php foreach ($taxonomies as $slug => $label):
                    $terms = get_terms([
                        'taxonomy' => $slug,
                        'hide_empty' => true,
                    ]);

                    if (is_wp_error($terms) || empty($terms)) {
                        continue;
                    }

                    $is_active = isset($active_filters[$slug]);
                    ?>
                    <div class="ht-filter-item <?php echo $is_active ? 'is-active' : ''; ?>"
                        data-taxonomy="<?php echo esc_attr($slug); ?>">
                        <button class="ht-filter-item__trigger" type="button" aria-expanded="false">
                            <span class="ht-filter-item__label"><?php echo esc_html($label); ?></span>
                            <svg class="ht-filter-item__chevron" width="10" height="6" viewBox="0 0 10 6" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div class="ht-filter-dropdown">
                            <ul class="ht-filter-dropdown__list">
                                <?php foreach ($terms as $term):
                                    $checked = isset($active_filters[$slug]) && in_array($term->slug, $active_filters[$slug]);
                                    ?>
                                    <li class="ht-filter-dropdown__item">
                                        <label class="ht-filter-checkbox">
                                            <input type="checkbox" name="filter_<?php echo esc_attr($slug); ?>[]"
                                                value="<?php echo esc_attr($term->slug); ?>" <?php checked($checked); ?> /> <span
                                                class="ht-filter-checkbox__mark"></span>
                                            <span class="ht-filter-checkbox__label"><?php echo esc_html($term->name); ?></span>
                                            <span class="ht-filter-checkbox__count">(<?php echo esc_html($term->count); ?>)</span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX handler: filter products by selected taxonomy terms.
     */
    public function ajax_filter_products()
    {
        check_ajax_referer('heritage_filter_nonce', 'nonce');

        $filters = isset($_POST['filters']) ? $_POST['filters'] : [];
        $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

        // Base taxonomy for archive pages
        $current_taxonomy = isset($_POST['current_taxonomy']) ? sanitize_text_field($_POST['current_taxonomy']) : '';
        $current_term = isset($_POST['current_term']) ? sanitize_text_field($_POST['current_term']) : '';

        $tax_query = ['relation' => 'AND'];

        // If on a taxonomy archive page, include the current taxonomy term
        if (!empty($current_taxonomy) && !empty($current_term)) {
            $tax_query[] = [
                'taxonomy' => $current_taxonomy,
                'field' => 'slug',
                'terms' => $current_term,
            ];
        }

        // Add user-selected filters
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $taxonomy => $terms) {
                $taxonomy = sanitize_key($taxonomy);
                if (!array_key_exists($taxonomy, $this->taxonomies)) {
                    continue;
                }
                $terms = array_map('sanitize_text_field', (array) $terms);
                if (!empty($terms)) {
                    $tax_query[] = [
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $terms,
                    ];
                }
            }
        }

        $args = [
            'post_type' => 'product',
            'posts_per_page' => apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page()),
            'paged' => $paged,
            'post_status' => 'publish',
        ];

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        $query = new \WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            woocommerce_product_loop_start();

            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }

            woocommerce_product_loop_end();
        } else {
            echo '<p class="woocommerce-info">' . esc_html__('No products were found matching your selection.', 'woocommerce') . '</p>';
        }

        $html = ob_get_clean();

        // Pagination
        ob_start();
        $total_pages = $query->max_num_pages;
        if ($total_pages > 1) {
            echo '<nav class="woocommerce-pagination">';
            echo paginate_links([
                'total' => $total_pages,
                'current' => $paged,
                'format' => '?paged=%#%',
                'type' => 'list',
            ]);
            echo '</nav>';
        }
        $pagination = ob_get_clean();

        wp_reset_postdata();

        wp_send_json_success([
            'html' => $html,
            'pagination' => $pagination,
            'found' => $query->found_posts,
            'max_pages' => $total_pages,
        ]);
    }
}
