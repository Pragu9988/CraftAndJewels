<?php
/**
 * Contact Us — Heritage Craft & Jewels
 *
 * ACF (page with this template):
 * - contact_cf7_shortcode (required for form) — paste e.g. [contact-form-7 id="123" title="Contact"]
 * - contact_intro (optional) — replaces default intro paragraph
 * - contact_email, contact_phone, contact_address, contact_hours (optional) — sidebar details
 *
 * Contact Form 7 — paste this into the CF7 form editor (Form tab):
 *
 * <div class="ht-cf7-grid">
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Your name *
 * [text* your-name class:ht-cf7-input autocomplete:name]</label>
 * </div>
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Your email *
 * [email* your-email class:ht-cf7-input autocomplete:email]</label>
 * </div>
 * </div>
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Phone
 * [tel your-phone class:ht-cf7-input autocomplete:tel]</label>
 * </div>
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Subject
 * [text your-subject class:ht-cf7-input]</label>
 * </div>
 * <div class="ht-cf7-field">
 * <label class="ht-cf7-label">Your message *
 * [textarea* your-message class:ht-cf7-textarea 40x6]</label>
 * </div>
 * [submit class:ht-cf7-submit "Send message"]
 *
 * @package octoways
 */

$page_id = get_queried_object_id();

$defaults = [
	'intro' => 'We would love to hear from you — whether you have a question about our collections, a custom order, or a B2B partnership. Send us a message and our team will respond as soon as possible.',
	'email' => 'hello@heritagecraftandjewels.com',
	'phone' => '+977-1-XXXXXXX',
	'address' => 'Kathmandu, Nepal',
	'hours' => 'Sun–Fri, 10:00 – 18:00 NPT',
];

$intro_raw = function_exists('get_field') ? get_field('contact_intro', $page_id) : '';
$intro_is_custom = is_string($intro_raw) && $intro_raw !== '';

$email = function_exists('get_field') ? get_field('contact_email', $page_id) : '';
$phone = function_exists('get_field') ? get_field('contact_phone', $page_id) : '';
$address_custom = function_exists('get_field') ? get_field('contact_address', $page_id) : '';
$address_is_custom = is_string($address_custom) && $address_custom !== '';
$hours = function_exists('get_field') ? get_field('contact_hours', $page_id) : '';

$email = (is_string($email) && $email !== '') ? $email : $defaults['email'];
$phone = (is_string($phone) && $phone !== '') ? $phone : $defaults['phone'];
$hours = (is_string($hours) && $hours !== '') ? $hours : $defaults['hours'];

$cf7_shortcode = function_exists('get_field') ? get_field('contact_cf7_shortcode', $page_id) : '';
?>

<section class="ht-contact" aria-labelledby="contact-section-title">
	<div class="kl-container">
		<div class="row justify-center mb-10">
			<div class="col-xs-12 col-md-10 col-lg-8 text-center">
				<div class="strapline mb-2">
					<span>Heritage Craft & Jewels</span>
				</div>
				<h1 class="section-title leading-tight mb-3" id="contact-section-title">
					Contact <span class="highlight">Us</span>
				</h1>
				<p class="normal-text">
					<?php
					if ($intro_is_custom) {
						echo wp_kses_post($intro_raw);
					} else {
						echo esc_html($defaults['intro']);
					}
					?>
				</p>
			</div>
		</div>

		<div class="row ht-contact__grid">
			<div class="col-xs-12 col-lg-5 mb-8 lg:mb-0">
				<div class="ht-contact__aside">
					<p class="ht-contact__aside-lead normal-text">
						Reach us directly for orders, partnerships, or bespoke design consultations.
					</p>
					<ul class="ht-contact__channels" role="list">
						<li class="ht-contact__channel">
							<span class="ht-contact__channel-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
							</span>
							<div>
								<span class="ht-contact__channel-label">Email</span>
								<a class="ht-contact__channel-value" href="mailto:<?php echo esc_attr(sanitize_email($email)); ?>"><?php echo esc_html($email); ?></a>
							</div>
						</li>
						<li class="ht-contact__channel">
							<span class="ht-contact__channel-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
							</span>
							<div>
								<span class="ht-contact__channel-label">Phone</span>
								<a class="ht-contact__channel-value" href="tel:<?php echo esc_attr(preg_replace('/[^\d+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
							</div>
						</li>
						<li class="ht-contact__channel">
							<span class="ht-contact__channel-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
							</span>
							<div>
								<span class="ht-contact__channel-label">Visit</span>
								<span class="ht-contact__channel-value ht-contact__channel-value--multiline">
									<?php
									if ($address_is_custom) {
										echo wp_kses_post($address_custom);
									} else {
										echo esc_html($defaults['address']);
									}
									?>
								</span>
							</div>
						</li>
						<li class="ht-contact__channel">
							<span class="ht-contact__channel-icon" aria-hidden="true">
								<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							</span>
							<div>
								<span class="ht-contact__channel-label">Hours</span>
								<span class="ht-contact__channel-value"><?php echo esc_html($hours); ?></span>
							</div>
						</li>
					</ul>
				</div>
			</div>

			<div class="col-xs-12 col-lg-7">
				<div class="ht-contact__form-card">
					<h2 class="ht-contact__form-title section-title mb-4">Send a message</h2>
					<?php if (is_string($cf7_shortcode) && $cf7_shortcode !== '') : ?>
						<div class="ht-contact__form-inner">
							<?php echo do_shortcode($cf7_shortcode); ?>
						</div>
					<?php else : ?>
						<p class="normal-text ht-contact__placeholder">
							<?php esc_html_e('Add your Contact Form 7 shortcode in the page fields (ACF: Contact Form 7 shortcode).', 'octoways'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
