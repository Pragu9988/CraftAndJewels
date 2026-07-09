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

	const SESSION_KEY = 'ht_display_currency';
	const COOKIE_NAME = 'ht_currency';
	const COOKIE_TTL  = 30 * DAY_IN_SECONDS;

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
		add_action('woocommerce_init', array($this, 'bootstrap_from_cookie'), 5);
		add_action('wp_ajax_ht_set_display_currency', array($this, 'ajax_set_currency'));
		add_action('wp_ajax_nopriv_ht_set_display_currency', array($this, 'ajax_set_currency'));
	}

	/**
	 * Restore session currency from cookie when WC session is empty.
	 */
	public function bootstrap_from_cookie()
	{
		if (!function_exists('WC') || !WC()->session) {
			return;
		}

		if ($this->get_session_currency()) {
			return;
		}

		$cookie = isset($_COOKIE[ self::COOKIE_NAME ]) ? sanitize_text_field(wp_unslash($_COOKIE[ self::COOKIE_NAME ])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ($this->is_valid_currency($cookie)) {
			WC()->session->set(self::SESSION_KEY, $cookie);
		}
	}

	/**
	 * @return string NPR|USD
	 */
	public function get_display_currency()
	{
		if (null !== $this->resolved_currency) {
			return $this->resolved_currency;
		}

		$session = $this->get_session_currency();

		if ($session) {
			$this->resolved_currency = $session;
			return $this->resolved_currency;
		}

		$cookie = isset($_COOKIE[ self::COOKIE_NAME ]) ? sanitize_text_field(wp_unslash($_COOKIE[ self::COOKIE_NAME ])) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$this->resolved_currency = $this->is_valid_currency($cookie) ? $cookie : self::CURRENCY_NPR;

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

		if (function_exists('WC') && WC()->session) {
			WC()->session->set(self::SESSION_KEY, $currency);
		}

		$this->set_cookie($currency);

		if (function_exists('WC') && WC()->cart) {
			WC()->cart->calculate_totals();
		}

		return true;
	}

	/**
	 * AJAX: switch display currency.
	 */
	public function ajax_set_currency()
	{
		check_ajax_referer('ht_set_display_currency', 'nonce');

		$currency = isset($_POST['currency']) ? sanitize_text_field(wp_unslash($_POST['currency'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if (!$this->set_currency($currency)) {
			wp_send_json_error(
				array('message' => __('Invalid currency.', 'octoways')),
				400
			);
		}

		wp_send_json_success(
			array(
				'currency' => $currency,
			)
		);
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
	 * @param string|null $currency Currency code.
	 * @return bool
	 */
	private function is_valid_currency($currency)
	{
		return in_array($currency, array(self::CURRENCY_NPR, self::CURRENCY_USD), true);
	}

	/**
	 * @param string $currency NPR|USD.
	 */
	private function set_cookie($currency)
	{
		if (headers_sent()) {
			return;
		}

		setcookie(
			self::COOKIE_NAME,
			$currency,
			time() + self::COOKIE_TTL,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);

		$_COOKIE[ self::COOKIE_NAME ] = $currency; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
}
