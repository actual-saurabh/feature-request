<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class featurerequestShortcodes {

	function __construct() {

		add_shortcode('feature_request', array($this,'avfr_sc'));
		add_shortcode('avfr_user_votes', array($this,'avfr_user_votes_sc'));

	}

	/**
	*	Show teh votes and vote form within a shortcode
	* 	@since version 1.1
	*/

	function avfr_sc($atts, $content = null) {

		$defaults = array(
			'hide_submit'	=> 'off',
			'hide_voting'	=> 'off',
			'hide_votes'	=> 'off',
			'groups' 		=> ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$postid = get_the_ID();

		$show_submit  	 = 'on' !== $atts['hide_submit'];
		$show_voting  	 = 'on' !== $atts['hide_voting'];
		$show_votes   	 = 'on' !== $atts['hide_votes'];
		$single_allowed  = avfr_get_option('if_single','if_settings_avfr');
		$taxonomy 	  	 = 'groups';
 		$all_terms    	 = get_terms( $taxonomy );
 		$domain 	  	 = avfr_get_option('if_domain','if_settings_main','avfr');
		ob_start();

		do_action('avfr_sc_layout_before', $postid);

		?><div class="avfr--wrap"><?php

			do_action('avfr_sc_layout_before_entries', $postid);

			if ( $show_submit ) { echo avfr_submit_header(); } ?>
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
							<option id="avfr-filter-status" value="?meta=_avfr_status&val=all"><?php _e('All','feature_request') ?></option>
							<option value="?meta=_avfr_status&val=open"><?php _e('Open','feature_request') ?></option>
							<option value="?meta=_avfr_status&val=approved"><?php _e('Approv','feature_request') ?></option>
							<option value="?meta=_avfr_status&val=completed"><?php _e('Completed','feature_request') ?></option>
							<option value="?meta=_avfr_status&val=declined"><?php _e('Decline','feature_request') ?></option>
						</select>
					</span>
					</li>
					<?php if ( is_user_logged_in() ) { ?>
					<li class="avfr-filter-control-item"><a href="?meta=my"><?php _e('My Features','feature_request') ?></a></li>
					<?php
					} ?>
					<li class="avfr-filter-control-item"><a href="?meta=hot"><?php _e('Hot','feature_request') ?></a></li>
					<li class="avfr-filter-control-item"><a href="?meta=_idea_votes"><?php _e('Top','feature_request') ?></a></li>
					<li class="avfr-filter-control-item"><a href="?meta=date"><?php _e('New','feature_request') ?></a></li>
				</ul>
			</div>
			<section class="avfr--layout-main">
				<?php

				$paged = get_query_var('paged') ? get_query_var('paged') : 1;

				$args = array(
					'post_type'			=> 'avfr',
					'meta_key'			=> '',
					'meta_value'		=> '',
					'author'			=> '',
					'post__in'			=> '',
					'tax_query'			=> '',
					'orderby'			=> 'meta_value_num',
					'paged'				=> $paged
				);


				if ( ! empty($atts['groups'] ) ) {
					$args['tax_query'] = array( array( 'taxonomy' => 'groups' , 'terms' => explode(',',$atts['groups']), ), );
				}

				if ( 'my' === $_GET['meta'] ) {

					$args['author'] = get_current_user_id();

				} elseif ( '_avfr_status' === $_GET['meta'] ) {
					if ('all' === $_GET['val']) {
						// continue
					} else {

						$args['meta_key'] = $_GET['meta'];
						$args['meta_value'] = $_GET['val'];
					}

				} elseif ( '_avfr_votes' === $_GET['meta'] ) {
					
					$args['meta_key'] = $_GET['meta'];

				} elseif ( 'hot' === $_GET['meta'] ) {

		        	$orderby_hot = order_avfr_hot();
		        	$args['post__in'] = $orderby_hot;
		        	$args['orderby'] = 'post__in';
		        	unset($args['meta_value'],$args['meta_key'],$args['author']);

		        } elseif ( 'date' === $_GET['meta'] ) {
		        	
		        	$args['orderby'] = 'date';
		        	unset($args['meta_value'],$args['meta_key'],$args['author']);

		        }

				$q = new WP_Query( apply_filters('avfr_query_args', $args ) );

				$max = $q->max_num_pages;

				wp_localize_script('avfr-script', 'feature_request',  avfr_localized_args( $max , $paged ) );

				if ( $q->have_posts() ):

					while( $q->have_posts() ) : $q->the_post();

						// setup some vars
						$id             = get_the_ID();

						if ( is_user_logged_in() ) {

							$has_voted 		= get_user_meta( get_current_user_ID(), '_avfr'.$id.'_has_voted', true);

						} elseif( $public_can_vote ) {

							$has_voted 		= avfr_has_public_voted( $id );

						}

						$total_votes 	= avfr_get_votes( $id );
						$status      	= avfr_get_status( $id );
						$userid 		= get_current_user_ID();
						$ip 			= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
						$status_class   = $status ? sprintf('avfr--entry__%s', $status ) : false;

						?>
						<article class="avfr--entry-wrap post <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'avfr--hasvoted' : false;?>">

							<?php do_action('avfr_sc_entry_wrap_top', $postid ); ?>

								<div class="avfr-votes-area" id="<?php echo (int) $id; ?>">
									<div class="avfr--controls">
										<div class="avfr--totals">
										<?php
											if ( $total_votes && $show_votes  ) { ?>	
												<?php
												if ( 1 == $total_votes ) { ?>

													<strong class="avfr--totals_num">1</strong><br>
													<span class="avfr--totals_label"><?php _e( 'vote','Feature-request' ); ?></span>
														
												<?php

												} elseif ( !empty( $total_votes ) ) { ?>
														
													<strong class="avfr--totals_num"><?php echo $total_votes ?></strong><br>
													<span class="avfr--totals_label"><?php _e( 'votes','Feature-request' ); ?></span>
							
												<?php
												} 
												?>
											<?php 
											} else { ?>

												<strong class="avfr--totals_num">0</strong><br>
												<span class="avfr--totals_label"><?php _e( 'vote','Feature-request' ); ?></span>

											<?php
											} ?>

										</div>
										<?php
										 if ( avfr_is_voting_active( $id, $ip, $userid ) ) {
											echo avfr_vote_controls($id);
										}
										?>
									</div>

									<?php echo avfr_vote_status( $id ); ?>
								</div>

								<header class="avfr--entry entry-header">
			 						<h2 class="entry-title">

				 						<?php 
				 						if ( ($single_allowed == 'on') || ( ($single_allowed != 'on')  && current_user_can('manage_options') ) ) { ?>
					 						<a href ="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				 						<?php
				 						} else {the_title(); } ?>

			 						</h2>
		 						</header>
							<div class="entry-content">
								<p>
								<?php

									$avfr_read_more= '<a href="' . get_permalink() . '" class="read-more" >Read More &rarr;</a>';								
									avfr_the_trim_excerpt(null,250,null,$avfr_read_more);    
								?>
								</p>
							</div>
							<footer>
								<div class="avfr_short_group">
									<span class="dashicons dashicons-category"></span>
									<?php
									the_terms( $id, 'groups', ' ', ' / ' );
									?>
								</div>

								<div class="avfr_short_tags">
									<span class="dashicons dashicons-tag"></span>
									<?php
									the_terms( $id, 'featureTags', ' ', ' / ' );
									?>
								</div>
								<span class="avfr_short_comment">
									<span class="dashicons dashicons-admin-comments"></span>

									<?php comments_popup_link( 'No comment', 'One comment', '% comments', get_permalink() ); ?>

								</span>
							</footer>
							<?php do_action('avfr_sc_entry_wrap_bottom', $postid ); ?>

						</article>

						<?php

					endwhile;

				else:

					apply_filters('avfr_no_ideas', _e('No ideas found. Why not submit one?','Feature-request'));

				endif;
				wp_reset_query();
				?>
			</section>

			<?php do_action('avfr_sc_layout_after_entries', $postid); ?>

		</div>

		<?php if ( $show_submit ) { echo avfr_submit_modal($atts['groups']); }

		do_action('avfr_sc_layout_after', $postid);

		return ob_get_clean();

	}

	function avfr_user_votes_sc($atts) {

		$show_total 	= "on" !== $atts['hide_total'];
		$show_remaining = "on" !== $atts['hide_remaining'];
		$defaults = array(
			'groups'		=> '',
			'total'			=> 'on',
			'remaining'		=> 'on'
		);
		$atts 				= shortcode_atts( $defaults, $atts );
			// Get limit for users from option
		$limit_time			= avfr_get_option('votes_limitation_time','if_settings_avfr');
			//Get user ID
		$userid 			= get_current_user_ID();
		$ip 				= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			//Calculate votes
		$fun 				= 'avfr_total_votes_'.$limit_time;

		$taxonomy 		 	= 'groups';
		$args 				= array( 'include'=>$atts['groups'] );
		$terms    		 	= get_terms( $taxonomy , $args );
		echo get_post_type_archive_link( $domain );
		?>
		<div class="user-votes-shortcode">

			<p><?php _e('Your voting status in current ','feature_request'); echo strtolower($limit_time); ?></p>
			<?php

			foreach ( $terms as $term ) {
				${'user_total_voted'.$term->slug} 	= $fun( $ip, $userid, $term->slug );
				${'user_vote_limit'.$term->slug}	= avfr_get_option('total_vote_limit_'.$term->slug,'if_settings_groups');
				${'remaining_votes'.$term->slug} 	= ${'user_vote_limit'.$term->slug} - ${'user_total_voted'.$term->slug} ;
				echo "<p>".$term->name."</p>";
				if ( $show_total ) {
					_e('Total Votes: ','feature_request'); echo ${'user_total_voted'.$term->slug}."<br>";
				}
				if ( $show_remaining ) {
					_e('Remaining Votes: ','feature_request'); echo ${'remaining_votes'.$term->slug}."<br>";
				}
			}
			?>
		</div>
		<?php
	}


}
new featurerequestShortcodes;