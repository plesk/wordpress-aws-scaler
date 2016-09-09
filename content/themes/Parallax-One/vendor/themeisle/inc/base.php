<?php
if ( ! class_exists( "THEMEISLE_SDK" ) ) :
	class THEMEISLE_SDK {

		/**
		 * @var string $product_slug Should contain the product slug
		 */
		public $product_slug;

		/**
		 * @var string $product_version Should contain the product version string
		 */
		public $product_version;

		/**
		 * @var string $product_name Should contain the product name string
		 */
		public $product_name;

		/**
		 * @var string $store_url Should contain the store url to check agains updates
		 */
		public $store_url;

		/**
		 * @var string $store_name Should contain the store name to check agains updates
		 */
		public $store_name;

		/**
		 * @var string $product_type Should contain the product type, either theme or plugin
		 */
		public $product_type;

		/**
		 * @var bool $wordpress_available Either is available on wordpress or not
		 */
		public $wordpress_available;

		/**
		 * @var bool $activation Either is requiring license activation or not
		 */
		public $paid;

		/**
		 * @var bool $product_data Product metadata and basename file for plugins
		 */
		public $product_data;

		public function __construct( $data = array() ) {
			$data                      = $data["product_data"];
			$args                      = wp_parse_args( $data, array(
				'store_url'           => '',
				'store_name'           => '',
				'product_slug'        => '',
				'product_type'        => 'theme',
				'wordpress_available' => false,
				'paid'                => false,
			) );
			$this->product_slug        = $args['product_slug'];
			$this->store_url           = $args['store_url'];
			$this->store_name           = $args['store_name'];
			$this->paid                = ( bool ) $args['paid'] ;
			$this->wordpress_available = ( bool ) $args['wordpress_available'] ;
			$this->product_type        = in_array( $args['product_type'], array(
				'theme',
				'plugin'
			) ) ? $args['product_type'] : "";
			if ( empty( $this->product_type ) ) {
				return false;
			}
			if ( $this->product_type === "theme" ) {
				$this->product_data    = wp_get_theme( $this->product_slug );
				$this->product_version = $this->product_data->get( "Version" );
				$this->product_name    = $this->product_data->get( "Name" );

			}
			if ( $this->product_type === 'plugin' ) {
				$this->product_data    = $this->get_plugin_data( $this->product_slug );
				$this->product_version = $this->product_data["data"]['Version'];
				$this->product_name    = $this->product_data["data"]['Name'];
			}
			if ( ! $this->wordpress_available ) {
				$logger = new THEMEISLE_LOGGER( $this->product_slug, $this->product_version );
				$logger->start();
				$licenser = new THEMEISLE_LICENSE( $this->product_name, $this->product_slug, $this->product_version, $this->product_type, $this->paid, $this->store_url, $this->product_data, $this->store_name );
				$licenser->enable();
			}

		}

		/**
		 * @param string $slug Slug of the plugin to get the information for
		 *
		 * @return mixed $metadata          The plugin metadata
		 */
		private function get_plugin_data( $slug ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins = get_plugins();
			foreach ( $plugins as $plugin_file => $plugin_data ) {
				if ( strpos( $plugin_file, $slug ) !== false ) {
					return array( "basename" => $plugin_file, "data" => $plugin_data );
				}
			}

		}

	}
endif;
