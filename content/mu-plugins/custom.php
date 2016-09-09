<?php

add_filter( 'parallax_one_plus_sections_filter', function( $sections ) {

	return array(
		'sections/parallax_one_logos_section',
		'sections/parallax_one_our_services_section',
		'sections/parallax_one_our_team_section',
		'sections/parallax_one_happy_customers_section',
		'sections/parallax_one_ribbon_section',
		'sections/parallax_one_latest_news_section'
	);
} );

add_filter( 'parallax_one_plus_footer_text_filter', '__return_null' );