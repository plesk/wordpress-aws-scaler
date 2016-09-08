<?php /* Template name: Authors List */ ?>  
<?php get_header(); ?>
	<section id="content" class="first clearfix" role="main">
		<div class="page-container full-width">
            <?php if (have_posts()) : ?>
	            <?php while ( have_posts() ) : the_post(); ?>
	                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
                        <div class="singlebox our_authors">
                           
		     					<header class="article-header">
								    <h1 class="post-title"><?php the_title(); ?></h1>
			  				    </header> <!-- end header -->
							    <section class="entry-content clearfix">
							        <?php the_content(); ?>
									    <?php $bubbly_guestauthors = get_users('orderby=post_count&role=contributor');
                                        if(isset($bubbly_guestauthors) && !empty($bubbly_guestauthors)){ 
				    foreach($bubbly_guestauthors as $author){
                         $posts = get_posts(array('author'=>$author->ID));
                               //if this author has posts, then include his name in the list otherwise don't
                         if(isset($posts) && !empty($posts)){?>
		                    <div class="item ">
                                <div class="desc center">
                                    <h6 class="authorName"><?php echo $author->display_name; ?></h6>
    			                        <p class="email"><a href="<?php echo get_author_posts_url( $author->ID ); ?>"><?php _e('View Posts', 'bubbly'); ?></a></p>                
                                </div>
                                <span class="line" style="width: 0px;"></span> 
                                <?php echo get_avatar( $author->user_email, '128' ); ?>                                   
                            </div> <?php }}} ?>
										
						    <?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'bubbly' ), 'after' => '</div>' ) ); ?>
							    <div class="clr"></div>
								</section> <!-- end section -->
							    <footer class="article-footer">
								    <?php edit_post_link( __( 'Edit', 'bubbly' ), '<span class="edit-link">', '</span>' ); ?>
							    </footer> <!-- end footer -->
							<?php if ( comments_open() || '0' != get_comments_number() ) comments_template( '', true ); ?>
                        </div>
					</article> <!-- end article -->
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</section> <!-- end #main -->
<div class="clr"></div>
<?php get_footer(); ?>