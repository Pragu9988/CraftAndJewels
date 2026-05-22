<?php
/**
 * Space Aside Content Section - SEO Optimized & Accessible
 */
$spotlightData = [
    [
        "number" => "30+",
        "description" => "Years of Manufacturing Excellence"
    ],
    [
        "number" => "500+",
        "description" => "Unique Jewellery Designs Crafted"
    ],
    [
        "number" => "100+",
        "description" => "Trusted B2B Partners Worldwide"
    ],
    [
        "number" => "100%",
        "description" => "Commitment to Quality & Purity"
    ],
];

// Images for the grid
$images = [
    'people-culture.jpg', // Left
    'collaboration.png',  // Center (usually the main/largest one or just balanced)
    'our-team-new.png'    // Right
];
?>
<section class="oct-home-about" aria-labelledby="about-title">
    <div class="kl-container">

        <!-- Header Section -->
        <div class="row justify-center mb-10">
            <div class="col-xs-12 col-md-10 col-lg-10 text-center">
                <div class="strapline mb-2">
                    <span>Heritage Craft & Jewels — About Us</span>
                </div>

                <h2 class="section-title leading-tight mb-3" id="about-title">
                    Timeless Craftsmanship in Premium<br>Silver, Gold &amp; Diamond Jewellery
                </h2>

                <p class="normal-text">
                    Experience elegance designed with passion and perfected through generations of skilled
                    craftsmanship.We bring you exclusive jewellery collections that reflect luxury, purity, and modern
                    sophistication. Each piece in our collection is inspired by the rich traditions and culture of
                    Nepal, meticulously handcrafted by artisans. We are dedicated to fostering long term business to
                    business (B2B) partnership by providing exceptional designs, services, competitive rate, quality of
                    work, prompt services with long term services and out diverse range of stunning jewellery

                </p>
            </div>
        </div>

        <!-- Images Grid -->
        <div class="row mb-16">
            <div class="col-xs-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <!-- Image 1 -->
                    <div class="rounded-2xl overflow-hidden aspect-[3/4] md:aspect-[4/5] relative group">
                        <img src="<?= OCTOWAYS_SRC_IMG_URI . '/homepage/about-1.webp' ?>" alt="Octoways Team Culture"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>

                    <!-- Image 2 (Center - maybe slightly higher or different aspect if desired, but sticking to grid for now) -->
                    <!-- The design shows the middle image is slightly higher/larger in the reference or just same. Let's make them same for consistency or center one larger. 
                         In the reference, they look like 3 cards. Center one is slightly taller. -->
                    <div
                        class="rounded-2xl overflow-hidden aspect-[3/4] md:aspect-[4/5] md:-mt-8 relative group shadow-xl z-10">
                        <img src="<?= OCTOWAYS_SRC_IMG_URI . '/homepage/about-2.webp' ?>" alt="Team Collaboration"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>

                    <!-- Image 3 -->
                    <div class="rounded-2xl overflow-hidden aspect-[3/4] md:aspect-[4/5] relative group">
                        <img src="<?= OCTOWAYS_SRC_IMG_URI . '/homepage/about-3.webp' ?>" alt="Our Team" loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row">
            <div class="col-xs-12">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <?php foreach ($spotlightData as $index => $item): ?>
                        <div class="stat-item">
                            <div class="text-4xl md:text-5xl font-bold text-primary-600 mb-2">
                                <?= htmlspecialchars($item['number']); ?>
                            </div>
                            <div class="text-gray-600 font-medium">
                                <?= htmlspecialchars($item['description']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</section>