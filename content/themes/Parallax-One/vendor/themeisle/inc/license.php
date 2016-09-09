<?php
if ( ! class_exists( 'THEMEISLE_LICENSE' ) ) :
	/**
	 * Class THEMEISLE_LICENSE
	 *
	 * Used to update the themeisle products
	 */
	class THEMEISLE_LICENSE extends THEMEISLE_SDK {

		/**
		 * @var string $license_key The license key string
		 */
		public $license_key;
		/**
		 * @var string $store_url The store url where to check against the updates
		 */
		public $store_url;
		/**
		 * @var string $store_name The store name where to check against the updates
		 */
		public $store_name;
		/**
		 * @var string $product_type Either theme or plugin
		 */
		public $product_type;
		/**
		 * @var bool $paid Either it's a paid or free product
		 */
		public $paid;
		/**
		 * @var string $product_name The current product name
		 */
		public $product_name;
		/**
		 * @var string $product_slug The current product slug
		 */
		public $product_slug;
		/**
		 * @var string $product_version The current product version
		 */
		public $product_version;
		/**
		 * @var string $product_data The product metadata as well as the basename for plugins
		 */
		public $product_data;

		/**
		 * @var string $product_key The key used for transients
		 */
		public $product_key;

		/**
		 * @var string $product_normalized The normalized product name
		 */
		public $product_normalized;

		/**
		 * @var bool $do_check This ensures that the custom API request only runs on the second time that WP fires the update check
		 */
		private $do_check = false;

		public function __construct( $product_name, $product_slug, $product_version, $product_type, $paid, $store_url, $product_data,$store_name ) {
			$this->product_version    = $product_version;
			$this->product_slug       = $product_slug;
			$this->product_name       = $product_name;
			$this->product_type       = $product_type;
			$this->store_url          = $store_url;
			$this->store_name         = $store_name;
			$this->paid               = $paid;
			$this->product_data       = $product_data;
			$this->product_key        = $this->product_slug . '-update-response';
			$this->product_normalized = THEMEISLE_LOGGER::key_ready_name( $this->product_slug );
			if ( ! $this->paid ) {
				$this->license_key = "free";
			} else {
				$license_data = get_option( $this->product_normalized . '_license_data', '' );
				if ( $license_data !== '' ) {
					$this->license_key = isset( $license_data->key ) ? $license_data->key : get_option( $this->product_normalized . '_license', '' );
				} else {
					$this->license_key = get_option( $this->product_normalized . '_license', '' );
				}
				$this->register_license_hooks();
			}
		}

		/**
		 * Register license hooks for the themeisle products
		 */
		public function register_license_hooks() {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_init', array( $this, 'activate_license' ) );
			add_action( 'admin_init', array( $this, 'product_valid' ), 99999999 );
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
		}

		/**
		 * Register the setting for the license of the product
		 *
		 * @return bool
		 */
		public function register_settings() {
			if ( ! is_admin() ) {
				return false;
			}
			add_settings_field(
				$this->product_normalized . '_license',
				$this->product_name . " license",
				array( $this, 'license_view' ),
				'general'
			);
		}

		/**
		 *  The license view field
		 */
		public function license_view() {
			$status = $this->get_license_status();
			$value  = $this->license_key;
			echo '<p ><input ' . ( ( $status === 'valid' ) ? ( 'style="border:1px solid #7ad03a; "' ) : '' ) . ' type="text" id="' . $this->product_normalized . '_license" name="' . $this->product_normalized . '_license" value="' . $value . '" /><a ' . ( ( $status === 'valid' ) ? ( 'style="color:#fff;background:  #7ad03a; display: inline-block;text-decoration: none;font-size: 13px;line-height: 26px;height: 26px; margin-left:5px; padding: 0 10px 1px;  -webkit-border-radius: 3px;border-radius: 3px; ">Valid' ) : ( 'style="color:#fff;background:  #dd3d36; display: inline-block;text-decoration: none;font-size: 13px;line-height: 26px;height: 26px; margin-left:5px; padding: 0 10px 1px;  -webkit-border-radius: 3px;border-radius: 3px; ">Invalid' ) ) . ' </a>&nbsp;&nbsp;&nbsp;<button name="' . $this->product_normalized . '_btn_trigger" ' . ( ( $status === 'valid' ) ? ( ' class="button button-primary">Deactivate' ) : ( ' class="button button-primary" value="yes" type="submit" >Activate' ) ) . ' </button></p><p class="description">Enter your license from <a  href="'.$this->store_url.'">'.$this->store_name.'</a> purchase history in order to get theme updates</p>';

		}

		/** Return the license status
		 *
		 * @return mixed|void
		 */
		public function get_license_status() {
			$license_data = get_option( $this->product_normalized . '_license_data', '' );
			if ( $license_data !== '' ) {
				return isset( $license_data->license ) ? $license_data->license : get_option( $this->product_normalized . '_license_status', '' );
			} else {
				return get_option( $this->product_normalized . '_license_status', '' );
			}

		}

		/**
		 *  Check if the license is active or not
		 *
		 * @return bool
		 */
		public function check_activation() {
			$license_data = get_option( $this->product_normalized . '_license_data', '' );
			if ( $license_data !== '' ) {
				return isset( $license_data->error ) ? ( $license_data->error == 'no_activations_left' ) : false;
			}

			return false;
		}

		/**
		 *  Check if the license is about to expire in the next month
		 *
		 * @return bool
		 */
		function check_expiration() {
			$license_data = get_option( $this->product_normalized . '_license_data', '' );
			if ( $license_data !== '' ) {
				if ( isset( $license_data->expires ) ) {
					if ( strtotime( $license_data->expires ) - time() < 30 * 24 * 3600 ) {
						return true;
					}
				}
			}

			return false;
		}

		/**
		 * Return the renew url from the store used
		 * @return string
		 */
		function renew_url() {
			$license_data = get_option( $this->product_normalized . '_license_data', '' );
			if ( $license_data !== '' ) {
				if ( isset( $license_data->download_id ) && isset( $license_data->key ) ) {
					return $this->store_url . "/checkout/?edd_license_key=" . $license_data->key . "&download_id=" . $license_data->download_id;
				}
			}

			return $this->store_url;
		}

		/**
		 * Check if we hide the notificatin nag or not
		 *
		 * @param $hide
		 *
		 * @return bool
		 */
		function check_hide( $hide ) {
			if ( isset( $_GET[ $this->product_normalized . '_hide_' . $hide ] ) ) {
				if ( $_GET[ $this->product_normalized . '_hide_' . $hide ] === 'yes' ) {
					$license                    = get_option( $this->product_normalized . '_license_data', '' );
					$license->{'hide_' . $hide} = true;
					update_option( $this->product_normalized . '_license_data', $license );

					return false;
				}
			} else {
				$license =
				$license = get_option( $this->product_normalized . '_license_data', '' );;
				if ( $license !== '' ) {
					if ( isset( $license->{'hide_' . $hide} ) ) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 *  Show the admin notice regarding the license status
		 *
		 * @return bool
		 */
		function show_notice() {
			if ( ! is_admin() ) {
				return false;
			}
			$status    = $this->get_license_status();
			$admin_url = admin_url( "options-general.php" );
			if ( $status != 'valid' ) {
				if ( $this->check_activation() ) {
					if ( $this->check_hide( 'activation' ) ) {
						?>
						<div class="error">
							<p><strong>No activations left for <?php echo $this->product_name; ?> !!!. You need to
									upgrade your plan in order to use <?php echo $this->product_name; ?> on more
									websites. Please ask the <a href="<?php echo $this->store_url; ?>" target="_blank" ><?php echo $this->store_name; ?> </a>
									Staff for more details.</strong>| <a
									href="<?php echo $admin_url; ?>?<?php echo $this->product_normalized; ?>_hide_activation=yes">Hide
									Notice</a></p>
						</div>
						<?php
						return false;
					}
				}
				?>
				<?php if ( $this->check_hide( 'valid' ) ): ?>
					<div class="error">
						<p><strong>You do not have a valid license
								for <?php echo $this->product_name; ?>  <?php echo $this->product_type; ?> !!!. You can
								get the license code from your purchase history on <a
									href="<?php echo $this->store_url; ?>" target="_blank"><?php echo $this->store_name; ?></a> and validate it <a
									href="<?php echo admin_url( "options-general.php" ); ?>#<?php echo $this->product_normalized; ?>_license">here</a>
							</strong>| <a
								href="<?php echo $admin_url; ?>?<?php echo $this->product_normalized; ?>_hide_valid=yes">Hide
								Notice</a></p>
					</div>
				<?php endif; ?>
				<?php
			} else {
				if ( $this->check_expiration() ) {
					if ( $this->check_hide( 'expiration' ) ) {
						?>
						<div class="update-nag">
							<p><strong>Your license is about to expire
									for <?php echo $this->product_name; ?>    <?php echo $this->product_type; ?> !!!.
									You can go to <a
										href="<?php echo $this->renew_url(); ?>" target="_blank"><?php echo $this->store_name; ?></a> and  renew it.</strong>| <a href="
										<?php echo $admin_url; ?>?<?php echo $this->product_normalized; ?>_hide_expiration=yes">Hide
										Notice</a></p>
						</div>
						<?php
					}
				}
			}
		}

		public function product_valid( $force = false ) {
			if ( false === ( $license = get_transient( $this->product_normalized . '_license_data' ) ) ) {
				$license = $this->check_license();
				set_transient( $this->product_normalized . '_license_data', $license, 12 * HOUR_IN_SECONDS );
				update_option( $this->product_normalized . '_license_data', $license );
			}

		}

		/**
		 *  Check the license status
		 * @return array|mixed|object|stdClass
		 */
		public function check_license() {
			$status = $this->get_license_status();
			if ( $status != "valid" ) {
				$license_data          = new stdClass();
				$license_data->license = "invalid";

				return $license_data;
			}
			$license    = trim( $this->license_key );
			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->product_name ),
				'url'        => home_url()
			);
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->store_url ), array(
				'timeout'   => 15,
				'sslverify' => false
			) );
			if ( is_wp_error( $response ) ) {
				$license_data          = new stdClass();
				$license_data->license = "valid";

			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! is_object( $license_data ) ) {
					$license_data          = new stdClass();
					$license_data->license = "valid";
				}
			}
			$license_old = get_option( $this->product_normalized . '_license_data', '' );
			if ( isset( $license_old->hide_valid ) ) {
				$license_data->hide_valid = true;
			}
			if ( ! isset( $license_data->key ) ) {
				$license_data->key = isset( $license_old->key ) ? $license_old->key : "";
			}
			if ( isset( $license_old->hide_expiration ) ) {
				$license_data->hide_expiration = true;
			}
			if ( isset( $license_old->hide_activation ) ) {
				$license_data->hide_activation = true;
			}

			return $license_data;

		}

		/**
		 * Activate the license remotely
		 */
		function activate_license() {
			// listen for our activate button to be clicked
			if ( isset( $_POST[ $this->product_normalized . '_btn_trigger' ] ) ) {
				$status = $this->get_license_status();
				// retrieve the license from the database
				$license = $_POST[ $this->product_normalized . '_license' ];
				if ( $status != "valid" ) {
					// data to send in our API request
					$api_params = array(
						'edd_action' => 'activate_license',
						'license'    => $license,
						'item_name'  => urlencode( $this->product_name ),
						'url'        => home_url()
					);
				} else {
					$api_params = array(
						'edd_action' => 'deactivate_license',
						'license'    => $license,
						'item_name'  => urlencode( $this->product_name ),
						'url'        => home_url()
					);
				}      
				// Call the custom API.
				$response = wp_remote_get( add_query_arg( $api_params, $this->store_url ) );
				// make sure the response came back okay
				if ( is_wp_error( $response ) ) {
					$license_data          = new stdClass();
					$license_data->license = ( $status != "valid" ) ? "valid" : "invalid";

				} else {
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					if ( ! is_object( $license_data ) ) {
						$license_data          = new stdClass();
						$license_data->license = ( $status != "valid" ) ? "valid" : "invalid";
					}
				}
				if ( ! isset( $license_data->key ) ) {
					$license_data->key = $license;
				}
				update_option( $this->product_normalized . '_license_data', $license_data );
				delete_transient( $this->product_normalized . '_license_data' );
				set_transient( $this->product_normalized . '_license_data', $license_data, 12 * HOUR_IN_SECONDS );

			}
		}

		/**
		 * Enable the license system
		 */
		public function enable() {
			if ( $this->product_type == 'plugin' ) {
				add_filter( 'pre_set_site_transient_update_plugins', array(
					$this,
					'pre_set_site_transient_update_plugins_filter'
				) );
				add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
				add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
			}
			if ( $this->product_type == 'theme' ) {
				add_filter( 'site_transient_update_themes', array( &$this, 'theme_update_transient' ) );
				add_filter( 'delete_site_transient_update_themes', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-update-core.php', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-themes.php', array( &$this, 'delete_theme_update_transient' ) );
				add_action( 'load-themes.php', array( &$this, 'load_themes_screen' ) );

			}

		}

		/**
		 * Load the Themes screen
		 */
		function load_themes_screen() {
			add_thickbox();
			add_action( 'admin_notices', array( &$this, 'update_nag' ) );
		}

		/**
		 * Alter the nag for themes update
		 */
		function update_nag() {
			$theme        = wp_get_theme( $this->product_slug );
			$api_response = get_transient( $this->product_key );
			if ( false === $api_response ) {
				return;
			}
			$update_url     = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->product_slug ), 'upgrade-theme_' . $this->product_slug );
			$update_onclick = ' onclick="if ( confirm(\'' . esc_js( __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update." ) ) . '\') ) {return true;}return false;"';
			if ( version_compare( $this->product_version, $api_response->new_version, '<' ) ) {
				echo '<div id="update-nag">';
				printf( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
					$theme->get( 'Name' ),
					$api_response->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $this->product_version . '_changelog',
					$theme->get( 'Name' ),
					$update_url,
					$update_onclick
				);
				echo '</div>';
				echo '<div id="' . $this->product_slug . '_' . 'changelog" style="display:none;">';
				echo wpautop( $api_response->sections['changelog'] );
				echo '</div>';
			}
		}

		/**
		 * @param mixed $value the transient data
		 *
		 * @return mixed
		 */
		function theme_update_transient( $value ) {
			$update_data = $this->check_for_update();
			if ( $update_data ) {
				$value->response[ $this->product_slug ] = $update_data;
			}

			return $value;
		}

		/**
		 * Delete the update transient
		 */
		function delete_theme_update_transient() {
			delete_transient( $this->product_key );
		}

		/**
		 * Check for updates
		 *
		 * @return array|bool Either the update data or false in case of failure
		 */
		function check_for_update() {
			$theme       = wp_get_theme( $this->product_slug );
			$update_data = get_transient( $this->product_key );
			if ( false === $update_data ) {
				$failed = false;
				if ( empty( $this->license_key ) ) {
					return false;
				}
				$api_params = array(
					'edd_action' => 'get_version',
					'version'    => $this->product_version,
					'license'    => $this->license_key,
					'name'       => $this->product_name,
					'slug'       => $this->product_slug,
					'author'     => $this->store_name,
					'url'        => home_url()
				);
				$response   = wp_remote_post( $this->store_url, array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				) );
				// make sure the response was successful
				if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
					$failed = true;
				}
				$update_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( ! is_object( $update_data ) ) {
					$failed = true;
				}
				// if the response failed, try again in 30 minutes
				if ( $failed ) {
					$data              = new stdClass;
					$data->new_version = $this->product_version;
					set_transient( $this->product_key, $data, strtotime( '+30 minutes' ) );

					return false;
				}
				// if the status is 'ok', return the update arguments
				if ( ! $failed ) {
					$update_data->sections = maybe_unserialize( $update_data->sections );
					set_transient( $this->product_key, $update_data, strtotime( '+12 hours' ) );
				}
			}
			if ( version_compare( $this->product_version, $update_data->new_version, '>=' ) ) {
				return false;
			}

			return (array) $update_data;
		}

		/**
		 * Check for Updates at the defined API endpoint and modify the update array.
		 *
		 * This function dives into the update API just when WordPress creates its update array,
		 * then adds a custom API call and injects the custom plugin data retrieved from the API.
		 * It is reassembled from parts of the native WordPress plugin update code.
		 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
		 *
		 * @uses api_request()
		 *
		 * @param array $_transient_data Update array build by WordPress.
		 *
		 * @return array Modified update array with custom plugin data.
		 */
		public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {
			if ( empty( $_transient_data ) || ! $this->do_check ) {
				$this->do_check = true;

				return $_transient_data;
			}
			$api_response = $this->api_request();
			if ( false !== $api_response && is_object( $api_response ) && isset( $api_response->new_version ) ) {
				if ( version_compare( $this->product_version, $api_response->new_version, '<' ) ) {
					$_transient_data->response[ $this->product_data["basename"] ] = $api_response;
				}
			}

			return $_transient_data;
		}

		/**
		 * Calls the API and, if successfull, returns the object delivered by the API.
		 *
		 * @uses get_bloginfo()
		 * @uses wp_remote_post()
		 * @uses is_wp_error()
		 *
		 * @param string $_action The requested action.
		 * @param array $_data Parameters for the API action.
		 *
		 * @return false||object
		 */
		private function api_request( $_action = "", $_data = "" ) {
			if ( empty( $this->license_key ) ) {
				return;
			}
			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_key,
				'name'       => urlencode( $this->product_name ),
				'slug'       => urlencode( $this->product_slug ),
				'author'     => $this->store_name,
				'url'        => home_url()
			);
			$request    = wp_remote_post( $this->store_url, array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			) );
			if ( ! is_wp_error( $request ) ):
				$request = json_decode( wp_remote_retrieve_body( $request ) );
				if ( $request && isset( $request->sections ) ) {
					$request->sections = maybe_unserialize( $request->sections );
				}

				return $request;
			else:
				return false;
			endif;
		}

		/**
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_request()
		 *
		 * @param mixed $_data
		 * @param string $_action
		 * @param object $_args
		 *
		 * @return object $_data
		 */
		public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
			if ( ( $_action != 'plugin_information' ) || ! isset( $_args->slug ) || ( $_args->slug != $this->product_slug ) ) {
				return $_data;
			}
			$api_response = $this->api_request();
			if ( false !== $api_response ) {
				$_data = $api_response;
			}

			return $_data;
		}

		/**
		 * Disable SSL verification in order to prevent download update failures
		 *
		 * @param array $args
		 * @param string $url
		 *
		 * @return object $array
		 */
		function http_request_args( $args, $url ) {
			// If it is an https request and we are performing a package download, disable ssl verification
			if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
				$args['sslverify'] = false;
			}

			return $args;
		}

	}
endif;