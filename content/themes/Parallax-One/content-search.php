<?php
/**
 * The template part for displaying results in search pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package parallax-one
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('border-bottom-hover'); ?>>
	<?php parallax_hook_search_top(); ?>
	<header class="entry-header">

			<div class="post-img-wrap">
			 	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >

					<?php
						if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
					?>
						<?php
							$image_id = get_post_thumbnail_id();
							$image_url_big = wp_get_attachment_image_src($image_id,'parallax-one-post-thumbnail-big', true);
							$image_url_mobile = wp_get_attachment_image_src($image_id,'parallax-one-post-thumbnail-mobile', true);
						?>
				 		<picture>
							<source media="(max-width: 600px)" srcset="<?php echo esc_url($image_url_mobile[0]); ?>">
							<img src="<?php echo esc_url($image_url_big[0]); ?>" alt="<?php the_title_attribute(); ?>">
						</picture>
					<?php
						} else {
					?>
				 		<picture>
							<source media="(max-width: 600px)" srcset=" <?php echo parallax_get_file('/images/no-thumbnail-mobile.jpg'); ?> ">
							<img src="<?php echo parallax_get_file('/images/no-thumbnail.jpg'); ?>" alt="<?php the_title_attribute(); ?>">
						</picture>
					<?php } ?>

				</a>
				<div class="post-date">
					<span class="post-date-day"><?php the_time('d'); ?></span>
					<span class="post-date-month"><?php the_time('M'); ?></span>
				</div>
			</div>

			<div class="entry-meta list-post-entry-meta">
				<span class="post-author">
					<i class="icon-man-people-streamline-user"></i><?php the_author_posts_link(); ?>
				</span>

					<?php
						/* translators: used between list items, there is a space after the comma */
						$categories_list = get_the_category_list( esc_html__( ', ', 'parallax-one' ) );
                        if(!empty($categories_list)){
                        ?>
                            <span class="posted-in">
                                <i class="icon-basic-elaboration-folder-check"></i>
                        <?php
                            esc_html_e('Posted in ','parallax-one');

                            $pos = strpos($categories_list, ',');
                            if ( $pos ) {
                                echo substr($categories_list, 0, $pos);
                            } else {
                                echo $categories_list;
                            }
                            echo '</span>';
                        }
					?>

				<a href="<?php comments_link(); ?>" class="post-comments">
					<i class="icon-comment-alt"></i><?php comments_number( esc_html__('No comments','parallax-one'), esc_html__('One comment','parallax-one'), esc_html__('% comments','parallax-one') ); ?>
				</a>
			</div><!-- .entry-meta -->

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		<div class="colored-line-left"></div>
		<div class="clearfix"></div>

	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php
			$ismore = @strpos( $post->post_content, '<!--more-->');
			if($ismore) : the_content();
			else : the_excerpt();
			endif;
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'parallax-one' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php parallax_hook_search_bottom(); ?>
</article><!-- #post-## -->
