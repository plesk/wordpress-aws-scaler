
<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package parallax-one
 */

	get_header();
?>

	</div>
	<!-- /END COLOR OVER IMAGE -->
	<?php parallax_hook_header_bottom(); ?>
</header>
<!-- /END HOME / HEADER  -->
<?php parallax_hook_header_after(); ?>

<?php parallax_hook_content_before(); ?>
<div role="main" id="content" class="content-warp">
	<?php parallax_hook_content_top(); ?>
	<div class="container">

		<div id="primary" class="content-area col-md-8">
			<main <?php if(have_posts()) echo 'itemscope itemtype="http://schema.org/Blog"'; ?> id="main" class="site-main" role="main">

				<?php if ( have_posts() ) : ?>

					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php parallax_hook_entry_before(); ?>
						<?php
							/* Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );
						?>
						<?php parallax_hook_entry_after(); ?>
					<?php endwhile; ?>

					<?php the_posts_navigation(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php get_sidebar(); ?>

	</div>
	<?php parallax_hook_content_bottom(); ?>
</div><!-- .content-wrap -->
<?php parallax_hook_content_after(); ?>
<?php get_footer(); ?>
