<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package octoways
 */

get_header();
?>

<main id="primary" class="site-main">

	<section class="error-404 not-found py-7 md:py-20 flex items-center justify-center">

		<div class="page-content w-full">
			<div class="kl-container mx-auto px-4">
				<div class="error-404-inner max-w-5xl mx-auto text-center">
					<div
						class="error-404-content flex flex-col items-center justify-center space-y-8">
						<img class="img-fluid d-table mx-auto w-full max-w-md md:max-w-lg object-contain mb-5 transition-transform duration-500 hover:scale-105"
							src="<?php echo OCTOWAYS_SRC_IMG_URI . '/404.webp'; ?>"
							alt="404 Not Found" loading="lazy">

						<p
							class="error-404-description text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
							<?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'octoways'); ?>
						</p>

						<a href="<?php echo esc_url(home_url('/')); ?>"
							class="button flex items-center gap-2 justify-center flex-nowrap  oct-btn ">
							<?php esc_html_e('Back to Home', 'octoways'); ?>
						</a>
					</div>
				</div>
			</div>
		</div><!-- .page-content -->
	</section><!-- .error-404 -->

</main><!-- #main -->

<?php
get_footer();