<?php
/**
 * Component - Diversity, equity, and inclusion goal of OCTOWAYS
 */

// Mission and Vision points
$mission_points = [
    __('Assist clients with smart travel choices', 'yaatra'),
    __('Offer peculiar holiday packages', 'yaatra'),
    __('Provide extensive hotel list at desired destinations', 'yaatra'),
    __('Deliver services within competitive prices', 'yaatra')
];

$vision_points = [
    __('Leading travel agency in Nepal', 'yaatra'),
    __('Brand name in the tourism industry', 'yaatra'),
    __('Create memorable experiences for your travel', 'yaatra'),
    __('Make trips convenient and memorable', 'yaatra')
];
?>

<section class="kl-diversity" aria-labelledby="diversity-heading">

    <div class="kl-container">
        <div class="kl-diversity__diversity-wrapper">

            <!-- Mission Section -->
            <div class="row kl-diversity__container mb-5 md:mb-8 lg:mb-12">
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div class="kl-diversity__image-wrapper relative">
                        <img src="<?php echo esc_url(OCTOWAYS_SRC_IMG_URI . '/about/mission.jpg'); ?>"
                            alt="<?php esc_attr_e('AI Engineer illustration representing our mission', 'octoways'); ?>"
                            loading="lazy" decoding="async" class="w-full h-auto">
                        <!-- <img class="overlap-img"
                            src="<?php echo esc_url(OCTOWAYS_SRC_IMG_URI . '/about/about-mision.jpg'); ?>"
                            alt="<?php esc_attr_e('Team collaboration for mission delivery', 'octoways'); ?>"
                            loading="lazy" decoding="async"> -->
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div class="kl-diversity__text-wrapper">
                        <h2 id="diversity-heading" class="title h3"><?php esc_html_e('Our Mission', 'octoways'); ?></h2>
                        <p class="description font-normal text-text-normal leading-normal">
                            <?php esc_html_e('Understand clients’ specific requirements and deliver the best travel package accordingly. '); ?>
                        </p>
                        <ul class="kl-mission-list mt-4 md:mt-5" role="list">
                            <?php foreach ($mission_points as $point): ?>
                                <li
                                    class="flex items-center gap-2 mb-3 font-normal text-text-normal leading-normal text-text-400">
                                    <i class="fa-solid fa-check-double text-primary-500" aria-hidden="true"></i>
                                    <span><?php echo esc_html($point); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Vision Section -->
            <div class="row kl-diversity__container vision">
                <div class="col-xs-12 col-md-6 col-lg-6 order-0 md:order-1">
                    <div class="kl-diversity__vision-image-wrapper">
                        <img src="<?php echo esc_url(OCTOWAYS_SRC_IMG_URI . '/about/vision.jpg'); ?>"
                            alt="<?php esc_attr_e('Digital transformation vision concept', 'octoways'); ?>"
                            loading="lazy" decoding="async" class="w-full h-auto">
                        <!-- <img class="overlap-img-vision"
                            src="<?php echo esc_url(OCTOWAYS_SRC_IMG_URI . '/about/vision.jpg'); ?>"
                            alt="<?php esc_attr_e('Digital transformation vision concept', 'octoways'); ?>"
                            loading="lazy" decoding="async" class="w-full h-auto"> -->
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-lg-6">
                    <div class="kl-diversity__text-wrapper">
                        <h2 class="title h3"><?php esc_html_e('Our Vision', 'octoways'); ?></h2>
                        <p class="description font-normal text-text-normal leading-normal">
                            <?php esc_html_e('Our services extend to hotel and Tour Reservation for enabling you to plan & finalize your economical travel plans.', 'octoways'); ?>
                        </p>
                        <ul class="kl-vision-list mt-4" role="list">
                            <?php foreach ($vision_points as $point): ?>
                                <li
                                    class="flex items-center gap-2 mb-3 font-normal text-text-normal leading-normal text-text-400">
                                    <i class="fa-solid fa-check-double text-primary-500" aria-hidden="true"></i>
                                    <span><?php echo esc_html($point); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>