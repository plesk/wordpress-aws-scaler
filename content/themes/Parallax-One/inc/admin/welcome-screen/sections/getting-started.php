<?php
/**
 * Getting started template
 */

$customizer_url = admin_url() . 'customize.php' ;
?>

<div id="getting_started" class="parallax-one-tab-pane active">

	<div class="prallax-one-tab-pane-center">
		<?php 
			$parallax_one = wp_get_theme();
			$parallax_one_version = $parallax_one->get('Version');
			$parallax_one_name = $parallax_one->get('Name');
		?>
		<h1 class="parallax-one-welcome-title">
			<?php printf( __( 'Welcome to %1$s!', 'parallax-one' ), $parallax_one_name ); ?>
			<?php if( !empty($parallax_one_version) ): ?> 
				<sup id="parallax-one-theme-version">
					<?php echo esc_attr( $parallax_one_version ); ?> 
				</sup>
			<?php endif; ?>
		</h1>

		<p><?php esc_html_e( 'Our most elegant and professional one-page theme, which turns your scrolling into a smooth and pleasant experience.','parallax-one'); ?></p>
		<p><?php esc_html_e( 'We want to make sure you have the best experience using Parallax One and that is why we gathered here all the necessary informations for you. We hope you will enjoy using Parallax One, as much as we enjoy creating great products.', 'parallax-one' ); ?>

	</div>

	<hr />

	<div class="prallax-one-tab-pane-center">

		<h1><?php esc_html_e( 'Getting started', 'parallax-one' ); ?></h1>

		<h4><?php esc_html_e( 'Customize everything in a single place.' ,'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Using the WordPress Customizer you can easily customize every aspect of the theme.', 'parallax-one' ); ?></p>
		<p><a href="<?php echo esc_url( $customizer_url ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Customizer', 'parallax-one' ); ?></a></p>

	</div>

	<hr />

	<div class="prallax-one-tab-pane-center">

		<h1><?php esc_html_e( 'FAQ', 'parallax-one' ); ?></h1>

	</div>

	<div class="prallax-one-tab-pane-half prallax-one-tab-pane-first-half">

		<h4><?php esc_html_e( 'Create a child theme', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'If you want to make changes to the theme\'s files, those changes are likely to be overwritten when you next update the theme. In order to prevent that from happening, you need to create a child theme. For this, please follow the documentation below.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/14-how-to-create-a-child-theme/" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'How to Internationalize Your Website', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Although English is the most used language on the internet, you should consider all your web users as well. Find out what it takes to make your website ready for foreign markets from this document.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/80-how-to-translate-zerif" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Change dimensions for footer social icons', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'In the below documentation you will find an easy way to change the default dimensions for you social icons.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/71-parallax-one-change-dimensions-for-footer-icons" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Change customizer in a child theme', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'If you want to add or remove customizer controls, check out our documentation to find out how.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/74-how-to-override-controls" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Build a landing page with a drag-and-drop content builder', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'In the below documentation you will find an easy way to build a great looking landing page using a drag-and-drop content builder plugin.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/219-how-to-build-a-landing-page-with-a-drag-and-drop-content-builder" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>


	</div>

	<div class="prallax-one-tab-pane-half">

		<h4><?php esc_html_e( 'Speed up your site', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'If you find yourself in the situation where everything on your site is running very slow, you might consider having a look at the below documentation where you will find the most common issues causing this and possible solutions for each of the issues.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/63-speed-up-your-wordpress-site/" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Link Menu to sections', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Linking the frontpage sections with the top menu is very simple, all you need to do is assign section anchors to the menu. In the below documentation you will find a nice tutorial about this.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/59-how-to-link-menu-to-sections-in-parallax-one" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Change anchors', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'To better suit your site\'s needs, you can change each section\'s anchor to what you want. The entire process is described below.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/72-parallax-one-how-to-change-section-anchor" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

		<hr />

		<h4><?php esc_html_e( 'Slider in big title section', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'If you are in the position where you want to change the default appearance of the big title section, you may want to replace it with a nice looking slider. This can be accomplished by following the documention below.', 'parallax-one' ); ?></p>
		<p><a href="http://docs.themeisle.com/article/70-parallax-one-replacing-big-title-section-with-an-image-slider" class="button"><?php esc_html_e( 'View how to do this', 'parallax-one' ); ?></a></p>

	</div>

	<div class="parallax-one-clear"></div>

	<hr />

	<div class="prallax-one-tab-pane-center">

		<h1><?php esc_html_e( 'View full documentation', 'parallax-one' ); ?></h1>
		<p><?php esc_html_e( 'Need more details? Please check our full documentation for detailed information on how to use Parallax One.', 'parallax-one' ); ?></p>
		<p><a href="http://themeisle.com/documentation-parallax-one/" class="button button-primary"><?php esc_html_e( 'Read full documentation', 'parallax-one' ); ?></a></p>

	</div>

	<hr />

	<div class="prallax-one-tab-pane-center">
		<h1><?php esc_html_e( 'Recommended plugins', 'parallax-one' ); ?></h1>
	</div>

	<div class="prallax-one-tab-pane-half prallax-one-tab-pane-first-half">

		<!-- Intergeo Maps -->
		<h4><?php esc_html_e( 'Intergeo Maps - Google Maps Plugin', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'The Intergeo Google Maps plugin is a simple, easy and in the same time quite powerful tool for handling Google Maps in your website. The plugin allows users to create new maps by using powerful UI builder.', 'parallax-one' ); ?></p>

		<?php if ( is_plugin_active( 'intergeo-maps/index.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=intergeo-maps' ), 'install-plugin_intergeo-maps' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Install Intergeo Maps', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>

		<hr />


		<!-- Adblock Notify -->
		<h4><?php esc_html_e( 'Adblock Notify by b*web', 'parallax-one' ); ?></h4>

		<?php if ( is_plugin_active( 'adblock-notify-by-bweb/adblock-notify.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=adblock-notify-by-bweb' ), 'install-plugin_adblock-notify-by-bweb' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Install Adblock Notify', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>


		<hr />


		<!-- Page Builder by SiteOrigin -->
		<h4><?php esc_html_e( 'Page Builder by SiteOrigin', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Build responsive page layouts using the widgets you know and love using this simple drag and drop page builder.', 'parallax-one' ); ?></p>	

		<?php if ( is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=siteorigin-panels' ), 'install-plugin_siteorigin-panels' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Install Page Builder by SiteOrigin', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>

	</div>



	<div class="prallax-one-tab-pane-half">

		<!--Pirate Forms -->
		<h4><?php esc_html_e( 'Pirate Forms', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Makes your contact page more engaging by creating a good-looking contact form on your website. The interaction with your visitors was never easier.', 'parallax-one' ); ?></p>

		<?php if ( is_plugin_active( 'pirate-forms/pirate-forms.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=pirate-forms' ), 'install-plugin_pirate-forms' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Install Pirate Forms', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>

		<hr />


		<!-- FEEDZY RSS Feeds -->
		<h4><?php esc_html_e( 'FEEDZY RSS Feeds', 'parallax-one' ); ?></h4>

		<?php if ( is_plugin_active( 'feedzy-rss-feeds/feedzy-rss-feed.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=feedzy-rss-feeds' ), 'install-plugin_feedzy-rss-feeds' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Install FEEDZY RSS Feeds', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>

		<hr />


		<!-- Easy Content Types -->
		<h4><?php esc_html_e( 'Easy Content Types', 'parallax-one' ); ?></h4>
		<p><?php esc_html_e( 'Custom Post Types, Taxonomies and Metaboxes in Minutes', 'parallax-one' ); ?></p>

		<?php if ( is_plugin_active( 'easy-content-types/easy-content-types.php' ) ) { ?>

				<p><span class="parallax-one-w-activated button"><?php esc_html_e( 'Already activated', 'parallax-one' ); ?></span></p>

			<?php
		}
		else { ?>

				<p><a href="http://themeisle.com/plugins/easy-content-types/" class="button button-primary" target="_blank"><?php esc_html_e( 'Download Easy Content Types', 'parallax-one' ); ?></a></p>

			<?php
		}

		?>
	</div>

	<div class="parallax-one-clear"></div>

</div>
