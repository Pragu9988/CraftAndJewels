<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;
?>
<div class="ht-acount-page">
	<div class="kl-container">
		<div class="flex flex-col lg:flex-row gap-4 lg:gap-8 account-layout-wrapper">
			<div class="acount-sidebar w-full lg:w-1/5 shrink-0">
				<div class="sidebar-inner">
					<?php
					/**
					 * My Account navigation.
					 *
					 * @since 2.6.0
					 */
					do_action('woocommerce_account_navigation'); ?>

					<div class="sidebar-footer">
						<!-- <a href="#" class="btn-premium">JOIN PREMIUM
							MEMBER</a> -->
						<div class="footer-links">
							<a href="#">Support</a>
							<a href="#">Privacy</a>
						</div>
					</div>
				</div>
			</div>

			<div class="woocommerce-MyAccount-content w-full lg:w-4/5 flex-1">
				<?php
				/**
				 * My Account content.
				 *
				 * @since 2.6.0
				 */
				do_action('woocommerce_account_content');
				?>
			</div>
		</div>
	</div>
</div>