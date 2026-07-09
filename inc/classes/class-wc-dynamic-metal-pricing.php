<?php
/**
 * WooCommerce integration for dynamic multi-material pricing.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Hooks runtime pricing, cart protection, and order snapshots.
 */
class WC_Dynamic_Metal_Pricing
{
	const CART_META_VERSION          = 'ht_rate_version';
	const CART_META_CALCULATED_PRICE = 'ht_snapshot_calculated_price';
	const CART_META_BREAKDOWN        = 'ht_snapshot_breakdown';

	const ORDER_META_GOLD_RATE       = '_order_gold_rate';
	const ORDER_META_SILVER_RATE     = '_order_silver_rate';
	const ORDER_META_DIAMOND_RATE    = '_order_diamond_rate';
	const ORDER_META_RATE_VERSION    = '_order_rate_version';
	const ORDER_META_TOTAL_WEIGHT    = '_order_total_weight';
	const ORDER_META_MAKING_CHARGE   = '_order_making_charge';
	const ORDER_META_GOLD_COST       = '_order_gold_cost';
	const ORDER_META_SILVER_COST     = '_order_silver_cost';
	const ORDER_META_DIAMOND_COST    = '_order_diamond_cost';
	const ORDER_META_GEMSTONE_COST   = '_order_gemstone_cost';
	const ORDER_META_GOLD_PLATING    = '_order_gold_plating_cost';
	const ORDER_META_RHODIUM_PLATING = '_order_rhodium_plating_cost';
	const ORDER_META_MISC_COST       = '_order_misc_cost';
	const ORDER_META_FINAL_PRICE     = '_order_final_price';
	const ORDER_META_GOLD_PURITY     = '_order_gold_purity';

	const CART_META_NPR_PRICE = 'ht_snapshot_npr_price';

	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @var Metal_Price_Calculator
	 */
	private $calculator;

	/**
	 * @var bool
	 */
	private $calculating = false;

	/**
	 * Modal payload for wp_footer render.
	 *
	 * @var array<string, mixed>|null
	 */
	private $product_modal_context = null;

	/**
	 * @param Metal_Rate_Store|null       $rate_store Rate store.
	 * @param Metal_Price_Calculator|null $calculator Calculator.
	 */
	public function __construct(Metal_Rate_Store $rate_store = null, Metal_Price_Calculator $calculator = null)
	{
		$this->rate_store = $rate_store ?: new Metal_Rate_Store();
		$this->calculator = $calculator ?: new Metal_Price_Calculator($this->rate_store);
	}

	/**
	 * Register WooCommerce hooks.
	 */
	public function register()
	{
		add_filter('woocommerce_product_get_price', array($this, 'filter_product_price'), 99, 2);
		add_filter('woocommerce_product_get_regular_price', array($this, 'filter_product_price'), 99, 2);
		add_filter('woocommerce_product_variation_get_price', array($this, 'filter_product_price'), 99, 2);
		add_filter('woocommerce_product_variation_get_regular_price', array($this, 'filter_product_price'), 99, 2);

		add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_snapshot'), 10, 3);
		add_filter('woocommerce_get_cart_item_from_session', array($this, 'restore_cart_item_snapshot'), 10, 2);

		add_action('woocommerce_before_calculate_totals', array($this, 'recalculate_cart_line_prices'), 20, 1);

		add_action('woocommerce_checkout_process', array($this, 'validate_checkout_rate_version'));
		add_action('woocommerce_before_checkout_form', array($this, 'maybe_show_rate_refresh_notice'), 5);

		add_action('woocommerce_checkout_create_order_line_item', array($this, 'snapshot_order_line_item'), 10, 4);
		add_action('woocommerce_checkout_create_order', array($this, 'snapshot_order_rates'), 10, 2);

		add_action('woocommerce_payment_complete', array($this, 'lock_order_pricing'), 5, 1);
		add_action('woocommerce_checkout_order_processed', array($this, 'lock_order_pricing'), 5, 1);

		add_action('template_redirect', array($this, 'exclude_dynamic_pages_from_cache'));

		add_action('woocommerce_single_product_summary', array($this, 'render_product_pricing_breakdown'), 11);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_product_breakdown_assets'), 99);
		add_action('wp_footer', array($this, 'render_product_pricing_modal_footer'), 20);
		add_action('woocommerce_order_item_meta_end', array($this, 'display_order_item_snapshot'), 10, 3);
	}

	/**
	 * Scripts for product pricing breakdown modal.
	 */
	public function enqueue_product_breakdown_assets()
	{
		if (!function_exists('is_product') || !is_product()) {
			return;
		}

		$product_id = get_queried_object_id();

		if (!$product_id || !$this->calculator->is_dynamic_product($product_id)) {
			return;
		}

		if (!defined('OCTOWAYS_BUILD_PATH') || !defined('OCTOWAYS_BUILD_JS_URI')) {
			return;
		}

		$script_path = OCTOWAYS_BUILD_PATH . '/js/metalPricingBreakdown.js';
		$script_uri  = OCTOWAYS_BUILD_JS_URI . '/metalPricingBreakdown.js';

		if (!file_exists($script_path)) {
			return;
		}

		wp_enqueue_script(
			'ht-metal-pricing-breakdown',
			$script_uri,
			array('jquery'),
			filemtime($script_path),
			true
		);
	}

	/**
	 * @param float       $price   Price.
	 * @param \WC_Product $product Product.
	 * @return float|string
	 */
	public function filter_product_price($price, $product)
	{
		if (!$product instanceof \WC_Product) {
			return $price;
		}

		$calculated = $this->calculator->calculate_for_product($product->get_id());

		if (null === $calculated) {
			return $price;
		}

		return $calculated;
	}

	/**
	 * @param array $cart_item_data Cart item data.
	 * @param int   $product_id     Product ID.
	 * @param int   $variation_id   Variation ID.
	 * @return array
	 */
	public function add_cart_item_snapshot($cart_item_data, $product_id, $variation_id)
	{
		$id = $variation_id ? $variation_id : $product_id;

		if (!$this->calculator->is_dynamic_product($id)) {
			return $cart_item_data;
		}

		$snapshot = $this->build_cart_snapshot($id);

		return $snapshot ? array_merge($cart_item_data, $snapshot) : $cart_item_data;
	}

	/**
	 * @param array $cart_item Cart item.
	 * @param array $values    Session values.
	 * @return array
	 */
	public function restore_cart_item_snapshot($cart_item, $values)
	{
		foreach (array(self::CART_META_VERSION, self::CART_META_CALCULATED_PRICE, self::CART_META_NPR_PRICE, self::CART_META_BREAKDOWN) as $key) {
			if (isset($values[ $key ])) {
				$cart_item[ $key ] = $values[ $key ];
			}
		}

		return $cart_item;
	}

	/**
	 * @param \WC_Cart $cart Cart.
	 */
	public function recalculate_cart_line_prices($cart)
	{
		if ($this->calculating || (is_admin() && !defined('DOING_AJAX')) || !$cart instanceof \WC_Cart) {
			return;
		}

		$this->calculating = true;

		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			if (empty($cart_item['data']) || !$cart_item['data'] instanceof \WC_Product) {
				continue;
			}

			$variation = !empty($cart_item['variation_id']) ? (int) $cart_item['variation_id'] : 0;
			$id        = $variation ? $variation : (int) $cart_item['product_id'];

			if (!$this->calculator->is_dynamic_product($id)) {
				continue;
			}

			$snapshot = $this->build_cart_snapshot($id);

			if (!$snapshot) {
				continue;
			}

			foreach ($snapshot as $key => $value) {
				$cart->cart_contents[ $cart_item_key ][ $key ] = $value;
			}

			$cart_item['data']->set_price($snapshot[ self::CART_META_CALCULATED_PRICE ]);
		}

		$this->calculating = false;
	}

	/**
	 * @param int $product_id Product ID.
	 * @return array<string, mixed>|null
	 */
	private function build_cart_snapshot($product_id)
	{
		$breakdown = $this->calculator->get_breakdown_for_product($product_id);

		if (!$breakdown) {
			return null;
		}

		$rates = $this->rate_store->get_rates();

		return array(
			self::CART_META_VERSION          => (int) $breakdown['rate_version'],
			self::CART_META_CALCULATED_PRICE => (float) $breakdown['final_price'],
			self::CART_META_NPR_PRICE        => (float) $breakdown['final_price'],
			self::CART_META_BREAKDOWN        => wp_json_encode(
				array(
					'gold_cost'            => $breakdown['gold_cost'],
					'silver_cost'          => $breakdown['silver_cost'],
					'diamond_cost'         => $breakdown['diamond_cost'],
					'gemstone_cost'        => $breakdown['gemstone_cost'],
					'gold_plating_cost'    => $breakdown['gold_plating_cost_calc'],
					'rhodium_plating_cost' => $breakdown['rhodium_plating_cost_calc'],
					'misc_cost'            => $breakdown['misc_cost_calc'],
					'making_charge'        => $breakdown['making_charge'],
					'gold_purity'          => $breakdown['gold_purity'],
					'gold_rate_24k'        => $rates['gold_rate_24k'],
					'silver_rate'          => $rates['silver_rate'],
					'diamond_rate'         => $rates['diamond_rate'],
				)
			),
		);
	}

	/**
	 * Checkout guard: refresh cart if global rate version changed.
	 */
	public function validate_checkout_rate_version()
	{
		if (!function_exists('WC') || !WC()->cart) {
			return;
		}

		$current_version = $this->rate_store->get_version();
		$mismatch        = false;

		foreach (WC()->cart->get_cart() as $cart_item) {
			if (empty($cart_item[ self::CART_META_VERSION ])) {
				continue;
			}

			if ((int) $cart_item[ self::CART_META_VERSION ] !== $current_version) {
				$mismatch = true;
				break;
			}
		}

		if (!$mismatch) {
			return;
		}

		WC()->cart->calculate_totals();

		Metal_Rate_Store::log(
			'version_mismatch',
			'Cart repriced due to material rate version change at checkout.',
			array('current_version' => $current_version)
		);

		wc_add_notice(
			__('Material rates updated. Your cart has been refreshed using the latest pricing.', 'octoways'),
			'notice'
		);

		if (WC()->session) {
			WC()->session->set('ht_metal_rate_refresh_notice', 1);
		}
	}

	/**
	 * Clear session flag after checkout notice shown.
	 */
	public function maybe_show_rate_refresh_notice()
	{
		if (!function_exists('WC') || !WC()->session) {
			return;
		}

		if (!WC()->session->get('ht_metal_rate_refresh_notice')) {
			return;
		}

		WC()->session->set('ht_metal_rate_refresh_notice', null);
	}

	/**
	 * @param \WC_Order_Item_Product $item          Line item.
	 * @param string                 $cart_item_key Cart key.
	 * @param array                  $values        Cart values.
	 * @param \WC_Order              $order         Order.
	 */
	public function snapshot_order_line_item($item, $cart_item_key, $values, $order)
	{
		$product_id = !empty($values['variation_id']) ? (int) $values['variation_id'] : (int) $values['product_id'];

		if (!$this->calculator->is_dynamic_product($product_id)) {
			return;
		}

		$breakdown = $this->calculator->get_breakdown_for_product($product_id);

		if (!$breakdown) {
			return;
		}

		$rates = $this->rate_store->get_rates();

		$item->add_meta_data(self::ORDER_META_GOLD_RATE, (float) $rates['gold_rate_24k'], true);
		$item->add_meta_data(self::ORDER_META_SILVER_RATE, (float) $rates['silver_rate'], true);
		$item->add_meta_data(self::ORDER_META_DIAMOND_RATE, (float) $rates['diamond_rate'], true);
		$item->add_meta_data(self::ORDER_META_RATE_VERSION, (int) $rates['rate_version'], true);
		$item->add_meta_data(self::ORDER_META_TOTAL_WEIGHT, (float) $breakdown['total_weight'], true);
		$item->add_meta_data(self::ORDER_META_MAKING_CHARGE, (float) $breakdown['making_charge'], true);
		$item->add_meta_data(self::ORDER_META_GOLD_COST, (float) $breakdown['gold_cost'], true);
		$item->add_meta_data(self::ORDER_META_SILVER_COST, (float) $breakdown['silver_cost'], true);
		$item->add_meta_data(self::ORDER_META_DIAMOND_COST, (float) $breakdown['diamond_cost'], true);
		$item->add_meta_data(self::ORDER_META_GEMSTONE_COST, (float) $breakdown['gemstone_cost'], true);
		$item->add_meta_data(self::ORDER_META_GOLD_PLATING, (float) $breakdown['gold_plating_cost_calc'], true);
		$item->add_meta_data(self::ORDER_META_RHODIUM_PLATING, (float) $breakdown['rhodium_plating_cost_calc'], true);
		$item->add_meta_data(self::ORDER_META_MISC_COST, (float) $breakdown['misc_cost_calc'], true);
		$item->add_meta_data(self::ORDER_META_FINAL_PRICE, (float) $breakdown['final_price'], true);
		$item->add_meta_data(WC_Currency_Integration::ORDER_META_NPR_FINAL_PRICE, (float) $breakdown['final_price'], true);

		if (!empty($breakdown['gold_purity'])) {
			$item->add_meta_data(self::ORDER_META_GOLD_PURITY, $breakdown['gold_purity'], true);
		}

		$qty      = max(1, (int) $item->get_quantity());
		$npr_unit = (float) $breakdown['final_price'];
		$currency = isset($values[ WC_Currency_Integration::CART_META_DISPLAY_CURRENCY ])
			? (string) $values[ WC_Currency_Integration::CART_META_DISPLAY_CURRENCY ]
			: Currency_Context::instance()->get_display_currency();

		if (Currency_Context::CURRENCY_USD === $currency) {
			$converter    = new Currency_Converter($this->rate_store);
			$display_unit = $converter->convert_for_display($npr_unit, Currency_Context::CURRENCY_USD);
			$item->add_meta_data(WC_Currency_Integration::ORDER_META_DISPLAY_CURRENCY, Currency_Context::CURRENCY_USD, true);
			$item->add_meta_data(WC_Currency_Integration::ORDER_META_FX_RATE, $converter->get_npr_per_usd(), true);
		} else {
			$display_unit = ( new Currency_Converter($this->rate_store) )->round_npr($npr_unit);
			$item->add_meta_data(WC_Currency_Integration::ORDER_META_DISPLAY_CURRENCY, Currency_Context::CURRENCY_NPR, true);
		}

		$line_total = $display_unit * $qty;

		$item->set_subtotal($line_total);
		$item->set_total($line_total);
	}

	/**
	 * @param \WC_Order $order Order.
	 * @param array     $data  Checkout data.
	 */
	public function snapshot_order_rates($order, $data)
	{
		$rates = $this->rate_store->get_rates();

		$order->update_meta_data(self::ORDER_META_GOLD_RATE, (float) $rates['gold_rate_24k']);
		$order->update_meta_data(self::ORDER_META_SILVER_RATE, (float) $rates['silver_rate']);
		$order->update_meta_data(self::ORDER_META_DIAMOND_RATE, (float) $rates['diamond_rate']);
		$order->update_meta_data(self::ORDER_META_RATE_VERSION, (int) $rates['rate_version']);
	}

	/**
	 * @param int $order_id Order ID.
	 */
	public function lock_order_pricing($order_id)
	{
		$order = wc_get_order($order_id);

		if (!$order) {
			return;
		}

		$order->update_meta_data('_ht_metal_pricing_locked', 'yes');
		$order->save();
	}

	/**
	 * Prevent full-page cache on cart/checkout.
	 */
	public function exclude_dynamic_pages_from_cache()
	{
		if (!function_exists('is_cart') || !function_exists('is_checkout')) {
			return;
		}

		if (is_cart() || is_checkout()) {
			if (!defined('DONOTCACHEPAGE')) {
				define('DONOTCACHEPAGE', true);
			}
		}
	}

	/**
	 * Compact price hint + modal breakdown on single product page.
	 */
	public function render_product_pricing_breakdown()
	{
		global $product;

		if (!$product instanceof \WC_Product) {
			return;
		}

		$breakdown = $this->calculator->get_breakdown_for_product($product->get_id());

		if (!$breakdown) {
			return;
		}

		$making_label = $this->get_making_charge_label($breakdown['making_charge_type']);

		$guide_page = get_page_by_path('how-pricing-works');
		$guide_url  = $guide_page ? get_permalink($guide_page) : '';

		$this->product_modal_context = array(
			'breakdown'    => $breakdown,
			'making_label' => $making_label,
			'guide_url'    => $guide_url,
		);
		?>
		<p class="ht-metal-price-hint">
			<span class="ht-metal-price-hint__label"><?php esc_html_e('Live material pricing', 'octoways'); ?></span>
			<button
				type="button"
				class="ht-metal-price-hint__trigger"
				data-ht-open-metal-pricing
				aria-haspopup="dialog"
				aria-controls="ht-metal-pricing-modal"
				aria-label="<?php esc_attr_e('How is this price calculated?', 'octoways'); ?>"
			>
				<svg class="ht-metal-price-hint__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
					<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/>
					<path d="M12 17v.5M12 7.5a2.5 2.5 0 0 1 2.5 2.5c0 1.5-2.5 1.75-2.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
				</svg>
				<span class="ht-metal-price-hint__trigger-text"><?php esc_html_e('Price breakdown', 'octoways'); ?></span>
			</button>
		</p>
		<p class="ht-metal-price-hint__disclaimer description">
			<?php esc_html_e('Based on current material rates and product configuration. May update at checkout.', 'octoways'); ?>
		</p>
		<?php
	}

	/**
	 * Modal at document root (avoids overflow/transform traps) + inline controller script.
	 */
	public function render_product_pricing_modal_footer()
	{
		if (empty($this->product_modal_context)) {
			return;
		}

		$breakdown    = $this->product_modal_context['breakdown'];
		$making_label = $this->product_modal_context['making_label'];
		$guide_url    = $this->product_modal_context['guide_url'];
		?>
		<div
			id="ht-metal-pricing-modal"
			class="ht-metal-pricing-modal ht-floating-modal"
			aria-hidden="true"
			role="dialog"
			aria-modal="true"
			aria-labelledby="ht-metal-pricing-modal-title"
		>
			<div class="ht-floating-modal__overlay" data-ht-close-metal-pricing tabindex="-1"></div>
			<div class="ht-floating-modal__dialog ht-metal-pricing-modal__dialog">
				<button
					type="button"
					class="ht-floating-modal__close ht-metal-pricing-modal__close"
					data-ht-close-metal-pricing
					aria-label="<?php esc_attr_e('Close', 'octoways'); ?>"
				>
					<span aria-hidden="true">&times;</span>
				</button>

				<div class="ht-metal-pricing-modal__header">
					<h2 id="ht-metal-pricing-modal-title" class="ht-metal-pricing-modal__title">
						<?php esc_html_e('How this price is calculated', 'octoways'); ?>
					</h2>
					<p class="ht-metal-pricing-modal__intro normal-text">
						<?php esc_html_e('Your price is built from live material rates and this product’s weights — shown below for transparency.', 'octoways'); ?>
					</p>
				</div>

				<div class="ht-metal-pricing-modal__body">
					<?php $this->render_breakdown_list($breakdown, $making_label); ?>
				</div>

				<div class="ht-metal-pricing-modal__footer">
					<p class="ht-metal-pricing-breakdown__disclaimer description">
						<?php esc_html_e('Prices are based on live material rates and may update during checkout.', 'octoways'); ?>
					</p>
					<?php if (Currency_Context::instance()->is_usd()) : ?>
						<p class="ht-metal-pricing-breakdown__disclaimer description">
							<?php esc_html_e('Material rates and component costs are shown in NPR. The estimated total is converted at the current exchange rate.', 'octoways'); ?>
						</p>
					<?php endif; ?>
					<?php if ($guide_url) : ?>
						<a class="ht-metal-pricing-modal__link" href="<?php echo esc_url($guide_url); ?>">
							<?php esc_html_e('Learn more about our pricing', 'octoways'); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<script>
		(function () {
			var modal = document.getElementById('ht-metal-pricing-modal');
			if (!modal) {
				return;
			}
			var lastFocus = null;
			function openModal() {
				lastFocus = document.activeElement;
				modal.classList.add('is-open');
				modal.setAttribute('aria-hidden', 'false');
				document.body.style.overflow = 'hidden';
				var closeBtn = modal.querySelector('.ht-floating-modal__close');
				if (closeBtn) {
					closeBtn.focus();
				}
			}
			function closeModal() {
				modal.classList.remove('is-open');
				modal.setAttribute('aria-hidden', 'true');
				document.body.style.overflow = '';
				if (lastFocus && typeof lastFocus.focus === 'function') {
					lastFocus.focus();
				}
			}
			document.addEventListener('click', function (e) {
				var openBtn = e.target.closest('[data-ht-open-metal-pricing]');
				if (openBtn) {
					e.preventDefault();
					openModal();
					return;
				}
				var closeEl = e.target.closest('[data-ht-close-metal-pricing]');
				if (closeEl) {
					e.preventDefault();
					closeModal();
				}
			});
			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape' && modal.classList.contains('is-open')) {
					e.preventDefault();
					closeModal();
				}
			});
		})();
		</script>
		<?php
	}

	/**
	 * Breakdown list markup (used inside modal).
	 *
	 * @param array<string, mixed> $breakdown    Breakdown data.
	 * @param string               $making_label Making charge label.
	 */
	private function render_breakdown_list(array $breakdown, $making_label)
	{
		$converter   = new Currency_Converter($this->rate_store);
		$format_npr  = static function ($amount) use ($converter) {
			return $converter->format_npr($amount);
		};
		$is_usd      = Currency_Context::instance()->is_usd();
		$final_price = $is_usd
			? $converter->convert_for_display((float) $breakdown['final_price'])
			: (float) $breakdown['final_price'];
		?>
		<ul class="ht-metal-pricing-breakdown__list">
			<?php if ($breakdown['gold_weight'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Gold (24K base rate / g)', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['gold_rate_24k'])); ?></strong>
				</li>
				<li>
					<span><?php printf(/* translators: %s: purity */ esc_html__('Gold %s effective rate / g', 'octoways'), esc_html($breakdown['gold_purity'])); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['gold_effective_rate'])); ?></strong>
				</li>
				<li>
					<span><?php printf(/* translators: %s: weight */ esc_html__('Gold weight (%sg)', 'octoways'), esc_html((string) $breakdown['gold_weight'])); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['gold_cost'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['silver_weight'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Silver rate / g', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['silver_rate'])); ?></strong>
				</li>
				<li>
					<span><?php printf(/* translators: %s: weight */ esc_html__('Silver (%sg)', 'octoways'), esc_html((string) $breakdown['silver_weight'])); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['silver_cost'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['diamond_weight'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Diamond rate / ct', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['diamond_rate'])); ?></strong>
				</li>
				<li>
					<span><?php printf(/* translators: %s: carats */ esc_html__('Diamond (%s ct)', 'octoways'), esc_html((string) $breakdown['diamond_weight'])); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['diamond_cost'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['gemstone_cost'] > 0) : ?>
				<li>
					<span><?php printf(/* translators: 1: qty 2: rate */ esc_html__('Gemstone (%1$s × %2$s)', 'octoways'), esc_html((string) $breakdown['gemstone_qty']), esc_html(number_format($breakdown['gemstone_rate'], 2))); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['gemstone_cost'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['gold_plating_cost_calc'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Gold plating', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['gold_plating_cost_calc'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['rhodium_plating_cost_calc'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Rhodium plating', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['rhodium_plating_cost_calc'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if ($breakdown['misc_cost_calc'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Miscellaneous', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['misc_cost_calc'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if (Metal_Price_Calculator::CHARGE_PERCENTAGE === $breakdown['making_charge_type'] && $breakdown['metal_value'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Metal value (gold + silver)', 'octoways'); ?></span>
					<strong><?php echo wp_kses_post($format_npr($breakdown['metal_value'])); ?></strong>
				</li>
			<?php endif; ?>

			<?php if (Metal_Price_Calculator::CHARGE_PER_GRAM === $breakdown['making_charge_type'] && $breakdown['total_weight'] > 0) : ?>
				<li>
					<span><?php esc_html_e('Total weight (making)', 'octoways'); ?></span>
					<strong><?php echo esc_html($breakdown['total_weight'] . ' g'); ?></strong>
				</li>
			<?php endif; ?>

			<li>
				<span><?php echo esc_html($making_label); ?></span>
				<strong>
					<?php
					if (Metal_Price_Calculator::CHARGE_PERCENTAGE === $breakdown['making_charge_type']) {
						echo esc_html($breakdown['making_charge_value'] . '%');
					} else {
						echo wp_kses_post($format_npr($breakdown['making_charge_value'] ?: $this->rate_store->get_rates()['default_making_charge']));
					}
					?>
				</strong>
			</li>
			<li>
				<span><?php esc_html_e('Making charge total', 'octoways'); ?></span>
				<strong><?php echo wp_kses_post($format_npr($breakdown['making_charge'])); ?></strong>
			</li>
			<li class="ht-metal-pricing-breakdown__total">
				<span><?php esc_html_e('Estimated total', 'octoways'); ?></span>
				<strong><?php echo wp_kses_post($converter->format_amount($final_price)); ?></strong>
			</li>
		</ul>
		<?php
	}

	/**
	 * @param string $charge_type Making charge type.
	 * @return string
	 */
	private function get_making_charge_label($charge_type)
	{
		if (Metal_Price_Calculator::CHARGE_PER_GRAM === $charge_type) {
			return __('Making charge (per gram)', 'octoways');
		}

		if (Metal_Price_Calculator::CHARGE_PERCENTAGE === $charge_type) {
			return __('Making charge (percentage)', 'octoways');
		}

		return __('Making charge (per piece)', 'octoways');
	}

	/**
	 * @param int                    $item_id Item ID.
	 * @param \WC_Order_Item_Product $item    Item.
	 * @param \WC_Order              $order   Order.
	 */
	public function display_order_item_snapshot($item_id, $item, $order)
	{
		if (!$item instanceof \WC_Order_Item_Product) {
			return;
		}

		$final     = $item->get_meta(self::ORDER_META_FINAL_PRICE, true);
		$npr_final = $item->get_meta(WC_Currency_Integration::ORDER_META_NPR_FINAL_PRICE, true);
		$currency  = $item->get_meta(WC_Currency_Integration::ORDER_META_DISPLAY_CURRENCY, true);
		$fx_rate   = $item->get_meta(WC_Currency_Integration::ORDER_META_FX_RATE, true);

		if ('' === $final || null === $final) {
			return;
		}

		$purity  = $item->get_meta(self::ORDER_META_GOLD_PURITY, true);
		$version = $item->get_meta(self::ORDER_META_RATE_VERSION, true);

		echo '<div class="ht-order-metal-snapshot"><small>';
		echo esc_html__('Pricing snapshot', 'octoways');
		if ($purity) {
			echo ' (' . esc_html($purity) . ')';
		}
		echo ': ';

		if (Currency_Context::CURRENCY_USD === $currency && '' !== $npr_final && null !== $npr_final) {
			$paid_unit = (float) $item->get_total() / max(1, (int) $item->get_quantity());
			echo wp_kses_post(wc_price($paid_unit, array('currency' => 'USD')));
			echo ' ';
			echo esc_html__('(NPR reference:', 'octoways') . ' ';
			echo wp_kses_post(wc_price((float) $npr_final, array('currency' => 'NPR')));
			if ($fx_rate) {
				printf(', %s %s', esc_html__('rate', 'octoways'), esc_html((string) $fx_rate));
			}
			echo ')';
		} else {
			echo wp_kses_post(wc_price((float) $final, array('currency' => 'NPR')));
		}

		echo ' ';
		printf('(%s %s)', esc_html__('rate v', 'octoways'), esc_html((string) $version));
		echo '</small></div>';
	}
}
