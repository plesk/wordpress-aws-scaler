<?php
/**
 * The template for displaying search results pages.
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
<div itemscope itemtype="http://schema.org/SearchResultsPage" role="main" id="content" class="content-warp">
	<?php parallax_hook_content_top(); ?>
	<div class="container">

		<div id="primary" class="content-area col-md-8 post-list">
			<main id="main" class="site-main" role="main">
				<?php parallax_hook_search_before(); ?>
				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h2 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'parallax-one' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
					</header><!-- .page-header -->


					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'content', 'search' );
						?>
					<?php endwhile; ?>

					<?php the_posts_navigation(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

				<?php parallax_hook_search_after(); ?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php get_sidebar(); ?>

	</div>
	<?php parallax_hook_content_bottom(); ?>
</div><!-- .content-wrap -->
<?php parallax_hook_content_after(); ?>
<?php get_footer(); ?>
