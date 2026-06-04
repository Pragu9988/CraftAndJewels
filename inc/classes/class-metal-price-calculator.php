<?php
/**
 * Dynamic multi-material jewellery price calculator (server-authoritative).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Computes gold (purity-adjusted), silver, diamond, gemstone, plating, misc, and making charge costs.
 */
class Metal_Price_Calculator
{
	const META_GOLD_WEIGHT           = '_gold_weight';
	const META_GOLD_PURITY           = '_gold_purity';
	const META_SILVER_WEIGHT         = '_silver_weight';
	const META_DIAMOND_WEIGHT        = '_diamond_weight';
	const META_GEMSTONE_QTY          = '_gemstone_qty';
	const META_GEMSTONE_RATE         = '_gemstone_rate';
	const META_GOLD_PLATING_COST     = '_gold_plating_cost';
	const META_RHODIUM_PLATING_COST  = '_rhodium_plating_cost';
	const META_MISC_COST             = '_misc_cost';
	const META_TOTAL_WEIGHT          = '_total_weight';
	const META_MAKING_CHARGE_TYPE    = '_making_charge_type';
	const META_MAKING_CHARGE_VALUE   = '_making_charge_value';

	const CHARGE_PER_GRAM    = 'per_gram';
	const CHARGE_PER_PIECE   = 'per_piece';
	const CHARGE_PERCENTAGE  = 'percentage';

	/**
	 * Supported gold purities => karat divisor numerator.
	 *
	 * @return array<string, int>
	 */
	public static function get_supported_purities()
	{
		return array(
			'9K'  => 9,
			'14K' => 14,
			'18K' => 18,
			'22K' => 22,
			'24K' => 24,
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_supported_charge_types()
	{
		return array(
			self::CHARGE_PER_GRAM   => self::CHARGE_PER_GRAM,
			self::CHARGE_PER_PIECE  => self::CHARGE_PER_PIECE,
			self::CHARGE_PERCENTAGE => self::CHARGE_PERCENTAGE,
		);
	}

	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @param Metal_Rate_Store|null $rate_store Rate store instance.
	 */
	public function __construct(Metal_Rate_Store $rate_store = null)
	{
		$this->rate_store = $rate_store ?: new Metal_Rate_Store();
	}

	/**
	 * Whether product has dynamic pricing configured.
	 *
	 * @param int $product_id Product ID.
	 * @return bool
	 */
	public function is_dynamic_product($product_id)
	{
		return null !== $this->get_product_formula($product_id);
	}

	/**
	 * @param string $purity Purity label e.g. 22K.
	 * @return bool
	 */
	public function is_valid_gold_purity($purity)
	{
		$purities = self::get_supported_purities();

		return is_string($purity) && isset($purities[ $purity ]);
	}

	/**
	 * @param string $charge_type Making charge type.
	 * @return bool
	 */
	public function is_valid_charge_type($charge_type)
	{
		return isset(self::get_supported_charge_types()[ $charge_type ]);
	}

	/**
	 * @param int $product_id Product ID.
	 * @return array<string, mixed>|null
	 */
	public function get_product_formula($product_id)
	{
		$gold_weight           = (float) get_post_meta($product_id, self::META_GOLD_WEIGHT, true);
		$gold_purity           = get_post_meta($product_id, self::META_GOLD_PURITY, true);
		$silver_weight         = (float) get_post_meta($product_id, self::META_SILVER_WEIGHT, true);
		$diamond_weight        = (float) get_post_meta($product_id, self::META_DIAMOND_WEIGHT, true);
		$gemstone_qty          = (float) get_post_meta($product_id, self::META_GEMSTONE_QTY, true);
		$gemstone_rate         = (float) get_post_meta($product_id, self::META_GEMSTONE_RATE, true);
		$gold_plating_cost     = (float) get_post_meta($product_id, self::META_GOLD_PLATING_COST, true);
		$rhodium_plating_cost  = (float) get_post_meta($product_id, self::META_RHODIUM_PLATING_COST, true);
		$misc_cost             = (float) get_post_meta($product_id, self::META_MISC_COST, true);
		$total_weight          = (float) get_post_meta($product_id, self::META_TOTAL_WEIGHT, true);
		$charge_type           = get_post_meta($product_id, self::META_MAKING_CHARGE_TYPE, true);
		$charge_val            = (float) get_post_meta($product_id, self::META_MAKING_CHARGE_VALUE, true);

		if ($gold_weight > 0 && !$this->is_valid_gold_purity($gold_purity)) {
			return null;
		}

		if (!$this->is_valid_charge_type($charge_type)) {
			$charge_type = self::CHARGE_PER_GRAM;
		}

		$has_material = ($gold_weight > 0)
			|| ($silver_weight > 0)
			|| ($diamond_weight > 0)
			|| ($gemstone_qty > 0 && $gemstone_rate > 0)
			|| ($gold_plating_cost > 0)
			|| ($rhodium_plating_cost > 0)
			|| ($misc_cost > 0);

		$rates      = $this->rate_store->get_rates();
		$has_making = ($charge_val > 0) || ((float) $rates['default_making_charge'] > 0);

		if (!$has_material && !$has_making) {
			return null;
		}

		if (self::CHARGE_PER_GRAM === $charge_type && $has_making && $total_weight <= 0) {
			if ($charge_val > 0 || (float) $rates['default_making_charge'] > 0) {
				return null;
			}
		}

		if (!$has_material && self::CHARGE_PER_PIECE !== $charge_type) {
			return null;
		}

		return array(
			'gold_weight'           => $gold_weight,
			'gold_purity'           => $gold_weight > 0 ? (string) $gold_purity : '',
			'silver_weight'         => $silver_weight,
			'diamond_weight'        => $diamond_weight,
			'gemstone_qty'          => $gemstone_qty,
			'gemstone_rate'         => max(0, $gemstone_rate),
			'gold_plating_cost'     => max(0, $gold_plating_cost),
			'rhodium_plating_cost'  => max(0, $rhodium_plating_cost),
			'misc_cost'             => max(0, $misc_cost),
			'total_weight'          => $total_weight,
			'making_charge_type'    => $charge_type,
			'making_charge_value'   => max(0, $charge_val),
		);
	}

	/**
	 * Effective gold rate per gram for a purity.
	 *
	 * @param float  $gold_rate_24k 24K rate per gram.
	 * @param string $purity        9K|14K|18K|22K|24K.
	 * @return float
	 */
	public function get_effective_gold_rate($gold_rate_24k, $purity)
	{
		$purities = self::get_supported_purities();

		if (!isset($purities[ $purity ])) {
			return 0.0;
		}

		$karat = $purities[ $purity ];

		return (float) $gold_rate_24k * ($karat / 24);
	}

	/**
	 * Calculate final price for a product using current global rates.
	 *
	 * @param int $product_id Product ID.
	 * @return float|null Null when not dynamic or cannot price.
	 */
	public function calculate_for_product($product_id)
	{
		$breakdown = $this->get_breakdown_for_product($product_id);

		if (!$breakdown) {
			return null;
		}

		return $breakdown['final_price'];
	}

	/**
	 * Full breakdown from product formula and global rates.
	 *
	 * @param array<string, mixed> $formula Product formula.
	 * @param array<string, mixed> $rates   Global rates from store.
	 * @return array<string, mixed>|null
	 */
	public function calculate_from_formula(array $formula, array $rates)
	{
		$gold_rate_24k = (float) $rates['gold_rate_24k'];
		$silver_rate   = (float) $rates['silver_rate'];
		$diamond_rate  = (float) $rates['diamond_rate'];

		$gold_cost           = 0.0;
		$gold_effective_rate = 0.0;

		if ($formula['gold_weight'] > 0 && $this->is_valid_gold_purity($formula['gold_purity'])) {
			$gold_effective_rate = $this->get_effective_gold_rate($gold_rate_24k, $formula['gold_purity']);
			$gold_cost           = (float) $formula['gold_weight'] * $gold_effective_rate;
		}

		$silver_cost  = (float) $formula['silver_weight'] * $silver_rate;
		$diamond_cost = (float) $formula['diamond_weight'] * $diamond_rate;

		$gemstone_cost          = (float) $formula['gemstone_qty'] * (float) $formula['gemstone_rate'];
		$gold_plating_cost_calc = (float) $formula['gold_plating_cost'];
		$rhodium_plating_cost   = (float) $formula['rhodium_plating_cost'];
		$misc_cost_calc         = (float) $formula['misc_cost'];

		$metal_value   = $gold_cost + $silver_cost;
		$making_value  = (float) $formula['making_charge_value'];
		$charge_type   = $formula['making_charge_type'];

		if ($making_value <= 0 && self::CHARGE_PERCENTAGE !== $charge_type) {
			$making_value = (float) $rates['default_making_charge'];
		}

		if (self::CHARGE_PER_GRAM === $charge_type) {
			$making_charge = (float) $formula['total_weight'] * $making_value;
		} elseif (self::CHARGE_PERCENTAGE === $charge_type) {
			$making_charge = $metal_value * ($making_value / 100);
		} else {
			$making_charge = $making_value;
		}

		$final = $gold_cost
			+ $silver_cost
			+ $diamond_cost
			+ $making_charge
			+ $gemstone_cost
			+ $gold_plating_cost_calc
			+ $rhodium_plating_cost
			+ $misc_cost_calc;

		return array(
			'gold_weight'              => (float) $formula['gold_weight'],
			'gold_purity'              => $formula['gold_purity'],
			'gold_effective_rate'      => $gold_effective_rate,
			'gold_cost'                => $gold_cost,
			'silver_weight'            => (float) $formula['silver_weight'],
			'silver_rate'              => $silver_rate,
			'silver_cost'              => $silver_cost,
			'diamond_weight'           => (float) $formula['diamond_weight'],
			'diamond_rate'             => $diamond_rate,
			'diamond_cost'             => $diamond_cost,
			'gemstone_qty'             => (float) $formula['gemstone_qty'],
			'gemstone_rate'            => (float) $formula['gemstone_rate'],
			'gemstone_cost'            => $gemstone_cost,
			'gold_plating_cost'        => $gold_plating_cost_calc,
			'gold_plating_cost_calc'   => $gold_plating_cost_calc,
			'rhodium_plating_cost'     => $rhodium_plating_cost,
			'rhodium_plating_cost_calc'=> $rhodium_plating_cost,
			'misc_cost'                => $misc_cost_calc,
			'misc_cost_calc'           => $misc_cost_calc,
			'metal_value'              => $metal_value,
			'total_weight'             => (float) $formula['total_weight'],
			'making_charge_type'       => $charge_type,
			'making_charge_value'      => (float) $formula['making_charge_value'],
			'making_charge'            => $making_charge,
			'making_charge_cost'       => $making_charge,
			'final_price'              => $this->round_price($final),
			'gold_rate_24k'            => $gold_rate_24k,
		);
	}

	/**
	 * @param int $product_id Product ID.
	 * @return array<string, mixed>|null
	 */
	public function get_breakdown_for_product($product_id)
	{
		$formula = $this->get_product_formula($product_id);

		if (!$formula) {
			return null;
		}

		$rates     = $this->rate_store->get_rates();
		$breakdown = $this->calculate_from_formula($formula, $rates);

		if (!$breakdown) {
			return null;
		}

		if ($breakdown['final_price'] <= 0) {
			$has_rate = ($rates['gold_rate_24k'] > 0 && $formula['gold_weight'] > 0)
				|| ($rates['silver_rate'] > 0 && $formula['silver_weight'] > 0)
				|| ($rates['diamond_rate'] > 0 && $formula['diamond_weight'] > 0)
				|| ($formula['gemstone_qty'] > 0 && $formula['gemstone_rate'] > 0)
				|| $formula['gold_plating_cost'] > 0
				|| $formula['rhodium_plating_cost'] > 0
				|| $formula['misc_cost'] > 0
				|| $breakdown['making_charge'] > 0;

			if (!$has_rate) {
				return null;
			}
		}

		$breakdown['rate_version'] = $this->rate_store->get_version();

		return $breakdown;
	}

	/**
	 * @param float $amount Raw amount.
	 * @return float
	 */
	public function round_price($amount)
	{
		if (function_exists('wc_format_decimal')) {
			return (float) wc_format_decimal($amount, wc_get_price_decimals());
		}

		return round((float) $amount, 2);
	}
}
