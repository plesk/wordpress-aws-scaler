<?php
/**
 * parallax-one Theme Customizer
 *
 * @package parallax-one
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function parallax_one_customize_register( $wp_customize ) {

	class Parallax_Theme_Font_Title extends WP_Customize_Control {
		public function render_content() {
			echo __('<span class="customize-control-title">Choose the character sets you want</span>','parallax-one');
		}
	}

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/********************************************************/
	/************** WP DEFAULT CONTROLS  ********************/
	/********************************************************/

	$wp_customize->remove_control('background_color');
	$wp_customize->get_section('background_image')->panel='panel_2';
	$wp_customize->get_section('colors')->panel='panel_2';


	/********************************************************/
	/********************* APPEARANCE  **********************/
	/********************************************************/
	$wp_customize->add_panel( 'panel_2', array(
		'priority' => 30,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => esc_html__( 'Appearance', 'parallax-one' )
	) );

	$wp_customize->add_setting( 'parallax_one_text_color', array(
		'default' => '#313131',
		'sanitize_callback' => 'parallax_one_sanitize_rgba'
	));

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'parallax_one_text_color',
			array(
				'label'      => esc_html__( 'Text color', 'parallax-one' ),
				'section'    => 'colors',
				'priority'   => 3
			)
		)
	);


	$wp_customize->add_setting( 'parallax_one_title_color', array(
		'default' => '#454545',
		'sanitize_callback' => 'parallax_one_sanitize_rgba'
	));

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'parallax_one_title_color',
			array(
				'label'      => esc_html__( 'Title color', 'parallax-one' ),
				'section'    => 'colors',
				'priority'   => 4
			)
		)
	);

	$wp_customize->add_section( 'parallax_one_appearance_general' , array(
		'title'       => esc_html__( 'General options', 'parallax-one' ),
      	'priority'    => 3,
      	'description' => esc_html__('Parallax One theme general appearance options','parallax-one'),
		'panel'		  => 'panel_2'
	));

		/* Logo	*/
	$wp_customize->add_setting( 'paralax_one_logo', array(
		'default' => parallax_get_file('/images/logo-nav.png'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_logo', array(
	      	'label'    => esc_html__( 'Logo', 'parallax-one' ),
	      	'section'  => 'parallax_one_appearance_general',
			'priority'    => 1,
	)));

	/* Sticky header */
	$wp_customize->add_setting( 'paralax_one_sticky_header', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox',
	));
	$wp_customize->add_control(
			'paralax_one_sticky_header',
			array(
				'type' => 'checkbox',
				'label' => esc_html__('Header visibility','parallax-one'),
				'description' => esc_html__('If this box is checked, the header will toggle on frontpage.','parallax-one'),
				'section' => 'parallax_one_appearance_general',
				'priority'    => 2,
			)
	);

	/* Full width for all pages */
	$wp_customize->add_setting( 'paralax_one_full_width_template', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox',
	));
	$wp_customize->add_control( 'paralax_one_full_width_template',
		array(
			'type' => 'checkbox',
			'label' => esc_html__('Change the template to Full width for all the pages?','parallax-one'),
			'section' => 'parallax_one_appearance_general',
			'priority'    => 3,
		)
	);


	/********************************************************/
	/************* HEADER OPTIONS  ********************/
	/********************************************************/
	$wp_customize->add_panel( 'panel_1', array(
		'priority' => 35,
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => esc_html__( 'Header section', 'parallax-one' )
	) );

	/* HEADER CONTENT */

	$wp_customize->add_section( 'parallax_one_header_content' , array(
			'title'       => esc_html__( 'Content', 'parallax-one' ),
			'priority'    => 1,
			'panel' => 'panel_1'
	));

	/* Header Logo	*/
	$wp_customize->add_setting( 'paralax_one_header_logo', array(
		'default' => parallax_get_file('/images/logo-2.png'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_header_logo', array(
	      	'label'    => esc_html__( 'Header Logo', 'parallax-one' ),
	      	'section'  => 'parallax_one_header_content',
			'active_callback' => 'parallax_one_show_on_front',
			'priority'    => 10
	)));

	/* Header title */
	$wp_customize->add_setting( 'parallax_one_header_title', array(
		'default' => esc_html__('Simple, Reliable and Awesome.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_header_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_header_content',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20
	));

	/* Header subtitle */
	$wp_customize->add_setting( 'parallax_one_header_subtitle', array(
		'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_header_subtitle', array(
		'label'    => esc_html__( 'Subtitle', 'parallax-one' ),
		'section'  => 'parallax_one_header_content',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 30
	));


	/*Header Button text*/
	$wp_customize->add_setting( 'parallax_one_header_button_text', array(
		'default' => esc_html__('GET STARTED','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_header_button_text', array(
		'label'    => esc_html__( 'Button label', 'parallax-one' ),
		'section'  => 'parallax_one_header_content',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 40
	));


	$wp_customize->add_setting( 'parallax_one_header_button_link', array(
		'default' => esc_html__('#','parallax-one'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_header_button_link', array(
		'label'    => esc_html__( 'Button link', 'parallax-one' ),
		'section'  => 'parallax_one_header_content',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 50
	));


	/* LOGOS SETTINGS */

	$wp_customize->add_section( 'parallax_one_logos_settings_section' , array(
			'title'       => esc_html__( 'Logos Bar', 'parallax-one' ),
			'priority'    => 2,
			'panel' => 'panel_1'
	));


    require_once ( 'class/parallax-one-general-control.php');

	$wp_customize->add_setting( 'parallax_one_logos_content', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode(
				array(
					array("image_url" => parallax_get_file('/images/companies/1.png') ,"link" => "#", "id" => "parallax_one_56d7ea7f40f56" ),
					array("image_url" => parallax_get_file('/images/companies/2.png') ,"link" => "#", "id" => "parallax_one_56d7f2cb8a158" ),
					array("image_url" => parallax_get_file('/images/companies/3.png') ,"link" => "#", "id" => "parallax_one_56d7f2cc8a159" ),
					array("image_url" => parallax_get_file('/images/companies/4.png') ,"link" => "#", "id" => "parallax_one_56d7f2ce8a15a" ),
					array("image_url" => parallax_get_file('/images/companies/5.png') ,"link" => "#", "id" => "parallax_one_56d7f2cf8a15b" )
				)
		)

	));
	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_logos_content', array(
		'label'   => esc_html__('Add new social icon','parallax-one'),
		'section' => 'parallax_one_logos_settings_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority' => 10,
        'parallax_image_control' => true,
        'parallax_icon_control' => false,
        'parallax_text_control' => false,
        'parallax_link_control' => true
	) ) );

	$wp_customize->get_section('header_image')->panel='panel_1';
	$wp_customize->get_section('header_image')->title=esc_html__( 'Background', 'parallax-one' );

	/* Enable parallax effect*/
	$wp_customize->add_setting( 'paralax_one_enable_move', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox',
	));
	$wp_customize->add_control(
			'paralax_one_enable_move',
			array(
				'type' => 'checkbox',
				'label' => esc_html__('Parallax effect','parallax-one'),
				'description' => esc_html__('If this box is checked, the parallax effect is enabled.','parallax-one'),
				'section' => 'header_image',
				'priority'    => 3,
			)
	);

	/* Layer one */
	$wp_customize->add_setting( 'paralax_one_first_layer', array(
		'default' => parallax_get_file('/images/background1.png'),
		'sanitize_callback' => 'esc_url',
		//'transport' => 'postMessage'
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_first_layer', array(
	      	'label'    => esc_html__( 'First layer', 'parallax-one' ),
	      	'section'  => 'header_image',
			'priority'    => 4,
	)));

	/* Layer two */
	$wp_customize->add_setting( 'paralax_one_second_layer', array(
		'default' => parallax_get_file('/images/background2.png'),
		'sanitize_callback' => 'esc_url',
		//'transport' => 'postMessage'
	));

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_second_layer', array(
	      	'label'    => esc_html__( 'Second layer', 'parallax-one' ),
	      	'section'  => 'header_image',
			'priority'    => 5,
	)));


	require_once ( 'class/parallax-one-alpha-control.php');
	/* bigtitle_background */
	$wp_customize->add_setting( 'parallax_one_bigtitle_background', array(
		'default' => 'rgba(0, 0, 0, 0.7)',
		'sanitize_callback' => 'parallax_one_sanitize_rgba',
	));

	$wp_customize->add_control(
		new Parallax_One_Customize_Alpha_Color_Control(
			$wp_customize,
			'parallax_one_bigtitle_background',
			array(
				'label'    => __( 'Overlay color and transparency', 'parallax-one' ),
				'palette' => true,
				'section'  => 'header_image',
				'priority'   => 6
			)
		)
	);


	/********************************************************/
	/****************** SERVICES OPTIONS  *******************/
	/********************************************************/


	/* SERVICES SECTION */
	$wp_customize->add_section( 'parallax_one_services_section' , array(
			'title'			=> esc_html__( 'Services section', 'parallax-one' ),
			'priority'		=> 40,
	));

	$wp_customize->add_setting( 'paralax_one_services_pinterest_style', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox',
		'default'    		=> '5'
	));
	$wp_customize->add_control(
		'paralax_one_services_pinterest_style',
		array(
			'type' 			=> 'checkbox',
			'label' 		=> esc_html__('Use pinterest layout?','parallax-one'),
			'description' 	=> esc_html__('If this box is checked, the Services section will use pinterest-style layout.','parallax-one'),
			'section' 		=> 'parallax_one_services_section',
			'priority'    	=> 1,
		)
	);

	/* Services title */
	$wp_customize->add_setting( 'parallax_one_our_services_title', array(
		'default' => esc_html__('Our Services','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_our_services_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_services_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 10
	));

	/* Services subtitle */
	$wp_customize->add_setting( 'parallax_one_our_services_subtitle', array(
		'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_our_services_subtitle', array(
		'type'     => 'textarea',
		'label'    => esc_html__( 'Subtitle', 'parallax-one' ),
		'section'  => 'parallax_one_services_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20
	));


    /* Services content */
	$wp_customize->add_setting( 'parallax_one_services_content', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode( array(
			array('choice'=>'parallax_icon','icon_value' => 'icon-basic-webpage-multiple','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d93f3013'),
			array('choice'=>'parallax_icon','icon_value' => 'icon-ecommerce-graph3','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d94f3014'),
			array('choice'=>'parallax_icon','icon_value' => 'icon-basic-geolocalize-05','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d95f3015')
		) )
	) );

	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_services_content', array(
		'label'   => esc_html__('Add new service box','parallax-one'),
		'section' => 'parallax_one_services_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority' => 30,
        'parallax_image_control' => true,
        'parallax_icon_control' => true,
		'parallax_title_control' => true,
        'parallax_text_control' => true,
		'parallax_link_control' => true
	) ) );
	/********************************************************/
	/******************** ABOUT OPTIONS  ********************/
	/********************************************************/


	$wp_customize->add_section( 'parallax_one_about_section' , array(
			'title'       => esc_html__( 'About section', 'parallax-one' ),
			'priority'    => 45,
	));

	/* About title */
	$wp_customize->add_setting( 'parallax_one_our_story_title', array(
		'default' => esc_html__('Our Story','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_our_story_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_about_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 10,
	));

	/* About Content */

	$wp_customize->add_setting( 'parallax_one_our_story_text', array(
		'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'

	));

	$wp_customize->add_control(
			'parallax_one_our_story_text',
			array(
				'type' => 'textarea',
				'label'   => esc_html__( 'Content', 'parallax-one' ),
				'section' => 'parallax_one_about_section',
				'active_callback' => 'parallax_one_show_on_front',
				'priority'    => 20,
				'description' => __( 'Allowed html: p,br,em,strong,a,button,ul,li', 'parallax-one' )
			)
	);

	/* About Image	*/
	$wp_customize->add_setting( 'paralax_one_our_story_image', array(
		'default' => parallax_get_file('/images/about-us.png'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_our_story_image', array(
	      	'label'    => esc_html__( 'Image', 'parallax-one' ),
	      	'section'  => 'parallax_one_about_section',
			'active_callback' => 'parallax_one_show_on_front',
			'priority'    => 30,
	)));

	/********************************************************/
	/*******************  TEAM OPTIONS  *********************/
	/********************************************************/


	$wp_customize->add_section( 'parallax_one_team_section' , array(
			'title'       => esc_html__( 'Team section', 'parallax-one' ),
			'priority'    => 50,
	));

	/* Team title */
	$wp_customize->add_setting( 'parallax_one_our_team_title', array(
		'default' => esc_html__('Our Team','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_our_team_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_team_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 10,
	));

	/* Team subtitle */
	$wp_customize->add_setting( 'parallax_one_our_team_subtitle', array(
		'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_our_team_subtitle', array(
		'type'     => 'textarea',
		'label'    => esc_html__( 'Subtitle', 'parallax-one' ),
		'section'  => 'parallax_one_team_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20,
	));


    /* Team content */
	$wp_customize->add_setting( 'parallax_one_team_content', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode( array(
			array('image_url' => parallax_get_file('/images/team/1.jpg'),'title' => esc_html__('Albert Jacobs','parallax-one'),'subtitle' => esc_html__('Founder & CEO','parallax-one'), 'id' => 'parallax_one_56fe9796baca4'),
			array('image_url' => parallax_get_file('/images/team/2.jpg'),'title' => esc_html__('Tonya Garcia','parallax-one'),'subtitle' => esc_html__('Account Manager','parallax-one'), 'id' => 'parallax_one_56fe9798baca5'),
			array('image_url' => parallax_get_file('/images/team/3.jpg'),'title' => esc_html__('Linda Guthrie','parallax-one'),'subtitle' => esc_html__('Business Development','parallax-one'), 'id' => 'parallax_one_56fe9799baca6')
		) )
	));
	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_team_content', array(
		'label'															=> esc_html__('Add new team member','parallax-one'),
		'section'														=> 'parallax_one_team_section',
		'active_callback' 									=> 'parallax_one_show_on_front',
		'priority' 													=> 30,
    'parallax_image_control' 						=> true,
		'parallax_title_control' 						=> true,
		'parallax_subtitle_control' 				=> true,
		'parallax_socials_repeater_control' => true
	) ) );

	/********************************************************/
	/********** TESTIMONIALS OPTIONS  ***********************/
	/********************************************************/


	$wp_customize->add_section( 'parallax_one_testimonials_section' , array(
			'title'       => esc_html__( 'Testimonial section', 'parallax-one' ),
			'priority'    => 55,
	));

	$wp_customize->add_setting( 'paralax_one_testimonials_pinterest_style', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox',
		'default'    		=> '5'
	));
	$wp_customize->add_control(
		'paralax_one_testimonials_pinterest_style',
		array(
			'type' 			=> 'checkbox',
			'label' 		=> esc_html__('Use pinterest layout?','parallax-one'),
			'description' 	=> esc_html__('If this box is checked, the Testimonials section will use pinterest-style layout.','parallax-one'),
			'section' 		=> 'parallax_one_testimonials_section',
			'priority'    	=> 1,
	));

	/* Testimonials title */
	$wp_customize->add_setting( 'parallax_one_happy_customers_title', array(
		'default' => esc_html__('Happy Customers','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_happy_customers_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_testimonials_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 10,
	));

	/* Testimonials subtitle */
	$wp_customize->add_setting( 'parallax_one_happy_customers_subtitle', array(
		'default' => esc_html__('Cloud computing subscription model out of the box proactive solution.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_happy_customers_subtitle', array(
		'type'     => 'textarea',
		'label'    => esc_html__( 'Subtitle', 'parallax-one' ),
		'section'  => 'parallax_one_testimonials_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20,
	));


    /* Testimonials content */
	$wp_customize->add_setting( 'parallax_one_testimonials_content', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode(
							array(
									array('image_url' => parallax_get_file('/images/clients/1.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd526edcd4e'),
									array('image_url' => parallax_get_file('/images/clients/2.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd526ddcd4d'),
									array('image_url' => parallax_get_file('/images/clients/3.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd5259dcd4c')
							)
						)
	));
	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_testimonials_content', array(
		'label'   => esc_html__('Add new testimonial','parallax-one'),
		'section' => 'parallax_one_testimonials_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority' => 30,
        'parallax_image_control' => true,
		'parallax_title_control' => true,
		'parallax_subtitle_control' => true,
		'parallax_text_control' => true
	) ) );


	/********************************************************/
	/***************** RIBBON OPTIONS  *****************/
	/********************************************************/


	/* RIBBON SETTINGS */
	$wp_customize->add_section( 'parallax_one_ribbon_section' , array(
		'title'       => esc_html__( 'Ribbon section', 'parallax-one' ),
		'priority'    => 60,
	));


	/* Ribbon Background	*/
	$wp_customize->add_setting( 'paralax_one_ribbon_background', array(
		'default' => parallax_get_file('/images/background-images/parallax-img/parallax-img1.jpg'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'paralax_one_ribbon_background', array(
	      	'label'    => esc_html__( 'Ribbon Background', 'parallax-one' ),
	      	'section'  => 'parallax_one_ribbon_section',
			'active_callback' => 'parallax_one_show_on_front',
			'priority'    => 10
	)));

	$wp_customize->add_setting( 'parallax_one_ribbon_title', array(
		'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_ribbon_title', array(
		'type'     => 'textarea',
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_ribbon_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20
	));


	$wp_customize->add_setting( 'parallax_one_button_text', array(
		'default' => esc_html__('GET STARTED','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_button_text', array(
		'label'    => esc_html__( 'Button label', 'parallax-one' ),
		'section'  => 'parallax_one_ribbon_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 30
	));


	$wp_customize->add_setting( 'parallax_one_button_link', array(
		'default' => esc_html__('#','parallax-one'),
		'sanitize_callback' => 'esc_url',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_button_link', array(
		'label'    => esc_html__( 'Button link', 'parallax-one' ),
		'section'  => 'parallax_one_ribbon_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 40
	));

	/********************************************************/
	/************ LATEST NEWS OPTIONS  **************/
	/********************************************************/


	$wp_customize->add_section( 'parallax_one_latest_news_section' , array(
			'title'       => esc_html__( 'Latest news section', 'parallax-one' ),
			'priority'    => 65
	));

	$wp_customize->add_setting( 'parallax_one_latest_news_title', array(
		'default' => esc_html__('Latest news','parallax-one'),
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_latest_news_title', array(
		'label'    => esc_html__( 'Main title', 'parallax-one' ),
		'section'  => 'parallax_one_latest_news_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 10
	));

	/********************************************************/
	/****************** CONTACT OPTIONS  ********************/
	/********************************************************/


	/* CONTACT SETTINGS */
	$wp_customize->add_section( 'parallax_one_contact_section' , array(
		'title'       => esc_html__( 'Contact section', 'parallax-one' ),
		'priority'    => 70,
	));


	$wp_customize->add_setting( 'parallax_one_contact_info_content', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode(
			array(
					array("icon_value" => "icon-basic-mail" ,"text" => "contact@site.com", "link" => "#", "id" => "parallax_one_56d450a72cb3a" ),
					array("icon_value" => "icon-basic-geolocalize-01" ,"text" => "Company address", "link" => "#", "id" => "parallax_one_56d069b88cb6f" ),
					array("icon_value" => "icon-basic-tablet" ,"text" => "0 332 548 954", "link" => "#", "id" => "parallax_one_56d069b98cb70" )
			)
		)
	));
	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_contact_info_content', array(
		'label'   => esc_html__('Add new contact field','parallax-one'),
		'section' => 'parallax_one_contact_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority' => 10,
        'parallax_image_control' => false,
        'parallax_icon_control' => true,
        'parallax_text_control' => true,
        'parallax_link_control' => true
	) ) );


	/* Map ShortCode  */
	$wp_customize->add_setting( 'parallax_one_frontpage_map_shortcode', array(
		'default' => '',
		'sanitize_callback' => 'parallax_one_sanitize_input'
	));
	$wp_customize->add_control( 'parallax_one_frontpage_map_shortcode', array(
		'label'    => esc_html__( 'Map shortcode', 'parallax-one' ),
		'description' => __('To use this section please install <a href="https://wordpress.org/plugins/intergeo-maps/">Intergeo Maps</a> plugin then use it to create a map and paste here the shortcode generated','parallax-one'),
		'section'  => 'parallax_one_contact_section',
		'active_callback' => 'parallax_one_show_on_front',
		'priority'    => 20
	));


	/********************************************************/
	/*************** CONTACT PAGE OPTIONS  ******************/
	/********************************************************/


	$wp_customize->add_section( 'parallax_one_contact_page' , array(
		'title'       => esc_html__( 'Contact page', 'parallax-one' ),
      	'priority'    => 75,
	));

	/* Contact Form  */
	$wp_customize->add_setting( 'parallax_one_contact_form_shortcode', array(
		'default' => '',
		'sanitize_callback' => 'parallax_one_sanitize_input'
	));
	$wp_customize->add_control( 'parallax_one_contact_form_shortcode', array(
		'label'    => esc_html__( 'Contact form shortcode', 'parallax-one' ),
		'description' => __('Create a form, copy the shortcode generated and paste it here. We recommend <a href="http://themeisle.com/plugins/pirate-forms" target="_blank">Pirate Forms</a> but you can use any plugin you like.','parallax-one'),
		'section'  => 'parallax_one_contact_page',
		'active_callback' => 'parallax_one_is_contact_page',
		'priority'    => 1
	));

	/* Map ShortCode  */
	$wp_customize->add_setting( 'parallax_one_contact_map_shortcode', array(
		'default' => '',
		'sanitize_callback' => 'parallax_one_sanitize_input'
	));
	$wp_customize->add_control( 'parallax_one_contact_map_shortcode', array(
		'label'    => esc_html__( 'Map shortcode', 'parallax-one' ),
		'description' => __('To use this section please install <a href="https://wordpress.org/plugins/intergeo-maps/" target="_blank">Intergeo Maps</a> plugin then use it to create a map and paste here the shortcode generated','parallax-one'),
		'section'  => 'parallax_one_contact_page',
		'active_callback' => 'parallax_one_is_contact_page',
		'priority'    => 2
	));

	/********************************************************/
	/****************** FOOTER OPTIONS  *********************/
	/********************************************************/

	$wp_customize->add_section( 'parallax_one_footer_section' , array(
		'title'       => esc_html__( 'Footer options', 'parallax-one' ),
      	'priority'    => 80,
      	'description' => esc_html__('The main content of this section is customizable in: Customize -> Widgets -> Footer area. ','parallax-one'),
	));

	/* Footer Menu */
	$nav_menu_locations_footer = $wp_customize->get_control('nav_menu_locations[parallax_footer_menu]');
	if(!empty($nav_menu_locations_footer)){
		$nav_menu_locations_footer->section = 'parallax_one_footer_section';
		$nav_menu_locations_footer->priority = 1;
	}
	/* Copyright */
	$wp_customize->add_setting( 'parallax_one_copyright', array(
		'default' => 'Themeisle',
		'sanitize_callback' => 'parallax_one_sanitize_input',
		'transport' => 'postMessage'
	));
	$wp_customize->add_control( 'parallax_one_copyright', array(
		'label'    => esc_html__( 'Copyright', 'parallax-one' ),
		'section'  => 'parallax_one_footer_section',
		'priority'    => 2
	));


	/* Socials icons */


	$wp_customize->add_setting( 'parallax_one_social_icons', array(
		'sanitize_callback' => 'parallax_one_sanitize_repeater',
		'default' => json_encode(
			array(
				array('icon_value' =>'icon-social-facebook' , 'link' => '#'),
				array('icon_value' =>'icon-social-twitter' , 'link' => '#'),
				array('icon_value' =>'icon-social-googleplus' , 'link' => '#')
			)
		)

	));
	$wp_customize->add_control( new Parallax_One_General_Repeater( $wp_customize, 'parallax_one_social_icons', array(
		'label'   => esc_html__('Add new social icon','parallax-one'),
		'section' => 'parallax_one_footer_section',
		'priority' => 3,
        'parallax_image_control' => false,
        'parallax_icon_control' => true,
        'parallax_text_control' => false,
        'parallax_link_control' => true
	) ) );

	/********************************************************/
	/************** ADVANCED OPTIONS  ***********************/
	/********************************************************/

	$wp_customize->add_section( 'parallax_one_general_section' , array(
		'title'       => esc_html__( 'Advanced options', 'parallax-one' ),
      	'priority'    => 85,
      	'description' => esc_html__('Parallax One theme general options','parallax-one'),
	));

	$blogname = $wp_customize->get_control('blogname');
	$blogdescription = $wp_customize->get_control('blogdescription');
	$blogicon = $wp_customize->get_control('site_icon');
	$show_on_front = $wp_customize->get_control('show_on_front');
	$page_on_front = $wp_customize->get_control('page_on_front');
	$page_for_posts = $wp_customize->get_control('page_for_posts');
	if(!empty($blogname)){
		$blogname->section = 'parallax_one_general_section';
		$blogname->priority = 1;
	}
	if(!empty($blogdescription)){
		$blogdescription->section = 'parallax_one_general_section';
		$blogdescription->priority = 2;
	}
	if(!empty($blogicon)){
		$blogicon->section = 'parallax_one_general_section';
		$blogicon->priority = 3;
	}
	if(!empty($show_on_front)){
		$show_on_front->section='parallax_one_general_section';
		$show_on_front->priority=4;
	}
	if(!empty($page_on_front)){
		$page_on_front->section='parallax_one_general_section';
		$page_on_front->priority=5;
	}
	if(!empty($page_for_posts)){
		$page_for_posts->section='parallax_one_general_section';
		$page_for_posts->priority=6;
	}

	$wp_customize->remove_section('static_front_page');
	$wp_customize->remove_section('title_tagline');

	$nav_menu_locations_primary = $wp_customize->get_control('nav_menu_locations[primary]');
	if(!empty($nav_menu_locations_primary)){
		$nav_menu_locations_primary->section = 'parallax_one_general_section';
		$nav_menu_locations_primary->priority = 6;
	}

	/* Disable preloader */
	$wp_customize->add_setting( 'paralax_one_disable_preloader', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox'
	));
	$wp_customize->add_control(
			'paralax_one_disable_preloader',
			array(
				'type' => 'checkbox',
				'label' => esc_html__('Disable preloader?','parallax-one'),
				'description' => esc_html__('If this box is checked, the preloader will be disabled from homepage.','parallax-one'),
				'section' => 'parallax_one_general_section',
				'priority'    => 7,
			)
	);



	/* Character sets */
	$wp_customize->add_setting( 'parallax_theme_font_title', array(
		'sanitize_callback' => 'zerif_sanitize_pro_version'
	));
	$wp_customize->add_control( new Parallax_Theme_Font_Title( $wp_customize, 'parallax_theme_font_title', array(
		'section' => 'parallax_one_general_section',
	)));

	$wp_customize->add_setting( 'parallax_one_character_cyrillic', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox'
	));
	$wp_customize->add_control( 'parallax_one_character_cyrillic', array(
		'type' => 'checkbox',
		'label' => esc_html__('Cyrillic','parallax-one'),
		'section' => 'parallax_one_general_section',
		'priority' => 10,
	));

	$wp_customize->add_setting( 'parallax_one_character_vietnamese', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox'
	));
	$wp_customize->add_control( 'parallax_one_character_vietnamese', array(
		'type' 			=> 'checkbox',
		'label' 		=> esc_html__('Vietnamese','parallax-one'),
		'section' 		=> 'parallax_one_general_section',
		'priority'    	=> 11,
	));

	$wp_customize->add_setting( 'parallax_one_character_greek', array(
		'sanitize_callback' => 'parallax_one_sanitize_checkbox'
	));
	$wp_customize->add_control( 'parallax_one_character_greek', array(
		'type' 			=> 'checkbox',
		'label' 		=> esc_html__('Greek','parallax-one'),
		'section' 		=> 'parallax_one_general_section',
		'priority'    	=> 12,
	));


	/*********************************/
	/******* PLUS SECTIONS ***********/
	/*********************************/
	require_once ( 'class/parallax-one-text-control.php');
	$wp_customize->add_section( 'parallax_one_sections_order' , array(
		'title'       => __( 'Sections management', 'parallax-one' ),
		'priority' => 20
	));

	$wp_customize->add_setting( 'parallax_one_sections_management', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_sections_management',
		array(
			'label'    => __( 'Sections management', 'parallax-one' ),
			'section' => 'parallax_one_sections_order',
			'priority' => 1,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the frontpage SECTIONS ORDER!', 'parallax-one' )
	   )
	));

	$wp_customize->add_section( 'parallax_one_portfolio_section' , array(
		'title'       => esc_html__( 'Portfolio section', 'parallax-one' ),
		'priority'    => 48,
	));

	$wp_customize->add_setting( 'parallax_one_portfolio_text', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_portfolio_text',
		array(
			'label'    => __( 'Portfolio', 'parallax-one' ),
			'section' => 'parallax_one_portfolio_section',
			'priority' => 1,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the NEW PORTFOLIO SECTION!', 'parallax-one' )
	   )
	));

	$wp_customize->add_section( 'parallax_one_new_features' , array(
		'title'       => esc_html__( 'New Features', 'parallax-one' ),
		'priority'    => 76,
	));

	$wp_customize->add_setting( 'parallax_one_new_layout', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_new_layout',
		array(
			'label'    => __( 'Header Layout', 'parallax-one' ),
			'section' => 'parallax_one_new_features',
			'priority' => 1,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the HEADER\'S LAYOUT SECTION!', 'parallax-one' )
	   )
	));


	$wp_customize->add_setting( 'parallax_one_new_color', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_new_color',
		array(
			'label'    => __( 'Color scheme', 'parallax-one' ),
			'section' => 'parallax_one_new_features',
			'priority' => 2,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the COLOR SCHEME!', 'parallax-one' )
	   )
	));


	$wp_customize->add_setting( 'parallax_one_new_preloader', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_new_preloader',
		array(
			'label'    => __( 'Preloader', 'parallax-one' ),
			'section' => 'parallax_one_new_features',
			'priority' => 3,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the PRELOADER IMAGE!', 'parallax-one' )
	   )
	));

	$wp_customize->add_setting( 'parallax_one_new_opacity', array(
			'sanitize_callback' => 'parallax_one_sanitize_input',
	) );

	$wp_customize->add_control( new Parallax_One_Message( $wp_customize, 'parallax_one_new_opacity',
		array(
			'label'    => __( 'Opacity', 'parallax-one' ),
			'section' => 'parallax_one_new_features',
			'priority' => 4,
			'parallax_message' => __( 'Check out the <a href="http://themeisle.com/plugins/parallax-one-plus/">PRO version</a> for full control over the background opacity and color of each section!', 'parallax-one' )
	   )
	));

}
add_action( 'customize_register', 'parallax_one_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function parallax_one_customize_preview_js() {
	wp_enqueue_script( 'parallax_one_customizer', parallax_get_file('/js/customizer.js'), array( 'customize-preview' ), '1.0.2', true );
}
add_action( 'customize_preview_init', 'parallax_one_customize_preview_js' );

if( !function_exists('parallax_one_sanitize_input')){
	function parallax_one_sanitize_input( $input ) {
	    return wp_kses_post( force_balance_tags( $input ) );
	}
}

function parallax_one_sanitize_checkbox( $input ){
	return ( isset( $input ) && true == $input ? true : false );
}

/* Sanitize RGBA colors */
function  parallax_one_sanitize_rgba($value)  {
	// If empty or an array return transparent
	if ( empty( $value ) || is_array( $value ) ) {
		return 'rgba(0,0,0,0)';
	}
	$value = str_replace( ' ', '', $value );
	if(substr( $value, 0, 4 ) == "rgba"){
		sscanf( $value, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );
		return 'rgba('.$red.','.$green.','.$blue.','.$alpha.')';
	}
	return sanitize_hex_color($value);
}

function parallax_one_sanitize_repeater($input){

	$input_decoded = json_decode($input,true);
	$allowed_html = array(
								'br' => array(),
								'em' => array(),
								'strong' => array(),
								'a' => array(
									'href' => array(),
									'class' => array(),
									'id' => array(),
									'target' => array()
								),
								'button' => array(
									'class' => array(),
									'id' => array()
								),
								'ul' => array(
									'class' => array(),
									'id' => array(),
									'style' => array()
								),
								'li' => array(
									'class' => array(),
									'id' => array(),
									'style' => array()
								),
							);


	if(!empty($input_decoded)) {
		foreach ($input_decoded as $boxk => $box ){
			foreach ($box as $key => $value){
				if ($key == 'text'){
					$value = html_entity_decode($value);
					$input_decoded[$boxk][$key] = wp_kses( $value, $allowed_html);
				} else {
					$input_decoded[$boxk][$key] = wp_kses_post( force_balance_tags( $value ) );
				}

			}
		}

		return json_encode($input_decoded);
	}

	return $input;
}

function parallax_one_is_contact_page() {
		return is_page_template('template-contact.php');
};
if( !function_exists('parallax_one_show_on_front')){
	function parallax_one_show_on_front(){
		if ( 'posts' == get_option( 'show_on_front' ) && is_front_page() ){
			return true;
		}
		return false;
	}
}
