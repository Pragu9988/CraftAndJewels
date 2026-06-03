<?php
/**
 * How Pricing Works — client-facing pricing transparency page.
 *
 * @package OCTOWAYS_THEME
 */

defined('ABSPATH') || exit;

$rates           = array();
$purity_rows     = array();
$example         = null;
$has_live_rates  = false;

if (class_exists('\OCTOWAYS_THEME\Inc\Metal_Rate_Store') && class_exists('\OCTOWAYS_THEME\Inc\Metal_Price_Calculator')) {
	$store      = new \OCTOWAYS_THEME\Inc\Metal_Rate_Store();
	$calculator = new \OCTOWAYS_THEME\Inc\Metal_Price_Calculator($store);
	$rates      = $store->get_rates();

	$gold_24k = (float) $rates['gold_rate_24k'];
	$silver   = (float) $rates['silver_rate'];
	$diamond  = (float) $rates['diamond_rate'];

	$has_live_rates = ($gold_24k > 0 || $silver > 0 || $diamond > 0);

	foreach (\OCTOWAYS_THEME\Inc\Metal_Price_Calculator::get_supported_purities() as $label => $karat) {
		$purity_rows[] = array(
			'label'     => $label,
			'karat'     => $karat,
			'effective' => $gold_24k > 0 ? $calculator->get_effective_gold_rate($gold_24k, $label) : 0,
		);
	}

	// Worked example from PRD (pendant set) — uses live rates when set, else illustrative defaults.
	$example_rates = array(
		'gold_rate_24k'         => $gold_24k > 0 ? $gold_24k : 15002.56,
		'silver_rate'           => $silver > 0 ? $silver : 428.66,
		'diamond_rate'          => $diamond > 0 ? $diamond : 5000.00,
		'default_making_charge' => 0,
	);
	$example_formula = array(
		'gold_weight'         => 0.25,
		'gold_purity'         => '14K',
		'silver_weight'       => 5.00,
		'diamond_weight'      => 0.50,
		'total_weight'        => 5.25,
		'making_charge_type'  => \OCTOWAYS_THEME\Inc\Metal_Price_Calculator::CHARGE_PER_GRAM,
		'making_charge_value' => 600,
	);
	$example = $calculator->calculate_from_formula($example_formula, $example_rates);
}

$steps = array(
	array(
		'num'   => '01',
		'title' => __('Gold', 'octoways'),
		'icon'  => 'gold',
		'formula' => __('Gold Cost = Gold Weight (g) × (24K Rate × Purity ÷ 24)', 'octoways'),
		'detail'  => __('The gold rate we publish is always 24 karat (24K) per gram. For 14K, 18K, or 22K pieces, we apply the standard purity factor so you only pay for the gold content in your jewellery.', 'octoways'),
	),
	array(
		'num'   => '02',
		'title' => __('Silver', 'octoways'),
		'icon'  => 'silver',
		'formula' => __('Silver Cost = Silver Weight (g) × Silver Rate', 'octoways'),
		'detail'  => __('If a design includes silver, its weight in grams is multiplied by the current silver rate per gram.', 'octoways'),
	),
	array(
		'num'   => '03',
		'title' => __('Diamond', 'octoways'),
		'icon'  => 'diamond',
		'formula' => __('Diamond Cost = Diamond Weight (ct) × Diamond Rate', 'octoways'),
		'detail'  => __('Diamond weight is measured in carats. The diamond rate is set by Heritage Craft & Jewels and multiplied by the carat weight on the product.', 'octoways'),
	),
	array(
		'num'   => '04',
		'title' => __('Making charge', 'octoways'),
		'icon'  => 'making',
		'formula' => __('Per gram: Total Weight × Making Rate  ·  Per piece: Fixed amount', 'octoways'),
		'detail'  => __('Craftsmanship is charged either per gram of total product weight or as a fixed amount per piece, depending on how the item is configured.', 'octoways'),
	),
);
?>

<section class="ht-pricing-guide" aria-labelledby="ht-pricing-guide-title">
	<div class="kl-container">

		<div class="row justify-center mb-10">
			<div class="col-xs-12 col-md-10 col-lg-9 text-center">
				<div class="strapline mb-2">
					<span><?php esc_html_e('Heritage Craft & Jewels — Transparency', 'octoways'); ?></span>
				</div>
				<h1 class="section-title leading-tight mb-3" id="ht-pricing-guide-title">
					<?php esc_html_e('How Your Price Is Calculated', 'octoways'); ?>
				</h1>
				<p class="normal-text ht-pricing-guide__intro">
					<?php esc_html_e('Our jewellery prices reflect live material rates and the exact composition of each piece — gold purity, silver, diamond, and making charge — combined transparently below.', 'octoways'); ?>
				</p>
			</div>
		</div>

		<div class="ht-pricing-guide__summary">
			<div class="ht-pricing-guide__summary-inner">
				<span class="ht-pricing-guide__summary-label"><?php esc_html_e('Final price', 'octoways'); ?></span>
				<p class="ht-pricing-guide__summary-formula">
					<?php esc_html_e('Gold Cost + Silver Cost + Diamond Cost + Making Charge', 'octoways'); ?>
				</p>
				<p class="ht-pricing-guide__summary-note normal-text">
					<?php esc_html_e('Any material not used in a product contributes zero to the total. Prices may refresh when market rates change before checkout.', 'octoways'); ?>
				</p>
			</div>
		</div>

		<?php if ($has_live_rates) : ?>
			<div class="ht-pricing-guide__rates">
				<h2 class="ht-pricing-guide__block-title section-title"><?php esc_html_e('Today’s reference rates', 'octoways'); ?></h2>
				<ul class="ht-pricing-guide__rates-grid">
					<?php if ((float) $rates['gold_rate_24k'] > 0) : ?>
						<li class="ht-pricing-guide__rate-card ht-pricing-guide__rate-card--gold">
							<span class="ht-pricing-guide__rate-label"><?php esc_html_e('Gold (24K) / gram', 'octoways'); ?></span>
							<strong><?php echo esc_html(number_format((float) $rates['gold_rate_24k'], 2)); ?></strong>
							<span class="ht-pricing-guide__rate-unit"><?php esc_html_e('NPR', 'octoways'); ?></span>
						</li>
					<?php endif; ?>
					<?php if ((float) $rates['silver_rate'] > 0) : ?>
						<li class="ht-pricing-guide__rate-card ht-pricing-guide__rate-card--silver">
							<span class="ht-pricing-guide__rate-label"><?php esc_html_e('Silver / gram', 'octoways'); ?></span>
							<strong><?php echo esc_html(number_format((float) $rates['silver_rate'], 2)); ?></strong>
							<span class="ht-pricing-guide__rate-unit"><?php esc_html_e('NPR', 'octoways'); ?></span>
						</li>
					<?php endif; ?>
					<?php if ((float) $rates['diamond_rate'] > 0) : ?>
						<li class="ht-pricing-guide__rate-card ht-pricing-guide__rate-card--diamond">
							<span class="ht-pricing-guide__rate-label"><?php esc_html_e('Diamond / carat', 'octoways'); ?></span>
							<strong><?php echo esc_html(number_format((float) $rates['diamond_rate'], 2)); ?></strong>
							<span class="ht-pricing-guide__rate-unit"><?php esc_html_e('NPR', 'octoways'); ?></span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="ht-pricing-guide__steps">
			<h2 class="ht-pricing-guide__block-title section-title text-center"><?php esc_html_e('Step by step', 'octoways'); ?></h2>
			<div class="ht-pricing-guide__steps-grid">
				<?php foreach ($steps as $step) : ?>
					<article class="ht-pricing-guide__step ht-pricing-guide__step--<?php echo esc_attr($step['icon']); ?>">
						<div class="ht-pricing-guide__step-head">
							<span class="ht-pricing-guide__step-num" aria-hidden="true"><?php echo esc_html($step['num']); ?></span>
							<h3 class="ht-pricing-guide__step-title"><?php echo esc_html($step['title']); ?></h3>
						</div>
						<p class="ht-pricing-guide__step-formula"><?php echo esc_html($step['formula']); ?></p>
						<p class="normal-text ht-pricing-guide__step-detail"><?php echo esc_html($step['detail']); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if (!empty($purity_rows)) : ?>
			<div class="ht-pricing-guide__purity">
				<h2 class="ht-pricing-guide__block-title section-title"><?php esc_html_e('Gold purity reference', 'octoways'); ?></h2>
				<p class="normal-text ht-pricing-guide__purity-intro">
					<?php esc_html_e('Applicable gold rate per gram = 24K rate × (Purity ÷ 24). Only 14K, 18K, 22K, and 24K are used.', 'octoways'); ?>
				</p>
				<div class="ht-pricing-guide__table-wrap">
					<table class="ht-pricing-guide__table">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e('Purity', 'octoways'); ?></th>
								<th scope="col"><?php esc_html_e('Factor', 'octoways'); ?></th>
								<?php if ($has_live_rates && (float) $rates['gold_rate_24k'] > 0) : ?>
									<th scope="col"><?php esc_html_e('Effective rate / g (NPR)', 'octoways'); ?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($purity_rows as $row) : ?>
								<tr>
									<td><strong><?php echo esc_html($row['label']); ?></strong></td>
									<td><?php echo esc_html($row['karat'] . ' ÷ 24'); ?></td>
									<?php if ($has_live_rates && (float) $rates['gold_rate_24k'] > 0) : ?>
										<td><?php echo esc_html(number_format($row['effective'], 2)); ?></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($example) : ?>
			<div class="ht-pricing-guide__example">
				<h2 class="ht-pricing-guide__block-title section-title"><?php esc_html_e('Example: pendant set', 'octoways'); ?></h2>
				<p class="normal-text ht-pricing-guide__example-intro">
					<?php esc_html_e('Illustrative configuration — 0.25 g gold (14K), 5 g silver, 0.5 ct diamond, 5.25 g total weight, making charge Rs. 600 per gram.', 'octoways'); ?>
				</p>
				<div class="ht-pricing-guide__table-wrap">
					<table class="ht-pricing-guide__table ht-pricing-guide__table--example">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e('Component', 'octoways'); ?></th>
								<th scope="col"><?php esc_html_e('Calculation', 'octoways'); ?></th>
								<th scope="col" class="ht-pricing-guide__col-amount"><?php esc_html_e('Amount (NPR)', 'octoways'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Gold cost', 'octoways'); ?></td>
								<td class="ht-pricing-guide__calc">0.25 × (24K × 14 ÷ 24)</td>
								<td class="ht-pricing-guide__col-amount"><?php echo esc_html(number_format($example['gold_cost'], 2)); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Silver cost', 'octoways'); ?></td>
								<td class="ht-pricing-guide__calc">5.00 × <?php echo esc_html(number_format($example['silver_rate'], 2)); ?></td>
								<td class="ht-pricing-guide__col-amount"><?php echo esc_html(number_format($example['silver_cost'], 2)); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Diamond cost', 'octoways'); ?></td>
								<td class="ht-pricing-guide__calc">0.50 × <?php echo esc_html(number_format($example['diamond_rate'], 2)); ?></td>
								<td class="ht-pricing-guide__col-amount"><?php echo esc_html(number_format($example['diamond_cost'], 2)); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Making charge', 'octoways'); ?></td>
								<td class="ht-pricing-guide__calc">5.25 × 600</td>
								<td class="ht-pricing-guide__col-amount"><?php echo esc_html(number_format($example['making_charge'], 2)); ?></td>
							</tr>
							<tr class="ht-pricing-guide__row-total">
								<td colspan="2"><strong><?php esc_html_e('Final price', 'octoways'); ?></strong></td>
								<td class="ht-pricing-guide__col-amount"><strong><?php echo esc_html(number_format($example['final_price'], 2)); ?></strong></td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php if (!$has_live_rates) : ?>
					<p class="ht-pricing-guide__example-note description">
						<?php esc_html_e('Example uses reference rates for demonstration. Product pages show prices from current live rates.', 'octoways'); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="ht-pricing-guide__footer-cta text-center">
			<p class="normal-text mb-4">
				<?php esc_html_e('Every product page shows a full cost breakdown before you add to cart. Rates are recalculated securely at checkout.', 'octoways'); ?>
			</p>
			<?php if (function_exists('wc_get_page_permalink')) : ?>
				<a class="button" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
					<?php esc_html_e('Explore our collection', 'octoways'); ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</section>
