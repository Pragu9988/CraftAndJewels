<?php
/**
 * Checkout payment proof modal (QR + Contact Form 7).
 *
 * Suggested CF7 form markup (Form tab):
 *
 * CF7 form (Form tab) — order_key is injected automatically on thank-you page.
 *
 * <div class="ht-cf7-payment-proof-form">
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Order number</label>
 * [hidden order_id]
 * </div>
 * <div class="ht-cf7-field ht-cf7-field--file">
 * <label class="ht-cf7-label">Payment proof (screenshot or receipt) *</label>
 * <p class="ht-cf7-hint">JPG, PNG or PDF — max 5 MB</p>
 * [file* payment-proof class:ht-cf7-file filetypes:jpg|jpeg|png|pdf limit:5mb]
 * </div>
 * [submit class:ht-cf7-submit "Upload payment proof"]
 * </div>
 *
 * Proofs are saved to the order in WooCommerce admin (Payment proof box + order notes).
 *
 * @package OCTOWAYS_THEME
 */

namespace OCTOWAYS_THEME\Inc;

defined('ABSPATH') || exit;

/**
 * Modal, assets, and CF7 integration for upload-proof payments.
 */
class Checkout_Payment_Proof
{
	/**
	 * Gateway ID.
	 */
	const GATEWAY_ID = 'ht_upload_proof';

	/**
	 * Order meta: list of attachment IDs for payment proofs.
	 */
	const ORDER_META_PROOF_IDS = '_ht_payment_proof_ids';

	/**
	 * Attachment meta: linked WooCommerce order ID.
	 */
	const ATTACHMENT_META_ORDER_ID = '_ht_payment_proof_order_id';

	/**
	 * Default CF7 file field name.
	 */
	const DEFAULT_FILE_FIELD = 'payment-proof';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		add_filter('woocommerce_payment_gateways', array($this, 'register_gateway'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
		add_action('wp_footer', array($this, 'render_modal'));
		add_filter('wpcf7_form_hidden_fields', array($this, 'inject_order_hidden_fields'));
		add_action('wpcf7_mail_sent', array($this, 'save_payment_proof_to_order'), 10, 1);
		add_action('add_meta_boxes', array($this, 'register_order_metabox'));
	}

	/**
	 * Register custom payment gateway.
	 *
	 * @param array $gateways Payment gateway class names.
	 * @return array
	 */
	public function register_gateway($gateways)
	{
		$gateways[] = 'HT_Gateway_Upload_Proof';
		return $gateways;
	}

	/**
	 * Whether scripts/modal should load on the current request.
	 *
	 * @return bool
	 */
	private function should_load()
	{
		if (!class_exists('HT_Gateway_Upload_Proof') || !\HT_Gateway_Upload_Proof::is_configured()) {
			return false;
		}

		return (function_exists('is_checkout') && is_checkout() && !is_order_received_page())
			|| $this->is_upload_proof_thankyou();
	}

	/**
	 * Thank-you page for an upload-proof order.
	 *
	 * @return bool
	 */
	private function is_upload_proof_thankyou()
	{
		if (!function_exists('is_order_received_page') || !is_order_received_page()) {
			return false;
		}

		$order = $this->get_thankyou_order();

		return $order && self::GATEWAY_ID === $order->get_payment_method();
	}

	/**
	 * Order from the order-received endpoint.
	 *
	 * @return \WC_Order|null
	 */
	private function get_thankyou_order()
	{
		global $wp;

		$order_id = 0;

		if (isset($wp->query_vars['order-received'])) {
			$order_id = absint($wp->query_vars['order-received']);
		}

		if (!$order_id) {
			$order_id = absint(get_query_var('order-received'));
		}

		if (!$order_id) {
			return null;
		}

		$order_key = isset($_GET['key']) ? wc_clean(wp_unslash($_GET['key'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ($order_key) {
			$order_id = wc_get_order_id_by_order_key($order_key);
		}

		$order = wc_get_order($order_id);

		if (!$order) {
			return null;
		}

		if ($order_key && $order->get_order_key() !== $order_key) {
			return null;
		}

		return $order;
	}

	/**
	 * Enqueue checkout payment proof script and CF7 assets.
	 */
	public function enqueue_assets()
	{
		if (!$this->should_load()) {
			return;
		}

		if (function_exists('wpcf7_enqueue_scripts')) {
			wpcf7_enqueue_scripts();
		}

		if (function_exists('wpcf7_enqueue_styles')) {
			wpcf7_enqueue_styles();
		}

		$script_path = OCTOWAYS_BUILD_PATH . '/js/checkoutPaymentProof.js';
		$script_uri  = OCTOWAYS_BUILD_JS_URI . '/checkoutPaymentProof.js';

		wp_enqueue_script(
			'ht-checkout-payment-proof',
			$script_uri,
			array('jquery'),
			file_exists($script_path) ? filemtime($script_path) : _S_VERSION,
			true
		);

		$order        = $this->get_thankyou_order();
		$is_thankyou  = $this->is_upload_proof_thankyou();
		$auto_open    = $is_thankyou;

		wp_localize_script(
			'ht-checkout-payment-proof',
			'htPaymentProof',
			array(
				'gatewayId'          => self::GATEWAY_ID,
				'isThankYou'         => $is_thankyou,
				'autoOpenOnThankYou' => $auto_open,
				'orderId'            => $order ? $order->get_id() : 0,
				'i18n'               => array(
					'uploadSuccess' => __('Thank you — your payment proof was submitted.', 'octoways'),
				),
			)
		);
	}

	/**
	 * Output modal markup in footer.
	 */
	public function render_modal()
	{
		if (!$this->should_load()) {
			return;
		}

		$gateway = \HT_Gateway_Upload_Proof::get_gateway();

		if (!$gateway) {
			return;
		}

		$qr_url      = esc_url($gateway->qr_image);
		$cf7         = trim($gateway->cf7_shortcode);
		$order       = $this->get_thankyou_order();
		$order_id    = $order ? $order->get_id() : 0;
		$description = $gateway->get_description();

		get_template_part(
			'template-parts/checkout/payment-proof-modal',
			null,
			array(
				'qr_url'      => $qr_url,
				'cf7'         => $cf7,
				'order_id'    => $order_id,
				'description' => $description,
			)
		);
	}

	/**
	 * Pass order context into CF7 hidden fields on thank-you page.
	 *
	 * @param array $fields Hidden fields.
	 * @return array
	 */
	public function inject_order_hidden_fields($fields)
	{
		if (!is_array($fields)) {
			$fields = array();
		}

		$order = $this->get_thankyou_order();

		if (!$order || self::GATEWAY_ID !== $order->get_payment_method()) {
			return $fields;
		}

		$fields['order_id']  = (string) $order->get_id();
		$fields['order_key'] = $order->get_order_key();

		return $fields;
	}

	/**
	 * Parse Contact Form 7 ID from gateway shortcode.
	 *
	 * @param string $shortcode CF7 shortcode.
	 * @return int
	 */
	public static function get_cf7_form_id_from_shortcode($shortcode)
	{
		if (!is_string($shortcode) || '' === trim($shortcode)) {
			return 0;
		}

		if (preg_match('/\bid=["\']?(\d+)["\']?/i', $shortcode, $matches)) {
			return absint($matches[1]);
		}

		return 0;
	}

	/**
	 * CF7 file field name used for payment proof uploads.
	 *
	 * @return string
	 */
	public static function get_file_field_name()
	{
		return apply_filters('ht_payment_proof_cf7_file_field', self::DEFAULT_FILE_FIELD);
	}

	/**
	 * Save uploaded proof to Media Library and attach to the WooCommerce order.
	 *
	 * @param \WPCF7_ContactForm $contact_form Submitted form.
	 */
	public function save_payment_proof_to_order($contact_form)
	{
		if (!class_exists('\WPCF7_Submission') || !function_exists('wc_get_order')) {
			return;
		}

		$gateway = \HT_Gateway_Upload_Proof::get_gateway();

		if (!$gateway) {
			return;
		}

		$expected_form_id = self::get_cf7_form_id_from_shortcode($gateway->cf7_shortcode);

		if (!$expected_form_id || (int) $contact_form->id() !== $expected_form_id) {
			return;
		}

		$submission = \WPCF7_Submission::get_instance();

		if (!$submission) {
			return;
		}

		$posted    = $submission->get_posted_data();
		$order_id  = isset($posted['order_id']) ? absint($posted['order_id']) : 0;
		$order_key = isset($posted['order_key']) ? sanitize_text_field(wp_unslash($posted['order_key'])) : '';

		if (!$order_id || '' === $order_key) {
			return;
		}

		$order = wc_get_order($order_id);

		if (
			!$order
			|| $order->get_order_key() !== $order_key
			|| self::GATEWAY_ID !== $order->get_payment_method()
		) {
			return;
		}

		$file_paths = $this->get_uploaded_file_paths($submission);

		if (empty($file_paths)) {
			return;
		}

		$attachment_ids = array();

		foreach ($file_paths as $file_path) {
			$attachment_id = $this->create_attachment_from_upload($file_path, $order_id);

			if ($attachment_id) {
				$attachment_ids[] = $attachment_id;
			}
		}

		if (empty($attachment_ids)) {
			return;
		}

		$existing = $this->get_order_proof_attachment_ids($order);
		$merged   = array_values(array_unique(array_merge($existing, $attachment_ids)));

		$order->update_meta_data(self::ORDER_META_PROOF_IDS, $merged);
		$order->update_meta_data('_ht_payment_proof_latest_id', end($attachment_ids));
		$order->save();

		foreach ($attachment_ids as $attachment_id) {
			$file_url = wp_get_attachment_url($attachment_id);
			$filename = basename(get_attached_file($attachment_id));

			if (!$file_url) {
				continue;
			}

			$order->add_order_note(
				sprintf(
					/* translators: 1: file name, 2: proof URL */
					__('Payment proof uploaded: %1$s — <a href="%2$s" target="_blank" rel="noopener noreferrer">View file</a>', 'octoways'),
					esc_html($filename),
					esc_url($file_url)
				),
				false,
				true
			);
		}
	}

	/**
	 * Resolve uploaded file paths from a CF7 submission.
	 *
	 * @param \WPCF7_Submission $submission CF7 submission.
	 * @return string[]
	 */
	private function get_uploaded_file_paths($submission)
	{
		$uploaded   = $submission->uploaded_files();
		$field_name = self::get_file_field_name();
		$paths      = array();

		if (!empty($uploaded[ $field_name ])) {
			$paths = $this->normalize_upload_paths($uploaded[ $field_name ]);
		} elseif (is_array($uploaded)) {
			foreach ($uploaded as $files) {
				$paths = array_merge($paths, $this->normalize_upload_paths($files));
			}
		}

		return array_values(array_filter($paths, 'file_exists'));
	}

	/**
	 * @param string|string[] $paths File path or list of paths.
	 * @return string[]
	 */
	private function normalize_upload_paths($paths)
	{
		if (is_string($paths)) {
			return array($paths);
		}

		if (!is_array($paths)) {
			return array();
		}

		$normalized = array();

		foreach ($paths as $path) {
			if (is_string($path) && '' !== $path) {
				$normalized[] = $path;
			}
		}

		return $normalized;
	}

	/**
	 * Import an uploaded file into the Media Library.
	 *
	 * @param string $file_path Absolute path to uploaded file.
	 * @param int    $order_id  WooCommerce order ID.
	 * @return int Attachment ID or 0 on failure.
	 */
	private function create_attachment_from_upload($file_path, $order_id)
	{
		if (!file_exists($file_path) || !is_readable($file_path)) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$original_name = basename($file_path);
		$tmp_name      = wp_tempnam($original_name);

		if (!$tmp_name || !copy($file_path, $tmp_name)) {
			if ($tmp_name && file_exists($tmp_name)) {
				wp_delete_file($tmp_name);
			}
			return 0;
		}

		$file_array = array(
			'name'     => sprintf('order-%d-payment-proof-%s', $order_id, $original_name),
			'tmp_name' => $tmp_name,
		);

		$attachment_id = media_handle_sideload($file_array, 0);

		if (is_wp_error($attachment_id)) {
			wp_delete_file($tmp_name);
			return 0;
		}

		update_post_meta($attachment_id, self::ATTACHMENT_META_ORDER_ID, $order_id);

		return (int) $attachment_id;
	}

	/**
	 * Attachment IDs stored on an order.
	 *
	 * @param \WC_Order $order Order object.
	 * @return int[]
	 */
	public static function get_order_proof_attachment_ids($order)
	{
		if (!$order instanceof \WC_Order) {
			return array();
		}

		$stored = $order->get_meta(self::ORDER_META_PROOF_IDS);

		if (!is_array($stored)) {
			$stored = $stored ? array(absint($stored)) : array();
		}

		return array_values(array_filter(array_map('absint', $stored)));
	}

	/**
	 * Register order admin metabox for payment proofs.
	 */
	public function register_order_metabox()
	{
		if (!function_exists('wc_get_page_screen_id')) {
			return;
		}

		$screen = wc_get_page_screen_id('shop-order');

		add_meta_box(
			'ht-order-payment-proof',
			__('Payment proof', 'octoways'),
			array($this, 'render_order_metabox'),
			$screen,
			'side',
			'default'
		);
	}

	/**
	 * Render payment proof files in WooCommerce order admin.
	 *
	 * @param \WP_Post|\WC_Order $post_or_order Post or order object.
	 */
	public function render_order_metabox($post_or_order)
	{
		$order = ($post_or_order instanceof \WC_Order)
			? $post_or_order
			: wc_get_order($post_or_order);

		if (!$order instanceof \WC_Order) {
			echo '<p>' . esc_html__('Order not found.', 'octoways') . '</p>';
			return;
		}

		$attachment_ids = self::get_order_proof_attachment_ids($order);

		if (empty($attachment_ids)) {
			if (self::GATEWAY_ID === $order->get_payment_method()) {
				echo '<p>' . esc_html__('No payment proof uploaded yet.', 'octoways') . '</p>';
			} else {
				echo '<p class="description">' . esc_html__('Not used for this payment method.', 'octoways') . '</p>';
			}
			return;
		}

		echo '<ul class="ht-order-payment-proof-list" style="margin:0;padding:0;list-style:none;">';

		foreach ($attachment_ids as $attachment_id) {
			$url   = wp_get_attachment_url($attachment_id);
			$title = get_the_title($attachment_id);
			$date  = get_post_field('post_date', $attachment_id);

			if (!$url) {
				continue;
			}

			echo '<li style="margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #e5e5e5;">';

			if (wp_attachment_is_image($attachment_id)) {
				echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" style="display:block;margin-bottom:6px;">';
				echo wp_get_attachment_image($attachment_id, array(120, 120), false, array('style' => 'max-width:100%;height:auto;border-radius:4px;'));
				echo '</a>';
			}

			echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer"><strong>';
			echo esc_html($title ? $title : __('View payment proof', 'octoways'));
			echo '</strong></a>';

			if ($date) {
				echo '<br><span style="color:#646970;font-size:12px;">';
				echo esc_html(
					sprintf(
						/* translators: %s: upload date */
						__('Uploaded: %s', 'octoways'),
						wp_date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($date))
					)
				);
				echo '</span>';
			}

			echo '</li>';
		}

		echo '</ul>';
	}
}
