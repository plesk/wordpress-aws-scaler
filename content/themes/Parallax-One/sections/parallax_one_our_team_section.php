<!-- =========================
 SECTION: TEAM
============================== -->
<?php
global $wp_customize;
$parallax_one_our_team_title = get_theme_mod('parallax_one_our_team_title',esc_html__('Our Team','parallax-one'));
$parallax_one_our_team_subtitle = get_theme_mod('parallax_one_our_team_subtitle',esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'));
$parallax_one_team_content = get_theme_mod('parallax_one_team_content', json_encode( array(
	array('image_url' => parallax_get_file('/images/team/1.jpg'),'title' => esc_html__('Albert Jacobs','parallax-one'),'subtitle' => esc_html__('Founder & CEO','parallax-one'), 'id' => 'parallax_one_56fe9796baca4'),
	array('image_url' => parallax_get_file('/images/team/2.jpg'),'title' => esc_html__('Tonya Garcia','parallax-one'),'subtitle' => esc_html__('Account Manager','parallax-one'), 'id' => 'parallax_one_56fe9798baca5'),
	array('image_url' => parallax_get_file('/images/team/3.jpg'),'title' => esc_html__('Linda Guthrie','parallax-one'),'subtitle' => esc_html__('Business Development','parallax-one'), 'id' => 'parallax_one_56fe9799baca6')
) ) );

if(!empty($parallax_one_our_team_title) || !empty($parallax_one_our_team_subtitle) || !parallax_one_general_repeater_is_empty($parallax_one_team_content) ){
	parallax_hook_team_before(); ?>
	<section class="team" id="team" role="region" aria-label="<?php esc_html_e('Team','parallax-one') ?>">
		<?php parallax_hook_team_top(); ?>
		<div class="section-overlay-layer">
			<div class="container">
				<?php
				if( !empty($parallax_one_our_team_title) || !empty($parallax_one_our_team_subtitle)){ ?>
					<div class="section-header">
						<?php
						if( !empty($parallax_one_our_team_title) ){ ?>
							<h2 class="dark-text"><?php echo esc_attr( $parallax_one_our_team_title ); ?></h2><div class="colored-line"></div>
						<?php
						} elseif ( isset( $wp_customize ) ) { ?>
							<h2 class="dark-text paralax_one_only_customizer"></h2><div class="colored-line paralax_one_only_customizer"></div>
						<?php
						}
								
						if( !empty($parallax_one_our_team_subtitle) ){ ?>
							<div class="sub-heading"><?php echo esc_attr($parallax_one_our_team_subtitle); ?></div>
						<?php		
						} elseif ( isset( $wp_customize ) ) { ?>
							<div class="sub-heading paralax_one_only_customizer"></div>
						<?php
						} ?>
					</div>
				<?php
				}

				if( !empty( $parallax_one_team_content ) ){ ?>
					<div class="row team-member-wrap">
						<?php
						$parallax_one_team_decoded = json_decode($parallax_one_team_content);
						foreach($parallax_one_team_decoded as $parallax_one_team_member){
							

							if( !empty( $parallax_one_team_member->id ) ){
								$id = $parallax_one_team_member->id;
							}

							if( !empty( $parallax_one_team_member->title ) ){
								if( function_exists('pll__') ){
									$title = pll__( $parallax_one_team_member->title );
								} else {
									$title = apply_filters( 'wpml_translate_single_string', $parallax_one_team_member->title, 'Parallax One -> Team section', 'Team box title '.$id );
								}
							}

							if( !empty( $parallax_one_team_member->subtitle ) ){
								if( function_exists('pll__') ){
									$subtitle = pll__($parallax_one_team_member->subtitle);
								} else {
									$subtitle = apply_filters( 'wpml_translate_single_string', $parallax_one_team_member->subtitle, 'Parallax One -> Team section', 'Team box subtitle '.$id );
								}
							}

							if( !empty( $parallax_one_team_member->image_url ) ){
								if( function_exists('pll__') ){
									$image = pll__( $parallax_one_team_member->image_url );
								} else{
									$image = apply_filters( 'wpml_translate_single_string', $parallax_one_team_member->image_url, 'Parallax One -> Team section', 'Team box image '.$id );
								}
							}

							if( !empty($image) ||  !empty($title) || !empty($subtitle) ){?>
								<div class="col-md-3 team-member-box">
									<div class="team-member border-bottom-hover">
										<div class="member-pic">
											<?php
											if( !empty( $image ) ){ ?>
												<img src="<?php echo parallax_one_make_protocol_relative_url( esc_url( $image ) ); ?>" <?php echo ( !empty( $title ) ? 'alt="'.$title.'"' : esc_html__('Avatar','parallax-one') ); ?>>
											<?php
											} else {
												$default_url = parallax_get_file('/images/team/default.png'); ?>
												<img src="<?php echo parallax_one_make_protocol_relative_url($default_url); ?>" alt="<?php esc_html_e('Avatar','parallax-one'); ?>">
											<?php
											} ?>
										</div>
										
										<?php 
										if( !empty( $title ) || !empty( $subtitle ) ){ ?>
											<div class="member-details">
												<div class="member-details-inner">
													<?php
													if( !empty( $title ) ){ ?>
														<h5 class="colored-text"> <?php echo esc_attr( $title ); ?></h5>
													<?php
													}

													if( !empty($parallax_one_team_member->subtitle) ){ ?>
														<div class="small-text"><?php echo esc_attr( $subtitle ); ?></div>
													<?php
													}

													if( !empty( $parallax_one_team_member->social_repeater) ){ 
														$socials = html_entity_decode( $parallax_one_team_member->social_repeater );
														$icons_decoded = json_decode( $socials, true ); ?>
												 		<ul class="social-icons">
												 			<?php
													 		foreach ($icons_decoded as $value) {
													 			$s_id = $value['id'];

													 			if( function_exists('pll__') ){
													 				$s_icon = pll__( $value['icon'] );
													 				$s_link = pll__( $value['link'] );
													 			} else {
														 			$s_icon = apply_filters( 'wpml_translate_single_string', $value['icon'], 'Parallax One -> Team section', 'Social icon '.$s_id);
																	$s_link = apply_filters( 'wpml_translate_single_string', $value['link'], 'Parallax One -> Team section', 'Social link '.$s_id);
													 			}
													 		
													 			if( !empty( $s_icon ) ){ ?>
													 				<li>
													 					<?php
													 					if( !empty( $s_link ) ){ ?>
												 							<a target="_blank" href="<?php echo esc_url( $s_link ); ?>">
												 								<span class="<?php echo esc_attr( $s_icon ); ?>"></span>
												 							</a>
													 					<?php
													 					} else { ?>
													 						<span class="<?php echo esc_attr( $s_icon ); ?>"></span>
													 					<?php
													 					} ?>
													 				</li>
													 			<?php	
													 			}
													 		} ?>
												 		</ul>
												 	<?php
												 	} ?>
												</div><!-- .member-details-inner -->
											</div><!-- .member-details -->
										<?php 
										} ?>
									</div><!-- .team-member -->
								</div><!-- .team-member -->
							<?php
							}
						} ?>
					</div>
				<?php	
				}?>
			</div>
		</div><!-- container  -->
		<?php parallax_hook_team_bottom(); ?>
	</section><!-- #section9 -->
	<?php parallax_hook_team_after();
} else {
	if( isset( $wp_customize ) ) {
		parallax_hook_team_before(); ?>
		<section class="team paralax_one_only_customizer" id="team" role="region" aria-label="<?php esc_html_e('Team','parallax-one') ?>">
			<?php parallax_hook_team_top(); ?>
			<div class="section-overlay-layer">
				<div class="container">
					<div class="section-header">
						<h2 class="dark-text paralax_one_only_customizer"></h2><div class="colored-line paralax_one_only_customizer"></div>
						<div class="sub-heading paralax_one_only_customizer"></div>
					</div>
				</div>
			</div>
			<?php parallax_hook_team_bottom(); ?>
		</section>
		<?php parallax_hook_team_after(); ?>
	<?php
	}
} ?>