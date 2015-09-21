<?php
/**
 	*
 	* 	@package   			Feature-request
 	* 	@author    			Averta
 	* 	@license   			GPL-2.0+
 	* 	@link      			http://averta.net
 	*	@copyright 			2015 Averta
 	*
 **/	

if ( !function_exists('avfr_get_status') ) {

	function avfr_get_status( $post_id ) {

		if ( empty( $post_id ) )
			return;

		$status = get_post_meta( $post_id, '_request_status', true );

		return !empty( $status ) ? $status : false;
	}

}


/**
 * Get users email's that voted to request with $post_id id
 * @since 1.0
 */

if ( !function_exists('avfr_get_voters_email') ) {

	function avfr_get_voters_email( $post_id ) {

		if ( empty( $post_id ) )
				return;

		global $wpdb;

	    $table 	= $wpdb->base_prefix.'feature_request';

	   	$sql   	=  $wpdb->prepare('SELECT email FROM '.$table.' WHERE postid ="%d" AND type="vote"', $post_id );

	   	$result =  $wpdb->get_col( $sql );

	   	return $result;
			
	}

}


/**
 * Get total time that request with $post_id ID voted/liked/disliked.
 * @since 1.0
 */

if ( !function_exists('avfr_get_total_votes') ) {

	function avfr_get_total_votes( $post_id ) {

		if ( empty( $post_id ) )
			return;

		$total_votes = get_post_meta( $post_id, '_feature_total_votes', true );

		return !empty( $total_votes ) ? $total_votes : false;
	}

}


/**
 * Get total votes/likes of request with $post_id ID.
 * @since 1.0
 */

if ( !function_exists('avfr_get_votes') ) {

	function avfr_get_votes( $post_id ) {

		if ( empty( $post_id ) )
			return;

		$votes = get_post_meta( $post_id, '_feature_votes', true );

		return !empty( $votes ) ? $votes : false;
	}

}


/**
 * Get option value.
 * @since 1.0
 */

if ( !function_exists('avfr_get_option') ) {

	function avfr_get_option( $option, $section, $default = '' ) {

		if ( empty( $option ) )
			return;

	    $options = get_option( $section );


	    if ( isset( $options[$option] ) ) {
	        return $options[$option];
	    }

	    return $default;
	}

}


/**
 * Filter content
 * @since 1.0
 */

if ( !function_exists('avfr_content_filter') ) {

	function avfr_content_filter( $input ) {

		// bail if no input
		if ( empty( $input ) )
			return;

		// setup our array of allowed content to pass
		$allowed_html = array(
			'a' 			=> array(
			    'href' 		=> array(),
			    'title' 	=> array(),
			    'rel'		=> array(),
			    'target'	=> array()
			),
			'img'			=> array(
				'src' 		=> array(),
				'alt'		=> array(),
				'title'		=> array()
			),
			'p'				=> array(),
			'br' 			=> array(),
			'em' 			=> array(),
			'strong' 		=> array(),
			'small' 		=> array()
		);

		$out = wp_kses( $input, apply_filters('avfr_allowed_html', $allowed_html ) );

		return $out;
	}

}


/**
 * Return ID of posts that ordered by votes average in past 14 days.
 * @since 1.0
 */

if ( !function_exists('avfr_order_features_hot') ) {

	function avfr_order_features_hot() {
		global $wpdb;
		$final_array 	= [];
	    $table 			= $wpdb->base_prefix.'feature_request';
	    $type 			= 'vote';
	   	$sql 			= $wpdb->prepare( 'SELECT postid, SUM(votes) as sum FROM '.$table.' WHERE type="%s" AND time>=(CURDATE() - INTERVAL 14 DAY) GROUP BY postid', $type );
	   	$post_ids 		= $wpdb->get_col( $sql, 0 );
	   	$post_votes 	= $wpdb->get_col( $sql, 1 );
	   	$initial_array 	= array_combine($post_ids, $post_votes);
	   	foreach ( $initial_array as $key => $value ) {
	   		$final_array[$key] = $value / 336;
	   	}
	   	arsort( $final_array );
	   	return array_keys($final_array);
	}

}


/**
 * Make some custom query_var public
 * @since 1.0
 */

if ( !function_exists('avfr_add_query_vars_filter') ) {

	function avfr_add_query_vars_filter( $vars ){
	  $vars[] = "meta";
	  $vars[] = "val";
	  $vars[] = "action";
	  return $vars;
	}

}

add_filter( 'query_vars', 'avfr_add_query_vars_filter' );


/**
 * Change query based on query vars in url
 * @since 1.0
 */

if ( !function_exists('avfr_archive_query') ) {

	function avfr_archive_query( $query ) {

		if ( is_admin() || ! $query->is_main_query() )
	        return;

	 	if ( is_post_type_archive( 'avfr' ) ) {

	 		$order_by = 'meta_value_num';
	 		$meta = get_query_var('meta');
	 		$val = get_query_var('val');

	 		if ( 'my' === $meta ) {
	 			if ( !empty($val) ) {
	 				$query->set( 'meta_key', '_author_email' );
		 			$query->set( 'meta_value', base64_decode($val) );
	 			}
	 			$query->set( 'author', get_current_user_id() );

	 		} elseif ( '_feature_status' === $meta ) {
	 			if ('all' === $val ) {
	 				//continue
	 			} else {
		 			$query->set( 'meta_key', $meta );
		 			$query->set( 'meta_value', $val );
	 			}

	 		} elseif ( '_feature_votes' === $meta ) {

	 			$query->set( 'meta_key', $meta );

	 		} elseif ( 'hot' === $meta ) {

	 			$orderby_hot = order_ideas_hot();
	 			$query->set( 'post__in' , $orderby_hot );
	 			$order_by = 'post__in';
	 		}

	        $query->set( 'orderby', $order_by );
	        $query->set( 'order', 'DESC' );

	        return;
	    }
	}

}

add_action( 'pre_get_posts', 'avfr_archive_query');

/**
 * If current user (visitor) can vote return true
 * @since 1.0
 */

if ( !function_exists('avfr_is_voting_active') ) {

	function avfr_is_voting_active( $post_id, $ip, $userid, $email ) {

		$status      	 = avfr_get_status( $post_id );

		$public_can_vote = avfr_get_option('avfr_public_voting','avfr_settings_main');

		if ( ( ( false == avfr_has_voted( $post_id, $ip, $userid, $email ) && is_user_logged_in() ) || ( ( false == avfr_has_voted( $post_id, $ip, $userid, $email ) ) && !is_user_logged_in() && "1" == $public_can_vote) ) && 'open' === $status ){

			return true;

		} else {

			return false;
		}
	}

}


/**
 * Add vote and update database
 * @since 1.0
 */

if ( !function_exists('avfr_add_vote') ) {

	function avfr_add_vote( $args = array() ) {

		$db = new ideaFactoryDB;

		$defaults = array(
			'postid' => get_the_ID(),
			'time'   => current_time('timestamp'),
			'ip'   	 => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
			'userid' => 0,
			'type'   => 'vote',
		);

		$args = array_merge( $defaults, $args );

		$db->insert( $args );

	}

}


/**
 * Check if idea has voted by current user
 * @since 1.0
 */

if ( !function_exists('avfr_has_voted') ) {

	function avfr_has_voted( $post_id, $ip, $userid = '0', $email ) {

		if ( empty( $post_id ) )
			return;

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	    $table = $wpdb->base_prefix.'feature_request';

	   	$sql =  $wpdb->prepare('SELECT * FROM '.$table.' WHERE ( ip ="%s" OR email="%s" ) AND userid="%s" AND postid ="%d" AND type="vote"', $ip, $email, $userid, $post_id );

	   	$result =  $wpdb->get_results( $sql );

		if ( $result ) {

			return true;

		} else {

			return false;

		}
	}

}


/**
 * Check if post flagged by user
 * @since 1.0
 */

if ( !function_exists('avfr_has_flag') ) {

	function avfr_has_flag( $post_id, $ip, $userid, $email ) {

		if ( empty( $post_id ) )
			return;

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	    $table = $wpdb->base_prefix.'feature_request';

	   	$sql =  $wpdb->prepare('SELECT * FROM '.$table.' WHERE ( ip ="%s" OR $email ) AND userid="%s" AND postid ="%d" AND type="flag"', $ip, $email, $userid, $post_id );

	   	$result =  $wpdb->get_results( $sql );

		if ( $result ) {

			return true;

		} else {

			return false;

		}
	}

}

/**
 * Check if post flagged by user
 * @since 1.0
 */

if ( !function_exists('avfr_add_flag') ) {

	function avfr_add_flag( $args = array() ) {

		$db = new ideaFactoryDB;

		$defaults = array(
			'postid' => get_the_ID(),
			'time'   => current_time('timestamp'),
			'ip'   	 => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
			'type'   => 'flag',
		);

		$args = array_merge( $defaults, $args );

		$db->insert( $args );

	}

}


/**
 * Get total votes in this week
 * @since 1.0
 */

if ( !function_exists('avfr_total_votes_WEEK') ) {

	function avfr_total_votes_WEEK( $ip, $userid = '', $idea_voted_group = '' ) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	    $table = $wpdb->base_prefix.'feature_request';

	   	$sql =  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ( ( ip ="%s" AND userid="%s" ) OR email="%s" ) AND groups ="%s" AND type="vote" AND YEARWEEK(time)=YEARWEEK(CURDATE()) AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $ip, $userid, $email, $idea_voted_group );

	   	$total =  $wpdb->get_col( $sql );

			return array_sum($total);
	}

}

/**
 * Get total votes in this month
 * @since 1.0
 */

if ( !function_exists('avfr_total_votes_MONTH') ) {

	function avfr_total_votes_MONTH( $ip, $userid = '', $email = '', $feature_group) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	    $table 	= $wpdb->base_prefix.'feature_request';

	   	$sql 	=  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ( userid="%s" AND ( ip ="%s" OR email="%s" ) AND groups ="%s" AND type="vote" AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $userid, $ip, $email, $idea_voted_group );

	   	$total 	=  $wpdb->get_col( $sql );

			return array_sum($total);
	}

}


/**
 * Get total votes in this year
 * @since 1.0
 */

if ( !function_exists('avfr_total_votes_YEAR') ) {

	function avfr_total_votes_YEAR( $ip, $userid = '', $idea_voted_group = '' ) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	    $table = $wpdb->base_prefix.'feature_request';

	   	$sql =  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ( ( ip ="%s" AND userid="%s" ) OR email="%s" ) AND groups ="%s" AND type="vote" AND YEAR(time)=YEAR(CURDATE())', $ip, $userid, $email, $idea_voted_group );

	   	$total =  $wpdb->get_col( $sql );

			return array_sum($total);
	}

}


/**
 * Localizing arguments
 * @since 1.0
 */

if ( !function_exists('avfr_localized_args') ) {

	function avfr_localized_args( $max = '', $paged = '' ){

		global $wp_query, $post;

		$args = array(
			'ajaxurl' 		  => admin_url( 'admin-ajax.php' ),
			'nonce'			  => wp_create_nonce('feature_request'),
			'error_message'   => apply_filters('avfr_error', __('Awww snap, something went wrong!', 'feature-request')),
			'label'			  => apply_filters('avfr_loadmore_label', __('Load more ideas', 'feature-request')),
			'label_loading'   => apply_filters('avfr_loadmore_loading', __('Loading ideas...', 'feature-request')),
			'thanks_voting'   => apply_filters('avfr_thanks_voting', __('Thanks for voting!', 'feature-request')),
			'already_voted'   => apply_filters('avfr_already_voted', __('You have already voted!', 'feature-request')),
			'already_flagged' => apply_filters('avfr_already_flagged', __('You have already flagged this idea!', 'feature-request')),
			'thanks_flag'	  => apply_filters('avfr_thanks_flag', __('Reported!', 'feature-request')),
			'reached_limit'   => apply_filters('avfr_reached_limit', __('You are reached voting limit for this groups of ideas.', 'feature-request')),
			'startPage'		  => $paged,
			'maxPages' 		  => $max,
			'nextLink' 		  => next_posts($max, false)
		);

		return apply_filters('avfr_localized_args', $args );

	}

}

/*--- PLUGGABLES ---*/


/**
 * Localizing arguments
 * @since 1.0
 */

if ( !function_exists('avfr_submit_box') ):

	function avfr_submit_box( $groups = '' ) {

		$public_can_vote = avfr_get_option('avfr_public_voting','avfr_settings_main');
		$userid 		 = $public_can_vote && !is_user_logged_in() ? 1 : get_current_user_ID();

		if ( is_user_logged_in() || '1' == $public_can_vote ) { 
			
			$allgroups = get_terms('groups', array('hide_empty' => 0, ));
			foreach ( $allgroups as $exclude ) {
				if ( '1' == avfr_get_option('avfr_disable_new_for'.$exclude->slug,'avfr_settings_groups') ) {
					$exluded[]=$exclude->term_id;
				}
			}

			$args = array(
				'show_option_all'    => '',
				'show_option_none'   => '',
				'option_none_value'  => '-1',
				'orderby'            => 'Name', 
				'order'              => 'ASC',
				'show_count'         => 0,
				'hide_empty'         => 0, 
				'include'			 => $groups,
				'exclude'            => $exluded,
				'echo'               => 1,
				'selected'           => 0,
				'hierarchical'       => 0, 
				'name'               => 'group',
				'id'                 => '',
				'class'              => 'ideagroup',
				'depth'              => 0,
				'tab_index'          => 0,
				'taxonomy'           => 'groups',
				'hide_if_empty'      => false,
				'value_field'	     => 'name',	
				); ?>
			<div class="fade avfr-modal" tabindex="-1">
				<div class="avfr-modal-dialog ">
				    <div class="avfr-modal-content">
						<button type="button" class="close" data-dismiss="avfr-modal">
						<span aria-hidden="true">&times;</span>
						</button>

				    	<div class="avfr-modal-header">
				    		<h3 class="avfr-modal-title"><?php apply_filters('avfr_submit_idea_label', _e('Submit feature','feature-request'));?></h3>
				    	</div>
				    	<div class="avfr-modal-body">

							<form id="avfr-entry-form" method="post" enctype="multipart/form-data">
								<div id="feature-form-group"><label for="feature-title"><?php apply_filters('avfr_form_title', _e('Submit feature for:','feature-request'));?></label>
								<?php if ( !is_archive() && !is_single() && count( explode(',', $groups) ) == 1 ) {
								 	$group_name = get_term( $groups, 'groups' );
								 	echo $group_name->name;
								 	echo "<input name='group' type='hidden' value=".$group_name->slug.">";
									} else { wp_dropdown_categories( $args ); } ?> </div>
								
								<script type="text/javascript">
								jQuery(document).ready(function($){

								    jQuery('#tags-data-list')
								        .textext({
								            plugins : 'tags autocomplete'
								        })
								        .bind('getSuggestions', function(e, data)
								        {
								        var list =  [<?php   $avfr_modal_tag = array(
												    'orderby'           => 'name', 
												    'order'             => 'ASC',
												    'hide_empty'        => false, 
												    'fields'            => 'all', 
												    'childless'         => false,
												    'pad_counts'        => false,  
												    'cache_domain'      => 'core'
												); 

										        $terms = get_terms('featureTags',$avfr_modal_tag );
										        foreach ($terms as $term) {
										         echo "'".$term->slug."',";	
										         } ?>  ],
								                textext = $(e.target).textext()[0],
								                query = (data ? data.query : '') || ''
								                ;

								            $(this).trigger(
								                'setSuggestions',
								                { result : textext.itemManager().filter(list, query) }
								            );
								        })
								        ;
								    });
								</script>

								<?php do_action('avfr_inside_form_top');

								if ( !is_user_logged_in() ) { ?>
									<div id="feature-form-email">
										<label for="avfr-entryform_email">
											<?php apply_filters('avfr_form_Email', _e('Email','feature-request'));?>
										</label>
									<input id="avfr-entry-form-email" type="text" name="feature-email" value="" placeholder="Email"></div>
								<?php
								}
								?>
								
								<div id="feature_form_title">
									<label for="avfr-entryform_title">
										<?php apply_filters('avfr_form_title', _e('Title','feature-request'));?>
									</label>
									<input id="avfr-entryform-title" type="text" name="feature-title" value="" placeholder="My Awesome Submission">
								</div>

								<div id="feature-form-desc">
									<label for="avfr-entryform-description">
										<?php apply_filters('avfr_form_description', _e('Description','feature-request'));?>
									</label>
									<textarea id="avfr-entryform_description" name="feature-description" value="" placeholder="<?php _e('Make the description meaningful!', 'feature-request') ?>"></textarea>		
  								</div>

  								<div id="feature-form-tags">
	  								<label for="tags-data-list">
	  									<?php apply_filters('avfr_form_title', _e('Idea tags:','feature-request'));?>
	  								</label>
								<textarea name="feature-tags" id="tags-data-list" rows="1" ></textarea>
								</div>

  								<?php $disable_upload = avfr_get_option('avfr_disable_upload','avfr_settings_fetures') ?>
  								<?php if ( '1' != $disable_upload ) : ?>

  								<div id="feature-form-upload">
  									<label for="feature-upload-form">
  										<?php _e('Select file to upload:','feature-request'); ?>
  									</label>
  									<?php echo avfr_get_option('avfr_echo_type_size','avfr_settings_features');?>
  									</br>
  									<input id="feature-upload-form" type="file"  name='feature-upload'>
  								</div>

								<?php endif; ?>

								<?php if ( '1' != avfr_get_option('avfr_disable_captcha', 'avfr_settings_main') ) : ?>
								<div id="feature_form_captcha">
								      <label for="captcha">
								      	<?php _e('Captcha','feature-request') ?>
								      </label>
								      <input id="captcha" type="text" name="captcha" value="" maxlength="4" size="40" />
								      <img id="imgCaptcha" src="<?php echo AVFR_URL; ?>/public/includes/create-image.php" />
								      <img id='reload' src="<?php echo AVFR_URL; ?>/public/assets/image/refresh.png" alt="Refresh" >
								</div>
								<?php endif; ?> 
															
								<?php do_action('avfr_inside_form_bottom');?>
								
								<input type="hidden" name="action" value="process_entry">
								<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('avfr-entry-nonce'); ?>"/>

								<div class="avfr-modal-footer">
									<input id="avfr-btn" class="avfr-button" type="submit" value="<?php apply_filters('avfr_submit_label', _e('Submit','feature-request'));?>">
									<div id="avfr-entry-form-results"><p></p></div>
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>

		<?php } else { ?>
					<div class="fade avfr-modal" tabindex="-1">
						<div class="avfr-modal-dialog ">
							<div class="avfr-modal-content">
								<div class="avfr-modal-body">
								<button type="button" class="close" data-dismiss="avfr-modal"><span aria-hidden="true">&times;</span></button>
									<p>Please <a href="<?php echo wp_login_url( home_url() ); ?>">login</a> or <a href="<?php echo wp_registration_url(); ?>"><?php _e('register', 'feature-request') ?></a><?php _e('to submit new idea.', 'feature-request') ?></p>
								</div>
							</div>
						</div>
					</div>
				<?php
			  }
	}

endif;


/**
 * Header area showing intor message and button to click to open submission modal
 * @since 1.0
 */

if ( !function_exists('avfr_submit_header') ):

	function avfr_submit_header(){

		$intro_message = avfr_get_option('avfr_welcome', 'avfr_settings_main');
		$public_can_vote = avfr_get_option('avfr_public_voting', 'avfr_settings_main');

		if ( is_user_logged_in() || $public_can_vote ): ?>

			<aside class="avfr-layout-submit">

				<div class="avfr-submit-left">

					<?php echo avfr_content_filter( $intro_message );?>

				</div>

				<div class="avfr-submit-right">

					<?php do_action('avfr_before_submit_button'); ?>

						<a href="#" data-toggle="avfr-modal" data-target=".avfr-modal" class="avfr-button avfr-trigger"><?php _e('Submit Idea','feature-request');?></a>

					<?php do_action('avfr_after_submit_button'); ?>

				</div>

			</aside>

		<?php endif;
	}

endif;


/**
 * Draw the actual voting controls
 * @since 1.0
 */

if ( !function_exists('avfr_vote_controls') ):

	function avfr_vote_controls( $post_id ) {
		
		if ( empty( $post_id ) ){
			$post_id = get_the_ID();
		}

		//get voting type
		$voting_type = avfr_get_option('avfr_voting_type','avfr_settings_features');
		//getting group of idea.
		$ideagroups = get_the_terms( $post_id, 'groups' );

		if ( 'vote' !== $voting_type ){
			//getting like/dislike limit option for each group
			$vote_limit = avfr_get_option('avfr_total_vote_limit_'.$ideagroups[0]->slug,'avfr_settings_groups');
		?>
			<a class="avfr-like avfr-vote-up" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" id="<?php echo (int) $post_id;?>" href="#"></a>
			<a class="avfr-like avfr-vote-down" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" id="<?php echo (int) $post_id;?>" href="#"></a>
			<div class="avfr-tooltip">
				<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="Enter your email to vote.">
						</p>
					<?php
						}?>
					<a class="avfr-submit" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" href="#"><?php _e('Vote it!','feature-request') ?></a>
				</div>
				<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','avfr_settings_features')); ?>!</p>
			</div>
		<?php

		} else {
		
		$voting_limit = avfr_get_option('vote_limit_'.$ideagroups[0]->slug,'if_settings_groups');
			if ( $voting_limit == '1' ) {
			?>
				<a class="avfr-like avfr-vote-up" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" href="#"></a>
				<div class="avfr-tooltip">
					<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="Enter your email to vote.">
						</p>
					<?php
						}?>
					</div>
					<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','avfr_settings_features')); ?>!</p>
				</div>
			<?php
			} else {
				?>
				<button class="avfr-vote-now" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>">Vote</button>
				<div class="avfr-tooltip">
					<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="Enter your email to vote.">
						</p>
					<?php
						}?>
						<?php 
						for ( $i=1; $i <= $voting_limit ; $i++ ) { 
							echo "<a class='avfr-votes-value' data-current-group=".$ideagroups[0]->slug." data-post-id=". (int) $post_id." data-vote=".$i." href='#'>".$i." vote</a>";
						}
					 ?>
					</div>
					<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','avfr_settings_features')); ?>!</p>
				</div>
				<?php
			}
		}
	}

endif;

/**
 * Draw the voting status
 * @since 1.0
 */

if ( !function_exists('avfr_vote_status') ):

	function avfr_vote_status( $post_id ) {

		$status = avfr_get_status( $post_id );

		if ( 'open' !== $status && false !== $status ) { ?>
			<div class="avfr-status">
				<?php echo '<span class="avfr-status_'.sanitize_html_class( $status ).'">'.esc_attr( $status ).'</span>';?>
			</div>
		<?php }
	}

endif;

						
/**
 * Flag control
 * @since 1.0
 */

if ( !function_exists('avfr_flag-control') ):

	function avfr_flag_control( $post_id ) {
		//getting group of idea.
		$ideagroups = get_the_terms( $post_id, 'groups' );
		//flag option applying
		$if_flag_disabled = avfr_get_option('if_flag','avfr_settings_features');
		if ( '1' == $if_flag_disabled ) {

			?>
			<div class="flag-show">
				<span class="dashicons dashicons-flag"></span>
				<a href="#" class="avfr-flag" data-current-group="<?php echo $ideagroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>"> <?php _e('Report this idea','feature-request'); ?></a>
			</div>
			<?php
		}
	}

endif;


/**
 * Trim excerpt
 * @since 1.0
 */

if ( ! function_exists( 'avfr_get_the_trim_excerpt' ) ) {

    // make shortcodes executable in excerpt
    function avfr_get_the_trim_excerpt( $post_id = null, $char_length = 250, $exclude_strip_shortcode_tags = null, $read_more = null ) {

        $post = get_post( $post_id );

        if ( ! isset( $post ) ) return "";

        $excerpt = $post->post_content;
        $raw_excerpt = $excerpt;
        $excerpt = apply_filters( 'the_content', $excerpt );
        $excerpt_more = apply_filters('excerpt_more', ' ...');

        if ( $read_more )
            $excerpt_more = $read_more;

        // Clean post content
        $excerpt = strip_tags( avfr_strip_shortcodes( $excerpt, $exclude_strip_shortcode_tags ) );
        $text    = avfr_get_trimmed_string( $excerpt, $char_length, $excerpt_more );

        return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
    }

}


/**
 *  Remove just shortcode tags from the given content but keep content of shortcodes
 * @since 1.0
 */

if ( !function_exists('avfr_strip_shortcodes') ) {

	function avfr_strip_shortcodes( $content, $exclude_strip_shortcode_tags = null ) {
	    if ( ! $content ) return $content;

	    if ( ! $exclude_strip_shortcode_tags )
	        $exclude_strip_shortcode_tags = avfr_exclude_strip_shortcode_tags();

	    if ( empty( $exclude_strip_shortcode_tags ) || !is_array( $exclude_strip_shortcode_tags ) )
	        return preg_replace( '/\[[^\]]*\]/', '', $content );

	    $exclude_codes = join( '|', $exclude_strip_shortcode_tags );
	    return preg_replace( "~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $content );
	}

}


/**
 * List of shortcodes to strip
 * @return array List of shortcodes to be exclude
 */

if ( !function_exists('avfr_exclude_strip_shortcode_tags') ) {

	function avfr_exclude_strip_shortcode_tags(){
	    return apply_filters( 'avfr_exclude_strip_shortcode_tags', array() );
	}

}


/**
 * add setting about the file uploaded type
 * @since 1.0
 */

if ( !function_exists('avfr_image_filter') ) {

	function avfr_image_filter( $input ) {

		if ( empty( $input ) )
			return;
		
		$allowed_image = array(
			$avfr_get_file_type = avfr_get_option('avfr_settings_features','idea_allowed_file_types')
		);

		return $allowed_image;
	}

}


/**
 * Validate that user has gravatar account
 * @since 1.0
 */

if ( !function_exists('avfr_validate_gravatar') ) {

	function avfr_validate_gravatar( $email ) {

		$hash 	 = md5( strtolower( trim($email) ) );
		$uri 	 = 'http://www.gravatar.com/' . $hash.'.php';
		$headers = @get_headers($uri);
		if ( $headers ) {

			if ( in_array('HTTP/1.1 404 Not Found', $headers) ) {
				
				$has_valid_avatar = FALSE;

			} else {

				$has_valid_avatar = TRUE;

			}

		} else {

			$has_valid_avatar = FALSE;
		}

		return $has_valid_avatar;
	}

}


/**
 * Get author avatar from gravatar
 * @since 1.0
 */

if ( !function_exists('avfr_get_author_avatar') ) {

	function avfr_get_author_avatar( $post_id ) {

		$author_email 	= get_post_meta( $post_id, '_author_email', true );

		if ( '' == $author_email ) {

			$author_email = get_the_author_meta('email');
			$author_link  = get_author_posts_url( get_the_author_meta( 'ID' ) ).'?post_type=ideas';
		
		} else {

			$author_link  = get_post_type_archive_link( 'ideas' ).'?meta=my&val='.base64_encode($author_email);
		
		}

		$author_avatar = get_avatar( $author_email );

		printf( '<a href="%s">%s</a>', $author_link, $author_avatar );
	}

}


/**
 * Get author name from local profile or gravatar
 * @since 1.0
 */

if ( !function_exists('avfr_get_author_name') ) {

	function avfr_get_author_name( $post_id ) {

		$author_email = get_post_meta( $post_id, '_author_email', true );
		
		if ( '' == $author_email ) {

			$author_email = get_the_author_meta('email');
			$author_name  = get_the_author_meta('display_name');
			$author_link  = get_author_posts_url( get_the_author_meta( 'ID' ) ).'?post_type=ideas';
		
		} else {

			$author_hash  = md5(strtolower($author_email));
			$author_link  = get_post_type_archive_link( 'ideas' ).'?meta=my&val='.base64_encode($author_email);

			if ( FALSE !== validate_gravatar( $author_email ) ) {

				$str 		 = file_get_contents( 'http://www.gravatar.com/'.$author_hash.'.php' );
			
			}

			if ( !empty($str) ) {

				$profile 	 = unserialize( $str );

				if ( is_array( $profile ) && isset( $profile['entry'] ) ) {

					$author_name = $profile['entry'][0]['displayName'];
				
				} else {

					$author_name = $profile['displayName'];

				}

			} else {

				$author_name = 'Guest';
			
			}

		}
		printf( '<a href="%s">%s</a>', $author_link, $author_name );
	}

}