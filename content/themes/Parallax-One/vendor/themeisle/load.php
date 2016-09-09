<?php
/*
 * Themeisle SDK
 *
 * @version 1.0.0
 *
 *
 */
/**
 * Themeisle SDK Version
 *
 * @var string
 */
$this_themeisle_sdk_version = "1.1.2";
/**
 * Themeisle SDK Path
 *
 * @var string
 */
$this_themeisle_sdk_path = dirname( __FILE__ );
global $themeisle_sdk_products;
if ( ! isset( $themeisle_sdk_products ) ) {
	$themeisle_sdk_products = array();
}
//load the product details
$themeisle_sdk_products[] = array(
	"version"  => $this_themeisle_sdk_version,
	"sdk_path" => $this_themeisle_sdk_path
);
// load the latest sdk version from the active Themeisle products
if ( ! function_exists( "themeisle_sdk_load" ) ) :
	function themeisle_sdk_load() {
		global $themeisle_sdk_products;
		global $this_themeisle_sdk_version;
		$max_version  = reset( $themeisle_sdk_products );
		if ( ! isset ( $max_version ["version"] )  ) { 
			$max_version = $this_themeisle_sdk_version;
		} else{
			$max_version = $max_version["version"];
		}
		
		$path_to_load = "";
		foreach ( $themeisle_sdk_products as $product ) {
			if ( version_compare( $product["version"], $max_version ) >= 0 ) {
				$path_to_load = $product["sdk_path"];
			}
		}
		include $path_to_load . "/start.php";
		foreach ( $themeisle_sdk_products as $registered_product ) {
			${$registered_product["product_data"]["product_slug"] . "_themeisle_sdk"} = new THEMEISLE_SDK( $registered_product );
		}
	}
endif;
//register the product which will use the sdk
if ( ! function_exists( "themeisle_sdk_register" ) ) :
	function themeisle_sdk_register( $array ) {
		global $themeisle_sdk_products;
		foreach ( $themeisle_sdk_products as $key => $product ) {
			if ( strpos( $product["sdk_path"], $array["product_slug"] ) !== false ) {
				$themeisle_sdk_products[ $key ]["product_data"] = $array;
			}
		}
	}
endif;
add_action( "wp_loaded", "themeisle_sdk_load" );