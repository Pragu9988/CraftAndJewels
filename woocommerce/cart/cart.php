<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */
?>
<div class="ht-cart-page">
	<div class="kl-container">
		<?php
		defined('ABSPATH') || exit;
		do_action('woocommerce_before_cart'); ?>

		<div class="flex flex-col lg:flex-row gap-12 w-full">
			<div class="w-full lg:w-2/3">
				<form class="woocommerce-cart-form"
					action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
					<div class="cart-header ">
						<h2 class="cart-title">
							CARTS
						</h2>
					</div>

					<?php do_action('woocommerce_before_cart_table'); ?>

					<div
						class="shop_table shop_table_responsive cart woocommerce-cart-form__contents flex flex-col w-full">
						<?php do_action('woocommerce_before_cart_contents'); ?>

						<?php
						foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
							$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
							$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
							$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

							if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
								$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

								// Primary term for category pill
								$terms = get_the_terms($product_id, 'product_cat');
								$primary_term = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
								?>
								<div
									class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?> flex flex-col md:flex-row gap-6 py-6 border-b border-solid border-neutra-200 relative">

									<div
										class="product-thumbnail w-[100px] shrink-0 bg-[#F7F7F5] relative h-[100px] flex items-center justify-center ">
										<?php
										$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
										if (!$product_permalink) {
											echo $thumbnail; // PHPCS: XSS ok.
										} else {
											printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
										}
										?>
									</div>

									<div
										class="flex-1 flex flex-col justify-center relative w-full">
										<div
											class="flex justify-between items-start mb-1">
											<div
												class="flex flex-col items-start gap-2">
												<?php if ($primary_term): ?>
													<span
														class="text-[9px] px-3 py-1 rounded-full border border-text-400 text-text-400 font-semibold uppercase tracking-widest mb-1"><?php echo esc_html($primary_term); ?></span>
												<?php endif; ?>

												<div class="product-name font-bold text-[var(--fs-410)] leading-tight mb-2"
													data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>"
													style="color: var(--clr-neutral-550);">
													<?php
													if (!$product_permalink) {
														echo wp_kses_post($product_name . '&nbsp;');
													} else {
														echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="product-title">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
													}
													do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
													?>
												</div>
											</div>

											<div class="product-price font-bold font-mono text-[var(--fs-410)] whitespace-nowrap mt-7"
												data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>"
												style="color: var(--clr-neutral-550);">
												<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
											</div>
										</div>

										<div
											class="flex justify-between items-end mt-4">
											<div
												class="product-meta text-sm text-[var(--clr-neutral-400)] flex items-center gap-4">
												<?php echo wc_get_formatted_cart_item_data($cart_item); ?>
											</div>

											<div
												class="flex items-center gap-5 actions-wrapper">
												<div
													class="product-remove mt-1  pr-3 border-r border-gray-300">
													<?php
													echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														'woocommerce_cart_item_remove_link',
														sprintf(
															'<a role="button" href="%s" class="remove flex items-center justify-center text-[var(--clr-neutral-400)] hover:text-red-500 transition-colors" aria-label="%s" data-product_id="%s" data-product_sku="%s">
																<svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M1.5 4H12.5M4.83333 4V2.44444C4.83333 1.58533 5.52978 0.888885 6.38889 0.888885H7.61111C8.47022 0.888885 9.16667 1.58533 9.16667 2.44444V4M3.05556 4V13.3333C3.05556 14.1924 3.752 14.8889 4.61111 14.8889H9.38889C10.248 14.8889 10.9444 14.1924 10.9444 13.3333V4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
																	<path d="M5.5 7.5V11M8.5 7.5V11" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
																</svg>
															</a>',
															esc_url(wc_get_cart_remove_url($cart_item_key)),
															esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
															esc_attr($product_id),
															esc_attr($_product->get_sku())
														),
														$cart_item_key
													);
													?>
												</div>

												<!-- cart wishlist removed -->

												<div class="product-quantity  flex gap-1 items-center justify-between ml-2 bg-transparent overflow-hidden"
													data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
													<?php
													if ($_product->is_sold_individually()) {
														$min_quantity = 1;
														$max_quantity = 1;
													} else {
														$min_quantity = 0;
														$max_quantity = $_product->get_max_purchase_quantity();
													}
													echo '<button type="button" class="qty-btn qty-minus text-gray-500 hover:text-black w-7 text-center cursor-pointer font-bold select-none">-</button>';
													$product_quantity = woocommerce_quantity_input(
														array(
															'input_name' => "cart[{$cart_item_key}][qty]",
															'input_value' => $cart_item['quantity'],
															'max_value' => $max_quantity,
															'min_value' => $min_quantity,
															'product_name' => $product_name,
															'classes' => array('input-text', 'qty', 'text', 'bg-transparent', 'border-0', 'text-center', 'w-[28px]', 'p-0', 'focus:ring-0', 'text-sm', 'font-bold', 'hide-spinners'),
														),
														$_product,
														false
													);
													echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
													echo '<button type="button" class="qty-btn qty-plus text-gray-500 hover:text-black w-7 text-center cursor-pointer font-bold select-none">+</button>';
													?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>

						<?php do_action('woocommerce_cart_contents'); ?>

						<div class="actions" style="display: none;">
							<button type="submit" class="button"
								name="update_cart"
								id="update_cart_btn"
								value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
							<?php do_action('woocommerce_cart_actions'); ?>
							<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
						</div>

						<?php do_action('woocommerce_after_cart_contents'); ?>
					</div>
					<?php do_action('woocommerce_after_cart_table'); ?>
				</form>
				<script>
					document.addEventListener('DOMContentLoaded', function () {
						const body = document.querySelector('body');
						body.addEventListener('click', function (e) {
							if (e.target.classList.contains('qty-plus') || e.target.classList.contains('qty-minus')) {
								const wrapper = e.target.closest('.product-quantity');
								const input = wrapper.querySelector('input.qty');
								let val = parseFloat(input.value) || 0;
								const max = parseFloat(input.getAttribute('max'));
								const min = parseFloat(input.getAttribute('min')) || 0;

								if (e.target.classList.contains('qty-plus')) {
									if (isNaN(max) || val < max) {
										input.value = val + 1;
									}
								} else {
									if (val > min) {
										input.value = val - 1;
									}
								}
								input.dispatchEvent(new Event('change', { bubbles: true }));
								document.getElementById('update_cart_btn').click();
							}
						});
					});
				</script>
			</div>

			<div class="w-full lg:w-1/3 max-w-[380px] ml-auto">
				<h2 class="cart-title mb-3">
					SUMMARY</h2>

				<div
					class="cart-collaterals-wrap bg-[#fcfcfb] border border-[var(--clr-secondary-200)] rounded-[var(--radius-lg)] p-7 shadow-sm relative">

					<?php do_action('woocommerce_before_cart_collaterals'); ?>

					<div class="cart-collaterals mb-6">
						<?php do_action('woocommerce_cart_collaterals'); ?>
					</div>

					<form class="woocommerce-cart-form-coupon mt-2"
						action="<?php echo esc_url(wc_get_cart_url()); ?>"
						method="post">
						<?php if (wc_coupons_enabled()) { ?>
							<div
								class="coupon flex items-center bg-white border border-gray-200  h-11 relative rounded-xl">
								<input type="text" name="coupon_code"
									class="input-text w-full bg-transparent border-0 focus:ring-0 px-5 text-sm"
									id="coupon_code" value=""
									placeholder="<?php esc_attr_e('Enter promo code', 'woocommerce'); ?>"
									style="color: var(--clr-neutral-550); outline: none; box-shadow:none; flex: 1; border:unset; padding: 2px 11px;" />
								<button type="submit"
									class="shrink-0 bg-black text-white hover:bg-gray-800 rounded-lg px-6 transition-colors h-full absolute right-0 top-0 bottom-0 text-[11px] font-bold tracking-wider"
									style="border: 2px solid white;"
									name="apply_coupon"
									value="<?php esc_attr_e('Apply', 'woocommerce'); ?>">APPLY</button>
							</div>
						<?php } ?>
					</form>
				</div>

				<div class="checkout-buttons flex flex-col gap-3 mt-4">
					<a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
						class="checkout-button ht-btn ht-btn--primary text-center">Checkout</a>

				</div>
			</div>
		</div>

		<?php do_action('woocommerce_after_cart'); ?>

	</div>
</div>