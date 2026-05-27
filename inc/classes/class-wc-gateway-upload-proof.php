<?php
/**
 * WooCommerce gateway: Pay and upload proof.
 *
 * @package octoways
 */

defined('ABSPATH') || exit;

/**
 * Pay and upload proof payment gateway.
 */
class HT_Gateway_Upload_Proof extends WC_Payment_Gateway
{
	/**
	 * QR code image URL (Media Library).
	 *
	 * @var string
	 */
	public $qr_image;

	/**
	 * Contact Form 7 shortcode.
	 *
	 * @var string
	 */
	public $cf7_shortcode;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->id                 = 'ht_upload_proof';
		$this->icon               = '';
		$this->has_fields         = true;
		$this->method_title       = __('Pay and upload proof', 'octoways');
		$this->method_description = __('Customers pay via QR code and upload payment proof after placing the order.', 'octoways');
		$this->supports           = array('products');

		$this->init_form_fields();
		$this->init_settings();

		$this->enabled       = $this->get_option('enabled');
		$this->title         = $this->get_option('title');
		$this->description   = $this->get_option('description');
		$this->qr_image      = $this->get_option('qr_image');
		$this->cf7_shortcode = $this->get_option('cf7_shortcode');

		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
	}

	/**
	 * Admin settings fields.
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'       => array(
				'title'   => __('Enable/Disable', 'octoways'),
				'type'    => 'checkbox',
				'label'   => __('Enable Pay and upload proof', 'octoways'),
				'default' => 'no',
			),
			'title'         => array(
				'title'       => __('Title', 'octoways'),
				'type'        => 'text',
				'description' => __('Payment method title shown at checkout.', 'octoways'),
				'default'     => __('Pay and upload proof', 'octoways'),
				'desc_tip'    => true,
			),
			'description'   => array(
				'title'       => __('Description', 'octoways'),
				'type'        => 'textarea',
				'description' => __('Short instructions shown in the payment box at checkout.', 'octoways'),
				'default'     => __('Pay using the QR code, then upload your payment proof. You can place your order first; upload proof on the confirmation screen.', 'octoways'),
				'desc_tip'    => true,
			),
			'qr_image'      => array(
				'title'       => __('QR code image URL', 'octoways'),
				'type'        => 'text',
				'description' => __('Paste the full image URL from the WordPress Media Library.', 'octoways'),
				'default'     => '',
				'desc_tip'    => true,
			),
			'cf7_shortcode' => array(
				'title'       => __('Contact Form 7 shortcode', 'octoways'),
				'type'        => 'text',
				'description' => __('e.g. [contact-form-7 id="123" title="Payment proof"]', 'octoways'),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Checkout payment fields.
	 */
	public function payment_fields()
	{
		if ($this->description) {
			echo wp_kses_post(wpautop(wptexturize($this->description)));
		}
		?>
		<p class="ht-upload-proof-trigger-wrap">
			<button type="button" class="button ht-upload-proof-open-modal" data-ht-open-payment-proof>
				<?php esc_html_e('View payment instructions', 'octoways'); ?>
			</button>
		</p>
		<?php
	}

	/**
	 * Process payment — order on-hold until proof is verified.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id)
	{
		$order = wc_get_order($order_id);

		if (!$order) {
			return array(
				'result'   => 'failure',
				'redirect' => '',
			);
		}

		$order->update_status('on-hold', __('Awaiting payment proof upload.', 'octoways'));
		wc_reduce_stock_levels($order_id);
		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url($order),
		);
	}

	/**
	 * Whether the gateway is enabled and configured for the modal.
	 *
	 * @return bool
	 */
	public static function is_configured()
	{
		if (!function_exists('WC') || !WC()->payment_gateways) {
			return false;
		}

		$gateways = WC()->payment_gateways->payment_gateways();
		$gateway  = isset($gateways['ht_upload_proof']) ? $gateways['ht_upload_proof'] : null;

		if (!$gateway || 'yes' !== $gateway->enabled) {
			return false;
		}

		$shortcode = is_string($gateway->cf7_shortcode) ? trim($gateway->cf7_shortcode) : '';

		return '' !== $shortcode;
	}

	/**
	 * Get the active gateway instance.
	 *
	 * @return HT_Gateway_Upload_Proof|null
	 */
	public static function get_gateway()
	{
		if (!function_exists('WC') || !WC()->payment_gateways) {
			return null;
		}

		$gateways = WC()->payment_gateways->payment_gateways();

		if (!isset($gateways['ht_upload_proof']) || !($gateways['ht_upload_proof'] instanceof HT_Gateway_Upload_Proof)) {
			return null;
		}

		return $gateways['ht_upload_proof'];
	}
}
