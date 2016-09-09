<?php
if ( ! class_exists( "THEMEISLE_LOGGER" ) ) :
	/**
	 * Class THEMEISLE_LOGGER
	 *
	 * Send the statistics to the Themeisle Endpoint
	 */
	/**
	 * Class THEMEISLE_LOGGER
	 */
	class THEMEISLE_LOGGER {

		/**
		 * @var string $logging_url Url where to send the logs
		 */
		private $logging_url = 'http://mirror.themeisle.com';

		/**
		 * @var string $product_slug Slug of the product
		 */
		private $product_slug;

		/**
		 * @var string $product_version Version of the product
		 */
		private $product_version;

		/**
		 * @var string $product_cron Cron name handler
		 */
		private $product_cron;

		public function __construct( $slug, $version ) {
			$this->product_slug    = $slug;
			$this->product_version = $version;
			$this->product_cron    = self::key_ready_name( $this->product_slug ) . "_log_activity";
		}

		/**
		 * @param string $string the String to be normalized for cron handler
		 *
		 * @return string $name         the normalized string
		 */
		static function key_ready_name( $string ) {
			return str_replace( "-", "_", strtolower( trim( $string ) ) );
		}

		/**
		 * Start the cron to send the log. It will randomize the interval in order to not send all the logs at the same time.
		 */
		public function start() {
			if ( ! wp_next_scheduled( $this->product_cron ) ) {
				wp_schedule_single_event( time() + ( rand( 15, 24 ) * 3600 ), $this->product_cron );
			}
			add_action( $this->product_cron, array( $this, "send_log" ) );
		}

		/**
		 * Send the statistics to the api endpoint
		 */
		public function send_log() {
			wp_remote_post( $this->logging_url, array(
				'method'      => 'POST',
				'timeout'     => 3,
				'redirection' => 5,
				'headers'     => array( "X-ThemeIsle-Event" => "log_site" ),
				'body'        => array(
					'site'    => get_site_url(),
					'product' => $this->product_slug,
					'version' => $this->product_version
				),
			) );
		}

	}
endif;