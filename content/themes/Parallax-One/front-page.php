<?php

if ( 'posts' == get_option( 'show_on_front' ) ) {

		get_header();

		parallax_one_get_template_part( apply_filters("parallax_one_plus_header_layout","/sections/parallax_one_header_section"));
	?>
		</div>
		<!-- /END COLOR OVER IMAGE -->
		<?php parallax_hook_header_bottom(); ?>
	</header>
	<!-- /END HOME / HEADER  -->
	<?php parallax_hook_header_after(); ?>

	<?php parallax_hook_content_before(); ?>
	<div itemprop id="content" class="content-warp" role="main">
	<?php parallax_hook_content_top(); ?>
	<?php

		$sections_array = apply_filters("parallax_one_plus_sections_filter",array('sections/parallax_one_logos_section','sections/parallax_one_our_services_section','sections/parallax_one_our_team_section','sections/parallax_one_happy_customers_section','sections/parallax_one_ribbon_section','sections/parallax_one_latest_news_section','sections/parallax_one_contact_info_section','sections/parallax_one_map_section'));

		if(!empty($sections_array)){
			foreach($sections_array as $section){
				parallax_one_get_template_part($section);
			}
		}
	?>
	<?php parallax_hook_content_bottom(); ?>
	</div><!-- .content-wrap -->
	<?php parallax_hook_content_after(); ?>
	<?php

	get_footer();
} else {

	include( get_page_template() );
}
?>
