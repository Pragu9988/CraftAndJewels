<?php
/**
 * Homepage - Luxury Within Reach
 * Dynamic price-category cards with specific icons matched to Term Names.
 */

// Fetch only Parent Terms from the 'luxury_within_reach' taxonomy
$terms = get_terms([
    'taxonomy' => 'luxury_within_reach',
    'hide_empty' => false,
    'parent' => 0,
]);

?>

<div class="ht-luxury kl-home">
    <div class="kl-container">
        <div class="row">
            <div class="col-xs-12">

                <header class="ht-luxury__header text-center">
                    <!-- <small data-aos="fade-up" class="strapline text-center">
                        Find your perfect price, find your perfect gift
                    </small> -->
                    <small data-aos="fade-up" class="strapline text-center">
                        Find Your Perfect Gift
                    </small>
                    <h2 data-aos="fade-up" data-aos-anchor-placement="center-bottom" data-aos-delay="150"
                        class="h3 section-title ht-luxury__title divider">
                        Luxury Within Reach
                    </h2>

                </header>

                <section data-aos="fade-up" data-aos-anchor-placement="center-bottom" data-aos-delay="300"
                    class="ht-luxury__result" aria-label="Luxury Price Grid">
                    <div class="ht-luxury__grid">
                        <?php
                        if (!empty($terms) && !is_wp_error($terms)):
                            foreach ($terms as $term):
                                $term_link = get_term_link($term);
                                if (is_wp_error($term_link))
                                    continue;

                                // Logic to keep specific icons based on the Term Name/Slug
                                $icon_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L17.5 12L12 22L6.5 12L12 2Z" /></svg>'; // Default Diamond
                        
                                if (strpos($term->name, '५,००० – १५,०००') !== false || strpos($term->slug, '5000-15000') !== false) {
                                    $icon_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="7" y="7" width="4" height="4" /><rect x="13" y="7" width="4" height="4" /><rect x="7" y="13" width="4" height="4" /><rect x="13" y="13" width="4" height="4" /></svg>';
                                } elseif (strpos($term->name, '१५,००० – ५०,०००') !== false || strpos($term->slug, '15000-50000') !== false) {
                                    $icon_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" /></svg>';
                                } elseif (strpos($term->name, 'माथि') !== false || strpos($term->slug, 'above') !== false) {
                                    $icon_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15 12L12 22L9 12L12 2Z" /></svg>';
                                }
                                ?>
                                <a href="<?php echo esc_url($term_link); ?>" class="ht-luxury__card">

                                    <span class="ht-luxury__card-bg" aria-hidden="true"></span>
                                    <span class="ht-luxury__card-overlay" aria-hidden="true"></span>

                                    <span class="ht-luxury__card-inner">
                                        <span class="ht-luxury__icon">
                                            <?php echo $icon_svg; ?>
                                        </span>

                                        <span class="ht-luxury__amount">
                                            <?php echo esc_html($term->name); ?>
                                        </span>

                                        <span class="ht-luxury__cta">
                                            EXPLORE
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <line x1="5" y1="12" x2="19" y2="12" />
                                                <polyline points="12 5 19 12 12 19" />
                                            </svg>
                                        </span>
                                    </span>
                                </a>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>