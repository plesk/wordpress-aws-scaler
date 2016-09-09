<?php 
/*
Template Name: Blog
*/
	query_posts( array( 'post_type' => 'post', 'posts_per_page' => 6, 'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1 ) ) );
	include( get_home_template() );
	wp_reset_postdata();
?>