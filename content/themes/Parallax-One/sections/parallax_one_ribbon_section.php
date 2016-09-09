<!-- =========================
 SECTION: RIBBON
============================== -->
<?php
global $wp_customize;
$ribbon_background = get_theme_mod('paralax_one_ribbon_background', parallax_get_file('/images/background-images/parallax-img/parallax-img1.jpg'));
$parallax_one_ribbon_title = get_theme_mod('parallax_one_ribbon_title',esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.','parallax-one'));
$parallax_one_button_text = get_theme_mod('parallax_one_button_text',esc_html__('GET STARTED','parallax-one'));
$parallax_one_button_link = get_theme_mod('parallax_one_button_link','#');
if( !empty($parallax_one_button_link) && strpos($parallax_one_button_link, '#') === 0) {
	$parallax_one_go_to = 'onclick="return false;" data-anchor="'.esc_attr($parallax_one_button_link).'"';
} else {
	$parallax_one_go_to = 'onclick="parent.location=\''.esc_url($parallax_one_button_link).'\'" data-anchor=""';
}

parallax_hook_ribbon_before();
if(!empty($parallax_one_ribbon_title) || !empty($parallax_one_button_text)) {

	echo '<section class="call-to-action ribbon-wrap" id="ribbon" '. ( !empty($ribbon_background) ? 'style="background-image:url(' . esc_url( $ribbon_background ) . ');"' : '' ) .' role="region" aria-label="' . esc_html__( 'Ribbon', 'parallax-one' ) . '">';
	parallax_hook_ribbon_top();

} else {
	if ( is_customize_preview() ) {
		echo '<section class="call-to-action ribbon-wrap paralax_one_only_customizer" id="ribbon" ' . ( ! empty( $ribbon_background ) ? 'style="background-image:url(' . esc_url( $ribbon_background ) . ');"' : '' ) . ' role="region" aria-label="' . esc_html__( 'Ribbon', 'parallax-one' ) . '">';
		parallax_hook_ribbon_top();
	}
}

if(!empty($parallax_one_ribbon_title) || !empty($parallax_one_button_text) || is_customize_preview()) { ?>
	<div class="section-overlay-layer">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					<?php
					if( !empty($parallax_one_ribbon_title) ){
						echo '<h2 class="white-text strong">'.wp_kses_post($parallax_one_ribbon_title).'</h2>';
					} elseif ( is_customize_preview() ) {
						echo '<h2 class="white-text strong paralax_one_only_customizer"></h2>';
					}


					if(!empty($parallax_one_button_text)){ ?>
						<button <?php if(!empty($parallax_one_go_to)){ echo $parallax_one_go_to; } ?> class="btn btn-primary standard-button inpage-scroll inpage_scroll_btn">
							<span class="screen-reader-text"><?php echo esc_html__('Header button label:','parallax-one').strip_tags(trim($parallax_one_button_text)); ?></span>
							<?php echo wp_kses_post( $parallax_one_button_text ); ?>
						</button>
						<?php
					} elseif( is_customize_preview() ){ ?>
						<button class="btn btn-primary standard-button inpage-scroll inpage_scroll_btn paralax_one_only_customizer"  <?php if(!empty($parallax_one_go_to)){ echo $parallax_one_go_to; } ?>></button>
						<?php
					} ?>

				</div>
			</div>
		</div>
	</div>
	<?php parallax_hook_ribbon_bottom(); ?>
	</section>
	<?php parallax_hook_ribbon_after(); ?>
	<?php
} ?>