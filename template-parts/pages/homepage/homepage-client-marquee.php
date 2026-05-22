<?php
$client_logos = [
    'jeep-logo.png',
    'emountaintv.png',
    'the-annapurna-express.png',
    'nepali-lami.png',
    'urja-logo.png',
    'gafencu-logo.png',
    'summy-access.png',
    'eproperty-logo.png',
];

// Repeat the logos once to create the scrolling effect
$repeated_logos = array_merge($client_logos, $client_logos);
?>

<section class="oct-home oct-clients-marquee bg-gradient-to-br from-secondary-100 to-primary-100">
    <div class="kl-container">
        <div class="">
            <div class="oct-clients__content">
                <div class="oct-clients__content-wrapper max-w-4xl m-auto mb-5 md:mb-6 lg:mb-8">
                    <p class="strapline text-center">Our Clients</p>
                    <div class="oct-clients-marquee__header text-center mb-3 md:mb-4 lg:mb-5">
                        <h3 class="text-section-title text-text-400 leading-snug">Empowering Our Clients Through Smart, Scalable Digital Solutions</h3>
                    </div>
                    <div class="cta-wrap flex justify-center gap-4 align items-center  mb-3 md:mb-4 lg:mb-5">
                        <a href="<?php echo esc_url(home_url('/contact-us')); ?>" class="oct-btn" aria-label="Book an Appointment">
                            Contact Us
                        </a>
                    </div>
                    <p class="text-normal-text text-center font-normal leading-normal ">
                        Octoways empowers businesses with innovative, scalable digital solutions, trusted by 100+ global clients. We tailor our approach to each client's unique needs, driving measurable success for startups and enterprises alike.
                    </p>
                </div>
            </div>
            <div class="splide logo-slider">
                <div class="splide__track">
                    <div class=" splide__list">
                        <?php foreach ($client_logos as $logo): ?>
                            <div class="logo-block splide__slide">
                                <a class="d-block">
                                    <picture>
                                        <img class="img-fluid d-table mx-auto"
                                            src="<?php echo OCTOWAYS_SRC_IMG_URI . '/logos/' . $logo; ?>"
                                            alt="logo" loading="lazy">
                                    </picture>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>



