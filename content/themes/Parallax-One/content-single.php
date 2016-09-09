<?php
/**
 * @package parallax-one
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('content-single-page'); ?>>
	<header class="entry-header single-header">
		<?php the_title( '<h1 itemprop="headline" class="entry-title single-title">', '</h1>' ); ?>
		<div class="colored-line-left"></div>
		<div class="clearfix"></div>

		<div class="entry-meta single-entry-meta">
			<span class="author-link" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
				<span itemprop="name" class="post-author author vcard">
					<i class="icon-man-people-streamline-user"></i><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' )); ?>" itemprop="url" rel="author"><?php the_author(); ?></a>
				</span>
        	</span>
			<time class="post-time posted-on published" datetime="<?php the_time('c'); ?>" itemprop="datePublished">
				<i class="icon-clock-alt"></i><?php the_time( get_option('date_format') ); ?>
			</time>
			<a href="<?php comments_link(); ?>" class="post-comments">
				<i class="icon-comment-alt"></i><?php comments_number( esc_html__('No comments','parallax-one'), esc_html__('One comment','parallax-one'), esc_html__('% comments','parallax-one') ); ?>
			</a>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div itemprop="text" class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'parallax-one' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php parallax_one_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
