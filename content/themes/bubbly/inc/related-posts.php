<?php
/** Latest Posts from the same Category
 * @package Bubbly **/

global $bubbly_option; 

$categories = get_the_category( $post->ID );
$first_cat ='';
if(isset($categories[0]->cat_ID)){
$first_cat = $categories[0]->cat_ID;
}
$posts_to_show =  $bubbly_option['single_related_posts_to_show'];
$args = array(
	'category__in' => array( $first_cat ),
	'post__not_in' => array( $post->ID ),
	'posts_per_page' => 3
);

$related_posts = get_posts( $args );
if( $related_posts ) {
?>

	<section class="single-box related-articles">
        <h4 class="entry-title">
           <?php _e('You May Also Like', 'bubbly'); ?>
        </h4>     	          
			<?php foreach( $related_posts as $post ): setup_postdata( $post ); ?>
    
			<div class="related-article four-col">
								  
                  <figure class="entry-image">
                  
                      <a href="<?php the_permalink(); ?>">
						  <?php 
                          if ( has_post_thumbnail() ) {
                              the_post_thumbnail( 'medium');
                          } else { ?>
                          <img src="<?php  echo get_template_directory_uri(); ?>/images/default-image.png" alt="<?php the_title();  ?>" />
                          <?php } ?>
                      </a>
              
                  </figure>
                          <h5><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h5>
                                       
              </div>
        
    		<?php endforeach; ?>       
	</section><!-- .single-box .related-posts -->
    
<?php
} else {
	return;
}
wp_reset_postdata();

?>