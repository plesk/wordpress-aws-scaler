<!-- =========================
 SECTION: SERVICES
============================== -->
<?php
global $wp_customize;
$parallax_one_our_services_title = get_theme_mod( 'parallax_one_our_services_title', esc_html__( 'Our Services', 'parallax-one' ) );
$parallax_one_our_services_subtitle = get_theme_mod( 'parallax_one_our_services_subtitle', esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'parallax-one' ) );
$parallax_one_services = get_theme_mod('parallax_one_services_content', json_encode( array(
	array('choice'=>'parallax_icon','icon_value' => 'icon-basic-webpage-multiple','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d93f3013'),
	array('choice'=>'parallax_icon','icon_value' => 'icon-ecommerce-graph3','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d94f3014'),
	array('choice'=>'parallax_icon','icon_value' => 'icon-basic-geolocalize-05','title' => esc_html__('Lorem Ipsum','parallax-one'),'text' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nullam vel eros sit amet arcu vestibulum accumsan in in leo.','parallax-one'), 'id' => 'parallax_one_56fd4d95f3015')
) )	);
$parallax_one_services_pinterest = get_theme_mod('paralax_one_services_pinterest_style','5');

if(!empty($parallax_one_our_services_title) || !empty($parallax_one_our_services_subtitle) || !parallax_one_general_repeater_is_empty($parallax_one_services)){ 
	parallax_hook_services_before(); ?>
	
	<section class="services" id="services" role="region" aria-label="<?php esc_html_e('Services','parallax-one') ?>">
		<?php 
		parallax_hook_services_top(); ?>
		<div class="section-overlay-layer">
			<div class="container">

				<div class="section-header">
					<?php
					if( !empty( $parallax_one_our_services_title ) ){ ?>
						<h2 class="dark-text"><?php echo esc_attr($parallax_one_our_services_title); ?></h2><div class="colored-line"></div>
					<?php
					} elseif ( isset( $wp_customize ) ) { ?>
						<h2 class="dark-text paralax_one_only_customizer"></h2><div class="colored-line paralax_one_only_customizer"></div>
					<?php
					} 
					
					if( !empty( $parallax_one_our_services_subtitle ) ){ ?>
						<div class="sub-heading"><?php echo esc_attr($parallax_one_our_services_subtitle); ?></div>
					<?php
					} elseif ( isset( $wp_customize ) ) { ?>
						<div class="sub-heading paralax_one_only_customizer"></div>
					<?php
					}?>
				</div>

				<?php
				if( !empty( $parallax_one_services ) ){
					$parallax_one_services_decoded = json_decode( $parallax_one_services );?>
					<div id="our_services_wrap" class="services-wrap <?php if( !empty($parallax_one_services_pinterest) ) echo 'our_services_wrap_piterest'; ?>">
						<?php
						foreach( $parallax_one_services_decoded as $parallax_one_service_box ){

							$id = $parallax_one_service_box->id;
							$choice = $parallax_one_service_box->choice;
							if( $choice == 'parallax_icon' ){
								if( function_exists( 'pll__' ) ){
									$icon = pll__( $parallax_one_service_box->icon_value );
								} else {
									$icon = apply_filters( 'wpml_translate_single_string', $parallax_one_service_box->icon_value , 'Parallax One -> Services section', 'Service box icon '.$id );
								}
							}
							if( $choice == 'parallax_image' ){
								if( function_exists( 'pll__' ) ){
									$image = pll__( $parallax_one_service_box->image_url );
								} else {
									$image = apply_filters( 'wpml_translate_single_string', $parallax_one_service_box->image_url , 'Parallax One -> Services section', 'Service box image '.$id );
								}
							}

							if( function_exists( 'pll__' ) ){
								$title = pll__( $parallax_one_service_box->title );
								$text = pll__( $parallax_one_service_box->text );
								if(isset($parallax_one_service_box->link)){
									$link = pll__( $parallax_one_service_box->link );
								}
							} else {
								$title = apply_filters( 'wpml_translate_single_string', $parallax_one_service_box->title , 'Parallax One -> Services section', 'Service box title '.$parallax_one_service_box->id );
								$text = apply_filters( 'wpml_translate_single_string', $parallax_one_service_box->text , 'Parallax One -> Services section', 'Service box text '.$parallax_one_service_box->id );
								if(isset($parallax_one_service_box->link)){
									$link = apply_filters( 'wpml_translate_single_string', $parallax_one_service_box->link , 'Parallax One -> Services section', 'Service box link '.$parallax_one_service_box->id );
								}
							}

							if( ( !empty( $icon  ) && $icon != 'No Icon' && $choice == 'parallax_icon' )  || ( !empty( $image )  && $choice == 'parallax_image' ) || !empty( $title ) || !empty( $text ) ){ ?>
								<div class="service-box">
									<?php
									parallax_hook_services_entry_before(); ?>
									<div class="single-service border-bottom-hover">
										<?php
										parallax_hook_services_entry_top();
										if( !empty( $choice ) && $choice !== 'parallax_none' ){

											if( $choice == 'parallax_icon' ){
												if( !empty( $icon ) ) {
													if( !empty( $link ) ){ ?>
														<div class="service-icon colored-text">
															<a href="<?php echo esc_url( $link ); ?>">
																<span class="<?php echo esc_attr( $icon ); ?>"></span>
															</a>
														</div>
													<?php
													} else {?>
														<div class="service-icon colored-text">
															<span class="<?php echo esc_attr( $icon ); ?>"></span>
														</div>
													<?php
													}
												}
											}

											if( $choice == 'parallax_image' ){
												if( !empty( $image ) ){
													if( !empty( $link ) ){ ?>
														<a href="<?php echo parallax_one_make_protocol_relative_url( esc_url( $link ) ); ?>">
															<img src="<?php echo esc_url( $image ); ?>" <?php echo ( !empty( $title ) ? 'alt="'. $title .'"' : ''); ?> />
														</a>
													<?php
													} else { ?>
														<img src="<?php echo esc_url( $image ); ?>" <?php echo ( !empty( $title ) ? 'alt="'. $title .'"' : ''); ?> />
													<?php
													} 
												}
											}
										}
											
										if( !empty( $title ) ){
											if( !empty( $link ) ){ ?>
												<h3 class="colored-text">
													<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_attr( $title ); ?></a>
												</h3>
											<?php
											} else { ?>
												<h3 class="colored-text"><?php echo esc_attr( $title ); ?></h3>
											<?php
											}
										}
											
										if( !empty( $text ) ){ ?>
											<p><?php echo html_entity_decode( $text ); ?></p>
										<?php
										} 
										parallax_hook_services_entry_bottom(); ?>
									</div>
									<?php
									parallax_hook_services_entry_after(); ?>
								</div>
							<?php
							}
						} ?>
					</div>
				<?php
				} ?>
			</div>
		</div>
		<?php parallax_hook_services_bottom(); ?>
	</section>
	<?php parallax_hook_services_after(); ?>
<?php
} else {
	if( isset( $wp_customize ) ) { 
		parallax_hook_services_before(); ?>
		<section class="services paralax_one_only_customizer" id="services" role="region" aria-label="<?php esc_html_e('Services','parallax-one') ?>">
			<?php parallax_hook_services_top(); ?>
			<div class="section-overlay-layer">
				<div class="container">
					<div class="section-header">
						<h2 class="dark-text paralax_one_only_customizer"></h2><div class="colored-line paralax_one_only_customizer"></div>
						<div class="sub-heading paralax_one_only_customizer"></div>
					</div>
				</div>
			</div>
			<?php parallax_hook_services_bottom(); ?>
		</section>
		<?php parallax_hook_services_after();
	}
} ?>
