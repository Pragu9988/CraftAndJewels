<?php
/**
 * WP-Cron and API sync for NPR/USD exchange rate.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Fetches USD/NPR rate from external API and updates global store only.
 */
class Currency_FX_Sync
{
	const CRON_HOOK     = 'ht_fx_rates_sync_event';
	const CRON_INTERVAL = 'ht_every_six_hours';

	/**
	 * @var Metal_Rate_Store
	 */
	private $rate_store;

	/**
	 * @param Metal_Rate_Store|null $rate_store Rate store.
	 */
	public function __construct(Metal_Rate_Store $rate_store = null)
	{
		$this->rate_store = $rate_store ?: new Metal_Rate_Store();
	}

	/**
	 * Register cron schedule and hooks.
	 */
	public function register()
	{
		add_filter('cron_schedules', array($this, 'add_cron_schedule'));
		add_action(self::CRON_HOOK, array($this, 'run_sync'));

		add_action('init', array($this, 'maybe_schedule_cron'));
	}

	/**
	 * @param array<string, array<string, int|string>> $schedules Schedules.
	 * @return array<string, array<string, int|string>>
	 */
	public function add_cron_schedule($schedules)
	{
		if (!isset($schedules[ self::CRON_INTERVAL ])) {
			$schedules[ self::CRON_INTERVAL ] = array(
				'interval' => 6 * HOUR_IN_SECONDS,
				'display'  => __('Every 6 hours (Heritage FX Rates)', 'octoways'),
			);
		}

		return $schedules;
	}

	/**
	 * Ensure recurring cron is scheduled.
	 */
	public function maybe_schedule_cron()
	{
		if (!wp_next_scheduled(self::CRON_HOOK)) {
			wp_schedule_event(time(), self::CRON_INTERVAL, self::CRON_HOOK);
		}
	}

	/**
	 * Run FX API sync.
	 *
	 * @param bool $manual Whether triggered manually from admin.
	 * @return bool True on successful update.
	 */
	public function run_sync($manual = false)
	{
		if (!$this->rate_store->is_fx_api_sync_enabled()) {
			Metal_Rate_Store::log(
				'fx_sync_skipped',
				'FX API sync skipped — manual FX mode is active.',
				array('manual' => $manual)
			);

			return false;
		}

		$npr_per_usd = $this->fetch_npr_per_usd();

		if ($npr_per_usd <= 0) {
			Metal_Rate_Store::log(
				'fx_sync_failure',
				'FX API returned no usable NPR/USD rate; retaining previous value.',
				array('manual' => $manual)
			);

			return false;
		}

		$updated = $this->rate_store->update_fx_from_api($npr_per_usd, Metal_Rate_Store::FX_SOURCE_API);

		Metal_Rate_Store::log(
			'fx_sync_success',
			'FX rate synced from API.',
			array(
				'manual'          => $manual,
				'fx_rate_version' => $updated['fx_rate_version'],
				'npr_per_usd'     => $npr_per_usd,
			)
		);

		return true;
	}

	/**
	 * Fetch NPR per 1 USD from configured API.
	 *
	 * @return float
	 */
	public function fetch_npr_per_usd()
	{
		$api_key = defined('HT_FX_API_KEY') ? HT_FX_API_KEY : '';

		if ($api_key) {
			$rate = $this->fetch_from_exchange_rate_api($api_key);

			if ($rate > 0) {
				return $rate;
			}
		}

		return $this->fetch_from_frankfurter();
	}

	/**
	 * @param string $api_key ExchangeRate-API key.
	 * @return float
	 */
	private function fetch_from_exchange_rate_api($api_key)
	{
		$url = apply_filters(
			'ht_fx_rates_api_url',
			'https://v6.exchangerate-api.com/v6/' . rawurlencode($api_key) . '/pair/USD/NPR'
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		if (is_wp_error($response)) {
			Metal_Rate_Store::log(
				'fx_sync_failure',
				'ExchangeRate-API request failed: ' . $response->get_error_message()
			);

			return 0.0;
		}

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		if (200 !== (int) $code || '' === $body) {
			return 0.0;
		}

		$data = json_decode($body, true);

		if (!is_array($data) || empty($data['conversion_rate'])) {
			return 0.0;
		}

		return max(0, (float) $data['conversion_rate']);
	}

	/**
	 * Frankfurter fallback (no API key required).
	 *
	 * @return float
	 */
	private function fetch_from_frankfurter()
	{
		$url = apply_filters(
			'ht_fx_rates_frankfurter_url',
			'https://api.frankfurter.app/latest?from=USD&to=NPR'
		);

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		if (is_wp_error($response)) {
			Metal_Rate_Store::log(
				'fx_sync_failure',
				'Frankfurter FX API request failed: ' . $response->get_error_message()
			);

			return 0.0;
		}

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		if (200 !== (int) $code || '' === $body) {
			return 0.0;
		}

		$data = json_decode($body, true);

		if (!is_array($data) || empty($data['rates']['NPR'])) {
			return 0.0;
		}

		return max(0, (float) $data['rates']['NPR']);
	}
}
