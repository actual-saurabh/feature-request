<?php

get_header();

	$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');
	$single_allowed  = avfr_get_option('if_single','if_settings_ideas');
	$taxonomy 		 = 'groups';
 	$all_terms    	 = get_terms( $taxonomy, array( 'hide_empty' => false ) );
 	$domain 		 = 'ideas';

	do_action('idea_factory_layout_before'); ?>
	<div class="container">
	<main class="idea-factory--wrap site-main"  id="main" role="main">

		<?php 
		global $wp_query;
   		$term = $wp_query->get_queried_object();
		if ($all_terms) {

			if (is_tax($term)) {
				if ( avfr_get_option('disable_new_for'.$term->slug,'if_settings_groups') == '1' || ( (is_single() && $single_allowed != '1') ) ) { 
					 _e('Submiting new idea for this group is closed.','idea-factory');
				} else {
					echo avfr_submit_header();
				}
			} else {
				echo avfr_submit_header();
			}
			
		}
		?>

		<div class="idea-factory-filter">
			<ul class="idea-factory-filter-controls">
				<li class="idea-factory-filter-control-item">
					<?php
					if ( $all_terms && !is_wp_error($all_terms) ) : ?>
					<span class="triangle-down">
						<select id="idea-factory-filter-groups" onchange="document.location.href=this.value">
						<option value="#"><?php _e('Select a group','idea_factory'); ?></option>
							<?php
							foreach ( $all_terms as $all_term ) { 
								echo "<option value=".get_post_type_archive_link( $domain )."?".$taxonomy."=".$all_term->slug.">".$all_term->name."</option>";
							} ?>
						</select>
					</span>
					<?php
					endif; ?>
				</li>
				<li class="idea-factory-filter-control-item">
				<span class="triangle-down">
					<select name="filter-status" id="idea-factory-filter-status" onchange="document.location.href=this.value">
						<option value="#"><?php _e('Status of idea','idea_factory'); ?></option>
						<option value="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_status&val=all"><?php _e('All','idea_factory') ?></option>
						<option value="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_status&val=open"><?php _e('Open','idea_factory') ?></option>
						<option value="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_status&val=approved"><?php _e('Approve','idea_factory') ?></option>
						<option value="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_status&val=completed"><?php _e('Completed','idea_factory') ?></option>
						<option value="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_status&val=declined"><?php _e('Decline','idea_factory') ?></option>
					</select>
				</span>
				</li>
				<?php if ( is_user_logged_in() ) { ?>
					<li class="idea-factory-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=my"><?php _e('My Ideas','idea_factory') ?></a></li>
				<?php
				} ?>
				<li class="idea-factory-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=hot"><?php _e('Hot','idea_factory') ?></a></li>
				<li class="idea-factory-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>?meta=_idea_votes"><?php _e('Top','idea_factory') ?></a></li>
				<li class="idea-factory-filter-control-item"><a href="<?php echo get_post_type_archive_link( $domain );?>"><?php _e('New','idea_factory') ?></a></li>
				<?php
				if ( current_user_can('manage_options') && is_single() ) { 
					$id = get_the_ID();
					?>
					<div class="status-changing">
					<span class="triangle-down">
						<select name="statusChanging" class="change-status-select" data-post-id="<?php echo (int) $id;?>">
							<option value="open"><?php _e('Open','idea_factory') ?></option>
							<option value="approved"><?php _e('Approve','idea_factory') ?></option>
							<option value="completed"><?php _e('Completed','idea_factory') ?></option>
							<option value="declined"><?php _e('Decline','idea_factory') ?></option>
						</select>
					</span>
							<a class="idea-factory idea-factory-change-status" data-val="open" data-post-id="<?php echo (int) $id;?>" href="#">Change</a>
					</div>
				<?php
				} ?>
			</ul>
		</div>

		<?php
		do_action('idea_factory_before_entries'); ?>

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
					$has_voted 		= idea_factory_has_voted( $id, $ip , $userid );
					$total_votes 	= idea_factory_get_votes( $id );
					$status      	= idea_factory_get_status( $id );
					$status_class   = $status ? sprintf('idea-factory--entry__%s', $status ) : false;
					?>
					<article class="idea-factory--entry-wrap post <?php if ( is_single() ) { echo "single-post";	} ?> <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'idea-factory--hasvoted' : false;?>">
						<?php do_action('idea_factory_entry_wrap_top', $id ); ?>
						<div class="idea-factory-votes-area" id="<?php echo (int) $id;?>">
							<div class="idea-factory--controls">
								<div class="idea-factory--totals">
								<?php
									if ( $total_votes ) { ?>	
										<?php
										if ( 1 == $total_votes ) { ?>

											<strong class="idea-factory--totals_num">1</strong><br>
											<span class="idea-factory--totals_label"><?php _e( 'vote','idea-factory' ); ?></span>
												
										<?php

										} elseif ( !empty( $total_votes ) ) { ?>
												
											<strong class="idea-factory--totals_num"><?php echo $total_votes ?></strong><br>
											<span class="idea-factory--totals_label"><?php _e( 'votes','idea-factory' ); ?></span>
					
										<?php
										} 
										?>
									<?php 
									} else { ?>

										<strong class="idea-factory--totals_num">0</strong><br>
										<span class="idea-factory--totals_label"><?php _e( 'vote','idea-factory' ); ?></span>

									<?php
									} ?>

								</div>
								<?php
								 if ( idea_factory_is_voting_active( $id, $ip, $userid ) ) {
									echo idea_factory_vote_controls($id);
								} ?>
							</div>
							
							<?php echo idea_factory_vote_status( $id ); ?>
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
			 						<div class="idea_short_group">
										<span class="dashicons dashicons-category"></span>
										<?php
										the_terms( $id, 'groups', ' ', ', ' );
										?>
									</div>
								<?php
								
								 if ( false != has_term( '', 'ideatags', $id) ) {?>
									<div class="idea_short_tags">
										<span class="dashicons dashicons-tag"></span>
										<?php
											the_terms( $id, 'ideatags', ' ', ', ' );
										?>
									</div>

									<?php
								}
									//comments option apply here
		 						 	$if_disabled_comment = avfr_get_option('disable_comment_for'.$terms[0]->slug,'if_settings_groups');
		 						 	if ( $if_disabled_comment == "on" ) {
		 								_e('Comments are closed for this idea.','idea-factory');
		 							} else {
									?>
									<div>
										<span class="idea_short_comment">
											<span class="dashicons dashicons-admin-comments"></span>
										<?php
											printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'textdomain' ), number_format_i18n( get_comments_number() ) );
										?>
										</span>
									</div>
										<?php echo idea_factory_flag_control($id); ?>
						     		</div>
						     <?php if ( is_single() ) :?>
								<div id="idea-avatar">
								<?php 
									idea_factory_get_author_avatar($id); ?>
									
								 <div id="idea_avatar_name"> 
								<?php 
									idea_factory_get_author_name($id);
											?>
										<span><?php  _e( " shared this idea", idea_factory ); ?> </span>
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
                        
 						<div class="idea-factory--entry entry-content hhhhh">
                       	<?php
                       	  	the_content(); ?>
                       	  	<?php
							if ( has_post_thumbnail() && is_single() ) { 
								$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

								?>
								<div class="attachments">
									<p class="image-caption"> <?php _e('Idea attachments:','idea_factory'); ?> </p>
									<figure class="idea-factory-idea-image post-image">
									<?php 
										echo '<a rel="lightbox" href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" alt="' . the_title_attribute( 'echo=0' ) . '">';
										the_post_thumbnail( 'thumbnail' );
										echo '</a>';
									?>

									</figure>
								</div>
						<?php 
							} ?>
						</div>
							<?php
							if (is_single()) {
								 if ( current_user_can( 'manage_options' ) ) { 
									if ($_GET['action']==='deletepost') {
	 									$id = get_the_id(); 
										wp_trash_post($id);
									} ?>
								<div id="idea_factory_delete">	
			 					    <span class="dashicons dashicons-trash"></span><a href="<?php the_permalink(); ?>&action=deletepost">Delete post</a>
									<span class="dashicons dashicons-edit"></span> <?php edit_post_link( 'Edit Post', '', '', '' ); ?>
								</div>
								<?php }
							} ?>

								<?php 
								if ( is_single() ) {
								
									//Get number of related post from option
									$related_posts_num = avfr_get_option('related_idea_num','if_settings_ideas');
									if ( isset( $related_posts_num ) && $related_posts_num != '0') {
									
								?>
								<div class="related-ideas-section">
									<h2 class="related-title"><?php _e('Related ideas :','idea_factory') ?></h2>
									<div class="related-ideas-list">
										<?php 
											$media = get_attached_media( 'image' );
											//Get array of terms (Groups and ideatags)
											$groups 	= wp_get_post_terms( $id, 'groups', array("fields" => "all") );
						 					$ideatags 	= wp_get_post_terms( $id, 'ideatags', array("fields" => "all") );
						 					//Pluck out the IDs to get an array of IDS
											$ideatags_ids = wp_list_pluck($ideatags,'term_id');

											$related_query = new WP_Query (array(
												'post_type'    	 => 'ideas',
												'tax_query'	  	 => array('relation' => 'AND',
													array(
													'taxonomy'	 => 'groups',
						                        	'field'    	 => 'slug',
						                        	'terms'    	 => $groups[0]->slug,
						                        	'operator' 	 => 'IN'
						                     		),
						                     		array(
													'taxonomy'	 => 'ideatags',
						                        	'field'    	 => 'term_id',
						                        	'terms'    	 => $ideatags_ids,
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
											} else { _e('No related idea exist.','idea-factory'); }
										 ?>
									</div>	
								</div>
								<?php } // If related post number not set to 0
								}//if is single ?>

						<?php if ( ! is_single() ) { ?>
													
						<footer class="entry-meta" role="category tag">
							<div class="idea_short_group">
								<span class="dashicons dashicons-category"></span>
								<?php
								the_terms( $id, 'groups', ' ', ', ' );
								?>
							</div>
								<?php
								 if ( false != has_term( '', 'ideatags', $id) ) {?>
									<div class="idea_short_tags">
										<span class="dashicons dashicons-tag"></span>
										<?php
											the_terms( $id, 'ideatags', ' ', ', ' );
										?>
									</div>

								<?php
								}
							//comments option apply here
 						 	$if_disabled_comment = avfr_get_option('disable_comment_for'.$terms[0]->slug,'if_settings_groups');
 						 	if ( $if_disabled_comment == "on" ) {
 								_e('Comments are closed for this idea.','idea-factory');
 							} else {
							?>
							<div>
								<span class="idea_short_comment">
									<span class="dashicons dashicons-admin-comments"></span>
								<?php
									printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'textdomain' ), number_format_i18n( get_comments_number() ) );
								
								?>
								</span>
							</div>
							<?php } ?>
						</footer>
								
							<?php
							}
							comments_template();
 							
							do_action('idea_factory_entry_bottom', $id ); 
							?>
			
						<?php do_action('idea_factory_entry_wrap_bottom', $id ); ?>

					</article>

					<?php

				endwhile;

				wp_reset_query();

			else:

				apply_filters('idea_factory_no_ideas', _e('No ideas found. Why not submit one?','idea-factory'));

			endif;
		}//if is single and single allowed
			?>
		</section>

		<?php do_action('idea_factory_after_entries'); ?>

	</main>
	</div>

	<?php do_action('idea_factory_layout_after');

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

