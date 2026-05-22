<?php

/**
 * Template Part: Homepage Hero Slider
 *
 * Requires:
 * - Advanced Custom Fields Pro
 * - Repeater field: hero_slides
 *
 * Repeater Fields:
 * - image (Image) — desktop
 * - mobile_image (Image) — mobile only; falls back to image when empty
 * - title (Text)
 * - sub (Textarea)
 * - link_primary (URL)
 * - btn_primary (Text)
 * - link_secondary (URL)
 * - btn_secondary (Text)
 */

if (! function_exists('have_rows')) {
    return;
}
?>

<?php if (have_rows('hero_slides')) : ?>

    <section class="ht-home ht-hero-slider-section">

        <div class="splide ht-splide-hero">

            <div class="splide__track">

                <ul class="splide__list">

                    <?php while (have_rows('hero_slides')) : the_row();

                        $image        = get_sub_field('image');
                        $mobile_image = get_sub_field('mobile_image');

                        $title = get_sub_field('title');
                        $sub = get_sub_field('sub');

                        $link_primary = get_sub_field('link_primary');
                        $btn_primary = get_sub_field('btn_primary');

                        $link_secondary = get_sub_field('link_secondary');
                        $btn_secondary = get_sub_field('btn_secondary');

                        // Skip slide if desktop image missing
                        if (empty($image)) {
                            continue;
                        }

                        if (empty($mobile_image)) {
                            $mobile_image = $image;
                        }

                        $slide_image_args = [
                            'alt'     => esc_attr($title),
                            'loading' => 'eager',
                        ];
                    ?>

                        <li class="splide__slide ht-hero-slider__slide">

                            <!-- Background Image -->
                            <div class="ht-hero-slider__bg">

                                <?php
                                echo wp_get_attachment_image(
                                    $image['ID'],
                                    'full',
                                    false,
                                    array_merge($slide_image_args, [
                                        'class' => 'ht-hero-slider__image ht-hero-slider__image--desktop',
                                    ])
                                );

                                echo wp_get_attachment_image(
                                    $mobile_image['ID'],
                                    'full',
                                    false,
                                    array_merge($slide_image_args, [
                                        'class' => 'ht-hero-slider__image ht-hero-slider__image--mobile',
                                    ])
                                );
                                ?>

                            </div>

                            <!-- Overlay -->
                            <div class="ht-hero-slider__overlay"></div>

                            <!-- Content -->
                            <div class="ht-hero-slider__container">

                                <?php if ($title) : ?>
                                    <h1 class="ht-title-reveal ht-hero-slider__title">
                                        <?php echo esc_html($title); ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if ($sub) : ?>
                                    <p class="ht-hero-slider__sub">
                                        <?php echo esc_html($sub); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (
                                    ($link_primary && $btn_primary) ||
                                    ($link_secondary && $btn_secondary)
                                ) : ?>

                                    <div class="ht-hero-slider__cta-wrap">

                                        <?php if ($link_primary && $btn_primary) : ?>
                                            <a href="<?php echo esc_url($link_primary); ?>"
                                                class="ht-btn ht-btn--primary">

                                                <?php echo esc_html($btn_primary); ?>

                                            </a>
                                        <?php endif; ?>

                                        <?php if ($link_secondary && $btn_secondary) : ?>
                                            <a href="<?php echo esc_url($link_secondary); ?>"
                                                class="ht-btn ht-btn--outline">

                                                <?php echo esc_html($btn_secondary); ?>

                                            </a>
                                        <?php endif; ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </li>

                    <?php endwhile; ?>

                </ul>

            </div>

        </div>

    </section>

<?php endif; ?>