<?php get_header(); ?>
	<section id="content" class="first clearfix" role="main">
		<div class="post-container">
		    <div class="singlebox">
			  <div class="not-found-block center">
	               <h1><?php _e('The Page You Are Looking For Doesn&rsquo;t Exist.', 'bubbly'); ?></h1>
	         <h3><?php _e('We are very sorry for the inconvenience.', 'bubbly'); ?></h3>
	         <p><?php _e('Perhaps, Try using the search box below.', 'bubbly'); ?></p>
	                    <?php get_search_form(); ?>
					   <p><?php _e('Or', 'bubbly'); ?></p>
					   <a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e('Go to Homepage', 'bubbly'); ?></a>
			  </div>
			</div>
		</div>
	</section> <!-- end #main -->

<?php get_footer(); ?>