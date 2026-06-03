<?php
/**
 * Centralized global material rate storage (gold 24K, silver, diamond).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Global rate option store with versioning.
 */
class Metal_Rate_Store
{
	const OPTION_KEY = 'ht_metal_rates';

	const SOURCE_MANUAL = 'manual';
	const SOURCE_API    = 'api';

	const LOG_SOURCE = 'ht-metal-pricing';

	/**
	 * Default rates when none configured.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_defaults()
	{
		return array(
			'gold_rate_24k'          => 0.0,
			'silver_rate'            => 0.0,
			'diamond_rate'           => 0.0,
			'default_making_charge'  => 0.0,
			'last_synced_at'         => '',
			'rate_version'           => 0,
			'rate_source'            => self::SOURCE_MANUAL,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_rates()
	{
		$stored = get_option(self::OPTION_KEY, array());

		if (!is_array($stored)) {
			$stored = array();
		}

		// Migrate legacy keys from v1 single-metal store.
		if (isset($stored['gold_rate_per_gram']) && !isset($stored['gold_rate_24k'])) {
			$stored['gold_rate_24k'] = $stored['gold_rate_per_gram'];
		}
		if (isset($stored['silver_rate_per_gram']) && !isset($stored['silver_rate'])) {
			$stored['silver_rate'] = $stored['silver_rate_per_gram'];
		}

		return wp_parse_args($stored, self::get_defaults());
	}

	/**
	 * @return int
	 */
	public function get_version()
	{
		return (int) $this->get_rates()['rate_version'];
	}

	/**
	 * Update rates and bump version atomically.
	 *
	 * @param array<string, float> $rates     Keys: gold_rate_24k, silver_rate, diamond_rate, default_making_charge.
	 * @param string               $source    manual|api.
	 * @param bool                 $increment Whether to increment rate_version.
	 * @return array<string, mixed>
	 */
	public function update_rates(array $rates, $source = self::SOURCE_MANUAL, $increment = true)
	{
		$current = $this->get_rates();
		$version = (int) $current['rate_version'];

		if ($increment) {
			++$version;
		}

		$data = array(
			'gold_rate_24k'         => isset($rates['gold_rate_24k'])
				? max(0, (float) $rates['gold_rate_24k'])
				: (float) $current['gold_rate_24k'],
			'silver_rate'           => isset($rates['silver_rate'])
				? max(0, (float) $rates['silver_rate'])
				: (float) $current['silver_rate'],
			'diamond_rate'          => isset($rates['diamond_rate'])
				? max(0, (float) $rates['diamond_rate'])
				: (float) $current['diamond_rate'],
			'default_making_charge' => isset($rates['default_making_charge'])
				? max(0, (float) $rates['default_making_charge'])
				: (float) $current['default_making_charge'],
			'last_synced_at'        => gmdate('Y-m-d H:i:s'),
			'rate_version'          => $version,
			'rate_source'           => in_array($source, array(self::SOURCE_MANUAL, self::SOURCE_API), true)
				? $source
				: self::SOURCE_MANUAL,
		);

		update_option(self::OPTION_KEY, $data, false);

		return $data;
	}

	/**
	 * API sync: update gold/silver only, preserve diamond and default making charge.
	 *
	 * @param float $gold_rate_24k 24K gold per gram.
	 * @param float $silver_rate   Silver per gram.
	 * @param string $source       Rate source.
	 * @return array<string, mixed>
	 */
	public function update_rates_from_api($gold_rate_24k, $silver_rate, $source = self::SOURCE_API)
	{
		return $this->update_rates(
			array(
				'gold_rate_24k' => $gold_rate_24k,
				'silver_rate'   => $silver_rate,
			),
			$source,
			true
		);
	}

	/**
	 * @param string $event   Event name.
	 * @param string $message Log message.
	 * @param array  $context Additional context.
	 */
	public static function log($event, $message, $context = array())
	{
		if (!function_exists('wc_get_logger')) {
			return;
		}

		$logger = wc_get_logger();
		$logger->info(
			$message,
			array(
				'source'  => self::LOG_SOURCE,
				'event'   => $event,
				'context' => $context,
			)
		);
	}
}
