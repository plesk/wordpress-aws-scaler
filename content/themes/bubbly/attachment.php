<?php get_header(); ?>
	<section id="content" class="first clearfix" role="main">
		<div class="post-container">
			<?php if (have_posts()) : ?>
               	<?php while ( have_posts() ) : the_post(); ?>
   			        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
					    <div class="singlebox">
		                    <?php bubbly_breadcrumbs() ?>
                                <header class="article-header">
									<h1 class="post-title"><?php the_title(); ?></h1>
										<div class="">
			                            <?php get_template_part( 'inc/post-meta' ); // Get Post Meta template ?>	
			
		                            </div><!-- .entry-meta -->
								</header> <!-- end article header -->
                                <section class="entry-content clearfix">
								<div class="attachment">
                                <?php  $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
                                    foreach ( $attachments as $k => $attachment ) {
                                        if ( $attachment->ID == $post->ID )
                                            break;
                                    }
                                    $k++;
                                    // If there is more than 1 attachment in a gallery
                                    if ( count( $attachments ) > 1 ) {
                                        if ( isset( $attachments[ $k ] ) )
                                            // get the URL of the next image attachment
                                            $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                                        else
                                            // or get the URL of the first image attachment
                                            $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
                                    } else {
                                        // or, if there's only 1 image, get the URL of the image
                                        $next_attachment_url = wp_get_attachment_url();
                                    }
                                ?>
 
                                <?php echo wp_get_attachment_image( $post->ID, 'large' ); ?>
                                </div><!-- .attachment -->
                                </section> <!-- end article section -->

								<footer class="article-footer">
								<?php the_tags('<p class="tags"><span class="tags-title">' . __('Tags:', 'bubbly') . '</span> ', ' ', '</p>'); ?>
									<p class="tags"></p>
                                    <?php edit_post_link( __( 'Edit', 'bubbly' ), '<span class="edit-link">', '</span>' ); ?>
								</footer> <!-- end article footer -->
								<nav id="image-navigation" class="site-navigation">
                                    <span class="previous-image"><?php previous_image_link( true, __( '<< Previous', 'bubbly' ) ); ?></span>
                                    <span class="next-image"><?php next_image_link( true, __( 'Next >>', 'bubbly' ) ); ?></span>
                                </nav><!-- #image-navigation -->
                                <?php get_template_part( 'inc/single', 'post-share' ); ?>
                                <?php if ( comments_open() || '0' != get_comments_number() ) comments_template( '', true ); ?>	
                        </div>
					</article> <!-- end article -->
                    <?php endwhile; ?>
				<?php endif; ?>
			
		</div>
	</section> <!-- end #main -->

<?php get_footer(); ?>