<?php /* Theme Customizer For Bubbly Theme */
   
 	function bubbly_customize_register($wp_customize){
    
	// Theme Colors
 
	$colors = array();
    $colors[] = array( 'slug'=>'bg_color', 'default' => '#fbfbfb',
    'label' => __( 'Background Color', 'bubbly' ) );
    $colors[] = array( 'slug'=>'primary_color', 'default' => '#00a9e0',
    'label' => __( 'Post Link Color ', 'bubbly' ) );
     
	
	foreach($colors as $color)
  {	
    $wp_customize->add_setting( $color['slug'], array( 'default' => $color['default'],
    'type' => 'theme_mod', 'capability' => 'edit_theme_options', 'sanitize_callback' => 'sanitize_hex_color', ));

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
     $color['slug'], array( 'label' => $color['label'], 'section' => 'colors',
     'settings' => $color['slug'] )));
  }
	// Theme Colors Ends
	// Logo Uploader
	
	$wp_customize->add_section( 'bubbly_logo_fav_section' , array(
    'title'       => __( 'Site Logo', 'bubbly' ),
    'priority'    => 30,
    'description' => __('Upload a logo to replace the default site name and description in the header','bubbly'),) );

    $wp_customize->add_setting( 'bubbly_logo', array(
		'sanitize_callback' => 'esc_url_raw') );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bubbly_logo', array(
    'label'    => __( 'Site Logo ( Recommended height - 60px)', 'bubbly' ),
    'section'  => 'bubbly_logo_fav_section',
    'settings' => 'bubbly_logo',
    ) ) );
	
	function bubbly_check_header_video($file){
  return validate_file($file, array('', 'mp4'));
}

	
	// Social Links
	
	$wp_customize->add_section( 'sociallinks', array(
        'title' => __('Social Links','bubbly'), // The title of section
        'description' => __('Add Your Social Links Here.','bubbly'), // The description of section
        'priority' => '900',
	) );
	
	$wp_customize->add_setting( 'facebooklink', array('default' => '#','sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'facebooklink', array('label' => 'Facebook URL', 'section' => 'sociallinks', ) );
	$wp_customize->add_setting( 'twitterlink', array('default' => '#','sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'twitterlink', array('label' => 'Twitter Handle', 'section' => 'sociallinks', ) );
    $wp_customize->add_setting( 'googlelink', array('default' => '#', 'sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'googlelink', array('label' => 'Google Plus URL','section' => 'sociallinks',) );
	$wp_customize->add_setting( 'pinterestlink', array('default' => '#', 'sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'pinterestlink', array('label' => 'Pinterest URL','section' => 'sociallinks',) );
	$wp_customize->add_setting( 'youtubelink', array('default' => '#', 'sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'youtubelink', array('label' => 'Youtube URL','section' => 'sociallinks',) );
	$wp_customize->add_setting( 'stumblelink', array('default' => '#', 'sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'stumblelink', array('label' => 'Stumbleupon Link','section' => 'sociallinks', ) );
	$wp_customize->add_setting( 'rsslink', array('default' => '#', 'sanitize_callback' => 'esc_url_raw') );
    $wp_customize->add_control( 'rsslink', array('label' => 'RSS Feeds URL','section' => 'sociallinks',) );

	// Social Links Ends
	
 	// Footer Copyright Section
	
	$wp_customize->add_section( 'fcopyright', array(
        'title' => __('Footer Copyright','bubbly'), // The title of section
        'description' => __('Add Your Copyright Notes Here.','bubbly'), // The description of section
        'priority' => '900',
	) );
 
	$wp_customize->add_setting( 'bubbly_footer_top', array('default' => __('Any Text Here','bubbly'),'sanitize_callback' => 'sanitize_footer_text',) );
    $wp_customize->add_control( 'bubbly_footer_top', array('label' => __('Footer Text','bubbly'),'section' => 'fcopyright',) );
	$wp_customize->add_setting( 'bubbly_footer_cr_left', array('default' => __('Your Copyright Here.','bubbly'),'sanitize_callback' => 'sanitize_footer_text',) );
	$wp_customize->add_control( 'bubbly_footer_cr_left', array('label' => __('Copyright Note Left','bubbly'),'section' => 'fcopyright',) );
    	
	function sanitize_footer_text( $input ) {
    return wp_kses_post( force_balance_tags( $input ) );
}
	
	
	  } // function ends here

	   // This will output the custom WordPress settings to the live theme's WP head. */
   
   function header_output() {
     
     $bgcolor = get_theme_mod('bg_color');
	 $primarycolor = get_theme_mod('primary_color');
	 
	 ?><?php echo get_theme_mod('textarea_setting'); 
      if (  ( ! empty( $bgcolor )) || (!empty($primarycolor))){
?>	  <!--Customizer CSS--> 
      
	  <style type="text/css">
	        

		    <?php if($bgcolor) { ?>
		      body{background-color: <?php echo $bgcolor; ?>}
		   	<?php } ?>
            <?php if($primarycolor) { ?>

  .search-block #s, .post-meta, .top-nav li a, #main-footer a,
			  .catbox a, .hcat a:visited, a, .cdetail h3 a:hover, .cdetail h2 a:hover,   
			  .related-article h5 a, #sidebar a:hover{color:<?php echo $primarycolor; ?>;}
			  		 #main-footer {border-bottom: 3px solid <?php echo $primarycolor; ?>;}  
					.not-found-block #s:focus , #gototop	{background-color:<?php echo $primarycolor; ?>;}
			  .comment-list blockquote, .entry-content blockquote { border-left: solid 3px <?php echo $primarycolor; ?>; }

				
		   	<?php } ?>

		
	  </style>
      <!--/Customizer CSS-->
	<?php } ?>
	<?php } 
	
	   function footer_output() {
	   ?><?php echo get_theme_mod('textarea_setting2'); 
	   }
	  
	  
add_action( 'customize_register', 'bubbly_customize_register', 11 );
add_action( 'wp_head', 'header_output', 11 );
add_action( 'wp_footer', 'footer_output', 11 );

//add_action( 'customize_register' , array( 'bubbly_Customize' , 'register' ) );
//add_action( 'wp_head' , array( 'bubbly_Customize' , 'header_output' ) );