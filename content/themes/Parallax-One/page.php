<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package parallax-one
 */
$paralax_one_full_width_template = get_theme_mod('paralax_one_full_width_template');
if(isset($paralax_one_full_width_template) && $paralax_one_full_width_template != 1) {
	get_header(); ?>

		</div>
		<!-- /END COLOR OVER IMAGE -->
		<?php parallax_hook_header_bottom(); ?>
	</header>
	<!-- /END HOME / HEADER  -->
	<?php parallax_hook_header_after(); ?>

	<?php parallax_hook_content_before(); ?>
	<div id="content" class="content-warp">
		<?php parallax_hook_content_top(); ?>
		<div class="container">
			<div id="primary" class="content-area <?php if ( is_active_sidebar( 'sidebar-1' ) ) { echo 'col-md-8';} else {echo 'col-md-12';}  ?>">
				<main itemscope itemtype="http://schema.org/WebPageElement" itemprop="mainContentOfPage" id="main" class="site-main" role="main">

				<?php parallax_hook_page_before();?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>

				<?php endwhile; // end of the loop. ?>
				<?php parallax_hook_page_after();?>

				</main><!-- #main -->
			</div><!-- #primary -->
			<?php get_sidebar(); ?>

		</div>
		<?php parallax_hook_content_bottom(); ?>
	</div><!-- .content-wrap -->
	<?php parallax_hook_content_after(); ?>
	<?php get_footer(); ?>
	<?php
} else {
	include ('template-fullwidth.php');
}