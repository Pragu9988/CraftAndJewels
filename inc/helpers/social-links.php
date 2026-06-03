<?php
/**
 * Social media link helpers (footer, contact page, etc.).
 *
 * @package octoways
 */

defined('ABSPATH') || exit;

/**
 * Supported networks and Font Awesome icon classes.
 *
 * @return array<string, array{label: string, icon: string}>
 */
function octoways_social_networks_config()
{
	return array(
		'instagram' => array(
			'label' => __('Instagram', 'octoways'),
			'icon'  => 'fa-brands fa-instagram',
		),
		'facebook'  => array(
			'label' => __('Facebook', 'octoways'),
			'icon'  => 'fa-brands fa-facebook-f',
		),
		'tiktok'    => array(
			'label' => __('TikTok', 'octoways'),
			'icon'  => 'fa-brands fa-tiktok',
		),
		'pinterest' => array(
			'label' => __('Pinterest', 'octoways'),
			'icon'  => 'fa-brands fa-pinterest-p',
		),
		'twitter'   => array(
			'label' => __('X (Twitter)', 'octoways'),
			'icon'  => 'fa-brands fa-x-twitter',
		),
		'youtube'   => array(
			'label' => __('YouTube', 'octoways'),
			'icon'  => 'fa-brands fa-youtube',
		),
	);
}

/**
 * Default profile URLs (used when the option has never been saved).
 *
 * @return array<string, string>
 */
function octoways_social_links_defaults()
{
	return array(
		'instagram' => 'https://www.instagram.com/heritagecraftandjewels?igsh=MTgwdXQ1bG5mYjNrZQ==',
		'facebook'  => 'https://www.facebook.com/share/17TiZ98EhY/',
		'tiktok'    => 'https://www.tiktok.com/@heritagecraftandjewels_?_r=1&_t=ZS-967v5ooBnSj',
		'pinterest' => '',
		'twitter'   => '',
		'youtube'   => '',
	);
}

/**
 * Seed social link option on first theme load.
 */
function octoways_seed_social_links_option()
{
	if (get_option('octoways_social_links', null) !== null) {
		return;
	}

	add_option('octoways_social_links', octoways_social_links_defaults(), '', 'no');
}
add_action('after_setup_theme', 'octoways_seed_social_links_option');

/**
 * Saved URLs keyed by network.
 *
 * @return array<string, string>
 */
function octoways_get_social_links_raw()
{
	$saved = get_option('octoways_social_links', null);

	if ($saved === null) {
		return octoways_social_links_defaults();
	}

	if (!is_array($saved)) {
		return octoways_social_links_defaults();
	}

	$merged = array();

	foreach (array_keys(octoways_social_networks_config()) as $key) {
		$merged[ $key ] = isset($saved[ $key ]) ? (string) $saved[ $key ] : '';
	}

	return $merged;
}

/**
 * Networks that have a non-empty URL, ready for templates.
 *
 * @return array<int, array{key: string, url: string, label: string, icon: string}>
 */
function octoways_get_social_links()
{
	$networks = octoways_social_networks_config();
	$saved    = octoways_get_social_links_raw();
	$links    = array();

	foreach ($networks as $key => $config) {
		$url = isset($saved[ $key ]) ? trim((string) $saved[ $key ]) : '';

		if ($url === '') {
			continue;
		}

		$links[] = array(
			'key'   => $key,
			'url'   => $url,
			'label' => $config['label'],
			'icon'  => $config['icon'],
		);
	}

	return $links;
}

/**
 * Render the social links list.
 *
 * @param array{modifier?: string, wrapper_class?: string} $args Template args.
 */
function octoways_render_social_links($args = array())
{
	$links = octoways_get_social_links();

	if ($links === array()) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'modifier'      => '',
			'wrapper_class' => '',
		)
	);

	$modifier      = $args['modifier'];
	$wrapper_class = $args['wrapper_class'];

	$template = get_template_directory() . '/template-parts/components/social-links.php';

	if (!is_readable($template)) {
		return;
	}

	include $template;
}
