<?php
/**
 * Custom Product Taxonomies
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

class Taxonomy
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
        add_action('init', [$this, 'register_taxonomies']);
        add_action('admin_enqueue_scripts', [$this, 'load_media']);

        foreach (array_keys($this->taxonomies) as $taxonomy) {
            add_action("{$taxonomy}_add_form_fields", [$this, 'add_image_field'], 10, 2);
            add_action("created_{$taxonomy}", [$this, 'save_image_field'], 10, 2);
            add_action("{$taxonomy}_edit_form_fields", [$this, 'edit_image_field'], 10, 2);
            add_action("edited_{$taxonomy}", [$this, 'updated_image_field'], 10, 2);
        }
    }

    public function load_media()
    {
        if (!isset($_GET['taxonomy']) || !array_key_exists($_GET['taxonomy'], $this->taxonomies)) {
            return;
        }

        wp_enqueue_media();

        // Attach to 'jquery' — always reliably enqueued in wp-admin
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($){

                $('body').on('click', '.taxonomy-image-upload', function(e){
                    e.preventDefault();
                    var button = $(this);

                    // Recreate frame on each click to avoid stale selection bugs
                    var meta_image_frame = wp.media({
                        title:   'Choose Taxonomy Image',
                        button:  { text: 'Use This Image' },
                        library: { type: 'image' },
                        multiple: false
                    });

                    meta_image_frame.on('select', function(){
                        var attachment = meta_image_frame.state().get('selection').first().toJSON();
                        var imageUrl = (attachment.sizes && attachment.sizes.full) ? attachment.sizes.full.url : attachment.url;
                        button.siblings('.taxonomy-image').val(attachment.id);
                        button.siblings('.taxonomy-image-preview').attr('src', imageUrl).show();
                        button.siblings('.taxonomy-image-remove').show();
                    });

                    meta_image_frame.open();
                });

                $('body').on('click', '.taxonomy-image-remove', function(e){
                    e.preventDefault();
                    var button = $(this);
                    button.siblings('.taxonomy-image').val('');
                    button.siblings('.taxonomy-image-preview').attr('src', '').hide();
                    button.hide();
                });

            });
        ");
    }

    public function add_image_field()
    {
        ?>
        <div class="form-field term-group">
            <label for="taxonomy-image-id">
                <?php _e('Image', 'octoways'); ?>
            </label>
            <input type="hidden" id="taxonomy-image-id" name="taxonomy_image" class="taxonomy-image" value="" />
            <div style="margin-top: 8px;">
                <img class="taxonomy-image-preview" src=""
                    style="max-width: 150px; height: auto; display: none; margin-bottom: 10px; display: block;" />
                <input type="button" class="button taxonomy-image-upload"
                    value="<?php _e('Upload / Add Image', 'octoways'); ?>" />
                <input type="button" class="button taxonomy-image-remove" value="<?php _e('Remove Image', 'octoways'); ?>"
                    style="display: none;" />
            </div>
        </div>
        <?php
    }

    public function save_image_field($term_id, $tt_id)
    {
        if (isset($_POST['taxonomy_image']) && '' !== $_POST['taxonomy_image']) {
            add_term_meta($term_id, 'taxonomy_image', absint($_POST['taxonomy_image']), true);
        }
    }

    public function edit_image_field($term, $taxonomy)
    {
        $image_id = get_term_meta($term->term_id, 'taxonomy_image', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        ?>
        <tr class="form-field term-group-wrap">
            <th scope="row">
                <label for="taxonomy-image-id">
                    <?php _e('Image', 'octoways'); ?>
                </label>
            </th>
            <td>
                <input type="hidden" id="taxonomy-image-id" name="taxonomy_image" class="taxonomy-image"
                    value="<?php echo esc_attr($image_id); ?>" />
                <img class="taxonomy-image-preview" src="<?php echo esc_url($image_url); ?>"
                    style="max-width: 150px; height: auto; display: <?php echo $image_id ? 'block' : 'none'; ?>; margin-bottom: 10px;" />
                <input type="button" class="button taxonomy-image-upload"
                    value="<?php _e('Upload / Add Image', 'octoways'); ?>" />
                <input type="button" class="button taxonomy-image-remove" value="<?php _e('Remove Image', 'octoways'); ?>"
                    style="display: <?php echo $image_id ? 'inline-block' : 'none'; ?>;" />
            </td>
        </tr>
        <?php
    }

    public function updated_image_field($term_id, $tt_id)
    {
        if (isset($_POST['taxonomy_image'])) {
            if ('' === $_POST['taxonomy_image']) {
                // Image was removed — delete the meta entirely
                delete_term_meta($term_id, 'taxonomy_image');
            } else {
                update_term_meta($term_id, 'taxonomy_image', absint($_POST['taxonomy_image']));
            }
        }
    }

    public function register_taxonomies()
    {
        foreach ($this->taxonomies as $slug => $name) {

            $labels = [
                'name' => __($name, 'octoways'),
                'singular_name' => __($name, 'octoways'),
                'search_items' => __('Search ' . $name, 'octoways'),
                'all_items' => __('All ' . $name, 'octoways'),
                'parent_item' => __('Parent ' . $name, 'octoways'),
                'parent_item_colon' => __('Parent ' . $name . ':', 'octoways'),
                'edit_item' => __('Edit ' . $name, 'octoways'),
                'update_item' => __('Update ' . $name, 'octoways'),
                'add_new_item' => __('Add New ' . $name, 'octoways'),
                'new_item_name' => __('New ' . $name . ' Name', 'octoways'),
                'menu_name' => __($name, 'octoways'),
            ];

            $args = [
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => ['slug' => $slug],
            ];

            register_taxonomy($slug, ['product'], $args);
        }
    }
}