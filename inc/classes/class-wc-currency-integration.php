<?php
/**
 * WooCommerce integration for NPR/USD display and checkout currency.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Applies currency conversion after dynamic metal pricing.
 */
class WC_Currency_Integration
{
	const CART_META_NPR_PRICE     = 'ht_snapshot_npr_price';
	const CART_META_DISPLAY_CURRENCY = 'ht_display_currency';
	const CART_META_FX_RATE       = 'ht_fx_rate_used';
	const CART_META_FX_VERSION    = 'ht_fx_rate_version';

	const ORDER_META_DISPLAY_CURRENCY = '_order_display_currency';
	const ORDER_META_FX_RATE          = '_order_fx_rate_npr_per_usd';
	const ORDER_META_NPR_FINAL_PRICE  = '_order_npr_final_price';

	/**
	 * @var Currency_Context
	 */
	private $context;

	/**
	 * @var Currency_Converter
	 */
	private $converter;

	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @var bool
	 */
	private $converting = false;

	/**
	 * @param Currency_Context|null   $context   Currency context.
	 * @param Currency_Converter|null $converter Converter.
	 * @param Metal_Rate_Store|null   $rate_store Rate store.
	 */
	public function __construct(
		Currency_Context $context = null,
		Currency_Converter $converter = null,
		Metal_Rate_Store $rate_store = null
	) {
		$this->rate_store = $rate_store ?: new Metal_Rate_Store();
		$this->context    = $context ?: Currency_Context::instance();
		$this->converter  = $converter ?: new Currency_Converter($this->rate_store, $this->context);
	}

	/**
	 * Register WooCommerce hooks.
	 */
	public function register()
	{
		add_filter('woocommerce_currency', array($this, 'filter_currency'), 20);
		add_filter('woocommerce_currency_symbol', array($this, 'filter_currency_symbol'), 20, 2);

		add_filter('woocommerce_product_get_price', array($this, 'filter_product_price'), 100, 2);
		add_filter('woocommerce_product_get_regular_price', array($this, 'filter_product_price'), 100, 2);
		add_filter('woocommerce_product_variation_get_price', array($this, 'filter_product_price'), 100, 2);
		add_filter('woocommerce_product_variation_get_regular_price', array($this, 'filter_product_price'), 100, 2);

		add_action('woocommerce_before_calculate_totals', array($this, 'convert_cart_line_prices'), 25, 1);

		add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_currency_meta'), 20, 3);
		add_filter('woocommerce_get_cart_item_from_session', array($this, 'restore_cart_currency_meta'), 20, 2);

		add_action('woocommerce_checkout_process', array($this, 'validate_checkout_fx_version'));
		add_action('woocommerce_checkout_process', array($this, 'validate_checkout_payment_currency'));
		add_action('woocommerce_before_checkout_form', array($this, 'maybe_show_usd_checkout_notice'), 6);
		add_action('woocommerce_before_checkout_form', array($this, 'maybe_show_fx_refresh_notice'), 7);

		add_action('woocommerce_checkout_create_order', array($this, 'snapshot_order_currency'), 10, 2);

		add_filter('woocommerce_available_payment_gateways', array($this, 'filter_payment_gateways'));

		add_action('wp_enqueue_scripts', array($this, 'enqueue_currency_switcher_assets'), 99);
	}

	/**
	 * @param string $currency Store currency.
	 * @return string
	 */
	public function filter_currency($currency)
	{
		if ($this->context->is_usd() && $this->converter->is_usd_available()) {
			return 'USD';
		}

		return $currency;
	}

	/**
	 * @param string $symbol   Currency symbol.
	 * @param string $currency Currency code.
	 * @return string
	 */
	public function filter_currency_symbol($symbol, $currency)
	{
		if ('USD' === $currency) {
			return '$';
		}

		if ('NPR' === $currency) {
			return 'Rs.';
		}

		return $symbol;
	}

	/**
	 * @param float|string $price   Price after metal pricing.
	 * @param \WC_Product  $product Product.
	 * @return float|string
	 */
	public function filter_product_price($price, $product)
	{
		unset($product);

		if ('' === $price || null === $price) {
			return $price;
		}

		if ($this->context->is_usd() && !$this->converter->is_usd_available()) {
			return $this->converter->round_npr((float) $price);
		}

		return $this->converter->convert_for_display((float) $price);
	}

	/**
	 * @param \WC_Cart $cart Cart.
	 */
	public function convert_cart_line_prices($cart)
	{
		if ($this->converting || (is_admin() && !defined('DOING_AJAX')) || !$cart instanceof \WC_Cart) {
			return;
		}

		$this->converting = true;

		$currency   = $this->context->get_display_currency();
		$npr_per_usd = $this->converter->get_npr_per_usd();
		$fx_version = $this->converter->get_fx_version();

		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			if (empty($cart_item['data']) || !$cart_item['data'] instanceof \WC_Product) {
				continue;
			}

			$npr_price = 0.0;

			if (isset($cart_item[ WC_Dynamic_Metal_Pricing::CART_META_NPR_PRICE ])) {
				$npr_price = (float) $cart_item[ WC_Dynamic_Metal_Pricing::CART_META_NPR_PRICE ];
			} elseif (isset($cart_item[ WC_Dynamic_Metal_Pricing::CART_META_CALCULATED_PRICE ])) {
				$npr_price = (float) $cart_item[ WC_Dynamic_Metal_Pricing::CART_META_CALCULATED_PRICE ];
			} elseif (isset($cart_item[ self::CART_META_NPR_PRICE ])) {
				$npr_price = (float) $cart_item[ self::CART_META_NPR_PRICE ];
			} elseif (!empty($cart_item['data'])) {
				$npr_price = (float) $cart_item['data']->get_price('edit');
			}

			$display_price = $this->converter->convert_for_display($npr_price, $currency);

			$cart->cart_contents[ $cart_item_key ][ self::CART_META_NPR_PRICE ]        = $npr_price;
			$cart->cart_contents[ $cart_item_key ][ self::CART_META_DISPLAY_CURRENCY ] = $currency;
			$cart->cart_contents[ $cart_item_key ][ self::CART_META_FX_RATE ]          = $npr_per_usd;
			$cart->cart_contents[ $cart_item_key ][ self::CART_META_FX_VERSION ]       = $fx_version;

			$cart_item['data']->set_price($display_price);
		}

		$this->converting = false;
	}

	/**
	 * @param array $cart_item_data Cart item data.
	 * @param int   $product_id     Product ID.
	 * @param int   $variation_id   Variation ID.
	 * @return array
	 */
	public function add_cart_currency_meta($cart_item_data, $product_id, $variation_id)
	{
		$npr_price = 0.0;

		if (isset($cart_item_data[ WC_Dynamic_Metal_Pricing::CART_META_CALCULATED_PRICE ])) {
			$npr_price = (float) $cart_item_data[ WC_Dynamic_Metal_Pricing::CART_META_CALCULATED_PRICE ];
		} elseif (isset($cart_item_data[ WC_Dynamic_Metal_Pricing::CART_META_NPR_PRICE ])) {
			$npr_price = (float) $cart_item_data[ WC_Dynamic_Metal_Pricing::CART_META_NPR_PRICE ];
		} elseif (function_exists('wc_get_product')) {
			$id      = $variation_id ? $variation_id : $product_id;
			$product = wc_get_product($id);

			if ($product) {
				$npr_price = (float) $product->get_price('edit');
			}
		}

		return array_merge($cart_item_data, $this->build_currency_meta_from_npr($npr_price));
	}

	/**
	 * @param array $cart_item Cart item.
	 * @param array $values    Session values.
	 * @return array
	 */
	public function restore_cart_currency_meta($cart_item, $values)
	{
		foreach (
			array(
				self::CART_META_NPR_PRICE,
				self::CART_META_DISPLAY_CURRENCY,
				self::CART_META_FX_RATE,
				self::CART_META_FX_VERSION,
			) as $key
		) {
			if (isset($values[ $key ])) {
				$cart_item[ $key ] = $values[ $key ];
			}
		}

		return $cart_item;
	}

	/**
	 * @param float $npr_price Canonical NPR unit price.
	 * @return array<string, mixed>
	 */
	public function build_currency_meta_from_npr($npr_price)
	{
		$currency = $this->context->get_display_currency();

		return array(
			self::CART_META_NPR_PRICE        => (float) $npr_price,
			self::CART_META_DISPLAY_CURRENCY => $currency,
			self::CART_META_FX_RATE          => $this->converter->get_npr_per_usd(),
			self::CART_META_FX_VERSION       => $this->converter->get_fx_version(),
		);
	}

	/**
	 * Checkout guard: refresh cart if FX version changed while in USD mode.
	 */
	public function validate_checkout_fx_version()
	{
		if (!function_exists('WC') || !WC()->cart || !$this->context->is_usd()) {
			return;
		}

		$current_version = $this->converter->get_fx_version();
		$mismatch        = false;

		foreach (WC()->cart->get_cart() as $cart_item) {
			if (empty($cart_item[ self::CART_META_FX_VERSION ])) {
				continue;
			}

			if ((int) $cart_item[ self::CART_META_FX_VERSION ] !== $current_version) {
				$mismatch = true;
				break;
			}
		}

		if (!$mismatch) {
			return;
		}

		WC()->cart->calculate_totals();

		Metal_Rate_Store::log(
			'fx_version_mismatch',
			'Cart repriced due to FX rate version change at checkout.',
			array('current_fx_version' => $current_version)
		);

		wc_add_notice(
			__('Exchange rate updated. Your cart has been refreshed.', 'octoways'),
			'notice'
		);

		if (WC()->session) {
			WC()->session->set('ht_fx_rate_refresh_notice', 1);
		}
	}

	/**
	 * Block eSewa when checkout currency is USD.
	 */
	public function validate_checkout_payment_currency()
	{
		if (!$this->context->is_usd()) {
			return;
		}

		$chosen = WC()->session ? WC()->session->get('chosen_payment_method') : '';

		if ($this->is_esewa_gateway($chosen)) {
			wc_add_notice(
				__('eSewa is available for NPR payments only. Please choose another payment method or switch to NPR.', 'octoways'),
				'error'
			);
		}

		if ($this->is_npr_only_gateway($chosen)) {
			wc_add_notice(
				__('QR payment upload is available for NPR payments only. Please choose another payment method or switch to NPR.', 'octoways'),
				'error'
			);
		}
	}

	/**
	 * @param \WC_Order $order Order.
	 * @param array     $data  Checkout data.
	 */
	public function snapshot_order_currency($order, $data)
	{
		unset($data);

		$currency = $this->context->get_display_currency();

		$order->set_currency($currency);
		$order->update_meta_data(self::ORDER_META_DISPLAY_CURRENCY, $currency);
		$order->update_meta_data(self::ORDER_META_FX_RATE, $this->converter->get_npr_per_usd());

		if (Currency_Context::CURRENCY_USD === $currency && WC()->cart) {
			$npr_total = 0.0;

			foreach (WC()->cart->get_cart() as $cart_item) {
				$npr_unit = isset($cart_item[ self::CART_META_NPR_PRICE ])
					? (float) $cart_item[ self::CART_META_NPR_PRICE ]
					: 0.0;
				$qty      = isset($cart_item['quantity']) ? (int) $cart_item['quantity'] : 1;
				$npr_total += $npr_unit * max(1, $qty);
			}

			$order->update_meta_data(self::ORDER_META_NPR_FINAL_PRICE, $npr_total);
		}
	}

	/**
	 * @param array<string, \WC_Payment_Gateway> $gateways Available gateways.
	 * @return array<string, \WC_Payment_Gateway>
	 */
	public function filter_payment_gateways($gateways)
	{
		if (!$this->context->is_usd()) {
			return $gateways;
		}

		foreach ($this->get_usd_disabled_gateway_ids() as $gateway_id) {
			unset($gateways[ $gateway_id ]);
		}

		return $gateways;
	}

	/**
	 * Notice on USD checkout about NPR-only payment methods.
	 */
	public function maybe_show_usd_checkout_notice()
	{
		if (!$this->context->is_usd()) {
			return;
		}

		wc_print_notice(
			__('You are checking out in USD. eSewa and QR payment upload are available for NPR payments only.', 'octoways'),
			'notice'
		);
	}

	/**
	 * Clear session flag after FX refresh notice shown.
	 */
	public function maybe_show_fx_refresh_notice()
	{
		if (!function_exists('WC') || !WC()->session) {
			return;
		}

		if (!WC()->session->get('ht_fx_rate_refresh_notice')) {
			return;
		}

		WC()->session->set('ht_fx_rate_refresh_notice', null);
	}

	/**
	 * Enqueue currency switcher script.
	 */
	public function enqueue_currency_switcher_assets()
	{
		if (!defined('OCTOWAYS_BUILD_PATH') || !defined('OCTOWAYS_BUILD_JS_URI')) {
			return;
		}

		$script_path = OCTOWAYS_BUILD_PATH . '/js/currencySwitcher.js';
		$script_uri  = OCTOWAYS_BUILD_JS_URI . '/currencySwitcher.js';

		if (!file_exists($script_path)) {
			return;
		}

		wp_enqueue_script(
			'ht-currency-switcher',
			$script_uri,
			array('jquery'),
			filemtime($script_path),
			true
		);

		wp_localize_script(
			'ht-currency-switcher',
			'htCurrencySwitcher',
			array(
				'ajaxUrl'          => admin_url('admin-ajax.php'),
				'nonce'            => wp_create_nonce('ht_set_display_currency'),
				'currentCurrency'  => $this->context->get_display_currency(),
				'usdAvailable'     => $this->converter->is_usd_available(),
			)
		);
	}

	/**
	 * @param string $gateway_id Gateway ID.
	 * @return bool
	 */
	private function is_esewa_gateway($gateway_id)
	{
		return in_array($gateway_id, $this->get_esewa_gateway_ids(), true);
	}

	/**
	 * @return string[]
	 */
	private function get_usd_disabled_gateway_ids()
	{
		$gateway_ids = array_merge(
			$this->get_esewa_gateway_ids(),
			array('ht_upload_proof')
		);

		return apply_filters('ht_usd_disabled_gateway_ids', $gateway_ids);
	}

	/**
	 * @param string $gateway_id Gateway ID.
	 * @return bool
	 */
	private function is_npr_only_gateway($gateway_id)
	{
		return in_array($gateway_id, array('ht_upload_proof'), true);
	}

	/**
	 * @return string[]
	 */
	private function get_esewa_gateway_ids()
	{
		return apply_filters(
			'ht_esewa_gateway_ids',
			array('esewa', 'wc_esewa', 'esewa_gateway', 'esewa-for-woocommerce')
		);
	}
}
