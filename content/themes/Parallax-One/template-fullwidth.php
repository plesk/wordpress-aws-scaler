<?php
/**
 * Template name: Full Width
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
<div class="content-wrap">
	<div class="container">
		<?php
			$page_title = get_the_title(); ?>
		<div id="primary" class="content-area col-md-12 <?php if( empty( $page_title ) ){ echo 'parallax-one-top-margin-5px'; } ?>">
			<main id="main" class="site-main" role="main">
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

	</div>
</div><!-- .content-wrap -->

<?php get_footer(); ?>
