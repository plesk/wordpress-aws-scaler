<?php /* Footer Template */ ?>
</div> <!-- end inner-content -->
    </div> <!-- end content -->
        <div class="clr"></div>
			<footer id="main-footer">
				
				<div id="bottom-footer">
				    <div class="layout-wrap container">
                       <div id="footer-logo" class="five-col">
                         <p><?php echo get_theme_mod( 'bubbly_footer_top' ); ?></p> 
                       </div>
					 <!-- Footer Menu -->  
     	            <?php if ( has_nav_menu( 'footer-menu' ) ) :
         			wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'menu_id' =>'footer-nav-links', 'menu_class'=>'footer-menu seven-col last', 'depth' => '1' ) );
			        endif; ?>
					<div id="footer-social-icons" class="seven-col last">
					
                        <?php if ( get_theme_mod( 'facebooklink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('facebooklink') ); ?>" target="_blank" class="facebook"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'twitterlink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('twitterlink') ); ?>" target="_blank" class="twitter"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'googlelink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('googlelink') ); ?>" target="_blank" class="googleplus"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'youtubelink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('youtubelink') ); ?>" target="_blank" class="youtube"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'pinterestlink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('pinterestlink') ); ?>" target="_blank" class="pinterest"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'stumblelink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('stumblelink') ); ?>" target="_blank" class="stumbleupon"></a><?php endif; ?>
                        <?php if ( get_theme_mod( 'rsslink' ) ) : ?>
						    <a href="<?php echo esc_url( get_theme_mod('rsslink') ); ?>" target="_blank" class="rss"></a><?php endif; ?>
  				    </div>
					<div id="footer-copyright">
                       <p class="vsmall"> <span class="alignleft"><?php echo get_theme_mod( 'bubbly_footer_cr_left' ); ?></span>
                     		   <span class="alignright "> <?php if(is_home() AND !is_paged()) { ?><a rel="nofollow" href="http://wpdean.com/bubbly-wordpress-theme/" >Bubbly</a> Theme. <?php }  else { ?><a href="https://wordpress.org/themes/bubbly/" >Bubbly</a> Theme.<?php  } ?> Powered by <a href="http://wordpress.org/">WordPress</a>.</span></p>
					                      </div>
                    </div>
                </div><!-- end #inner-footer -->
			</footer> <!-- end footer -->
        <div id="gototop"><?php _e('Scroll To Top' , 'bubbly'); ?></div>

        <?php wp_footer(); ?>
    </body>
</html>