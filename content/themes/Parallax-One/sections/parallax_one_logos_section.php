<!-- =========================
 SECTION: CLIENTS LOGOs
============================== -->
<?php
$parallax_one_logos = get_theme_mod('parallax_one_logos_content', json_encode( array(	
	array("image_url" => parallax_get_file('/images/companies/1.png') ,"link" => "#", "id" => "parallax_one_56d7ea7f40f56" ),
	array("image_url" => parallax_get_file('/images/companies/2.png') ,"link" => "#", "id" => "parallax_one_56d7f2cb8a158" ),
	array("image_url" => parallax_get_file('/images/companies/3.png') ,"link" => "#", "id" => "parallax_one_56d7f2cc8a159" ),
	array("image_url" => parallax_get_file('/images/companies/4.png') ,"link" => "#", "id" => "parallax_one_56d7f2ce8a15a" ),
	array("image_url" => parallax_get_file('/images/companies/5.png') ,"link" => "#", "id" => "parallax_one_56d7f2cf8a15b" )
) ) );

if( !empty( $parallax_one_logos ) ){
	$parallax_one_logos_decoded = json_decode( $parallax_one_logos );
	parallax_hook_logos_before(); ?>

	<div class="clients white-bg" id="clients" role="region" aria-label="<?php esc_html_e(' Affiliates Logos', 'parallax-one' ); ?>">
		<?php
		parallax_hook_logos_top(); ?>
		<div class="container">
			<ul class="client-logos">
				<?php
				foreach( $parallax_one_logos_decoded as $parallax_one_logo ){

					$id = $parallax_one_logo->id;
					if( function_exists( 'pll__' ) ){
						if( !empty( $parallax_one_logo->link ) ){
							$link = pll__( $parallax_one_logo->link );
						}

						if( !empty( $parallax_one_logo->image_url ) ){
							$image = pll__( $parallax_one_logo->image_url ); 
						}
					} else {
						if( !empty( $parallax_one_logo->link ) ){
							$link = apply_filters( 'wpml_translate_single_string', $parallax_one_logo->link, 'Parallax One -> Logos section', 'Logo link '.$parallax_one_logo->id );
						}

						if( !empty( $parallax_one_logo->image_url ) ){
							$image = apply_filters( 'wpml_translate_single_string', $parallax_one_logo->image_url, 'Parallax One -> Logos section', 'Logo image '.$parallax_one_logo->id );
						}
					}

					if( !empty( $image ) ){ ?>
						<li>
							<?php
							if( !empty( $link ) ){ ?>
									<a href="<?php echo esc_url( $link ); ?>" title="">
										<img src="<?php echo parallax_one_make_protocol_relative_url( $image ); ?>" alt="<?php esc_html_e( 'Logo', 'parallax-one' ); ?>">
									</a>
							<?php
							} else { ?>
								<img src="<?php echo parallax_one_make_protocol_relative_url( $image ); ?>" alt="<?php esc_html_e('Logo','parallax-one'); ?>">
							<?php
							} ?>
						</li>
					<?php
					}
				} ?>
			</ul>
		</div>
		<?php
		parallax_hook_logos_bottom();?>
	</div>
	<?php
	parallax_hook_logos_after();
} ?>
