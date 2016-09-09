<?php
/**
 * Changelog
 */

$parallax_one = wp_get_theme( 'parallax-one' );

?>
<div class="parallax-one-tab-pane" id="changelog">

	<div class="prallax-one-tab-pane-center">
	
		<h1>Parallax One <?php if( !empty($parallax_one['Version']) ): ?> <sup id="parallax-one-theme-version"><?php echo esc_attr( $parallax_one['Version'] ); ?> </sup><?php endif; ?></h1>

	</div>

	<?php
	WP_Filesystem();
	global $wp_filesystem;
	$parallax_one_changelog = $wp_filesystem->get_contents( get_template_directory().'/CHANGELOG.md' );
	$parallax_one_changelog_lines = explode(PHP_EOL, $parallax_one_changelog);
	foreach($parallax_one_changelog_lines as $parallax_one_changelog_line){
		if(substr( $parallax_one_changelog_line, 0, 3 ) === "###"){
			echo '<hr /><h1>'.substr($parallax_one_changelog_line,3).'</h1>';
		} else {
			echo $parallax_one_changelog_line.'<br/>';
		}
	}

?>
	
</div>
