<?php
/**
 * ACF field group: Contact Us page (local).
 *
 * @package octoways
 */

if (!function_exists('acf_add_local_field_group')) {
	return;
}

acf_add_local_field_group([
	'key' => 'group_ht_contact_page',
	'title' => 'Contact Us',
	'fields' => [
		[
			'key' => 'field_ht_contact_cf7_shortcode',
			'label' => 'Contact Form 7 shortcode',
			'name' => 'contact_cf7_shortcode',
			'type' => 'text',
			'instructions' => 'Paste the shortcode from Contact → Contact Forms (e.g. [contact-form-7 id="123" title="Contact form 1"]).',
			'required' => 0,
			'default_value' => '',
			'placeholder' => '[contact-form-7 id="1" title="Contact form 1"]',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		],
		[
			'key' => 'field_ht_contact_intro',
			'label' => 'Intro paragraph',
			'name' => 'contact_intro',
			'type' => 'textarea',
			'instructions' => 'Optional. Replaces the default intro under the page title.',
			'required' => 0,
			'rows' => 4,
			'new_lines' => 'wpautop',
		],
		[
			'key' => 'field_ht_contact_email',
			'label' => 'Display email',
			'name' => 'contact_email',
			'type' => 'email',
			'instructions' => 'Optional. Shown in the sidebar.',
			'required' => 0,
		],
		[
			'key' => 'field_ht_contact_phone',
			'label' => 'Display phone',
			'name' => 'contact_phone',
			'type' => 'text',
			'instructions' => 'Optional. Shown in the sidebar.',
			'required' => 0,
		],
		[
			'key' => 'field_ht_contact_address',
			'label' => 'Address',
			'name' => 'contact_address',
			'type' => 'textarea',
			'instructions' => 'Optional. Shown in the sidebar.',
			'required' => 0,
			'rows' => 3,
			'new_lines' => 'wpautop',
		],
		[
			'key' => 'field_ht_contact_hours',
			'label' => 'Hours',
			'name' => 'contact_hours',
			'type' => 'text',
			'instructions' => 'Optional. Shown in the sidebar.',
			'required' => 0,
		],
	],
	'location' => [
		[
			[
				'param' => 'page_template',
				'operator' => '==',
				'value' => 'page-contact-us.php',
			],
		],
	],
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'active' => true,
]);
