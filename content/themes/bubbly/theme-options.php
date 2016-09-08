<?php

/* create theme options page */
function ct_bubbly_register_theme_page(){
    add_theme_page( 'Bubbly Dashboard', 'Bubbly Dashboard', 'edit_theme_options', 'bubbly-options', 'ct_bubbly_options_content');
}
add_action( 'admin_menu', 'ct_bubbly_register_theme_page' );

/* callback used to add content to options page */
function ct_bubbly_options_content(){ ?>

    <div id="bubbly-dashboard-wrap" class="wrap">
        <h2><?php _e('bubbly Dashboard', 'bubbly'); ?></h2>

        <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'dashboard'; ?>

        <h2 class="nav-tab-wrapper">
            <?php _e('Welcome To WPDean Themes', 'bubbly'); ?>
         </h2>
        
            <div class="content-customization content">
                <h3><?php _e('Theme Options', 'bubbly'); ?></h3>
                <p><?php _e('Click the "Customize" link in your menu, or use the button below to get started customizing bubbly', 'bubbly'); ?>.</p>
                <p>
                    <a class="button-primary" href="<?php echo admin_url('customize.php'); ?>"><?php _e('Use Customizer', 'bubbly') ?></a>
                </p>
            </div>
	        <div class="content-support content">
		        <h3><?php _e('Free Support', 'bubbly'); ?></h3>
				<p><?php _e("Our Free Support is available to all our users at Twitter. Just Tweet your question to us.", "bubbly"); ?>.</p>
		        <p>
			        <a target="_blank" class="button-primary" href="https://twitter.com/TheWPDean/"><?php _e('@TheWPDean', 'bubbly'); ?></a>
		        </p>
	        </div>
	       
	        <div class="content content-resources">
		        <h3><?php _e('WordPress Resources', 'bubbly'); ?></h3>
		        <p><?php _e("Save time and money searching for WordPress products by following our recommendations", "bubbly"); ?>.</p>
		        <p>
			        <a target="_blank" class="button-primary" href="http://wpdean.com/wordpress-resources/"><?php _e('View Resources', 'bubbly'); ?></a>
		        </p>
	        </div>
			<div class="content-design content">
		        <h3><?php _e('Custom Design', 'bubbly'); ?></h3>
		        <p><?php _e("Want a custom design for your Theme? Get in touch with us for a custom Quote", "bubbly"); ?>.</p>
		        <p>
			        <a target="_blank" class="button-primary" href="http://wpdean.com/contact/"><?php _e('Contact Us', 'bubbly'); ?></a>
		        </p>
	        </div>
			 <div class="content-premium-upgrades content">
		        <h3><?php _e('Rate this theme', 'bubbly'); ?></h3>
		        <p><?php _e('If you like this theme, I will appreciate any of the following:', 'bubbly');?></p>
				<p>
				<a target="_blank" class="button-primary" href="https://wordpress.org/support/view/theme-reviews/bubbly?filter=5"><?php _e('Rate this theme', 'bubbly'); ?></a>
				
			        <a target="_blank" class="button-primary" href="https://twitter.com/TheWPDean/"><?php _e('Follow on Twitter', 'bubbly'); ?></a>
					<a target="_blank" class="button-primary" href="https://www.facebook.com/TheWPDean"><?php _e('Like on Facebook', 'bubbly'); ?></a>
		        </p>
	        </div>
       
    </div>
<?php } 