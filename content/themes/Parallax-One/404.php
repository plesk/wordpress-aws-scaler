<?php
/**
 *
 * The template for displaying 404 pages (not found).
 *
 * @package parallax-one
 */

	get_header();
?>

	</div>
	<!-- /END COLOR OVER IMAGE -->
	<?php parallax_hook_header_bottom(); ?>
</header>
<!-- /END HOME / HEADER  -->
<?php parallax_hook_header_after(); ?>
<div class="content-wrap">
	<div class="container">
		<?php parallax_hook_404_content() ?>
	</div>
</div><!-- .content-wrap -->

<?php get_footer(); ?>
