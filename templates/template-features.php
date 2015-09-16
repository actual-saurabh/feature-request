<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

get_header();

	$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');
	$single_allowed  = avfr_get_option('if_single','if_settings_avfr');
	$taxonomy 		 = 'groups';
 	$all_terms    	 = get_terms( $taxonomy, array( 'hide_empty' => false ) );
 	$domain 		 = avfr_get_option('if_domain','if_settings_main','avfr');

	do_action('avfr_layout_before'); ?>
	<div class="container">
	<main class="avfr--wrap site-main"  id="main" role="main">

		<?php 
		global $wp_query;
   		$term = $wp_query->get_queried_object();
		if ($all_terms) {

			if (is_tax($term)) {
				if ( avfr_get_option('disable_new_for'.$term->slug,'if_settings_groups')=='on' || ( (is_single() && $single_allowed!='on') ) ) { 
					 _e('Submiting new feature for this group is closed.','feature-request');
				} else {
					echo avfr_submit_header();
				}
			} else {
				echo avfr_submit_header();
			}
			
		}
		?>

		<div class="avfr-filter">
			<ul class="avfr-filter-controls">
				<li class="avfr-filter-control-item">
					<?php
					if ( $all_terms && !is_wp_error($all_terms) ) : ?>
					<span class="triangle-down">
						<select id="avfr-filter-groups" onchange="document.location.href=this.value">
						<option value="#"><?php _e('Select a group','feature_request'); ?></option>
							<?php
							foreach ( $all_terms as $all_term ) { 
								echo "<option value=".get_post_type_archive_link( $domain )."?".$taxonomy."=".$all_term->slug.">".$all_term->slug."</option>";
							} ?>
						</select>
					</span>
					<?php
					endif; ?>
				</li>
				<li class="avfr-filter-control-item">
				<span class="triangle-down">
					<select name="filter-status" id="avfr-filter-status" onchange="document.location.href=this.value">
						<option value="#"><?php _e('Status of feature','feature_request'); ?></option>
						<option value="?meta=_avfr_status&val=all"><?php _e('All','feature_request') ?></option>
						<option value="?meta=_avfr_status&val=open"><?php _e('Open','feature_request') ?></option>
						<option value="?meta=_avfr_status&val=approved"><?php _e('Approv','feature_request') ?></option>
						<option value="?meta=_avfr_status&val=completed"><?php _e('Completed','feature_request') ?></option>
						<option value="?meta=_avfr_status&val=declined"><?php _e('Decline','feature_request') ?></option>
					</select>
				</span>
				</li>
				<?php if ( is_user_logged_in() ) { ?>
					<li class="avfr-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=my"><?php _e('My Feature','feature_request') ?></a></li>
				<?php
				} ?>
				<li class="avfr-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=hot"><?php _e('Hot','feature_request') ?></a></li>
				<li class="avfr-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=_avfr_votes"><?php _e('Top','feature_request') ?></a></li>
				<li class="avfr-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>"><?php _e('New','feature_request') ?></a></li>
				<?php
				if ( current_user_can('manage_options') && is_single() ) { 
					$id = get_the_ID();
					?>
					<div class="status-changing">
					<span class="triangle-down">
						<select name="statusChanging" class="change-status-select" data-post-id="<?php echo (int) $id;?>">
							<option value="open"><?php _e('Open','feature_request') ?></option>
							<option value="approved"><?php _e('Approv','feature_request') ?></option>
							<option value="completed"><?php _e('Completed','feature_request') ?></option>
							<option value="declined"><?php _e('Decline','feature_request') ?></option>
						</select>
					</span>
							<a class="avfr avfr-change-status" data-val="open" data-post-id="<?php echo (int) $id;?>" href="#">Change</a>
					</div>
				<?php
				} ?>
			</ul>
		</div>

		<?php
		do_action('avfr_before_entries'); ?>

		<section class="idea-factory--layout-main site-content">
	
		<?php

			if (is_single() && $single_allowed!='on' && !current_user_can('manage_options')) {
				global $wp_query;
  				$wp_query->set_404();
  				status_header( 404 );
  				get_template_part( 404 );
  				exit();
			} else {

			if ( have_posts() ):

				while( have_posts() ) : the_post();

					// setup some vars
					$id             = get_the_ID();
					$userid 		= get_current_user_ID();
					$ip 			= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
					$has_voted 		= avfr_has_voted( $id, $ip , $userid );
					$total_votes 	= avfr_get_votes( $id );
					$status      	= avfr_get_status( $id );
					$status_class   = $status ? sprintf('avfr--entry__%s', $status ) : false;
					?>
					<article class="avfr--entry-wrap post <?php if ( is_single() ) { echo "single-post";	} ?> <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'avfr--hasvoted' : false;?>">
						<?php do_action('avfr_entry_wrap_top', $id ); ?>
						<div class="avfr-votes-area" id="<?php echo (int) $id;?>">
							<div class="avfr--controls">
								<div class="avfr--totals">
								<?php
									if ( $total_votes ) { ?>	
										<?php
										if ( 1 == $total_votes ) { ?>

											<strong class="avfr--totals_num">1</strong><br>
											<span class="avfr--totals_label"><?php _e( 'vote','feature-request' ); ?></span>
												
										<?php

										} elseif ( !empty( $total_votes ) ) { ?>
												
											<strong class="avfr-totals_num"><?php echo $total_votes ?></strong><br>
											<span class="avfr--totals_label"><?php _e( 'votes','feature-request' ); ?></span>
					
										<?php
										} 
										?>
									<?php 
									} else { ?>

										<strong class="avfr--totals_num">0</strong><br>
										<span class="avfr--totals_label"><?php _e( 'vote','feature-request' ); ?></span>

									<?php
									} ?>

								</div>
								<?php
								 if ( avfr_is_voting_active( $id, $ip, $userid ) ) {
									echo avfr_vote_controls($id);
								} ?>
							</div>
							
							<?php echo avfr_vote_status( $id ); ?>
						</div>

						<header class="entry-header">
	 						<h2 class="entry-title">

		 						<?php 
		 						if ( ( $single_allowed == 'on') || ( ( $single_allowed != 'on')  && current_user_can('manage_options') ) ) { ?>
			 						<a href ="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		 						<?php
		 						} else { the_title(); } ?>

	 						</h2>
	 						<?php 
	 						if ( is_single() ) { ?>
		 						<div class="entry-meta" role="category tag">
			 						<div class="avfr_short_group">
										<span class="dashicons dashicons-category"></span>
										<?php
										the_terms( $id, 'groups', ' ', ' / ' );
										?>
									</div>

									<div class="avfr_short_tags">
										<span class="dashicons dashicons-tag"></span>
										<?php
										the_terms( $id, 'featuretags', ' ', ' / ' );
										?>
									</div>
									<?php
									//comments option apply here
		 						 	$if_disabled_comment = avfr_get_option('disable_comment_for'.$terms[0]->slug,'if_settings_groups');
		 						 	if ( $if_disabled_comment == "on" ) {
		 								_e('Comments are closed for this feature.','feature-request');
		 							} else {
									?>
									<span class="avfr_short_comment">
										<span class="dashicons dashicons-admin-comments"></span>
									<?php
										printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'textdomain' ), number_format_i18n( get_comments_number() ) );
									?>
									</span>
										<?php echo avfr_flag_control($id); ?>
						     		</div>
						     <?php if ( is_single() ) :?>
								<div id="avfr-avatar" class="clearfix">
									<?php avfr_the_author_avatar(); ?>
									
								 <div id="avfr_avatar_name"> 
									    <?php if (is_user_logged_in()) {
									    $avfr_avatar_link = get_the_author_meta( 'user_email' );
									    $avfr_avatar_md5 = md5($avfr_avatar_link);
									    
										$str =  file_get_contents (  'https://www.gravatar.com/'.$avfr_avatar_md5.'.php'  );
												$profile = unserialize( $str );
												if ( is_array( $profile ) && isset( $profile['entry'] ) )
												    echo $profile['entry'][0]['displayName']; 
												}else{
												_e( "Unknown", feature_request );	
												}
													
										?>	
										<span><?php  _e( " shared this idea", feature_request ); ?> </span>
										</br>
										<p><?php the_time('F j, Y'); ?></p>
								 </div>
								</div>
	                        
                            <?php endif; ?>
							<?php
	 						}
						}
	 						 ?>

 						</header>
                        
 						<div class="avfr--entry entry-content">
                       	<?php
                       	  	the_content(); ?>
                       	  	<?php
							if ( has_post_thumbnail() && is_single() ) { 
								$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

								?>
								<div class="attachments">
									<p class="image-caption"> <?php _e('Idea attachments:','feature_request'); ?> </p>
									<figure class="avfr-idea-image post-image">
									<?php 
										echo '<a rel="lightbox" href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" alt="' . the_title_attribute( 'echo=0' ) . '">';
										the_post_thumbnail( 'thumbnail' );
										echo '</a>';
									?>

									</figure>
								</div>
						<?php 
							} ?>
								<?php if ($_GET['action']==='deletepost') {
 									$id=get_the_id(); 
									wp_trash_post($id);
								}
								?>
								<?php if (is_user_admin()) : ?>
								<div id="avfr_delete">	
			 					    <span class="dashicons dashicons-trash"></span><a href="<?php the_permalink(); ?>&action=deletepost">Delete post</a>
									<span class="dashicons dashicons-edit"></span> <?php edit_post_link( 'Edit Post', '', '', '' ); ?>
								</div>
								<?php endif ; ?>	
								<?php 
								if (is_single()) {
								
									//Get number of related post from option
									$related_posts_num = avfr_get_option('related_avfr_num','if_settings_ideas');
									if ( isset( $related_posts_num ) && $related_posts_num != '0') {
									
								?>
								<div class="related-avfr-section">
									<h2 class="related-title"><?php _e('Related features :','feature_request') ?></h2>
									<div class="related-avfr-list">
										<?php 
											$media = get_attached_media( 'image' );
											//Get array of terms (Groups and ideatags)
											$groups 	= wp_get_post_terms( $id, 'groups', array("fields" => "all") );
						 					$featuretags 	= wp_get_post_terms( $id, 'featureTags', array("fields" => "all") );
						 					//Pluck out the IDs to get an array of IDS
											$featuretags_ids = wp_list_pluck($featuretags,'term_id');

											$related_query = new WP_Query (array(
												'post_type'    	 => 'avfr',
												'tax_query'	  	 => array('relation' => 'AND',
													array(
													'taxonomy'	 => 'groups',
						                        	'field'    	 => 'slug',
						                        	'terms'    	 => $groups[0]->slug,
						                        	'operator' 	 => 'IN'
						                     		),
						                     		array(
													'taxonomy'	 => 'featuretags',
						                        	'field'    	 => 'term_id',
						                        	'terms'    	 => $featuretags_ids,
						                        	'operator' 	 => 'IN'
						                     		),
												 ),
											'posts_per_page'      => $related_posts_num,
											'ignore_sticky_post' => 1,
											'order_by' 			 => 'rand',
											'post__not_in' 		 => array($id)
											));
											
											if ( $related_query->have_posts() ) {
												while ($related_query->have_posts() ) : $related_query->the_post(); ?>
												<ul class="single_related">
													<li><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
												</ul>
											<?php endwhile;
											 wp_reset_query();
											} else { _e('No related feature exist.','feature-request'); }
										 ?>
									</div>	
								</div>
								<?php } // If related post number not set to 0
								}//if is single ?>

						<?php if ( ! is_single() ) { ?>
													
						<footer class="entry-meta" role="category tag">
							<div class="avfr_short_group">
								<span class="dashicons dashicons-category"></span>
								<?php
								the_terms( $id, 'groups', ' ', ' / ' );
								?>
							</div>

							<div class="avfr_short_tags">
								<span class="dashicons dashicons-tag"></span>
								<?php
								the_terms( $id, 'featuretags', ' ', ' / ' );
								?>
							</div>
							<?php
							//comments option apply here
 						 	$if_disabled_comment = avfr_get_option('disable_comment_for'.$terms[0]->slug,'if_settings_groups');
 						 	if ( $if_disabled_comment == "on" ) {
 								_e('Comments are closed for this feature.','feature-request');
 							} else {
							?>
							<span class="avfr_short_comment">
								<span class="dashicons dashicons-admin-comments"></span>
							<?php
								printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'textdomain' ), number_format_i18n( get_comments_number() ) );
							
							?>
							</span>
							<?php } ?>
						</footer>
								
							<?php
							}
							comments_template();
 							
							do_action('avfr_entry_bottom', $id ); 
							?>
			
						<?php do_action('avfr_entry_wrap_bottom', $id ); ?>

					</article>

					<?php

				endwhile;

				wp_reset_query();

			else:

				apply_filters('avfr_no_ideas', _e('No feature found. Why not submit one?','feature-request'));

			endif;
		}//if is single and single allowed
			?>
		</section>

		<?php do_action('avfr_after_entries'); ?>

	</main>
	</div>

	<?php do_action('avfr_layout_after');

		if ($all_terms) {

			if (is_tax($term)) {
				if ( ! ( avfr_get_option('disable_new_for'.$term->slug,'if_settings_groups')=='on' || ( (is_single() && $single_allowed!='on') ) ) ) { 
					echo avfr_submit_modal();
				}
			} else {
				echo avfr_submit_modal();
			}
			
		}

	get_footer();

