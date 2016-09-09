<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package parallax-one
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<?php parallax_hook_sidebar_before(); ?>
<div itemscope itemtype="http://schema.org/WPSideBar" role="complementary" aria-label="<?php esc_html_e('Main sidebar','parallax-one')?>" id="sidebar-secondary" class="col-md-4 widget-area">
	<?php parallax_hook_sidebar_top(); ?>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php parallax_hook_sidebar_bottom(); ?>
</div><!-- #sidebar-secondary -->
<?php parallax_hook_sidebar_after(); ?>
