<?php
/**
 * NPR/USD conversion helpers.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Converts canonical NPR amounts for display and checkout.
 */
class Currency_Converter
{
	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @var Currency_Context
	 */
	private $context;

	/**
	 * @param Metal_Rate_Store|null $rate_store Rate store.
	 * @param Currency_Context|null $context    Currency context.
	 */
	public function __construct(Metal_Rate_Store $rate_store = null, Currency_Context $context = null)
	{
		$this->rate_store = $rate_store ?: new Metal_Rate_Store();
		$this->context    = $context ?: Currency_Context::instance();
	}

	/**
	 * @return float NPR per 1 USD.
	 */
	public function get_npr_per_usd()
	{
		return $this->rate_store->get_npr_per_usd();
	}

	/**
	 * @return int
	 */
	public function get_fx_version()
	{
		return $this->rate_store->get_fx_version();
	}

	/**
	 * @param float $npr Amount in NPR.
	 * @return float Rounded display USD.
	 */
	public function npr_to_usd($npr)
	{
		return self::round_usd_amount($this->get_converted_usd($npr));
	}

	/**
	 * @param float $npr Amount in NPR.
	 * @return float Unrounded USD equivalent.
	 */
	public function get_converted_usd($npr)
	{
		$rate = $this->get_npr_per_usd();

		if ($rate <= 0) {
			return 0.0;
		}

		return (float) $npr / $rate;
	}

	/**
	 * @param float $usd Amount in USD.
	 * @return float Amount in NPR.
	 */
	public function usd_to_npr($usd)
	{
		$rate = $this->get_npr_per_usd();

		if ($rate <= 0) {
			return 0.0;
		}

		$npr = (float) $usd * $rate;

		return self::round_npr_amount($npr);
	}

	/**
	 * Round NPR to the nearest 100.
	 *
	 * @param float $npr Amount in NPR.
	 * @return float
	 */
	public static function round_npr_amount($npr)
	{
		$npr = (float) $npr;

		if ($npr <= 0) {
			return 0.0;
		}

		return round($npr / 100) * 100;
	}

	/**
	 * Round USD for display/checkout.
	 * Under 100: nearest whole dollar. 100 and above: nearest hundred.
	 *
	 * @param float $usd Amount in USD.
	 * @return float
	 */
	public static function round_usd_amount($usd)
	{
		$usd = (float) $usd;

		if ($usd <= 0) {
			return 0.0;
		}

		if ($usd < 100) {
			return (float) round($usd);
		}

		return round($usd / 100) * 100;
	}

	/**
	 * @param float $npr Amount in NPR.
	 * @return float Rounded NPR display amount.
	 */
	public function round_npr($npr)
	{
		return self::round_npr_amount($npr);
	}

	/**
	 * Convert NPR canonical price for the active display currency.
	 *
	 * @param float       $npr      Amount in NPR.
	 * @param string|null $currency Target currency; defaults to session currency.
	 * @return float
	 */
	public function convert_for_display($npr, $currency = null)
	{
		$currency = $currency ?: $this->context->get_display_currency();

		if (Currency_Context::CURRENCY_USD === $currency && $this->is_usd_available()) {
			return $this->npr_to_usd($npr);
		}

		return $this->round_npr($npr);
	}

	/**
	 * @param float       $amount   Amount in the given currency.
	 * @param string|null $currency NPR|USD; defaults to session currency.
	 * @return string
	 */
	public function format_amount($amount, $currency = null)
	{
		$currency = $currency ?: $this->context->get_display_currency();
		$decimals = 0;

		if (Currency_Context::CURRENCY_USD === $currency) {
			return wc_price($amount, array('currency' => 'USD', 'decimals' => $decimals));
		}

		return wc_price($amount, array('currency' => 'NPR', 'decimals' => $decimals));
	}

	/**
	 * Format NPR amount always in NPR (for breakdown material lines).
	 *
	 * @param float $npr NPR amount.
	 * @return string
	 */
	public function format_npr($npr)
	{
		return wc_price($this->round_npr($npr), array('currency' => 'NPR', 'decimals' => 0));
	}

	/**
	 * Whether USD conversion is available (valid FX rate configured).
	 *
	 * @return bool
	 */
	public function is_usd_available()
	{
		return $this->get_npr_per_usd() > 0;
	}
}
