<?php get_header(); ?>
	<section id="content" class="first clearfix">
		<div class="cat-container">	
	    	<div class="cat-head mbottom">
					<h1 class="archive-title">
						<?php _e("Search Results For:", "bubbly"); ?> <?php echo get_search_query(); ?>
					</h1>
                    <?php echo category_description(); ?>
				</div>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                				
				<article class="item-list mbottom">
			        <div class="cthumb">
		                <?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
					        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium');?></a> 
                 			<?php endif; ?>
					        <div class="catbox"><?php printf('%1$s', get_the_category_list( ) ); ?></div>
				    </div>
			        <div class="cdetail">		
			            <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					        <div class="catpost"><?php the_excerpt(); ?></div>
                            <div class="postmeta">
			           		    <p class="vsmall pnone">by  <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) )?>" title="<?php sprintf( esc_attr__( 'View all posts by %s', 'bubbly' ), get_the_author() ) ?>"><?php echo get_the_author() ?> </a>
						        <span class="mdate alignright"><?php echo the_date('F j, Y') ?></span></p>
						    </div>
			        </div>
			    </article>				
				<?php endwhile; ?>
				    <div class="pagenavi alignright">
					    <?php if ($wp_query->max_num_pages > 1) bubbly_wp_pagination(); ?>
					</div>
				<?php else : get_template_part( 'no-results', 'archive' ); endif; ?>
		</div>
	</section>

<?php get_footer(); ?>