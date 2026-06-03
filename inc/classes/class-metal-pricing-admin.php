<?php
/**
 * Admin UI for material rates and product configuration (Spec v1.0).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * WooCommerce admin settings and product meta fields.
 */
class Metal_Pricing_Admin
{
	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @var Metal_Rate_Sync
	 */
	private $rate_sync;

	/**
	 * @var Metal_Price_Calculator
	 */
	private $calculator;

	/**
	 * @param Metal_Rate_Store|null       $rate_store Rate store.
	 * @param Metal_Rate_Sync|null        $rate_sync  Sync service.
	 * @param Metal_Price_Calculator|null $calculator Calculator.
	 */
	public function __construct(
		Metal_Rate_Store $rate_store = null,
		Metal_Rate_Sync $rate_sync = null,
		Metal_Price_Calculator $calculator = null
	) {
		$this->rate_store  = $rate_store ?: new Metal_Rate_Store();
		$this->rate_sync   = $rate_sync ?: new Metal_Rate_Sync($this->rate_store);
		$this->calculator  = $calculator ?: new Metal_Price_Calculator($this->rate_store);
	}

	/**
	 * Register admin hooks.
	 */
	public function register()
	{
		add_action('admin_menu', array($this, 'register_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
		add_action('admin_post_ht_metal_rates_save', array($this, 'handle_manual_save'));
		add_action('admin_post_ht_metal_rates_sync', array($this, 'handle_manual_sync'));

		add_action('woocommerce_product_options_general_product_data', array($this, 'render_product_fields'));
		add_action('woocommerce_process_product_meta', array($this, 'save_product_fields'), 10, 1);
		$this->register_admin_notices();
	}

	/**
	 * Add submenu under WooCommerce.
	 */
	public function register_admin_menu()
	{
		add_submenu_page(
			'woocommerce',
			__('Material Rates', 'octoways'),
			__('Material Rates', 'octoways'),
			'manage_woocommerce',
			'ht-metal-rates',
			array($this, 'render_admin_page')
		);
	}

	/**
	 * Admin styles for material rates screen.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_assets($hook_suffix)
	{
		if ('woocommerce_page_ht-metal-rates' !== $hook_suffix) {
			return;
		}

		wp_add_inline_style(
			'wp-admin',
			'
			.ht-pricing-admin-layout { display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start; margin-top: 16px; }
			.ht-pricing-admin-layout__main { flex: 1 1 420px; min-width: 0; }
			.ht-pricing-admin-layout__aside { flex: 0 1 380px; max-width: 100%; }
			.ht-pricing-formula-block { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,.04); overflow: hidden; }
			.ht-pricing-formula-block__header { background: linear-gradient(135deg, #1d2327 0%, #2c3338 100%); color: #fff; padding: 14px 18px; }
			.ht-pricing-formula-block__header h2 { margin: 0; font-size: 15px; font-weight: 600; color: #fff; }
			.ht-pricing-formula-block__header p { margin: 6px 0 0; font-size: 12px; opacity: .85; }
			.ht-pricing-formula-block__body { padding: 16px 18px; }
			.ht-pricing-formula-card { background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px; padding: 12px 14px; margin-bottom: 12px; }
			.ht-pricing-formula-card:last-child { margin-bottom: 0; }
			.ht-pricing-formula-card--final { background: #f0f6fc; border-color: #72aee6; }
			.ht-pricing-formula-card__label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #50575e; margin-bottom: 6px; }
			.ht-pricing-formula-card__label--gold { color: #9a6700; }
			.ht-pricing-formula-card__label--silver { color: #50575e; }
			.ht-pricing-formula-card__label--diamond { color: #2271b1; }
			.ht-pricing-formula-card__eq { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 13px; line-height: 1.5; color: #1d2327; margin: 0; word-break: break-word; }
			.ht-pricing-formula-card__note { font-size: 11px; color: #646970; margin: 8px 0 0; }
			.ht-pricing-formula-purity-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 8px; }
			.ht-pricing-formula-purity-table th, .ht-pricing-formula-purity-table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #dcdcde; }
			.ht-pricing-formula-purity-table th { font-weight: 600; color: #50575e; }
			.ht-pricing-formula-purity-table tr:last-child td { border-bottom: 0; }
			.ht-pricing-formula-live { margin-top: 14px; padding-top: 14px; border-top: 1px dashed #dcdcde; }
			.ht-pricing-formula-live__title { font-size: 11px; font-weight: 600; text-transform: uppercase; color: #50575e; margin: 0 0 8px; }
			.ht-pricing-formula-live dl { margin: 0; display: grid; grid-template-columns: 1fr auto; gap: 4px 12px; font-size: 12px; }
			.ht-pricing-formula-live dt { color: #646970; }
			.ht-pricing-formula-live dd { margin: 0; font-weight: 600; font-family: ui-monospace, monospace; text-align: right; }
			'
		);
	}

	/**
	 * Formula reference block for the rates admin screen.
	 *
	 * @param array<string, mixed> $rates Current global rates.
	 */
	private function render_pricing_formula_block(array $rates)
	{
		$gold_24k = (float) $rates['gold_rate_24k'];
		$silver   = (float) $rates['silver_rate'];
		$diamond  = (float) $rates['diamond_rate'];

		$purity_examples = array();
		foreach (Metal_Price_Calculator::get_supported_purities() as $label => $karat) {
			$effective = $gold_24k > 0 ? $this->calculator->get_effective_gold_rate($gold_24k, $label) : 0;
			$purity_examples[] = array(
				'label'     => $label,
				'karat'     => $karat,
				'effective' => $effective,
			);
		}
		?>
		<aside class="ht-pricing-admin-layout__aside" aria-label="<?php esc_attr_e('Pricing formulas', 'octoways'); ?>">
			<div class="ht-pricing-formula-block">
				<div class="ht-pricing-formula-block__header">
					<h2><?php esc_html_e('Pricing formulas', 'octoways'); ?></h2>
					<p><?php esc_html_e('How product prices are calculated from the rates you set below.', 'octoways'); ?></p>
				</div>
				<div class="ht-pricing-formula-block__body">

					<div class="ht-pricing-formula-card">
						<span class="ht-pricing-formula-card__label ht-pricing-formula-card__label--gold"><?php esc_html_e('Gold cost', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Gold Cost = Gold Weight (g) × (24K Rate × Purity ÷ 24)', 'octoways'); ?>
						</p>
						<p class="ht-pricing-formula-card__note">
							<?php esc_html_e('24K Rate is the gold rate you enter on this page. Purity is set per product (14K–24K).', 'octoways'); ?>
						</p>
						<table class="ht-pricing-formula-purity-table">
							<thead>
								<tr>
									<th><?php esc_html_e('Purity', 'octoways'); ?></th>
									<th><?php esc_html_e('Multiplier', 'octoways'); ?></th>
									<?php if ($gold_24k > 0) : ?>
										<th><?php esc_html_e('Rate / g', 'octoways'); ?></th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($purity_examples as $row) : ?>
									<tr>
										<td><strong><?php echo esc_html($row['label']); ?></strong></td>
										<td><?php echo esc_html($row['karat'] . ' ÷ 24'); ?></td>
										<?php if ($gold_24k > 0) : ?>
											<td><?php echo esc_html(number_format($row['effective'], 2)); ?></td>
										<?php endif; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<div class="ht-pricing-formula-card">
						<span class="ht-pricing-formula-card__label ht-pricing-formula-card__label--silver"><?php esc_html_e('Silver cost', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Silver Cost = Silver Weight (g) × Silver Rate', 'octoways'); ?>
						</p>
					</div>

					<div class="ht-pricing-formula-card">
						<span class="ht-pricing-formula-card__label ht-pricing-formula-card__label--diamond"><?php esc_html_e('Diamond cost', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Diamond Cost = Diamond Weight (ct) × Diamond Rate', 'octoways'); ?>
						</p>
					</div>

					<div class="ht-pricing-formula-card">
						<span class="ht-pricing-formula-card__label"><?php esc_html_e('Making charge', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Per gram: Total Weight (g) × Making Rate', 'octoways'); ?>
						</p>
						<p class="ht-pricing-formula-card__eq" style="margin-top:6px;">
							<?php esc_html_e('Per piece: Fixed amount (Rs.)', 'octoways'); ?>
						</p>
					</div>

					<div class="ht-pricing-formula-card ht-pricing-formula-card--final">
						<span class="ht-pricing-formula-card__label"><?php esc_html_e('Final product price', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Final = Gold Cost + Silver Cost + Diamond Cost + Making Charge', 'octoways'); ?>
						</p>
						<p class="ht-pricing-formula-card__note">
							<?php esc_html_e('Unused materials (zero weight) contribute Rs. 0.', 'octoways'); ?>
						</p>
					</div>

					<?php if ($gold_24k > 0 || $silver > 0 || $diamond > 0) : ?>
						<div class="ht-pricing-formula-live">
							<p class="ht-pricing-formula-live__title"><?php esc_html_e('Current saved rates', 'octoways'); ?></p>
							<dl>
								<dt><?php esc_html_e('Gold (24K) / g', 'octoways'); ?></dt>
								<dd><?php echo esc_html($gold_24k > 0 ? number_format($gold_24k, 2) : '—'); ?></dd>
								<dt><?php esc_html_e('Silver / g', 'octoways'); ?></dt>
								<dd><?php echo esc_html($silver > 0 ? number_format($silver, 2) : '—'); ?></dd>
								<dt><?php esc_html_e('Diamond / ct', 'octoways'); ?></dt>
								<dd><?php echo esc_html($diamond > 0 ? number_format($diamond, 2) : '—'); ?></dd>
							</dl>
						</div>
					<?php endif; ?>

				</div>
			</div>
		</aside>
		<?php
	}

	/**
	 * Render rates admin page.
	 */
	public function render_admin_page()
	{
		if (!current_user_can('manage_woocommerce')) {
			return;
		}

		$rates = $this->rate_store->get_rates();
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Dynamic Pricing — Material Rates', 'octoways'); ?></h1>

			<?php if (isset($_GET['updated'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e('Material rates saved successfully.', 'octoways'); ?></p>
				</div>
			<?php endif; ?>

			<?php if (isset($_GET['synced'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e('Gold and silver rates synced from API. Diamond rate unchanged.', 'octoways'); ?></p>
				</div>
			<?php endif; ?>

			<?php if (isset($_GET['sync_failed'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="notice notice-error is-dismissible">
					<p><?php esc_html_e('API sync failed. Previous rates were retained.', 'octoways'); ?></p>
				</div>
			<?php endif; ?>

			<div class="ht-pricing-admin-layout">
				<div class="ht-pricing-admin-layout__main">
			<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<?php wp_nonce_field('ht_metal_rates_save', 'ht_metal_rates_nonce'); ?>
				<input type="hidden" name="action" value="ht_metal_rates_save" />

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="ht_gold_rate_24k"><?php esc_html_e('Gold rate (24K) per gram (NPR)', 'octoways'); ?></label>
						</th>
						<td>
							<input type="number" step="0.01" min="0" class="regular-text" id="ht_gold_rate_24k" name="ht_gold_rate_24k"
								value="<?php echo esc_attr($rates['gold_rate_24k']); ?>" required />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ht_silver_rate"><?php esc_html_e('Silver rate per gram (NPR)', 'octoways'); ?></label>
						</th>
						<td>
							<input type="number" step="0.01" min="0" class="regular-text" id="ht_silver_rate" name="ht_silver_rate"
								value="<?php echo esc_attr($rates['silver_rate']); ?>" required />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ht_diamond_rate"><?php esc_html_e('Diamond rate per carat (NPR)', 'octoways'); ?></label>
						</th>
						<td>
							<input type="number" step="0.01" min="0" class="regular-text" id="ht_diamond_rate" name="ht_diamond_rate"
								value="<?php echo esc_attr($rates['diamond_rate']); ?>" />
							<p class="description"><?php esc_html_e('Manual only; not updated by API sync.', 'octoways'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ht_default_making_charge"><?php esc_html_e('Default making charge (NPR)', 'octoways'); ?></label>
						</th>
						<td>
							<input type="number" step="0.01" min="0" class="regular-text" id="ht_default_making_charge" name="ht_default_making_charge"
								value="<?php echo esc_attr($rates['default_making_charge']); ?>" />
							<p class="description"><?php esc_html_e('Fallback when product making charge is empty.', 'octoways'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e('Rate version', 'octoways'); ?></th>
						<td><code><?php echo esc_html((string) $rates['rate_version']); ?></code></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e('Last synced', 'octoways'); ?></th>
						<td>
							<?php
							echo $rates['last_synced_at']
								? esc_html($rates['last_synced_at'] . ' UTC')
								: esc_html__('Never', 'octoways');
							?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e('Source', 'octoways'); ?></th>
						<td><code><?php echo esc_html((string) $rates['rate_source']); ?></code></td>
					</tr>
				</table>

				<?php submit_button(__('Save rates', 'octoways')); ?>
			</form>

			<hr />

			<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<?php wp_nonce_field('ht_metal_rates_sync', 'ht_metal_rates_sync_nonce'); ?>
				<input type="hidden" name="action" value="ht_metal_rates_sync" />
				<?php submit_button(__('Sync gold & silver from API', 'octoways'), 'secondary'); ?>
			</form>

			<p class="description">
				<?php esc_html_e('Rates sync automatically every 5 minutes (gold 24K and silver). Updating rates increments the version and refreshes all dynamic product prices instantly.', 'octoways'); ?>
			</p>
				</div>

				<?php $this->render_pricing_formula_block($rates); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save manual rates.
	 */
	public function handle_manual_save()
	{
		if (!current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('Unauthorized', 'octoways'));
		}

		check_admin_referer('ht_metal_rates_save', 'ht_metal_rates_nonce');

		$this->rate_store->update_rates(
			array(
				'gold_rate_24k'         => isset($_POST['ht_gold_rate_24k']) ? (float) wp_unslash($_POST['ht_gold_rate_24k']) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'silver_rate'           => isset($_POST['ht_silver_rate']) ? (float) wp_unslash($_POST['ht_silver_rate']) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'diamond_rate'          => isset($_POST['ht_diamond_rate']) ? (float) wp_unslash($_POST['ht_diamond_rate']) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
				'default_making_charge' => isset($_POST['ht_default_making_charge']) ? (float) wp_unslash($_POST['ht_default_making_charge']) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Missing
			),
			Metal_Rate_Store::SOURCE_MANUAL,
			true
		);

		Metal_Rate_Store::log('manual_update', 'Material rates updated manually from admin.');

		wp_safe_redirect(add_query_arg('updated', '1', admin_url('admin.php?page=ht-metal-rates')));
		exit;
	}

	/**
	 * Trigger manual API sync.
	 */
	public function handle_manual_sync()
	{
		if (!current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('Unauthorized', 'octoways'));
		}

		check_admin_referer('ht_metal_rates_sync', 'ht_metal_rates_sync_nonce');

		$success = $this->rate_sync->run_sync(true);
		$arg     = $success ? 'synced' : 'sync_failed';

		wp_safe_redirect(add_query_arg($arg, '1', admin_url('admin.php?page=ht-metal-rates')));
		exit;
	}

	/**
	 * Product material fields in General tab.
	 */
	public function render_product_fields()
	{
		global $post;

		if (!$post instanceof \WP_Post) {
			return;
		}

		$gold_weight    = get_post_meta($post->ID, Metal_Price_Calculator::META_GOLD_WEIGHT, true);
		$gold_purity    = get_post_meta($post->ID, Metal_Price_Calculator::META_GOLD_PURITY, true);
		$silver_weight  = get_post_meta($post->ID, Metal_Price_Calculator::META_SILVER_WEIGHT, true);
		$diamond_weight = get_post_meta($post->ID, Metal_Price_Calculator::META_DIAMOND_WEIGHT, true);
		$total_weight   = get_post_meta($post->ID, Metal_Price_Calculator::META_TOTAL_WEIGHT, true);
		$charge_type    = get_post_meta($post->ID, Metal_Price_Calculator::META_MAKING_CHARGE_TYPE, true);
		$charge_val     = get_post_meta($post->ID, Metal_Price_Calculator::META_MAKING_CHARGE_VALUE, true);

		$purity_options = array('' => __('— Select —', 'octoways'));
		foreach (array_keys(Metal_Price_Calculator::get_supported_purities()) as $label) {
			$purity_options[ $label ] = $label;
		}

		echo '<div class="options_group ht-metal-pricing-fields">';
		echo '<p class="form-field"><strong>' . esc_html__('Dynamic Material Pricing', 'octoways') . '</strong></p>';
		echo '<p class="description" style="padding:0 12px 12px;">' . esc_html__('Leave material weights empty (0) when not used. Gold purity required when gold weight is set.', 'octoways') . '</p>';

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_GOLD_WEIGHT,
				'label'             => __('Gold weight (g)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $gold_weight,
			)
		);

		woocommerce_wp_select(
			array(
				'id'      => Metal_Price_Calculator::META_GOLD_PURITY,
				'label'   => __('Gold purity', 'octoways'),
				'options' => $purity_options,
				'value'   => $gold_purity ?: '22K',
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_SILVER_WEIGHT,
				'label'             => __('Silver weight (g)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $silver_weight,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_DIAMOND_WEIGHT,
				'label'             => __('Diamond weight (carat)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $diamond_weight,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_TOTAL_WEIGHT,
				'label'             => __('Total product weight (g)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $total_weight,
				'description'       => __('Used for per-gram making charge.', 'octoways'),
			)
		);

		woocommerce_wp_select(
			array(
				'id'      => Metal_Price_Calculator::META_MAKING_CHARGE_TYPE,
				'label'   => __('Making charge type', 'octoways'),
				'options' => array(
					Metal_Price_Calculator::CHARGE_PER_GRAM  => __('Per gram', 'octoways'),
					Metal_Price_Calculator::CHARGE_PER_PIECE => __('Per piece', 'octoways'),
				),
				'value'   => $charge_type ?: Metal_Price_Calculator::CHARGE_PER_GRAM,
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_MAKING_CHARGE_VALUE,
				'label'             => __('Making charge value (NPR)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $charge_val,
				'description'       => __('Overrides default making charge when set.', 'octoways'),
			)
		);

		echo '</div>';
	}

	/**
	 * @param int $post_id Product ID.
	 */
	public function save_product_fields($post_id)
	{
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		$gold_weight    = isset($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$gold_purity    = isset($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: '';
		$silver_weight  = isset($_POST[ Metal_Price_Calculator::META_SILVER_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_SILVER_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$diamond_weight = isset($_POST[ Metal_Price_Calculator::META_DIAMOND_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_DIAMOND_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$total_weight   = isset($_POST[ Metal_Price_Calculator::META_TOTAL_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_TOTAL_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$charge_type    = isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: Metal_Price_Calculator::CHARGE_PER_GRAM;
		$charge_val     = isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;

		if ($gold_weight > 0 && !$this->calculator->is_valid_gold_purity($gold_purity)) {
			// Reject invalid purity per spec — do not save gold fields.
			add_filter(
				'redirect_post_location',
				static function ($location) {
					return add_query_arg('ht_metal_purity_error', '1', $location);
				}
			);
			return;
		}

		if (!in_array($charge_type, array(Metal_Price_Calculator::CHARGE_PER_GRAM, Metal_Price_Calculator::CHARGE_PER_PIECE), true)) {
			$charge_type = Metal_Price_Calculator::CHARGE_PER_GRAM;
		}

		update_post_meta($post_id, Metal_Price_Calculator::META_GOLD_WEIGHT, max(0, $gold_weight));
		update_post_meta($post_id, Metal_Price_Calculator::META_GOLD_PURITY, $gold_weight > 0 ? $gold_purity : '');
		update_post_meta($post_id, Metal_Price_Calculator::META_SILVER_WEIGHT, max(0, $silver_weight));
		update_post_meta($post_id, Metal_Price_Calculator::META_DIAMOND_WEIGHT, max(0, $diamond_weight));
		update_post_meta($post_id, Metal_Price_Calculator::META_TOTAL_WEIGHT, max(0, $total_weight));
		update_post_meta($post_id, Metal_Price_Calculator::META_MAKING_CHARGE_TYPE, $charge_type);
		update_post_meta($post_id, Metal_Price_Calculator::META_MAKING_CHARGE_VALUE, max(0, $charge_val));

		// Remove deprecated v1 meta.
		delete_post_meta($post_id, '_metal_type');
		delete_post_meta($post_id, '_metal_weight');
	}

	/**
	 * Admin notice for invalid gold purity on product save.
	 */
	public function register_admin_notices()
	{
		add_action(
			'admin_notices',
			static function () {
				if (!isset($_GET['ht_metal_purity_error'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					return;
				}
				echo '<div class="notice notice-error"><p>';
				echo esc_html__('Product not saved: gold purity must be 14K, 18K, 22K, or 24K when gold weight is set.', 'octoways');
				echo '</p></div>';
			}
		);
	}
}
