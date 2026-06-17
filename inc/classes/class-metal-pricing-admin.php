<?php
/**
 * Admin UI for material rates and product configuration (Spec v1.1).
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
		add_action('wp_ajax_ht_preview_metal_price', array($this, 'ajax_preview_metal_price'));
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
		$is_rates_page  = ('woocommerce_page_ht-metal-rates' === $hook_suffix);
		$is_product_edit = $this->is_product_edit_screen($hook_suffix);

		if (!$is_rates_page && !$is_product_edit) {
			return;
		}

		if ($is_product_edit) {
			$this->enqueue_product_pricing_admin_assets();
		}

		if (!$is_rates_page) {
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
							<?php esc_html_e('24K Rate is the gold rate you enter on this page. Purity is set per product (9K–24K).', 'octoways'); ?>
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
						<span class="ht-pricing-formula-card__label"><?php esc_html_e('Gemstone cost', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Gemstone Cost = Gemstone Qty × Gemstone Rate (per product)', 'octoways'); ?>
						</p>
					</div>

					<div class="ht-pricing-formula-card">
						<span class="ht-pricing-formula-card__label"><?php esc_html_e('Plating & misc', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Gold plating, rhodium plating, and miscellaneous costs are fixed Rs. amounts per product.', 'octoways'); ?>
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
						<p class="ht-pricing-formula-card__eq" style="margin-top:6px;">
							<?php esc_html_e('Percentage: (Gold Cost + Silver Cost) × Making Charge %', 'octoways'); ?>
						</p>
					</div>

					<div class="ht-pricing-formula-card ht-pricing-formula-card--final">
						<span class="ht-pricing-formula-card__label"><?php esc_html_e('Final product price', 'octoways'); ?></span>
						<p class="ht-pricing-formula-card__eq">
							<?php esc_html_e('Final = Gold + Silver + Diamond + Making + Gemstone + Gold Plating + Rhodium Plating + Misc', 'octoways'); ?>
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
	 * @param string $hook_suffix Admin screen hook.
	 * @return bool
	 */
	private function is_product_edit_screen($hook_suffix)
	{
		if (!in_array($hook_suffix, array('post.php', 'post-new.php'), true)) {
			return false;
		}

		$screen = function_exists('get_current_screen') ? get_current_screen() : null;

		return $screen && 'product' === $screen->post_type;
	}

	/**
	 * Styles and behaviour for conditional product pricing fields.
	 */
	private function enqueue_product_pricing_admin_assets()
	{
		wp_enqueue_style('dashicons');

		wp_add_inline_style(
			'woocommerce_admin_styles',
			'
			.ht-metal-pricing-fields { border-top: 1px solid #eee; padding-top: 4px; }
			.ht-metal-pricing-toggles { display: flex; flex-wrap: wrap; gap: 8px 16px; padding: 4px 12px 14px; }
			.ht-metal-pricing-toggles label { display: inline-flex; align-items: center; gap: 6px; font-weight: 500; cursor: pointer; margin: 0; }
			.ht-metal-pricing-section { display: none; margin: 0; padding: 0; border-top: 1px solid #f0f0f1; }
			.ht-metal-pricing-section.is-active { display: block; }
			.ht-metal-pricing-section__title { margin: 0; padding: 10px 12px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; color: #50575e; }
			.ht-metal-pricing-section--making { display: block; border-top: 1px solid #dcdcde; margin-top: 8px; }
			.ht-metal-pricing-section--making .ht-metal-pricing-section__title { color: #1d2327; }
			.ht-metal-pricing-fields .ht-metal-pricing-field--conditional { display: none; }
			.ht-metal-pricing-fields .ht-metal-pricing-field--conditional.is-active { display: block; }
			.ht-metal-pricing-preview { margin: 12px; padding: 12px 14px; background: #f0f6fc; border: 1px solid #72aee6; border-radius: 6px; }
			.ht-metal-pricing-preview.is-empty { background: #f6f7f7; border-color: #dcdcde; }
			.ht-metal-pricing-preview__head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
			.ht-metal-pricing-preview__label { font-weight: 600; color: #1d2327; }
			.ht-metal-pricing-preview__total { font-size: 16px; font-weight: 700; font-family: ui-monospace, monospace; color: #135e96; margin-left: auto; }
			.ht-metal-pricing-preview.is-empty .ht-metal-pricing-preview__total { color: #646970; font-weight: 500; font-size: 13px; }
			.ht-metal-pricing-preview__info-wrap { position: relative; display: inline-flex; }
			.ht-metal-pricing-preview__info { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; padding: 0; border: 0; background: transparent; color: #2271b1; cursor: pointer; border-radius: 50%; }
			.ht-metal-pricing-preview__info:hover, .ht-metal-pricing-preview__info:focus { background: #dbeafe; outline: none; }
			.ht-metal-pricing-preview__info .dashicons { font-size: 18px; width: 18px; height: 18px; }
			.ht-metal-pricing-preview__tooltip { display: none; position: absolute; z-index: 100; right: 0; top: calc(100% + 6px); width: min(380px, 92vw); padding: 12px; background: #1d2327; color: #f0f0f1; border-radius: 6px; box-shadow: 0 4px 16px rgba(0,0,0,.25); font-size: 12px; line-height: 1.45; text-align: left; }
			.ht-metal-pricing-preview__tooltip.is-open { display: block; }
			.ht-metal-pricing-preview__tooltip::before { content: ""; position: absolute; top: -6px; right: 8px; border: 6px solid transparent; border-bottom-color: #1d2327; border-top: 0; }
			.ht-metal-pricing-preview__tooltip-title { margin: 0 0 8px; font-weight: 600; font-size: 12px; color: #fff; }
			.ht-metal-pricing-preview__tooltip-table { width: 100%; border-collapse: collapse; margin: 0 0 8px; }
			.ht-metal-pricing-preview__tooltip-table th, .ht-metal-pricing-preview__tooltip-table td { padding: 5px 0; border-bottom: 1px solid rgba(255,255,255,.12); vertical-align: top; }
			.ht-metal-pricing-preview__tooltip-table tr:last-child th, .ht-metal-pricing-preview__tooltip-table tr:last-child td { border-bottom: 0; font-weight: 700; color: #fff; }
			.ht-metal-pricing-preview__tooltip-table th { text-align: left; font-weight: 500; color: #c3c4c7; width: 28%; }
			.ht-metal-pricing-preview__tooltip-formula { color: #a7aaad; font-family: ui-monospace, monospace; font-size: 11px; }
			.ht-metal-pricing-preview__tooltip-amount { text-align: right; font-family: ui-monospace, monospace; white-space: nowrap; }
			.ht-metal-pricing-preview__note { margin: 0; font-size: 11px; color: #646970; }
			.ht-metal-pricing-preview__tooltip .ht-metal-pricing-preview__note { color: #a7aaad; }
			.ht-metal-pricing-preview.is-loading .ht-metal-pricing-preview__total { opacity: .5; }
			'
		);

		wp_enqueue_script('jquery');

		$charge_per_gram   = Metal_Price_Calculator::CHARGE_PER_GRAM;
		$charge_per_piece  = Metal_Price_Calculator::CHARGE_PER_PIECE;
		$charge_percentage = Metal_Price_Calculator::CHARGE_PERCENTAGE;

		wp_localize_script(
			'jquery',
			'htMetalPricingAdmin',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('ht_preview_metal_price'),
				'i18n'    => array(
					'empty'    => __('Configure materials to see price', 'octoways'),
					'error'    => __('Unable to calculate price', 'octoways'),
					'loading'  => __('Calculating…', 'octoways'),
					'tooltip'  => __('Price breakdown', 'octoways'),
					'rateNote' => __('Uses current global material rates. Updates as you edit fields.', 'octoways'),
				),
			)
		);

		wp_add_inline_script(
			'jquery',
			"(function ($) {
				var previewTimer = null;

				function syncMetalPricingFields() {
					var \$root = $('.ht-metal-pricing-fields');
					if (!\$root.length) return;

					\$root.find('[data-ht-component]').each(function () {
						var key = $(this).data('ht-component');
						var on = \$root.find('[data-ht-toggle=\"' + key + '\"]').is(':checked');
						$(this).toggleClass('is-active', on);
					});

					var chargeType = $('#" . esc_js(Metal_Price_Calculator::META_MAKING_CHARGE_TYPE) . "').val();
					var showWeight = chargeType === '" . esc_js($charge_per_gram) . "';
					\$root.find('[data-ht-making-field=\"total_weight\"]').toggleClass('is-active', showWeight);

					var \$valueField = $('#" . esc_js(Metal_Price_Calculator::META_MAKING_CHARGE_VALUE) . "').closest('.form-field');
					var \$valueDesc = \$valueField.find('.description');
					if (chargeType === '" . esc_js($charge_percentage) . "') {
						\$valueField.find('label').text('" . esc_js(__('Making charge (%)', 'octoways')) . "');
						\$valueDesc.text('" . esc_js(__('Percent of gold + silver metal value (e.g. 12 = 12%).', 'octoways')) . "');
					} else if (chargeType === '" . esc_js($charge_per_piece) . "') {
						\$valueField.find('label').text('" . esc_js(__('Making charge (NPR per piece)', 'octoways')) . "');
						\$valueDesc.text('" . esc_js(__('Fixed amount per piece. Overrides global default when set.', 'octoways')) . "');
					} else {
						\$valueField.find('label').text('" . esc_js(__('Making charge (NPR per gram)', 'octoways')) . "');
						\$valueDesc.text('" . esc_js(__('Multiplied by total product weight. Overrides global default when set.', 'octoways')) . "');
					}
				}

				function renderPreviewTooltip(lines, totalFormatted) {
					if (!lines || !lines.length) {
						return '<p class=\"ht-metal-pricing-preview__note\">' + htMetalPricingAdmin.i18n.empty + '</p>';
					}
					var html = '<p class=\"ht-metal-pricing-preview__tooltip-title\">' + htMetalPricingAdmin.i18n.tooltip + '</p>';
					html += '<table class=\"ht-metal-pricing-preview__tooltip-table\"><tbody>';
					lines.forEach(function (line) {
						html += '<tr><th>' + line.label + '</th><td class=\"ht-metal-pricing-preview__tooltip-formula\">' + line.formula + '</td><td class=\"ht-metal-pricing-preview__tooltip-amount\">' + line.amount + '</td></tr>';
					});
					html += '<tr><th>" . esc_js(__('Total', 'octoways')) . "</th><td></td><td class=\"ht-metal-pricing-preview__tooltip-amount\">' + totalFormatted + '</td></tr>';
					html += '</tbody></table><p class=\"ht-metal-pricing-preview__note\">' + htMetalPricingAdmin.i18n.rateNote + '</p>';
					return html;
				}

				function updatePreviewPanel(data) {
					var \$panel = $('.ht-metal-pricing-preview');
					if (!\$panel.length) return;

					\$panel.removeClass('is-loading');

					if (!data || !data.success) {
						\$panel.addClass('is-empty');
						\$panel.find('.ht-metal-pricing-preview__total').text(htMetalPricingAdmin.i18n.empty);
						\$panel.find('.ht-metal-pricing-preview__tooltip').html('<p class=\"ht-metal-pricing-preview__note\">' + htMetalPricingAdmin.i18n.empty + '</p>');
						\$panel.find('.ht-metal-pricing-preview__info-wrap').hide();
						return;
					}

					var payload = data.data;
					\$panel.toggleClass('is-empty', !payload.lines || !payload.lines.length);
					\$panel.find('.ht-metal-pricing-preview__total').text(payload.total_formatted || htMetalPricingAdmin.i18n.empty);
					\$panel.find('.ht-metal-pricing-preview__tooltip').html(renderPreviewTooltip(payload.lines, payload.total_formatted));
					\$panel.find('.ht-metal-pricing-preview__info-wrap').toggle(!!(payload.lines && payload.lines.length));
				}

				function refreshPricePreview() {
					var \$root = $('.ht-metal-pricing-fields');
					var \$panel = $('.ht-metal-pricing-preview');
					if (!\$root.length || !\$panel.length || typeof htMetalPricingAdmin === 'undefined') return;

					\$panel.addClass('is-loading');
					\$panel.find('.ht-metal-pricing-preview__total').text(htMetalPricingAdmin.i18n.loading);

					$.post(htMetalPricingAdmin.ajaxUrl, \$root.find(':input').serialize() + '&action=ht_preview_metal_price&nonce=' + encodeURIComponent(htMetalPricingAdmin.nonce))
						.done(function (response) { updatePreviewPanel(response); })
						.fail(function () {
							updatePreviewPanel({ success: false });
						});
				}

				function schedulePreview() {
					clearTimeout(previewTimer);
					previewTimer = setTimeout(refreshPricePreview, 350);
				}

				$(document).on('change input', '.ht-metal-pricing-fields :input', function () {
					syncMetalPricingFields();
					schedulePreview();
				});
				$(document).on('change', '.ht-metal-pricing-fields [data-ht-toggle], #" . esc_js(Metal_Price_Calculator::META_MAKING_CHARGE_TYPE) . "', syncMetalPricingFields);
				$(document).on('click', '.ht-metal-pricing-preview__info', function (e) {
					e.preventDefault();
					e.stopPropagation();
					var \$tip = $(this).closest('.ht-metal-pricing-preview__info-wrap').find('.ht-metal-pricing-preview__tooltip');
					\$tip.toggleClass('is-open');
				});
				$(document).on('click', function (e) {
					if (!$(e.target).closest('.ht-metal-pricing-preview__info-wrap').length) {
						$('.ht-metal-pricing-preview__tooltip').removeClass('is-open');
					}
				});

				syncMetalPricingFields();
			})(jQuery);"
		);
	}

	/**
	 * @param int $post_id Product ID.
	 * @return array<string, bool>
	 */
	private function get_product_component_toggles($post_id)
	{
		$gold_weight    = (float) get_post_meta($post_id, Metal_Price_Calculator::META_GOLD_WEIGHT, true);
		$silver_weight  = (float) get_post_meta($post_id, Metal_Price_Calculator::META_SILVER_WEIGHT, true);
		$diamond_weight = (float) get_post_meta($post_id, Metal_Price_Calculator::META_DIAMOND_WEIGHT, true);
		$gemstone_qty   = (float) get_post_meta($post_id, Metal_Price_Calculator::META_GEMSTONE_QTY, true);
		$gemstone_rate  = (float) get_post_meta($post_id, Metal_Price_Calculator::META_GEMSTONE_RATE, true);
		$gold_plating   = (float) get_post_meta($post_id, Metal_Price_Calculator::META_GOLD_PLATING_COST, true);
		$rhodium        = (float) get_post_meta($post_id, Metal_Price_Calculator::META_RHODIUM_PLATING_COST, true);
		$misc           = (float) get_post_meta($post_id, Metal_Price_Calculator::META_MISC_COST, true);

		return array(
			'gold'     => $gold_weight > 0,
			'silver'   => $silver_weight > 0,
			'diamond'  => $diamond_weight > 0,
			'gemstone' => ($gemstone_qty > 0) || ($gemstone_rate > 0),
			'plating'  => ($gold_plating > 0) || ($rhodium > 0) || ($misc > 0),
		);
	}

	/**
	 * @param string $key     Component key.
	 * @param string $label   Checkbox label.
	 * @param bool   $checked Initial state.
	 */
	private function render_component_toggle($key, $label, $checked)
	{
		printf(
			'<label><input type="checkbox" name="%1$s" value="1" data-ht-toggle="%2$s" %3$s /> %4$s</label>',
			esc_attr('ht_use_' . $key),
			esc_attr($key),
			checked($checked, true, false),
			esc_html($label)
		);
	}

	/**
	 * @param string $key   Section key.
	 * @param string $title Section heading.
	 */
	private function render_pricing_section_open($key, $title)
	{
		printf(
			'<div class="ht-metal-pricing-section" data-ht-component="%1$s"><p class="ht-metal-pricing-section__title">%2$s</p>',
			esc_attr($key),
			esc_html($title)
		);
	}

	/**
	 * Close a pricing section wrapper.
	 */
	private function render_pricing_section_close()
	{
		echo '</div>';
	}

	/**
	 * Product material fields in General tab (progressive disclosure).
	 */
	public function render_product_fields()
	{
		global $post;

		if (!$post instanceof \WP_Post) {
			return;
		}

		$gold_weight          = get_post_meta($post->ID, Metal_Price_Calculator::META_GOLD_WEIGHT, true);
		$gold_purity          = get_post_meta($post->ID, Metal_Price_Calculator::META_GOLD_PURITY, true);
		$silver_weight        = get_post_meta($post->ID, Metal_Price_Calculator::META_SILVER_WEIGHT, true);
		$diamond_weight       = get_post_meta($post->ID, Metal_Price_Calculator::META_DIAMOND_WEIGHT, true);
		$gemstone_qty         = get_post_meta($post->ID, Metal_Price_Calculator::META_GEMSTONE_QTY, true);
		$gemstone_rate        = get_post_meta($post->ID, Metal_Price_Calculator::META_GEMSTONE_RATE, true);
		$gold_plating_cost    = get_post_meta($post->ID, Metal_Price_Calculator::META_GOLD_PLATING_COST, true);
		$rhodium_plating_cost = get_post_meta($post->ID, Metal_Price_Calculator::META_RHODIUM_PLATING_COST, true);
		$misc_cost            = get_post_meta($post->ID, Metal_Price_Calculator::META_MISC_COST, true);
		$total_weight         = get_post_meta($post->ID, Metal_Price_Calculator::META_TOTAL_WEIGHT, true);
		$charge_type          = get_post_meta($post->ID, Metal_Price_Calculator::META_MAKING_CHARGE_TYPE, true);
		$charge_val           = get_post_meta($post->ID, Metal_Price_Calculator::META_MAKING_CHARGE_VALUE, true);

		$toggles = $this->get_product_component_toggles($post->ID);

		$purity_options = array('' => __('— Select —', 'octoways'));
		foreach (array_keys(Metal_Price_Calculator::get_supported_purities()) as $label) {
			$purity_options[ $label ] = $label;
		}

		$charge_type = $charge_type ?: Metal_Price_Calculator::CHARGE_PER_GRAM;

		echo '<div class="options_group ht-metal-pricing-fields">';
		echo '<p class="form-field"><strong>' . esc_html__('Dynamic Material Pricing', 'octoways') . '</strong></p>';
		echo '<p class="description" style="padding:0 12px 8px;">' . esc_html__('Choose which materials apply to this product. Only selected sections are shown.', 'octoways') . '</p>';

		echo '<div class="ht-metal-pricing-toggles form-field">';
		$this->render_component_toggle('gold', __('Gold', 'octoways'), $toggles['gold']);
		$this->render_component_toggle('silver', __('Silver', 'octoways'), $toggles['silver']);
		$this->render_component_toggle('diamond', __('Diamond', 'octoways'), $toggles['diamond']);
		$this->render_component_toggle('gemstone', __('Gemstone', 'octoways'), $toggles['gemstone']);
		$this->render_component_toggle('plating', __('Plating & misc', 'octoways'), $toggles['plating']);
		echo '</div>';

		$this->render_pricing_section_open('gold', __('Gold', 'octoways'));
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
		$this->render_pricing_section_close();

		$this->render_pricing_section_open('silver', __('Silver', 'octoways'));
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_SILVER_WEIGHT,
				'label'             => __('Silver weight (g)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $silver_weight,
			)
		);
		$this->render_pricing_section_close();

		$this->render_pricing_section_open('diamond', __('Diamond', 'octoways'));
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_DIAMOND_WEIGHT,
				'label'             => __('Diamond weight (carat)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $diamond_weight,
			)
		);
		$this->render_pricing_section_close();

		$this->render_pricing_section_open('gemstone', __('Gemstone', 'octoways'));
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_GEMSTONE_QTY,
				'label'             => __('Gemstone quantity', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $gemstone_qty,
				'description'       => __('Carats or pieces, depending on product.', 'octoways'),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_GEMSTONE_RATE,
				'label'             => __('Gemstone rate (NPR per unit)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $gemstone_rate,
				'description'       => __('Per-product rate (ruby, emerald, sapphire, etc.).', 'octoways'),
			)
		);
		$this->render_pricing_section_close();

		$this->render_pricing_section_open('plating', __('Plating & miscellaneous', 'octoways'));
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_GOLD_PLATING_COST,
				'label'             => __('Gold plating cost (NPR)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $gold_plating_cost,
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_RHODIUM_PLATING_COST,
				'label'             => __('Rhodium plating cost (NPR)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $rhodium_plating_cost,
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_MISC_COST,
				'label'             => __('Miscellaneous cost (NPR)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $misc_cost,
			)
		);
		$this->render_pricing_section_close();

		echo '<div class="ht-metal-pricing-section ht-metal-pricing-section--making is-active">';
		echo '<p class="ht-metal-pricing-section__title">' . esc_html__('Making charge', 'octoways') . '</p>';

		woocommerce_wp_select(
			array(
				'id'      => Metal_Price_Calculator::META_MAKING_CHARGE_TYPE,
				'label'   => __('Making charge type', 'octoways'),
				'options' => array(
					Metal_Price_Calculator::CHARGE_PER_GRAM    => __('Per gram', 'octoways'),
					Metal_Price_Calculator::CHARGE_PER_PIECE   => __('Per piece', 'octoways'),
					Metal_Price_Calculator::CHARGE_PERCENTAGE  => __('Percentage of metal value', 'octoways'),
				),
				'value'   => $charge_type,
			)
		);

		echo '<div class="ht-metal-pricing-field--conditional' . (Metal_Price_Calculator::CHARGE_PER_GRAM === $charge_type ? ' is-active' : '') . '" data-ht-making-field="total_weight">';
		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_TOTAL_WEIGHT,
				'label'             => __('Total product weight (g)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $total_weight,
				'description'       => __('Required for per-gram making charge.', 'octoways'),
			)
		);
		echo '</div>';

		woocommerce_wp_text_input(
			array(
				'id'                => Metal_Price_Calculator::META_MAKING_CHARGE_VALUE,
				'label'             => __('Making charge (NPR per gram)', 'octoways'),
				'type'              => 'number',
				'custom_attributes' => array('step' => '0.01', 'min' => '0'),
				'value'             => $charge_val,
				'description'       => __('Overrides global default when set.', 'octoways'),
			)
		);

		echo '</div>';

		$this->render_product_price_preview($post->ID);

		echo '</div>';
	}

	/**
	 * Live calculated price panel for product edit screen.
	 *
	 * @param int $post_id Product ID.
	 */
	private function render_product_price_preview($post_id)
	{
		$payload = $this->build_preview_payload_from_product($post_id);
		$is_empty = empty($payload['lines']);
		$classes  = 'ht-metal-pricing-preview' . ($is_empty ? ' is-empty' : '');

		echo '<div class="' . esc_attr($classes) . '" data-ht-price-preview>';
		echo '<div class="ht-metal-pricing-preview__head">';
		echo '<span class="ht-metal-pricing-preview__label">' . esc_html__('Calculated product price', 'octoways') . '</span>';

		if (!$is_empty) {
			echo '<span class="ht-metal-pricing-preview__info-wrap">';
			echo '<button type="button" class="ht-metal-pricing-preview__info" aria-label="' . esc_attr__('Show price breakdown', 'octoways') . '" aria-expanded="false">';
			echo '<span class="dashicons dashicons-info-outline" aria-hidden="true"></span>';
			echo '</button>';
			echo '<div class="ht-metal-pricing-preview__tooltip" role="tooltip">';
			echo wp_kses_post($this->render_preview_tooltip_html($payload));
			echo '</div>';
			echo '</span>';
		}

		echo '<strong class="ht-metal-pricing-preview__total">' . esc_html($payload['total_formatted']) . '</strong>';
		echo '</div>';
		echo '<p class="ht-metal-pricing-preview__note">' . esc_html__('Based on current global material rates. Save the product to apply on the storefront.', 'octoways') . '</p>';
		echo '</div>';
	}

	/**
	 * AJAX: recalculate price from unsaved product field values.
	 */
	public function ajax_preview_metal_price()
	{
		check_ajax_referer('ht_preview_metal_price', 'nonce');

		if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce')) {
			wp_send_json_error(array('message' => __('Unauthorized', 'octoways')), 403);
		}

		$formula = $this->parse_product_formula_from_post();

		if (!$formula) {
			wp_send_json_success($this->empty_preview_payload());
		}

		$rates     = $this->rate_store->get_rates();
		$breakdown = $this->calculator->calculate_from_formula($formula, $rates);

		if (!$breakdown || $breakdown['final_price'] <= 0) {
			wp_send_json_success($this->empty_preview_payload());
		}

		wp_send_json_success($this->build_preview_payload_from_breakdown($breakdown, $rates));
	}

	/**
	 * @param int $post_id Product ID.
	 * @return array<string, mixed>
	 */
	private function build_preview_payload_from_product($post_id)
	{
		$breakdown = $this->calculator->get_breakdown_for_product($post_id);

		if (!$breakdown) {
			return $this->empty_preview_payload();
		}

		return $this->build_preview_payload_from_breakdown($breakdown, $this->rate_store->get_rates());
	}

	/**
	 * @return array<string, mixed>
	 */
	private function empty_preview_payload()
	{
		return array(
			'total'            => 0,
			'total_formatted'  => __('Configure materials to see price', 'octoways'),
			'lines'            => array(),
			'rate_version'     => $this->rate_store->get_version(),
		);
	}

	/**
	 * @param array<string, mixed> $breakdown Price breakdown.
	 * @param array<string, mixed> $rates     Global rates.
	 * @return array<string, mixed>
	 */
	private function build_preview_payload_from_breakdown(array $breakdown, array $rates)
	{
		$lines = $this->get_breakdown_display_lines($breakdown, $rates);

		return array(
			'total'           => (float) $breakdown['final_price'],
			'total_formatted' => $this->format_admin_currency($breakdown['final_price']),
			'lines'           => $lines,
			'rate_version'    => (int) $rates['rate_version'],
		);
	}

	/**
	 * @param array<string, mixed> $payload Preview payload.
	 * @return string
	 */
	private function render_preview_tooltip_html(array $payload)
	{
		if (empty($payload['lines'])) {
			return '<p class="ht-metal-pricing-preview__note">' . esc_html__('Configure materials to see price', 'octoways') . '</p>';
		}

		ob_start();
		?>
		<p class="ht-metal-pricing-preview__tooltip-title"><?php esc_html_e('Price breakdown', 'octoways'); ?></p>
		<table class="ht-metal-pricing-preview__tooltip-table">
			<tbody>
				<?php foreach ($payload['lines'] as $line) : ?>
					<tr>
						<th><?php echo esc_html($line['label']); ?></th>
						<td class="ht-metal-pricing-preview__tooltip-formula"><?php echo esc_html($line['formula']); ?></td>
						<td class="ht-metal-pricing-preview__tooltip-amount"><?php echo esc_html($line['amount']); ?></td>
					</tr>
				<?php endforeach; ?>
				<tr>
					<th><?php esc_html_e('Total', 'octoways'); ?></th>
					<td></td>
					<td class="ht-metal-pricing-preview__tooltip-amount"><?php echo esc_html($payload['total_formatted']); ?></td>
				</tr>
			</tbody>
		</table>
		<p class="ht-metal-pricing-preview__note"><?php esc_html_e('Uses current global material rates.', 'octoways'); ?></p>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $breakdown Breakdown from calculator.
	 * @param array<string, mixed> $rates     Global rates.
	 * @return array<int, array<string, string>>
	 */
	private function get_breakdown_display_lines(array $breakdown, array $rates)
	{
		$lines = array();

		if ($breakdown['gold_weight'] > 0 && $breakdown['gold_cost'] > 0) {
			$lines[] = array(
				'label'    => __('Gold', 'octoways'),
				'formula'  => sprintf(
					/* translators: 1: weight 2: rate 3: purity */
					__('%1$s g × %2$s/g (%3$s)', 'octoways'),
					$this->format_admin_number($breakdown['gold_weight']),
					$this->format_admin_number($breakdown['gold_effective_rate']),
					$breakdown['gold_purity']
				),
				'amount'   => $this->format_admin_currency($breakdown['gold_cost']),
			);
		}

		if ($breakdown['silver_weight'] > 0 && $breakdown['silver_cost'] > 0) {
			$lines[] = array(
				'label'   => __('Silver', 'octoways'),
				'formula' => sprintf(
					/* translators: 1: weight 2: rate */
					__('%1$s g × %2$s/g', 'octoways'),
					$this->format_admin_number($breakdown['silver_weight']),
					$this->format_admin_number($breakdown['silver_rate'])
				),
				'amount'  => $this->format_admin_currency($breakdown['silver_cost']),
			);
		}

		if ($breakdown['diamond_weight'] > 0 && $breakdown['diamond_cost'] > 0) {
			$lines[] = array(
				'label'   => __('Diamond', 'octoways'),
				'formula' => sprintf(
					/* translators: 1: carats 2: rate */
					__('%1$s ct × %2$s/ct', 'octoways'),
					$this->format_admin_number($breakdown['diamond_weight']),
					$this->format_admin_number($breakdown['diamond_rate'])
				),
				'amount'  => $this->format_admin_currency($breakdown['diamond_cost']),
			);
		}

		if ($breakdown['making_charge'] > 0) {
			$lines[] = array(
				'label'   => __('Making', 'octoways'),
				'formula' => $this->get_making_charge_formula_label($breakdown, $rates),
				'amount'  => $this->format_admin_currency($breakdown['making_charge']),
			);
		}

		if ($breakdown['gemstone_cost'] > 0) {
			$lines[] = array(
				'label'   => __('Gemstone', 'octoways'),
				'formula' => sprintf(
					/* translators: 1: qty 2: rate */
					__('%1$s × %2$s', 'octoways'),
					$this->format_admin_number($breakdown['gemstone_qty']),
					$this->format_admin_currency($breakdown['gemstone_rate'])
				),
				'amount'  => $this->format_admin_currency($breakdown['gemstone_cost']),
			);
		}

		if ($breakdown['gold_plating_cost_calc'] > 0) {
			$lines[] = array(
				'label'   => __('Gold plating', 'octoways'),
				'formula' => __('Fixed', 'octoways'),
				'amount'  => $this->format_admin_currency($breakdown['gold_plating_cost_calc']),
			);
		}

		if ($breakdown['rhodium_plating_cost_calc'] > 0) {
			$lines[] = array(
				'label'   => __('Rhodium plating', 'octoways'),
				'formula' => __('Fixed', 'octoways'),
				'amount'  => $this->format_admin_currency($breakdown['rhodium_plating_cost_calc']),
			);
		}

		if ($breakdown['misc_cost_calc'] > 0) {
			$lines[] = array(
				'label'   => __('Miscellaneous', 'octoways'),
				'formula' => __('Fixed', 'octoways'),
				'amount'  => $this->format_admin_currency($breakdown['misc_cost_calc']),
			);
		}

		return $lines;
	}

	/**
	 * @param array<string, mixed> $breakdown Breakdown.
	 * @param array<string, mixed> $rates     Global rates.
	 * @return string
	 */
	private function get_making_charge_formula_label(array $breakdown, array $rates)
	{
		$type  = $breakdown['making_charge_type'];
		$value = (float) $breakdown['making_charge_value'];

		if (Metal_Price_Calculator::CHARGE_PERCENTAGE === $type) {
			return sprintf(
				/* translators: 1: metal value 2: percent */
				__('(%1$s metal) × %2$s%%', 'octoways'),
				$this->format_admin_currency($breakdown['metal_value']),
				$this->format_admin_number($value)
			);
		}

		if (Metal_Price_Calculator::CHARGE_PER_PIECE === $type) {
			$effective = $value > 0 ? $value : (float) $rates['default_making_charge'];

			return sprintf(
				/* translators: %s: amount */
				__('Fixed %s / piece', 'octoways'),
				$this->format_admin_currency($effective)
			);
		}

		$effective = $value > 0 ? $value : (float) $rates['default_making_charge'];

		return sprintf(
			/* translators: 1: weight 2: rate */
			__('%1$s g × %2$s/g', 'octoways'),
			$this->format_admin_number($breakdown['total_weight']),
			$this->format_admin_currency($effective)
		);
	}

	/**
	 * @param float $amount Amount.
	 * @return string
	 */
	private function format_admin_currency($amount)
	{
		return 'Rs. ' . number_format((float) $amount, 2);
	}

	/**
	 * @param float $number Number.
	 * @return string
	 */
	private function format_admin_number($number)
	{
		$formatted = number_format((float) $number, 2);

		return rtrim(rtrim($formatted, '0'), '.');
	}

	/**
	 * Build formula array from current POST (respects component toggles).
	 *
	 * @return array<string, mixed>|null
	 */
	private function parse_product_formula_from_post()
	{
		$gold_weight     = isset($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$gold_purity     = isset($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: '';
		$silver_weight   = isset($_POST[ Metal_Price_Calculator::META_SILVER_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_SILVER_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$diamond_weight  = isset($_POST[ Metal_Price_Calculator::META_DIAMOND_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_DIAMOND_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$gemstone_qty    = isset($_POST[ Metal_Price_Calculator::META_GEMSTONE_QTY ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GEMSTONE_QTY ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$gemstone_rate   = isset($_POST[ Metal_Price_Calculator::META_GEMSTONE_RATE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GEMSTONE_RATE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$gold_plating    = isset($_POST[ Metal_Price_Calculator::META_GOLD_PLATING_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_PLATING_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$rhodium_plating = isset($_POST[ Metal_Price_Calculator::META_RHODIUM_PLATING_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_RHODIUM_PLATING_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$misc_cost       = isset($_POST[ Metal_Price_Calculator::META_MISC_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_MISC_COST ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$total_weight    = isset($_POST[ Metal_Price_Calculator::META_TOTAL_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_TOTAL_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;
		$charge_type     = isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: Metal_Price_Calculator::CHARGE_PER_GRAM;
		$charge_val      = isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: 0;

		if (!$this->is_component_enabled('gold')) {
			$gold_weight = 0;
			$gold_purity = '';
		}

		if (!$this->is_component_enabled('silver')) {
			$silver_weight = 0;
		}

		if (!$this->is_component_enabled('diamond')) {
			$diamond_weight = 0;
		}

		if (!$this->is_component_enabled('gemstone')) {
			$gemstone_qty  = 0;
			$gemstone_rate = 0;
		}

		if (!$this->is_component_enabled('plating')) {
			$gold_plating    = 0;
			$rhodium_plating = 0;
			$misc_cost       = 0;
		}

		if ($gold_weight > 0 && !$this->calculator->is_valid_gold_purity($gold_purity)) {
			return null;
		}

		if (!$this->calculator->is_valid_charge_type($charge_type)) {
			$charge_type = Metal_Price_Calculator::CHARGE_PER_GRAM;
		}

		if (Metal_Price_Calculator::CHARGE_PER_GRAM !== $charge_type) {
			$total_weight = 0;
		}

		$has_material = ($gold_weight > 0)
			|| ($silver_weight > 0)
			|| ($diamond_weight > 0)
			|| ($gemstone_qty > 0 && $gemstone_rate > 0)
			|| ($gold_plating > 0)
			|| ($rhodium_plating > 0)
			|| ($misc_cost > 0);

		$rates      = $this->rate_store->get_rates();
		$has_making = ($charge_val > 0) || ((float) $rates['default_making_charge'] > 0);

		if (!$has_material && !$has_making) {
			return null;
		}

		if (Metal_Price_Calculator::CHARGE_PER_GRAM === $charge_type && $has_making && $total_weight <= 0) {
			if ($charge_val > 0 || (float) $rates['default_making_charge'] > 0) {
				if (!$has_material) {
					return null;
				}
			}
		}

		if (!$has_material && Metal_Price_Calculator::CHARGE_PER_PIECE !== $charge_type) {
			return null;
		}

		return array(
			'gold_weight'           => max(0, $gold_weight),
			'gold_purity'           => $gold_weight > 0 ? $gold_purity : '',
			'silver_weight'         => max(0, $silver_weight),
			'diamond_weight'        => max(0, $diamond_weight),
			'gemstone_qty'          => max(0, $gemstone_qty),
			'gemstone_rate'         => max(0, $gemstone_rate),
			'gold_plating_cost'     => max(0, $gold_plating),
			'rhodium_plating_cost'  => max(0, $rhodium_plating),
			'misc_cost'             => max(0, $misc_cost),
			'total_weight'          => max(0, $total_weight),
			'making_charge_type'    => $charge_type,
			'making_charge_value'   => max(0, $charge_val),
		);
	}

	/**
	 * @param string $toggle_key Toggle field suffix (gold, silver, …).
	 * @return bool
	 */
	private function is_component_enabled($toggle_key)
	{
		$field = 'ht_use_' . $toggle_key;

		return !empty($_POST[ $field ]); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	}

	/**
	 * @param int $post_id Product ID.
	 */
	public function save_product_fields($post_id)
	{
		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		$formula = $this->parse_product_formula_from_post();

		if (null === $formula) {
			$gold_weight = isset($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				? (float) wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_WEIGHT ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				: 0;
			$gold_purity = isset($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_GOLD_PURITY ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				: '';

			if ($gold_weight > 0 && $this->is_component_enabled('gold') && !$this->calculator->is_valid_gold_purity($gold_purity)) {
				add_filter(
					'redirect_post_location',
					static function ($location) {
						return add_query_arg('ht_metal_purity_error', '1', $location);
					}
				);
				return;
			}

			$charge_type = isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				? sanitize_text_field(wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_TYPE ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
				: Metal_Price_Calculator::CHARGE_PER_GRAM;

			if (!$this->calculator->is_valid_charge_type($charge_type)) {
				$charge_type = Metal_Price_Calculator::CHARGE_PER_GRAM;
			}

			$formula = array(
				'gold_weight'          => 0,
				'gold_purity'          => '',
				'silver_weight'        => 0,
				'diamond_weight'       => 0,
				'gemstone_qty'         => 0,
				'gemstone_rate'        => 0,
				'gold_plating_cost'    => 0,
				'rhodium_plating_cost' => 0,
				'misc_cost'            => 0,
				'total_weight'         => 0,
				'making_charge_type'   => $charge_type,
				'making_charge_value'  => isset($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ]) // phpcs:ignore WordPress.Security.NonceVerification.Missing
					? max(0, (float) wp_unslash($_POST[ Metal_Price_Calculator::META_MAKING_CHARGE_VALUE ])) // phpcs:ignore WordPress.Security.NonceVerification.Missing
					: 0,
			);
		}

		update_post_meta($post_id, Metal_Price_Calculator::META_GOLD_WEIGHT, $formula['gold_weight']);
		update_post_meta($post_id, Metal_Price_Calculator::META_GOLD_PURITY, $formula['gold_purity']);
		update_post_meta($post_id, Metal_Price_Calculator::META_SILVER_WEIGHT, $formula['silver_weight']);
		update_post_meta($post_id, Metal_Price_Calculator::META_DIAMOND_WEIGHT, $formula['diamond_weight']);
		update_post_meta($post_id, Metal_Price_Calculator::META_GEMSTONE_QTY, $formula['gemstone_qty']);
		update_post_meta($post_id, Metal_Price_Calculator::META_GEMSTONE_RATE, $formula['gemstone_rate']);
		update_post_meta($post_id, Metal_Price_Calculator::META_GOLD_PLATING_COST, $formula['gold_plating_cost']);
		update_post_meta($post_id, Metal_Price_Calculator::META_RHODIUM_PLATING_COST, $formula['rhodium_plating_cost']);
		update_post_meta($post_id, Metal_Price_Calculator::META_MISC_COST, $formula['misc_cost']);
		update_post_meta($post_id, Metal_Price_Calculator::META_TOTAL_WEIGHT, $formula['total_weight']);
		update_post_meta($post_id, Metal_Price_Calculator::META_MAKING_CHARGE_TYPE, $formula['making_charge_type']);
		update_post_meta($post_id, Metal_Price_Calculator::META_MAKING_CHARGE_VALUE, $formula['making_charge_value']);

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
				echo esc_html__('Product not saved: gold purity must be 9K, 14K, 18K, 22K, or 24K when gold weight is set.', 'octoways');
				echo '</p></div>';
			}
		);
	}
}
