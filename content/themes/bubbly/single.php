<?php get_header(); ?>
	<section id="content" class="first clearfix" role="main">
		<div class="post-container">
			<?php if (have_posts()) : ?>
               	<?php while ( have_posts() ) : the_post(); ?>
   			        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
  						<div class="singlebox">
						   
                                <header class="article-header">
									<h1 class="post-title"><?php the_title(); ?></h1>
									<div id="post-meta"><?php get_template_part( 'inc/post-meta' ); ?></div>
								</header> <!-- end header -->
								<section class="entry-content clearfix">
									<?php the_content(); ?>
									<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'bubbly' ), 'after' => '</div>' ) ); ?>
									<div class="clr"></div>
								</section> <!-- end section -->
								<footer class="article-footer">
								    <?php the_tags('<p class="tags"><span class="tags-title">' . __('Tags:', 'bubbly') . '</span> ', ' ', '</p>'); ?>
									<p class="tags"></p>
                                    <?php edit_post_link( __( 'Edit', 'bubbly' ), '<span class="edit-link">', '</span>' ); ?>
								</footer> <!-- end footer -->
                               
                                <?php get_template_part( 'inc/author', 'bio' ); ?>
                                <?php get_template_part( 'inc/related', 'posts' ); ?>                   	
                                <?php if ( comments_open() || '0' != get_comments_number() ) comments_template( '', true ); ?>	
                        </div>
					</article> <!-- end article -->
                <?php endwhile; ?>
			<?php endif; ?>
		</div>															
	</section> <!-- end #main -->  

<?php get_footer(); ?>