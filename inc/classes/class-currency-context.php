<?php
/**
 * Storefront display currency preference (NPR default, USD optional).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Session and cookie backed currency context.
 */
class Currency_Context
{
	const CURRENCY_NPR = 'NPR';
	const CURRENCY_USD = 'USD';

	const SESSION_KEY = 'ht_shop_display_currency';
	const COOKIE_NAME = 'ht_currency';
	const COOKIE_TTL  = 30 * DAY_IN_SECONDS;
	const NONCE_ACTION = 'ht_switch_currency';

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @var string|null
	 */
	private $resolved_currency = null;

	/**
	 * @return self
	 */
	public static function instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 */
	public function register()
	{
		add_action('init', array($this, 'maybe_switch_currency_from_request'), 1);
		add_action('woocommerce_init', array($this, 'bootstrap_from_cookie'), 5);
	}

	/**
	 * Handle ?ht_currency=NPR|USD via full-page redirect (reliable cookie persistence).
	 */
	public function maybe_switch_currency_from_request()
	{
		if (empty($_GET['ht_currency'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if (
			empty($_GET['_wpnonce'])
			|| !wp_verify_nonce(
				sanitize_text_field(wp_unslash($_GET['_wpnonce'])), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				self::NONCE_ACTION
			)
		) {
			return;
		}

		$currency = sanitize_text_field(wp_unslash($_GET['ht_currency'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if (self::CURRENCY_USD === $currency) {
			$converter = new Currency_Converter();
			if (!$converter->is_usd_available()) {
				$currency = self::CURRENCY_NPR;
			}
		}

		if ($this->is_valid_currency($currency)) {
			$this->set_currency($currency);
		}

		$redirect = remove_query_arg(array('ht_currency', '_wpnonce'));

		if (!$redirect) {
			$redirect = home_url('/');
		}

		wp_safe_redirect($redirect);
		exit;
	}

	/**
	 * Keep WooCommerce session aligned with the persisted cookie preference.
	 */
	public function bootstrap_from_cookie()
	{
		if (!$this->ensure_woocommerce_session()) {
			return;
		}

		$cookie = $this->get_cookie_currency();

		if (!$cookie) {
			return;
		}

		WC()->session->set(self::SESSION_KEY, $cookie);
		$this->resolved_currency = $cookie;
	}

	/**
	 * @return string NPR|USD
	 */
	public function get_display_currency()
	{
		if (null !== $this->resolved_currency) {
			return $this->resolved_currency;
		}

		$cookie = $this->get_cookie_currency();

		if ($cookie) {
			$this->resolved_currency = $cookie;
			return $this->resolved_currency;
		}

		$session = $this->get_session_currency();

		if ($session) {
			$this->resolved_currency = $session;
			return $this->resolved_currency;
		}

		$this->resolved_currency = self::CURRENCY_NPR;

		return $this->resolved_currency;
	}

	/**
	 * @return bool
	 */
	public function is_usd()
	{
		return self::CURRENCY_USD === $this->get_display_currency();
	}

	/**
	 * @return bool
	 */
	public function is_npr()
	{
		return !$this->is_usd();
	}

	/**
	 * @param string $currency NPR|USD.
	 * @return bool
	 */
	public function set_currency($currency)
	{
		if (!$this->is_valid_currency($currency)) {
			return false;
		}

		$this->resolved_currency = $currency;
		$this->set_cookie($currency);

		if ($this->ensure_woocommerce_session()) {
			WC()->session->set(self::SESSION_KEY, $currency);
			$this->persist_session();
		}

		if (function_exists('WC') && WC()->cart) {
			WC()->cart->calculate_totals();
		}

		return true;
	}

	/**
	 * @return string
	 */
	public static function get_switch_nonce()
	{
		return wp_create_nonce(self::NONCE_ACTION);
	}

	/**
	 * @return string|null
	 */
	private function get_session_currency()
	{
		if (!function_exists('WC') || !WC()->session) {
			return null;
		}

		$value = WC()->session->get(self::SESSION_KEY);

		return $this->is_valid_currency($value) ? $value : null;
	}

	/**
	 * @return string|null
	 */
	private function get_cookie_currency()
	{
		if (!isset($_COOKIE[ self::COOKIE_NAME ])) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return null;
		}

		$currency = sanitize_text_field(wp_unslash($_COOKIE[ self::COOKIE_NAME ])); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return $this->is_valid_currency($currency) ? $currency : null;
	}

	/**
	 * @param string|null $currency Currency code.
	 * @return bool
	 */
	private function is_valid_currency($currency)
	{
		return in_array($currency, array(self::CURRENCY_NPR, self::CURRENCY_USD), true);
	}

	/**
	 * @return bool
	 */
	private function ensure_woocommerce_session()
	{
		if (!function_exists('WC') || !class_exists('WooCommerce')) {
			return false;
		}

		if (null === WC()->session) {
			WC()->initialize_session();
		}

		if (null === WC()->cart && function_exists('wc_load_cart')) {
			wc_load_cart();
		}

		return null !== WC()->session;
	}

	/**
	 * Persist session immediately.
	 */
	private function persist_session()
	{
		if (!function_exists('WC') || !WC()->session) {
			return;
		}

		if (method_exists(WC()->session, 'save_data')) {
			WC()->session->save_data();
		}
	}

	/**
	 * @param string $currency NPR|USD.
	 */
	private function set_cookie($currency)
	{
		$expire = time() + self::COOKIE_TTL;
		$secure = is_ssl();

		if (function_exists('wc_setcookie')) {
			wc_setcookie(self::COOKIE_NAME, $currency, $expire, $secure, true);
		} elseif (!headers_sent()) {
			setcookie(
				self::COOKIE_NAME,
				$currency,
				array(
					'expires'  => $expire,
					'path'     => defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '',
					'secure'   => $secure,
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}

		$_COOKIE[ self::COOKIE_NAME ] = $currency; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
}
