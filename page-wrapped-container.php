<?php
/**
 * Template Name: Wrapped Container Page
 * Template Post Type: page
 *
 * A reusable WordPress page template featuring:
 *   - A centered content container (.container)
 *   - A full outer page wrapper (.page-wrapper)
 *   - Support for full-width breakout sections (.full-width)
 *   - Semantic HTML5 markup
 *   - Standard WordPress template hierarchy
 *
 * Usage: Assign via WordPress Admin → Edit Page → Page Attributes → Template
 *
 * @package Heritage-website
 * @version 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main page-wrapper" role="main">

    <?php
    // Standard WordPress Loop
    while ( have_posts() ) :
        the_post();
    ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>

            <?php
            /**
             * Page Header
             * Renders the page title inside a constrained container.
             * Remove this block if the page design calls for no visible title,
             * or if the title is embedded inside the_content() via blocks.
             */
            if ( get_the_title() ) :
            ?>
                <header class="page-header">
                    <div class="container">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </div>
                </header>
            <?php endif; ?>

            <?php
            /**
             * Page Content
             * the_content() renders whatever the editor saved for this page.
             * Full-width sections are achieved by adding the class `.full-width`
             * to any block or custom HTML element inside the editor.
             *
             * See wrapped-container.css for the breakout technique.
             */
            ?>
            <div class="page-content entry-content" itemprop="text">
                <?php the_content(); ?>
            </div>

            <?php
            /**
             * Page navigation links (previous / next page links for paginated content).
             * Safe to remove if you never use the <!--nextpage--> tag.
             */
            wp_link_pages(
                array(
                    'before' => '<div class="page-links container">' . esc_html__( 'Pages:', 'Heritage-website' ),
                    'after'  => '</div>',
                )
            );
            ?>

        </article>

    <?php endwhile; // End of the loop. ?>

</main><!-- #primary -->

<?php
get_footer();
