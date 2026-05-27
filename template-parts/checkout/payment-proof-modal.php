<?php
/**
 * Payment proof modal (QR + CF7 upload form).
 *
 * @package octoways
 *
 * @var string $qr_url
 * @var string $cf7
 * @var int    $order_id
 * @var string $description
 */

defined('ABSPATH') || exit;

$qr_url      = isset($args['qr_url']) ? $args['qr_url'] : '';
$cf7         = isset($args['cf7']) ? $args['cf7'] : '';
$order_id    = isset($args['order_id']) ? absint($args['order_id']) : 0;
$description = isset($args['description']) ? $args['description'] : '';
?>
<div
	id="ht-payment-proof-modal"
	class="ht-payment-proof-modal ht-floating-modal"
	aria-hidden="true"
	role="dialog"
	aria-labelledby="ht-payment-proof-modal-title"
	data-order-id="<?php echo esc_attr((string) $order_id); ?>"
>
	<div class="ht-floating-modal__overlay" data-ht-close-payment-proof></div>
	<div class="ht-floating-modal__dialog ht-payment-proof-modal__dialog">
		<button
			type="button"
			class="ht-floating-modal__close ht-payment-proof-modal__close"
			data-ht-close-payment-proof
			aria-label="<?php esc_attr_e('Close', 'octoways'); ?>"
		>
			<span aria-hidden="true">&times;</span>
		</button>

		<div class="ht-payment-proof-modal__header">
			<h2 id="ht-payment-proof-modal-title" class="ht-payment-proof-modal__title">
				<?php esc_html_e('Pay and upload proof', 'octoways'); ?>
			</h2>
			<?php if ($description) : ?>
				<div class="ht-payment-proof-modal__intro">
					<?php echo wp_kses_post(wpautop(wptexturize($description))); ?>
				</div>
			<?php endif; ?>
			<?php if ($order_id) : ?>
				<p class="ht-payment-proof-modal__order">
					<?php
					printf(
						/* translators: %s: order number */
						esc_html__('Order #%s', 'octoways'),
						esc_html((string) $order_id)
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<div class="ht-payment-proof-modal__body">
			<?php if ($qr_url) : ?>
				<div class="ht-payment-proof-modal__qr">
					<img src="<?php echo esc_url($qr_url); ?>" alt="<?php esc_attr_e('Payment QR code', 'octoways'); ?>" width="240" height="240" loading="lazy" />
				</div>
			<?php endif; ?>

			<div class="ht-payment-proof-modal__form ht-cf7-payment-proof">
				<?php
				if (is_string($cf7) && $cf7 !== '' && shortcode_exists('contact-form-7')) {
					echo do_shortcode($cf7); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<p>' . esc_html__('Payment upload form is not configured.', 'octoways') . '</p>';
				}
				?>
			</div>

			<div class="ht-payment-proof-modal__success" hidden></div>
		</div>
	</div>
</div>
