<?php
/**
 * Admin settings for social media profile URLs.
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Registers Appearance → Social Links and persists option `octoways_social_links`.
 */
class Social_Links_Settings
{
	const OPTION_KEY = 'octoways_social_links';

	const SETTINGS_GROUP = 'octoways_social_links_group';

	/**
	 * Register hooks.
 */
	public function register()
	{
		add_action('admin_menu', array($this, 'register_admin_menu'));
		add_action('admin_init', array($this, 'register_settings'));
	}

	/**
	 * Add submenu under Appearance.
 */
	public function register_admin_menu()
	{
		add_theme_page(
			__('Social Links', 'octoways'),
			__('Social Links', 'octoways'),
			'edit_theme_options',
			'ht-social-links',
			array($this, 'render_admin_page')
		);
	}

	/**
	 * Register option and fields.
 */
	public function register_settings()
	{
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array($this, 'sanitize_links'),
				'default'           => octoways_social_links_defaults(),
			)
		);

		add_settings_section(
			'octoways_social_links_section',
			__('Profile URLs', 'octoways'),
			array($this, 'render_section_intro'),
			'ht-social-links'
		);

		foreach (octoways_social_networks_config() as $key => $config) {
			add_settings_field(
				'octoways_social_' . $key,
				$config['label'],
				array($this, 'render_url_field'),
				'ht-social-links',
				'octoways_social_links_section',
				array(
					'key'         => $key,
					'label'       => $config['label'],
					'placeholder' => 'https://',
				)
			);
		}
	}

	/**
	 * Section description.
 */
	public function render_section_intro()
	{
		echo '<p>' . esc_html__(
			'Add profile URLs for each network you use. Empty fields are hidden on the site footer and Contact page.',
			'octoways'
		) . '</p>';
	}

	/**
	 * URL input for one network.
	 *
	 * @param array{key: string, label: string, placeholder: string} $args Field args.
 */
	public function render_url_field($args)
	{
		$key   = $args['key'];
		$value = octoways_get_social_links_raw();
		$url   = isset($value[ $key ]) ? $value[ $key ] : '';

		printf(
			'<input type="url" class="regular-text code" id="octoways_social_%1$s" name="%2$s[%1$s]" value="%3$s" placeholder="%4$s" />',
			esc_attr($key),
			esc_attr(self::OPTION_KEY),
			esc_attr($url),
			esc_attr($args['placeholder'])
		);
	}

	/**
	 * Settings page markup.
 */
	public function render_admin_page()
	{
		if (!current_user_can('edit_theme_options')) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e('Social Links', 'octoways'); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields(self::SETTINGS_GROUP);
				do_settings_sections('ht-social-links');
				submit_button(__('Save Social Links', 'octoways'));
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize submitted URLs.
	 *
	 * @param mixed $input Raw POST data.
	 * @return array<string, string>
	 */
	public function sanitize_links($input)
	{
		$networks = octoways_social_networks_config();
		$output   = array();

		if (!is_array($input)) {
			return octoways_social_links_defaults();
		}

		foreach (array_keys($networks) as $key) {
			$raw = isset($input[ $key ]) ? trim((string) $input[ $key ]) : '';

			if ($raw === '') {
				$output[ $key ] = '';
				continue;
			}

			$output[ $key ] = esc_url_raw($raw);
		}

		return $output;
	}
}
