<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package parallax-one
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php parallax_hook_page_top(); ?>

	<?php
		$page_title = get_the_title();
	 if( !empty( $page_title ) ){ ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title single-title" itemprop="headline">', '</h1>' ); ?>
			<div class="colored-line-left"></div>
			<div class="clearfix"></div>
		</header><!-- .entry-header -->
	<?php } ?>

	<div class="entry-content content-page <?php if( empty( $page_title ) ){ echo 'parallax-one-top-margin-5px'; } ?>" itemprop="text">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'parallax-one' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( esc_html__( 'Edit', 'parallax-one' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .fentry-footer -->
	<?php parallax_hook_page_bottom(); ?>
</article><!-- #post-## -->
