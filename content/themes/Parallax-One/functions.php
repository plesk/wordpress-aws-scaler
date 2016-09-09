<?php
/**
 * parallax-one functions and definitions
 *
 * @package parallax-one
 */


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 730; /* pixels */
}
if ( ! function_exists( 'parallax_one_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function parallax_one_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on parallax-one, use a find and replace
	 * to change 'parallax-one' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'parallax-one', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );


	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'parallax-one' ),
		'parallax_footer_menu' => esc_html__('Footer Menu', 'parallax-one'),
	) );


	 /* Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support('custom-background',apply_filters( 'parallax_one_custom_background_args', array(
		'default-repeat'         => 'no-repeat',
		'default-position-x'     => 'center',
		'default-attachment'     => 'fixed'
	)));

	 /*
	 * This feature enables Custom_Headers support for a theme as of Version 3.4.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Custom_Header
	 */

	add_theme_support( 'custom-header',apply_filters( 'parallax_one_custom_header_args', array(
		'default-image' => parallax_get_file('/images/background-images/background.jpg'),
		'width'         => 1000,
		'height'        => 680,
		'flex-height'   => true,
		'flex-width'    => true,
		'header-text' 	=> false
	)));

	register_default_headers( array(
		'parallax_one_default_header_image' => array(
			'url'   => parallax_get_file('/images/background-images/background.jpg'),
			'thumbnail_url' => parallax_get_file('/images/background-images/background_thumbnail.jpg'),
		),
	));

	//Theme Support for WooCommerce
	add_theme_support( 'woocommerce' );

    /*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/* Set the image size by cropping the image */
	add_image_size( 'parallax-one-post-thumbnail-big', 730, 340, true );
	add_image_size( 'parallax-one-post-thumbnail-mobile', 500, 233, true );

	// Latest news Section (homepage)
	add_image_size( 'parallax-one-post-thumbnail-latest-news', 150, 150, true );
	add_image_size( 'parallax_one_team', 268, 273, true );
	add_image_size( 'parallax_one_services',60,62,true );
	add_image_size( 'parallax_one_customers',75,75,true );

	/**
	* Welcome screen
	*/
	if ( is_admin() ) {
		require get_template_directory() . '/inc/admin/welcome-screen/welcome-screen.php';
	}
}
endif; // parallax_one_setup
add_action( 'after_setup_theme', 'parallax_one_setup' );


add_filter( 'image_size_names_choose', 'parallax_one_media_uploader_custom_sizes' );

function parallax_one_media_uploader_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'parallax_one_team' => esc_html__('Parallax One Team Member','parallax-one'),
		'parallax_one_services' => esc_html__('Parallax One Services','parallax-one'),
		'parallax_one_customers' => esc_html__('Parallax One Testimonials','parallax-one')
    ) );
}


/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function parallax_one_widgets_init() {

	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'parallax-one' ),
			'id'            => 'sidebar-1',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2><div class="colored-line-left"></div><div class="clearfix widget-title-margin"></div>',
		)
	);

	register_sidebars( 4,
		array(
			'name' => esc_html__('Footer area %d','parallax-one'),
			'id' => 'footer-area',
			'before_widget'	=> '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'	=> '<h3 class="widget-title">',
			'after_title'	=> '</h3>'
		)
	);

}
add_action( 'widgets_init', 'parallax_one_widgets_init' );




/**
 * Fallback Menu
 *
 * If the menu doesn't exist, the fallback function to use.
 */
function parallax_one_wp_page_menu()
{
    echo '<ul class="nav navbar-nav navbar-right main-navigation small-text no-menu">';
    wp_list_pages(array('title_li' => '', 'depth' => 1));
    echo '</ul>';
}



function parallax_one_customizer_scripts(){
	wp_register_script( 'parallax_ddslick', get_template_directory_uri() .'/js/jquery.ddslick.js', array("jquery"), '1.0.0');

	wp_enqueue_script( 'parallax_one_customizer_script', parallax_get_file('/js/parallax_one_customizer.js'), array("jquery","jquery-ui-draggable","parallax_ddslick"),'1.0.2', true  );

	wp_localize_script( 'parallax_one_customizer_script', 'parallaxOneCustomizerObject', array(

		'documentation' => esc_html__( 'Documentation', 'parallax-one' ),
		'support' => esc_html__('Support','parallax-one'),
		'pro' => __('Upgrade to PRO','parallax-one'),

	) );
}
add_action( 'customize_controls_enqueue_scripts', 'parallax_one_customizer_scripts' );

function parallax_one_fonts_url(){
	$fonts_url = '';
 
	/* Translators: If there are characters in your language that are not
	* supported by Lora, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$cabin = _x( 'on', 'Cabin font: on or off', 'parallax-one' );
	 
	/* Translators: If there are characters in your language that are not
	* supported by Open Sans, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$open_sans = _x( 'on', 'Open Sans font: on or off', 'parallax-one' );

	if ( 'off' !== $cabin || 'off' !== $open_sans ) {
	$font_families = array();
	 
		if ( 'off' !== $cabin ) {
		$font_families[] = 'Cabin:400,600';
		}

		if ( 'off' !== $open_sans ) {
		$font_families[] = 'Open Sans:400,300,600';
		}

		$parallax_one_character_cyrillic = get_theme_mod('parallax_one_character_cyrillic');
		$parallax_one_character_vietnamese = get_theme_mod('parallax_one_character_vietnamese');
		$parallax_one_character_greek = get_theme_mod('parallax_one_character_greek');

		$parallax_one_character_cyrillic_text = ( isset($parallax_one_character_cyrillic) && ($parallax_one_character_cyrillic != 1) ?  '' : ',cyrillic' );
		$parallax_one_character_greek_text = ( isset($parallax_one_character_greek) && ($parallax_one_character_greek != 1) ?  '' : ',greek' );
		$parallax_one_character_vietnamese_text = ( isset($parallax_one_character_vietnamese) && ($parallax_one_character_vietnamese != 1) ?  '' : ',vietnamese' );

		$query_args = array(
		'family' => urlencode( implode( '|', $font_families ) ),
		'subset' => urlencode( 'latin,latin-ext'.$parallax_one_character_cyrillic_text.$parallax_one_character_greek_text.$parallax_one_character_vietnamese_text ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Enqueue scripts and styles.
 */
function parallax_one_scripts() {

	wp_enqueue_style( 'parallax-one-fonts', parallax_one_fonts_url(), array(), null );

	wp_enqueue_style( 'parallax-one-bootstrap-style', parallax_get_file( '/css/bootstrap.min.css'),array(), '3.3.1');

	wp_enqueue_style( 'parallax-one-style', get_stylesheet_uri(), array('parallax-one-bootstrap-style'),'1.0.0');

	wp_enqueue_script( 'parallax-one-bootstrap', parallax_get_file('/js/bootstrap.min.js'), array(), '3.3.5', true );

	wp_enqueue_script( 'parallax-one-custom-all', parallax_get_file('/js/custom.all.js'), array('jquery'), '2.0.2', true );

	wp_localize_script( 'parallax-one-custom-all', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . esc_html__( 'expand child menu', 'parallax-one' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . esc_html__( 'collapse child menu', 'parallax-one' ) . '</span>',
	) );


	$parallax_one_enable_move = get_theme_mod('paralax_one_enable_move');
	if ( !empty($parallax_one_enable_move) && $parallax_one_enable_move && 'posts' == get_option( 'show_on_front' ) && is_front_page() ) {

		wp_enqueue_script( 'parallax-one-home-plugin', parallax_get_file('/js/plugin.home.js'), array('jquery','parallax-one-custom-all'), '1.0.1', true );

	}

	if ( 'posts' == get_option( 'show_on_front' ) && is_front_page() ) {

		wp_enqueue_script( 'parallax-one-custom-home', parallax_get_file('/js/custom.home.js'), array('jquery'), '1.0.0', true );
	}

	wp_enqueue_script( 'parallax-one-skip-link-focus-fix', parallax_get_file('/js/skip-link-focus-fix.js'), array(), '1.0.0', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'parallax_one_scripts' );

function parallax_one_id_is_duplicate($id, $parallax_one_array_decoded){
	$count = 0;
	foreach ($parallax_one_array_decoded as $it) {
		if(!empty($it->id)){
			if($it->id == $id){
				$count++;
				if($count == 2){
					return true;
				}
			}
		}
	}
	return false;
}

function parallax_one_add_id(){

	$migrate = get_option( 'parallax_one_migrate_translation2' );
	if( isset($migrate) && $migrate == false ) {

		/*Logo*/
		$parallax_one_logos = get_theme_mod('parallax_one_logos_content', json_encode( array( 
			array("image_url" => parallax_get_file('/images/companies/1.png') ,"link" => "#", "id" => "parallax_one_56d7ea7f40f56" ),
			array("image_url" => parallax_get_file('/images/companies/2.png') ,"link" => "#", "id" => "parallax_one_56d7f2cb8a158" ),
			array("image_url" => parallax_get_file('/images/companies/3.png') ,"link" => "#", "id" => "parallax_one_56d7f2cc8a159" ),
			array("image_url" => parallax_get_file('/images/companies/4.png') ,"link" => "#", "id" => "parallax_one_56d7f2ce8a15a" ),
			array("image_url" => parallax_get_file('/images/companies/5.png') ,"link" => "#", "id" => "parallax_one_56d7f2cf8a15b" )
		) ) );

		if(!empty($parallax_one_logos)){

			$parallax_one_logos_decoded = json_decode( $parallax_one_logos );
			foreach( $parallax_one_logos_decoded as &$it ){
				if( !array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}
				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_logos_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}
			}
			$parallax_one_logos = json_encode($parallax_one_logos_decoded);
			set_theme_mod( 'parallax_one_logos_content', $parallax_one_logos );
		}


		/*Services*/
		$parallax_one_services = get_theme_mod('parallax_one_services_content', json_encode(
		array(
				array('choice'=>'parallax_icon','icon_value' => 'icon-basic-webpage-multiple','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d93f3013'),
				array('choice'=>'parallax_icon','icon_value' => 'icon-ecommerce-graph3','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d94f3014'),
				array('choice'=>'parallax_icon','icon_value' => 'icon-basic-geolocalize-05','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d95f3015')
		) ) );
		if(!empty($parallax_one_services)){

			$parallax_one_services_decoded = json_decode($parallax_one_services);
			foreach($parallax_one_services_decoded as &$it){
				if(!array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}
				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_services_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}
			}

			$parallax_one_services = json_encode($parallax_one_services_decoded);
			set_theme_mod( 'parallax_one_services_content', $parallax_one_services );
		}

		/*Team*/
		$parallax_one_team = get_theme_mod('parallax_one_team_content', json_encode( array(
			array('image_url' => parallax_get_file('/images/team/1.jpg'),'title' => esc_html__('Albert Jacobs','parallax-one'),'subtitle' => esc_html__('Founder & CEO','parallax-one'), 'id' => 'parallax_one_56fe9796baca4'),
			array('image_url' => parallax_get_file('/images/team/2.jpg'),'title' => esc_html__('Tonya Garcia','parallax-one'),'subtitle' => esc_html__('Account Manager','parallax-one'), 'id' => 'parallax_one_56fe9798baca5'),
			array('image_url' => parallax_get_file('/images/team/3.jpg'),'title' => esc_html__('Linda Guthrie','parallax-one'),'subtitle' => esc_html__('Business Development','parallax-one'), 'id' => 'parallax_one_56fe9799baca6')
		) ) );
		
		if(!empty($parallax_one_team)){

			$parallax_one_team_decoded = json_decode($parallax_one_team);
			foreach($parallax_one_team_decoded as &$it){
				
				if( !empty( $it->social_repeater ) ){
					$icons = html_entity_decode( $it->social_repeater );
					$icons_decoded = json_decode( $icons, true );
					foreach($icons_decoded as &$it2){
						if(!array_key_exists ( "id" , $it2 ) || !($it2->id) ){
							$it2 = (object) array_merge( (array)$it2, array( 'id' => uniqid('parallax_one_social_repeater_', true) ) );
						}
					}
					$icons = json_encode( $icons_decoded );
					// $it->social_repeater = $icons;
				}
				
				if(!array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}

				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_team_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}

			}

			$parallax_one_team = json_encode($parallax_one_team_decoded);
			set_theme_mod( 'parallax_one_team_content', $parallax_one_team );
		}

		/*Testimonials*/
		$parallax_one_testimonials = get_theme_mod('parallax_one_testimonials_content', json_encode(
			array(
				array('image_url' => parallax_get_file('/images/clients/1.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd526edcd4e'),
				array('image_url' => parallax_get_file('/images/clients/2.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd526ddcd4d'),
				array('image_url' => parallax_get_file('/images/clients/3.jpg'),'title' => esc_html__('Happy Customer','parallax-one'),'subtitle' => esc_html__('Lorem ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo. Fusce malesuada vulputate faucibus. Integer in hendrerit nisi. Praesent a hendrerit urna. In non imperdiet elit, sed molestie odio. Fusce ac metus non purus sollicitudin laoreet.','parallax-one'),'id' => 'parallax_one_56fd5259dcd4c')
		) ) );
		
		if(!empty($parallax_one_testimonials)){

			$parallax_one_testimonials_decoded = json_decode($parallax_one_testimonials);
			foreach($parallax_one_testimonials_decoded as &$it){
				if(!array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}
				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_testimonials_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}
			}

			$parallax_one_testimonials = json_encode($parallax_one_testimonials_decoded);
			set_theme_mod( 'parallax_one_testimonials_content', $parallax_one_testimonials );
		}

		/*Contact Info*/
		$parallax_one_contact_info = get_theme_mod('parallax_one_contact_info_content', json_encode(
			array(
					array("icon_value" => "icon-basic-mail" ,"text" => "contact@site.com", "link" => "#", "id" => "parallax_one_56d450a72cb3a" ),
					array("icon_value" => "icon-basic-geolocalize-01" ,"text" => "Company address", "link" => "#", "id" => "parallax_one_56d069b88cb6f" ),
					array("icon_value" => "icon-basic-tablet" ,"text" => "0 332 548 954", "link" => "#", "id" => "parallax_one_56d069b98cb70" )
			)
		));
		if(!empty($parallax_one_contact_info)){

			$parallax_one_contact_info_decoded = json_decode($parallax_one_contact_info);
			foreach($parallax_one_contact_info_decoded as &$it){
				if(!array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}
				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_contact_info_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}
			}

			$parallax_one_contact_info = json_encode($parallax_one_contact_info_decoded);
			set_theme_mod( 'parallax_one_contact_info_content', $parallax_one_contact_info );
		}

		/*Social Icons*/
		$parallax_one_social_icons = get_theme_mod('parallax_one_social_icons', json_encode(
			array(
				array('icon_value' =>'icon-social-facebook' , 'link' => '#'),
				array('icon_value' =>'icon-social-twitter' , 'link' => '#'),
				array('icon_value' =>'icon-social-googleplus' , 'link' => '#')
			)
		));
		if(!empty($parallax_one_social_icons)){

			$parallax_one_social_icons_decoded = json_decode($parallax_one_social_icons);
			foreach($parallax_one_social_icons_decoded as &$it){
				if(!array_key_exists ( "id" , $it ) || !($it->id) ){
					$it = (object) array_merge( (array)$it, array( 'id' => uniqid('parallax_one_', true) ) );
				}
				if ( array_key_exists ( "id", $it) && parallax_one_id_is_duplicate( $it->id, $parallax_one_social_icons_decoded ) ){
					$it->id = uniqid('parallax_one_', true);
				}
			}

			$parallax_one_social_icons = json_encode($parallax_one_social_icons_decoded);
			set_theme_mod( 'parallax_one_social_icons', $parallax_one_social_icons );
		}

		update_option( 'parallax_one_migrate_translation2', true );
	}
}
add_action( 'after_setup_theme', 'parallax_one_add_id' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Enables user customization via WordPress plugin API
 */
require get_template_directory() . '/inc/hooks.php';

/**
 * TAV_Remote_Notification_Client.
 */
if (!class_exists('TAV_Remote_Notification_Client')) {
	require( get_template_directory() . '/inc/admin/class-remote-notification-client.php' );
}
$parallax_one_notification = new TAV_Remote_Notification_Client( 49, 'a0973bf1bd1fe265', 'https://themeisle.com?post_type=notification' );


function parallax_one_admin_styles() {
	wp_enqueue_style( 'parallax_admin_stylesheet', parallax_get_file('/css/admin-style.css'),'1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'parallax_one_admin_styles', 10 );

// Adding IE-only scripts to header.
function parallax_one_ie () {
    echo '<!--[if lt IE 9]>' . "\n";
    echo '<script src="'. parallax_get_file('/js/html5shiv.min.js').'"></script>' . "\n";
    echo '<![endif]-->' . "\n";
}
add_action('wp_head', 'parallax_one_ie');




	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	add_action('woocommerce_before_main_content', 'parallax_one_wrapper_start', 10);
	add_action('woocommerce_after_main_content', 'parallax_one_wrapper_end', 10);
	function parallax_one_wrapper_start() {
		echo '</div>';
		parallax_hook_header_bottom();
		echo '</header>';
		parallax_hook_header_after();
		echo '<div class="content-wrap">
				<div class="container">
					<div id="primary" class="content-area col-md-12">';
	}
	function parallax_one_wrapper_end() {
		echo '</div></div></div>';
	}


// add this code directly, no action needed
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );


/* tgm-plugin-activation */
require_once get_template_directory() . '/class-tgm-plugin-activation.php';


add_action( 'tgmpa_register', 'parallax_one_register_required_plugins' );
function parallax_one_register_required_plugins() {

		$plugins = array(
			array(

				'name'      => 'Intergeo Maps - Google Maps Plugin',

				'slug'      => 'intergeo-maps',

				'required'  => false

			),

			array(

				'name'     => 'Pirate Forms',

				'slug' 	   => 'pirate-forms',

				'required' => false

			)
		);

	$config = array(
        'default_path' => '',
        'menu'         => 'tgmpa-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'dismiss_msg'  => '',
        'is_automatic' => false,
        'message'      => '',
        'strings'      => array(
            'page_title'                      => esc_html__( 'Install Recommended Plugins', 'parallax-one' ),
            'menu_title'                      => esc_html__( 'Install Plugins', 'parallax-one' ),
            'installing'                      => esc_html__( 'Installing Plugin: %s', 'parallax-one' ),
            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'parallax-one' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'parallax-one' ),
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'parallax-one' ),
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'parallax-one' ),
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'parallax-one' ),
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'parallax-one' ),
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'parallax-one' ),
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'parallax-one' ),
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'parallax-one' ),
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'parallax-one' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'parallax-one' ),
            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'parallax-one' ),
            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'parallax-one' ),
            'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'parallax-one' ),
            'nag_type'                        => 'updated'
        )
    );

	tgmpa( $plugins, $config );

}

add_action('wp_footer','parallax_one_php_style', 100);
function parallax_one_php_style() {

	echo '<style type="text/css">';

	$parallax_one_title_color = get_theme_mod('parallax_one_title_color');
	if(!empty($parallax_one_title_color)){
		echo '.dark-text, .entry-header h1, #sidebar-secondary .widget-title { color: '. $parallax_one_title_color .' }';
	}
	$parallax_one_text_color = get_theme_mod('parallax_one_text_color');
	if(!empty($parallax_one_text_color)){
		echo 'body, .entry-content p, .pirate_forms_thankyou_wrap p { color: '.$parallax_one_text_color.'}';
	}

	$parallax_one_enable_move = get_theme_mod('paralax_one_enable_move');
	$parallax_one_first_layer = get_theme_mod('paralax_one_first_layer', parallax_get_file('/images/background1.png'));
	$parallax_one_second_layer = get_theme_mod('paralax_one_second_layer',parallax_get_file('/images/background2.png'));

	if( ( empty($parallax_one_enable_move) || !$parallax_one_enable_move) && 'posts' == get_option( 'show_on_front' ) && is_front_page() ) {
		$parallax_one_header_image = get_header_image();
		if(!empty($parallax_one_header_image)){
			echo '.header{ background-image: url('.parallax_one_make_protocol_relative_url($parallax_one_header_image).');}';
		}
	}

	$parallax_one_bigtitle_background = get_theme_mod('parallax_one_bigtitle_background','rgba(0, 0, 0, 0.7)');
	if(!empty($parallax_one_bigtitle_background)){
		echo '.overlay-layer-wrap{ background:'.$parallax_one_bigtitle_background.';}';
	}

	echo '</style>';
}



$pro_functions_path = parallax_get_file('/pro/functions.php');
if (file_exists($pro_functions_path)) {
	require $pro_functions_path;
}


function parallax_get_file($file){
	$file_parts = pathinfo($file);
	$accepted_ext = array('jpg','img','png','css','js');
	if( in_array($file_parts['extension'], $accepted_ext) ){
		$file_path = get_stylesheet_directory() . $file;
		if ( file_exists( $file_path ) ){
			return esc_url(get_stylesheet_directory_uri() . $file);
		} else {
			return esc_url(get_template_directory_uri() . $file);
		}
	}
}


/**
 * WooCommerce Extra Feature
 * --------------------------
 *
 * Change number of related products on product page
 * Set your own value for 'posts_per_page'
 *
 */

add_filter( 'woocommerce_output_related_products_args', 'parallax_one_related_products_args' );

function parallax_one_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns'] = 4;
	return $args;
}

/* Prevent theme from beeing updated by wordpress.org updates */

function parallax_one_prevent_wporg_update( $r, $url ) {
    if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
        return $r; // Not a theme update request. Bail immediately.

    $themes = @unserialize( $r['body']['themes'] );
    unset( $themes[ get_option( 'template' ) ] );
    unset( $themes[ get_option( 'stylesheet' ) ] );
    $r['body']['themes'] = serialize( $themes );
    return $r;
}

add_filter( 'http_request_args', 'parallax_one_prevent_wporg_update', 5, 2 );



function parallax_one_responsive_embed($html, $url, $attr, $post_ID) {
	$return = '<div class="parallax-one-video-container">'.$html.'</div>';
	return $return;
}

add_filter( 'embed_oembed_html', 'parallax_one_responsive_embed', 10, 4 );



/* Comments callback function*/
function parallax_one_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :
		case 'pingback' :


		case 'trackback' :
		?>
			<li class="post pingback">
				<p><?php _e( 'Pingback:', 'parallax-one' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'parallax-one' ), ' ' ); ?></p>
		<?php
		break;


		default :
		?>
			<li itemscope itemtype="http://schema.org/UserComments" <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment-body">
					<footer>
						<div itemscope itemprop="creator" itemtype="http://schema.org/Person" class="comment-author vcard" >
							<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
							<?php printf( __( '<span itemprop="name">%s </span><span class="says">says:</span>', 'parallax-one' ), sprintf( '<b class="fn">%s</b>', get_comment_author_link() ) ); ?>
						</div><!-- .comment-author .vcard -->
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'parallax-one' ); ?></em>
							<br />
						<?php endif; ?>
						<div class="comment-metadata">
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="comment-permalink" itemprop="url">
								<time class="comment-published" datetime="<?php comment_time( 'Y-m-d\TH:i:sP' ); ?>" title="<?php comment_time( _x( 'l, F j, Y, g:i a', 'comment time format', 'parallax-one' ) ); ?>" itemprop="commentTime">
									<?php printf( __( '%1$s at %2$s', 'parallax-one' ), get_comment_date(), get_comment_time() ); ?>
								</time>
							</a>
							<?php edit_comment_link( __( '(Edit)', 'parallax-one' ), ' ' );?>
						</div><!-- .comment-meta .commentmetadata -->
					</footer>

					<div class="comment-content" itemprop="commentText"><?php comment_text(); ?></div>

					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div><!-- .reply -->
				</article><!-- #comment-## -->

<?php
		break;

	endswitch;
}

/*WPML Compatibility*/

if( has_action('wpml_register_single_string') || function_exists( 'pll_register_string' ) ) {
	
	/*Logos section*/
	$parallax_one_logos = get_theme_mod('parallax_one_logos_content');
	if( !empty( $parallax_one_logos ) ){
		$parallax_one_logos_decoded = json_decode($parallax_one_logos);
		foreach ($parallax_one_logos_decoded as $parallax_one_logo_box) {

			if( !empty( $parallax_one_logo_box->id ) ){
				$id = $parallax_one_logo_box->id;
			}

			if( !empty( $parallax_one_logo_box->image_url ) ){
				$image = $parallax_one_logo_box->image_url;
			}

			if( !empty( $parallax_one_logo_box->link ) ){
				$link = $parallax_one_logo_box->link;
			}

			if( !empty( $id ) ){
				if( !empty( $image ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Logos section', 'Logo image '.$id, $image );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_logo_image', $image, 'Logos');
					}
				}

				if( !empty( $link ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Logos section', 'Logo link '.$id, $link );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_logo_link', $link, 'Logos');
					}
				}
			}
		}
	}

	/*Services section*/
	$parallax_one_services = get_theme_mod('parallax_one_services_content');
	if( !empty( $parallax_one_services ) ){
		$parallax_one_services_decoded = json_decode( $parallax_one_services );
		foreach( $parallax_one_services_decoded as $parallax_one_service_box ){
			
			if( !empty( $parallax_one_service_box->id ) ){
				$id = $parallax_one_service_box->id;
			}
			
			if( !empty( $parallax_one_service_box->icon_value ) ){
				$icon = $parallax_one_service_box->icon_value;
			}
			
			if( !empty( $parallax_one_service_box->image_url ) ){
				$image = $parallax_one_service_box->image_url;
			}
			
			if( !empty( $parallax_one_service_box->choice ) ){
				$choice =  $parallax_one_service_box->choice;
			}
			
			if( !empty( $parallax_one_service_box->title ) ){
				$title = $parallax_one_service_box->title;
			}
			
			if( !empty( $parallax_one_service_box->text ) ){
				$text = $parallax_one_service_box->text;
			}

			if( !empty( $parallax_one_service_box->link ) ){
				$link = $parallax_one_service_box->link;
			}

			if( !empty( $id ) ) {
				if( !empty( $choice ) ){
					if( $choice == 'parallax_image' ){
						if( !empty( $image ) ){
							do_action( 'wpml_register_single_string', 'Parallax One -> Services section', 'Service box image '.$id, $image );
							if( function_exists('pll_register_string')){
								pll_register_string($id.'_services_image', $image, 'Featured Area');
							}
						}
					} else {
						if( !empty($icon) ){
							do_action( 'wpml_register_single_string', 'Parallax One -> Services section', 'Service box icon '.$id, $icon );
							if( function_exists('pll_register_string')){
								pll_register_string($id.'_services_icon', $icon, 'Featured Area');
							}
						}
					}
				}

				if( !empty( $title ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Services section', 'Service box title '.$id, $title );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_services_title', $title, 'Featured Area');
					}
				}

				if( !empty( $text ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Services section', 'Service box text '.$id, $text );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_services_text', $text, 'Featured Area');
					}
				}

				if( !empty( $link ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Services section', 'Service box link '.$id, $link );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_services_link', $link, 'Featured Area');
					}
				}
			}
		}
	}

	/*Team*/
	$parallax_one_team = get_theme_mod('parallax_one_team_content');
	if( !empty( $parallax_one_team ) ){
		$parallax_one_team_decoded = json_decode( $parallax_one_team );
		foreach( $parallax_one_team_decoded as $parallax_one_team_box ){
			
			if( !empty( $parallax_one_team_box->id ) ){
				$id = esc_attr($parallax_one_team_box->id);
			}
					
			if( !empty( $parallax_one_team_box->title ) ){
				$title = $parallax_one_team_box->title;
			}
				
			if( !empty( $parallax_one_team_box->subtitle ) ){
				$subtitle = $parallax_one_team_box->subtitle;
			}

			if( !empty( $parallax_one_team_box->image_url ) ){
				$image = $parallax_one_team_box->image_url;
			}

			if( !empty( $id ) ){
				if( !empty( $title ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Team section', 'Team box title '.$id, $title );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_team_title', $title, 'Team');
					}
				}

				if( !empty( $subtitle ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Team section', 'Team box subtitle '.$id, $subtitle );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_team_subtitle', $subtitle, 'Team');
					}
				}

				if( !empty( $image) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Team section', 'Team box image '.$id, $image );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_team_image', $image, 'Team');
					}
				}

				if( !empty( $parallax_one_team_box->social_repeater  ) ){
					$socials = html_entity_decode( $parallax_one_team_box->social_repeater );
					$icons_decoded = json_decode( $socials, true );

					if( !empty( $icons_decoded ) ){
						foreach ($icons_decoded as $value) {
							$s_id = $value['id'];
							$s_icon = $value['icon'];
							$s_link = $value['link'];
							if( !empty( $s_icon ) ){
								do_action( 'wpml_register_single_string', 'Parallax One -> Team section', 'Social icon '.$s_id, $s_icon );
								if( function_exists('pll_register_string')){
									pll_register_string($s_id.'_team_social_icon', $s_icon, 'Team');
								}
							}

							if( !empty( $s_link ) ){
								do_action( 'wpml_register_single_string', 'Parallax One -> Team section', 'Social link '.$s_id, $s_link );
								if( function_exists('pll_register_string')){
									pll_register_string($s_id.'_team_social_link', $s_link, 'Team');
								}
							}
						}
					}
				}
			}
		}
	}

	/*Testimonials*/
	$parallax_one_testimonials = get_theme_mod('parallax_one_testimonials_content');
	if( !empty( $parallax_one_testimonials ) ){
		$parallax_one_testimonials_decoded = json_decode($parallax_one_testimonials);
		foreach($parallax_one_testimonials_decoded as $parallax_one_testimonials_box){
			
			if( !empty( $parallax_one_testimonials_box->id ) ){
				$id = esc_attr($parallax_one_testimonials_box->id);
			}

			if( !empty( $parallax_one_testimonials_box->image_url ) ){
				$image = $parallax_one_testimonials_box->image_url;
			}

			if( !empty( $parallax_one_testimonials_box->title ) ){
				$title = $parallax_one_testimonials_box->title;
			}
			
			if( !empty( $parallax_one_testimonials_box->subtitle ) ){
				$subtitle = $parallax_one_testimonials_box->subtitle;
			}

			if( !empty( $parallax_one_testimonials_box->text ) ){
				$text = $parallax_one_testimonials_box->text;
			}

			if( !empty( $id ) ){
				if( !empty( $image ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Testimonials section', 'Testimonial box image '.$id, $image );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_testimonials_image', $image, 'Testimonials');
					}
				}

				if( !empty( $title ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Testimonials section', 'Testimonial box title '.$id, $title );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_testimonials_title', $title, 'Testimonials');
					}
				}

				if( !empty( $subtitle ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Testimonials section', 'Testimonial box subtitle '.$id, $subtitle );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_testimonials_subtitle', $subtitle, 'Testimonials');
					}
				}

				if( !empty( $text) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Testimonials section', 'Testimonial box text '.$id, $text );
					if( function_exists('pll_register_string')){
						pll_register_string($id.'_testimonials_text', $text, 'Testimonials');
					}
				}
			}
		}
	}

	/*Contact*/
	$parallax_one_contact = get_theme_mod('parallax_one_contact_info_content');
	if( !empty( $parallax_one_contact ) ){
		$parallax_one_contact_decoded = json_decode($parallax_one_contact);
		foreach($parallax_one_contact_decoded as $parallax_one_contact_box){
			if( !empty( $parallax_one_contact_box->id ) ){
				$id = esc_attr($parallax_one_contact_box->id);
			}

			if( !empty( $parallax_one_contact_box->icon_value ) ){
				$icon = $parallax_one_contact_box->icon_value;
			}
			
			if( !empty( $parallax_one_contact_box->text ) ){
				$text = $parallax_one_contact_box->text;
			}
			
			if( !empty( $parallax_one_contact_box->link ) ){
				$link = $parallax_one_contact_box->link;
			}

			if( !empty( $id ) ) {
				if( !empty( $icon ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Contact section', 'Contact box icon '.$id, $icon );
					if( function_exists('pll_register_string')){
						pll_register_string( $id.'_contact_icon', $icon, 'Contact' );
					}
				}

				if( !empty( $link ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Contact section', 'Contact box link '.$id, $link );
					if( function_exists('pll_register_string')){
						pll_register_string( $id.'_contact_link', $link, 'Contact' );
					}
				}

				if( !empty( $text ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Contact section', 'Contact box text '.$id, $text );
					if( function_exists('pll_register_string')){
						pll_register_string( $id.'_contact_text', $text, 'Contact' );
					}
				}
			}
		}
	}

	/*Footer*/
	$parallax_one_social_icons = get_theme_mod('parallax_one_social_icons');
	if( !empty( $parallax_one_social_icons ) ){
		$parallax_one_social_icons_decoded = json_decode($parallax_one_social_icons);
		foreach($parallax_one_social_icons_decoded as $parallax_one_footer_social){
			if( !empty( $parallax_one_footer_social->id ) ){
				$id = esc_attr($parallax_one_footer_social->id);
			}

			if( !empty( $parallax_one_footer_social->link ) ){
				$link = $parallax_one_footer_social->link;
			}

			if( !empty( $parallax_one_footer_social->icon_value ) ){
				$icon = $parallax_one_footer_social->icon_value;
			}

			if( !empty( $id ) ) {
				if( !empty( $icon ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Footer', 'Footer social icon '.$id, $icon );
					if( function_exists('pll_register_string')){
						pll_register_string( $id.'_footer_icon', $icon, 'Footer' );
					}
				}

				if( !empty( $link ) ){
					do_action( 'wpml_register_single_string', 'Parallax One -> Footer', 'Footer social link '.$id, $link );
					if( function_exists('pll_register_string')){
						pll_register_string( $id.'_footer_link', $link, 'Footer' );
					}
				}
			}
		}
	}
}



/*Check if Repeater is empty*/

if( !function_exists('parallax_one_general_repeater_is_empty')){
	function parallax_one_general_repeater_is_empty($parallax_one_arr){
		$parallax_one_services_decoded = json_decode($parallax_one_arr);
		foreach($parallax_one_services_decoded as $parallax_box){
			if(!empty($parallax_box->choice) && $parallax_box->choice == 'parallax_none'){
				$parallax_box->icon_value = '';
				$parallax_box->image_url = '';
			}
			foreach ($parallax_box as $key => $value){
				if(!empty($value) && $key!='choice' && $key!='id' && ($value!='No Icon' && $key=='icon_value') ) {
					return false;
				}
			}
		}
		return true;
	}
}

function parallax_one_get_template_part($template){
    if(locate_template($template.'.php')) {
		get_template_part($template);
    } else {
		if(defined('PARALLAX_ONE_PLUS_PATH')){
			$template = basename ($template);
			if(file_exists ( PARALLAX_ONE_PLUS_PATH.'public/templates/'.$template.'.php' )){
				require_once ( PARALLAX_ONE_PLUS_PATH.'public/templates/'.$template.'.php' );
			}
		}
	}
}

function parallax_one_is_not_post(){
	return ('posts' == get_option( 'show_on_front' ) ? true : false);
}


if(!function_exists('parallax_one_make_protocol_relative_url')){
	function parallax_one_make_protocol_relative_url( $url ) {
		return preg_replace( '(https?://)', '//', $url );
	}
}

/**
 * Transform Enqueued Stylesheet URLs
 */
function parallax_one_style_loader_src( $src, $handle ) {
	return parallax_one_make_protocol_relative_url( $src );
}
add_filter( 'style_loader_src', 'parallax_one_style_loader_src', 10, 2 );

/**
 * Transform Enqueued JavaScript URLs
 */
function parallax_one_script_loader_src( $src, $handle ) {
	return parallax_one_make_protocol_relative_url( $src );
}
add_filter( 'script_loader_src', 'parallax_one_script_loader_src', 10, 2 );

/**
 * Transform Enqueued Theme Files
 */
function parallax_one_template_directory_uri( $template_dir_uri, $template, $theme_root_uri ) {
	return parallax_one_make_protocol_relative_url( $template_dir_uri );
}
add_filter( 'template_directory_uri', 'parallax_one_template_directory_uri', 10, 3 );

/**
 * Transform Enqueued Theme Files
 */
function parallax_one_stylesheet_directory_uri( $stylesheet_dir_uri, $stylesheet, $theme_root_uri ) {
	return parallax_one_make_protocol_relative_url( $stylesheet_dir_uri );
}
add_filter( 'stylesheet_directory_uri', 'parallax_one_stylesheet_directory_uri', 10, 3 );


add_action( 'parallax_404_content', 'parallax_output_404_content' ); # Outputs a helpful message on 404 pages
function parallax_output_404_content(){
	?>
	<div id="primary" class="content-area col-md-8">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h2 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'parallax-one' ); ?></h2>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'parallax-one' ); ?></p>

					<?php get_search_form(); ?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php get_sidebar(); ?>
	<?php
}


function parallax_one_add_editor_styles() {
	add_editor_style( array( 'css/custom-editor-style.css', parallax_one_fonts_url() ) );
}
add_action( 'after_setup_theme', 'parallax_one_add_editor_styles' );


function Parallax_One_themeisle_sdk(){
	require 'vendor/themeisle/load.php';
	themeisle_sdk_register (
		array(
			'product_slug'=>'Parallax-One',
			'store_url'=>'http://themeisle.com',
			'store_name'=>'Themeisle',
			'product_type'=>'theme',
			'wordpress_available'=>false,
			'paid'=>false,
		)
	);
}

Parallax_One_themeisle_sdk(); 

 
