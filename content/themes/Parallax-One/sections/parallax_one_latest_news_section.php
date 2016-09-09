<!-- =========================
 SECTION: LATEST NEWS
============================== -->
<?php

	$parallax_number_of_posts = get_option('posts_per_page');
	$args = array( 'post_type' => 'post', 'posts_per_page' => $parallax_number_of_posts, 'order' => 'DESC','ignore_sticky_posts' => true );

	$parallax_latestnews_cat = parallax_latest_news_cat();
	if ( !empty($parallax_latestnews_cat) ):
	$args['cat'] = $parallax_latestnews_cat;
	endif;

	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		$parallax_one_latest_news_title = get_theme_mod('parallax_one_latest_news_title',esc_html__('Latest news','parallax-one'));
		if($parallax_number_of_posts > 0) {
		?>
			<?php parallax_hook_news_before(); ?>
			<section class="brief timeline" id="latestnews" role="region" aria-label="<?php esc_html_e('Latest blog posts','parallax-one'); ?>">
				<?php parallax_hook_news_top(); ?>
				<div class="section-overlay-layer">
					<div class="container">
						<div class="row">

							<!-- TIMELINE HEADING / TEXT  -->
							<?php
								if(!empty($parallax_one_latest_news_title)){
									echo '<div class="col-md-12 timeline-text text-left"><h2 class="text-left dark-text">'.esc_attr($parallax_one_latest_news_title).'</h2><div class="colored-line-left"></div></div>';
								} elseif ( isset( $wp_customize )   ) {
									echo '<div class="col-md-12 timeline-text text-left paralax_one_only_customizer"><h2 class="text-left dark-text "></h2><div class="colored-line-left "></div></div>';
								}
							?>

							<div class="parallax-slider-whole-wrap">
								<div class="controls-wrap">
									<button class="control_next icon icon-arrow-carrot-down"><span class="screen-reader-text"><?php esc_attr_e('Post slider navigation: Down','parallax-one')?></span></button>
									<button class="control_prev fade-btn icon icon-arrow-carrot-up"><span class="screen-reader-text"><?php esc_attr_e('Post slider navigation: Up','parallax-one')?></span></button>
								</div>
								<!-- TIMLEINE SCROLLER -->
								<div itemscope itemtype="http://schema.org/Blog" id="parallax_slider" class="col-md-12 timeline-section">
									<ul class="vertical-timeline" id="timeline-scroll">

										<?php

											$i_latest_posts= 0;

											while (  $the_query->have_posts() ) :  $the_query->the_post();

												$i_latest_posts++;


												if ( !wp_is_mobile() ){
													if($i_latest_posts % 2 == 1){
														echo '<li>';
													}
												} else  {
													echo '<li>';
												}
										?>

												<div itemscope itemprop="blogPosts" itemtype="http://schema.org/BlogPosting" id="post-<?php the_ID(); ?>" class="timeline-box-wrap" title="<?php printf( esc_html__( 'Latest News: %s', 'parallax-one' ), get_the_title() ) ?>">
													<div datetime="<?php the_time( 'Y-m-d\TH:i:sP' ); ?>" title="<?php the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'parallax-one' ) ); ?>" class="entry-published date small-text strong">
													<?php echo get_the_date('M, j'); ?>
													</div>
													<div itemscope itemprop="image" class="icon-container white-text">
														<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
															<?php

																if ( has_post_thumbnail() ) :
																	the_post_thumbnail('parallax-one-post-thumbnail-latest-news');
																else: ?>
																	<img src="<?php echo parallax_one_make_protocol_relative_url(parallax_get_file('/images/no-thumbnail-latest-news.jpg')); ?>" width="150" height="150" alt="<?php the_title(); ?>">
															<?php
																endif;
															?>
														</a>
													</div>
													<div class="info">
														<header class="entry-header">
															<h3 itemprop="headline" class="entry-title">
																<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
															</h3>
															<div class="entry-meta">
																<span class="entry-date">
																	<a href="<?php echo esc_url( get_day_link(get_the_date('Y'), get_the_date('m'), get_the_date('d')) ) ?>" rel="bookmark">
																		<time itemprop="datePublished" datetime="<?php the_time( 'Y-m-d\TH:i:sP' ); ?>" title="<?php the_time( _x( 'l, F j, Y, g:i a', 'post time format', 'parallax-one' ) ); ?>" class="entry-date entry-published updated"><?php echo the_time( get_option('date_format') ); ?></time>
																	</a>
																</span>
																<span> <?php esc_html_e('by','parallax-one');?> </span>
																<span itemscope itemprop="author" itemtype="http://schema.org/Person" class="author-link">
																	<span  itemprop="name" class="entry-author author vcard">
																		<a itemprop="url" class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' )); ?>" rel="author"><?php the_author(); ?> </a>
																	</span>
																</span>
															</div><!-- .entry-meta -->
														</header>
														<div itemprop="description" class="entry-content entry-summary">
															<?php the_excerpt(); ?>
															<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="read-more"><?php printf( esc_html__( 'Read more %s', 'parallax-one' ), '<span class="screen-reader-text">  '.get_the_title().'</span>' ); ?></a>
														</div>
													</div>
												</div>

											<?php
											if ( !wp_is_mobile() ){
												if($i_latest_posts % 4 == 0){
													echo '</li>';
												}
											} else {
												echo '</li>';
											}

										endwhile;
										wp_reset_postdata();
										?>
									</ul>
								</div>
							</div><!-- .parallax-slider-whole-wrap -->
						</div>
					</div>
				</div>
				<?php parallax_hook_news_bottom(); ?>
			</section>
			<?php parallax_hook_news_after(); ?>
	<?php
		}
	} ?>
