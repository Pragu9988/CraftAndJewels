<?php
/**
 * WP-Cron and API sync for Nepal gold/silver rates (24K + silver).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Fetches rates from external API and updates global store only.
 */
class Metal_Rate_Sync
{
	const CRON_HOOK     = 'ht_metal_rates_sync_event';
	const CRON_INTERVAL = 'ht_every_five_minutes';

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
		add_action('init', array($this, 'maybe_visitor_fallback_sync'), 20);
	}

	/**
	 * @param array<string, array<string, int|string>> $schedules Schedules.
	 * @return array<string, array<string, int|string>>
	 */
	public function add_cron_schedule($schedules)
	{
		if (!isset($schedules[ self::CRON_INTERVAL ])) {
			$schedules[ self::CRON_INTERVAL ] = array(
				'interval' => 5 * MINUTE_IN_SECONDS,
				'display'  => __('Every 5 minutes (Heritage Metal Rates)', 'octoways'),
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
	 * Visitor-triggered fallback when cron is delayed (at most once per 4 minutes).
	 */
	public function maybe_visitor_fallback_sync()
	{
		if (is_admin() || wp_doing_cron()) {
			return;
		}

		$last_attempt = (int) get_transient('ht_metal_sync_visitor_lock');

		if ($last_attempt && (time() - $last_attempt) < 4 * MINUTE_IN_SECONDS) {
			return;
		}

		set_transient('ht_metal_sync_visitor_lock', time(), 4 * MINUTE_IN_SECONDS);
		$this->run_sync(false);
	}

	/**
	 * Run API sync (gold 24K + silver only; diamond remains manual).
	 *
	 * @param bool $manual Whether triggered manually from admin.
	 * @return bool True on successful update.
	 */
	public function run_sync($manual = false)
	{
		if (!$this->rate_store->is_api_sync_enabled()) {
			Metal_Rate_Store::log(
				'sync_skipped',
				'API sync skipped — manual pricing mode is active.',
				array('manual' => $manual)
			);

			return false;
		}

		$api_url = apply_filters(
			'ht_metal_rates_api_url',
			'https://gold-silver.sabinmagar.com.np/wp-json/v1/metal-prices/'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		if (is_wp_error($response)) {
			Metal_Rate_Store::log(
				'sync_failure',
				'Metal rate API request failed: ' . $response->get_error_message(),
				array('manual' => $manual)
			);
			return false;
		}

		$code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		if (200 !== (int) $code || '' === $body) {
			Metal_Rate_Store::log(
				'sync_failure',
				'Metal rate API returned invalid response.',
				array('status' => $code, 'manual' => $manual)
			);
			return false;
		}

		$parsed = $this->parse_api_response($body);

		if (!$parsed) {
			Metal_Rate_Store::log(
				'sync_failure',
				'Metal rate API payload could not be parsed.',
				array('manual' => $manual)
			);
			return false;
		}

		$current = $this->rate_store->get_rates();

		$gold   = $parsed['gold_rate_24k'] > 0 ? $parsed['gold_rate_24k'] : (float) $current['gold_rate_24k'];
		$silver = $parsed['silver_rate'] > 0 ? $parsed['silver_rate'] : (float) $current['silver_rate'];

		if ($gold <= 0 && $silver <= 0) {
			Metal_Rate_Store::log(
				'sync_failure',
				'Metal rate API returned no usable rates; retaining previous values.',
				array('manual' => $manual)
			);
			return false;
		}

		$updated = $this->rate_store->update_rates_from_api($gold, $silver, Metal_Rate_Store::SOURCE_API);

		Metal_Rate_Store::log(
			'sync_success',
			'Metal rates synced from API.',
			array(
				'manual'       => $manual,
				'rate_version' => $updated['rate_version'],
				'gold_24k'     => $gold,
				'silver'       => $silver,
			)
		);

		return true;
	}

	/**
	 * Parse Nepal metal prices API (per 10g -> per gram).
	 *
	 * @param string $body JSON body.
	 * @return array<string, float>|null
	 */
	public function parse_api_response($body)
	{
		$data = json_decode($body, true);

		if (!is_array($data) || empty($data['data'])) {
			return null;
		}

		$rows = $data['data'];

		if (isset($rows[0]) && is_array($rows[0]) && isset($rows[0][0])) {
			$rows = $rows[0];
		}

		$gold_per_gram   = 0.0;
		$silver_per_gram = 0.0;

		foreach ($rows as $row) {
			if (!is_array($row) || empty($row['metal']['name'])) {
				continue;
			}

			$name         = strtolower((string) $row['metal']['name']);
			$per_ten_gram = isset($row['price_per_ten_gram']) ? (float) str_replace(',', '', (string) $row['price_per_ten_gram']) : 0.0;

			if ($per_ten_gram <= 0) {
				continue;
			}

			$per_gram = $per_ten_gram / 10;

			if (false !== strpos($name, 'gold')) {
				$gold_per_gram = $per_gram;
			} elseif (false !== strpos($name, 'silver')) {
				$silver_per_gram = $per_gram;
			}
		}

		if ($gold_per_gram <= 0 && $silver_per_gram <= 0) {
			return null;
		}

		return array(
			'gold_rate_24k' => $gold_per_gram,
			'silver_rate'   => $silver_per_gram,
		);
	}
}
