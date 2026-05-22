<?php
/**
 * Homepage - Heritage Assurance
 */

$assurance_features = [
    [
        'bold' => '925',
        'label' => 'Fine Silver',
        'before' => false,
        'delay' => 150,
        'icon' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />',
    ],
   
    [
        'bold' => 'Authenticity',
        'label' => 'Certificate',
        'before' => false,
        'delay' => 600,
        'icon' => '<circle cx="12" cy="8" r="6" /><path d="M8.56 14.41L7 22l5-3 5 3-1.56-7.59" />',
    ],
];
?>

<section class="ht-assurance">
    <div class="ht-assurance__inner">
        <!-- Splide Slider Wrapper -->
        <div class="splide ht-assurance-slider">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php foreach ($assurance_features as $feature): ?>
                        <li class="splide__slide">
                            <span class="ht-assurance__tag">
                                <span class="ht-assurance__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <?php echo $feature['icon']; ?>
                                    </svg>
                                </span>

                                <?php if ($feature['before']): ?>
                                    Easy&nbsp;<?php echo esc_html($feature['bold']); ?>&nbsp;<?php echo esc_html($feature['label']); ?>
                                <?php else: ?>
                                    <?php echo esc_html($feature['bold']); ?>&nbsp;<?php echo esc_html($feature['label']); ?>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    
                    <!-- Duplicate slides for smoother loop if needed, or let Splide handle it -->
                    <?php foreach ($assurance_features as $feature): ?>
                        <li class="splide__slide">
                            <span class="ht-assurance__tag">
                                <span class="ht-assurance__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <?php echo $feature['icon']; ?>
                                    </svg>
                                </span>

                                <?php if ($feature['before']): ?>
                                    Easy&nbsp;<?php echo esc_html($feature['bold']); ?>&nbsp;<?php echo esc_html($feature['label']); ?>
                                <?php else: ?>
                                    <?php echo esc_html($feature['bold']); ?>&nbsp;<?php echo esc_html($feature['label']); ?>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>