<?php get_header(); ?>
	<section id="content" class="first clearfix">
		<div class="cat-container">
			<div class="cat-head mbottom">
				<h1 class="archive-title"><?php _e("Latest Posts Under:", "bubbly"); ?> <?php single_cat_title(); ?></h1>
                <?php echo category_description(); ?>
			</div>
		    	<?php get_template_part( 'loop', 'category' ); ?>
		</div>
	</section>

<?php get_footer(); ?>