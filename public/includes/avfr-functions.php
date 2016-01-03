<?php
/**
 * Functions that used entire plugin
 * 
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */

if ( !function_exists('avfr_get_status') ) {

	function avfr_get_status( $post_id ) {

		if ( empty( $post_id ) )
			return;

		$status = get_post_meta( $post_id, '_avfr_status', true );

		return !empty( $status ) ? $status : false;
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

		$total_votes = get_post_meta( $post_id, '_avfr_total_votes', true );

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

		$votes = get_post_meta( $post_id, '_avfr_votes', true );

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
 * Change query based on query vars in URL
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
	 				$query->set( 'meta_key', '_avfr_author_email' );
		 			$query->set( 'meta_value', base64_decode($val) );
	 			}
	 			$query->set( 'author', get_current_user_id() );

	 		} elseif ( '_avfr_status' === $meta ) {
	 			if ('all' === $val ) {
	 				//continue
	 			} else {
		 			$query->set( 'meta_key', $meta );
		 			$query->set( 'meta_value', $val );
	 			}

	 		} elseif ( '_avfr_votes' === $meta ) {

	 			$query->set( 'meta_key', $meta );

	 		} elseif ( 'hot' === $meta ) {
	 			global $avfr_db;
	 			$orderby_hot = $avfr_db->avfr_order_features_hot();
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
 * Localizing arguments
 * @since 1.0
 */

if ( !function_exists('avfr_localized_args') ) {

	function avfr_localized_args( $max = '', $paged = '' ){

		global $wp_query, $post;
		$current_user = wp_get_current_user();

		$args = array(
			'ajaxurl' 		  => admin_url('admin-ajax.php'),
			'nonce'			  => wp_create_nonce('feature_request'),
			'user_email' 	  => $current_user->user_email,
			'label'			  => apply_filters('avfr_loadmore_label', __('Load more ...', 'feature-request')),
			'label_loading'   => apply_filters('avfr_loadmore_loading', __('Loading ...', 'feature-request')),
			'thanks_voting'   => apply_filters('avfr_thanks_voting', __('Thanks for voting!', 'feature-request')),
			'already_voted'   => apply_filters('avfr_already_voted', __('You have already voted!', 'feature-request')),
			'confirm_flag'	  => apply_filters('avfr_confirm_flag', __('Are you sure to report this feature as inappropriate ?', 'feature-request')),
			'reached_limit'   => apply_filters('avfr_reached_limit', __('You are reached voting limit for this groups of features.', 'feature-request')),
			'startPage'		  => $paged,
			'maxPages' 		  => $max,
			'nextLink' 		  => next_posts($max, false)
		);

		return apply_filters('avfr_localized_args', $args );

	}

}

/*--- PLUGGABLES ---*/


/**
 * Submit modal box
 * @since 1.0
 */

if ( !function_exists('avfr_submit_box') ):

	function avfr_submit_box( $groups = '' ) {

		$public_can_vote = avfr_get_option('avfr_public_voting','avfr_settings_main');
		$userid 		 = $public_can_vote && !is_user_logged_in() ? 1 : get_current_user_ID();
		$exluded  		 = '';
		if ( is_user_logged_in() || 'on' == $public_can_vote ) { 
			
			$allgroups = get_terms('groups', array('hide_empty' => 0, ));
			foreach ( $allgroups as $exclude ) {
				if ( 'on' == avfr_get_option('avfr_disable_new_for'.$exclude->slug,'avfr_settings_groups') ) {
					$exluded[]=$exclude->term_id;
				}
			}

			$args = array(
				'show_option_all'    => '',
				'show_option_none'   => '',
				'option_none_value'  => '-1',
				'orderby'            => 'Name', 
				'order'              => 'ASC',
				'hide_empty'         => 0, 
				'include'			 => $groups,
				'exclude'            => $exluded,
				'echo'               => 1,
				'selected'           => 0,
				'hierarchical'       => 0, 
				'name'               => 'group',
				'class'              => 'featureroup',
				'taxonomy'           => 'groups',
				'hide_if_empty'      => false,
				'value_field'	     => 'name',	
				); ?>
			<div class="avfr-modal" id="avfr-modal" aria-hidden="true" tabindex="-1">
				<a href="#close" type="button" class="close" id="avfr-close" aria-hidden="true"></a>
				<div class="avfr-modal-dialog ">
				    <div class="avfr-modal-content">
				    	<div class="avfr-modal-header">
				    	<a href="#close" type="button" class="modal-close" id="avfr-close">
						<span aria-hidden="true">&times;</span>
						</a>
				    		<h3 class="avfr-modal-title"><?php _e('Submit feature','feature-request');?></h3>
				    	</div>
				    	<div class="avfr-modal-body">

							<form id="avfr-entry-form" method="post" enctype="multipart/form-data">
								<div id="avfr-form-group" class="form-input-group">
								<label for="avfr-title">
									<?php _e('Submit feature for:','feature-request');?>
								</label>
								<?php if ( !is_archive() && !is_single() && !empty($groups) && count( explode(',', $groups) ) == 1 ) {
								 	$group_name = get_term( $groups, 'groups' );
								 	echo $group_name->name;
								 	echo "<input name='group' type='hidden' value=".$group_name->slug.">";
									} else { ?> <span class="triangle-down"> <?php wp_dropdown_categories( $args ); } ?></span></div>
								
								<script type="text/javascript">
								jQuery(document).ready(function($){

								    $('#tags-data-list')
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
									<div id="avfr-form-email">
										<label for="avfr-entryform_email">
											<?php apply_filters('avfr_form_Email', _e('Email','feature-request'));?>
										</label>
									<input id="avfr-entry-form-email" type="text" name="avfr-email" value="" placeholder="Email"></div>
								<?php
								}
								?>
								
								<div id="avfr-form-title" class="form-input-group">
									<label for="avfr-entryform-title">
										<?php apply_filters('avfr_form_title', _e('Title','feature-request'));?>
									</label>
									<input id="avfr-entryform-title" type="text" name="avfr-title" value="" placeholder="My Awesome Submission">
								</div>

								<div id="avfr-form-desc" class="form-input-group">
									<label for="avfr-entryform-description">
										<?php apply_filters('avfr_form_description', _e('Description','feature-request'));?>
									</label>
									<textarea id="avfr-entryform-description" name="avfr-description" value="" placeholder="<?php _e('Make the description meaningful!', 'feature-request') ?>"></textarea>		
  								</div>

  								<div id="avfr-form-tags" class="form-input-group">
	  								<label for="tags-data-list">
	  									<?php apply_filters('avfr_form_title', _e('Feature tags:','feature-request'));?>
	  								</label>
								<textarea name="avfr-tags" id="tags-data-list" rows="1"></textarea>
								</div>

  								<?php $disable_upload = avfr_get_option('avfr_disable_upload','avfr_settings_fetures') ?>
  								<?php if ( 'on' != $disable_upload ) : ?>

  								<div id="avfr-form-upload" class="form-input-group">
  								  	<p class="avfr-upload-tip">
  										<?php echo avfr_get_option('avfr_echo_type_size','avfr_settings_features');?>
  										
  									</p>
  									<label for="avfr-upload-form">
  										<?php apply_filters('avfr_form_upload', _e('Select file to upload:','feature-request')); ?>
  									</label>
  									<input id="avfr-upload-form" type="file"  name='avfr-upload'>
  								</div>

								<?php endif; ?>

								<?php if ( 'on' != avfr_get_option('avfr_disable_captcha', 'avfr_settings_main') ) : ?>
								<div id="avfr-form-captcha" class="form-input-group">
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
					<div class="avfr-modal" id="avfr-modal" aria-hidden="true" tabindex="-1">
						<a href="#close" type="button" class="close" id="avfr-close" aria-hidden="true"></a>
						<div class="avfr-modal-dialog ">
							<div class="avfr-modal-content">
								<div class="avfr-modal-body">
									<p>
										<?php _e('Please', 'feature-request'); ?> <a href="<?php echo wp_login_url( home_url() ); ?>"><?php _e('login', 'feature-request'); ?></a> <?php _e('or', 'feature-request'); ?> <a href="<?php echo wp_registration_url(); ?>"><?php _e('register', 'feature-request') ?></a><?php _e('to submit new feature request.', 'feature-request') ?>
										<a href="#close" type="button" class="modal-close" id="avfr-close"><span aria-hidden="true">&times;</span></a>
									</p>
								</div>
							</div>
						</div>
					</div>
				<?php
			  }
	}

endif;


/**
 * Header area showing intro message and button to click to open submission modal
 * @since 1.0
 */

if ( !function_exists('avfr_submit_header') ):

	function avfr_submit_header(){

		$intro_message = avfr_get_option('avfr_welcome', 'avfr_settings_main');
		?>
			<aside class="avfr-layout-submit">

				<div class="avfr-submit-left">

					<?php echo avfr_content_filter( $intro_message );?>

				</div>

				<div class="avfr-submit-right">

					<?php do_action('avfr_before_submit_button'); ?>

						<a href="#avfr-modal" class="avfr-button avfr-trigger"><?php _e('Submit feature','feature-request');?></a>

					<?php do_action('avfr_after_submit_button'); ?>

				</div>

			</aside>
<?php }
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
		$voting_type = avfr_get_option('avfr_voting_type','avfr_settings_main');
		//getting group of feature.
		$featuregroups = get_the_terms( $post_id, 'groups' );

		if ( 'vote' !== $voting_type ){
			//getting like/dislike limit option for each group
			$vote_limit = avfr_get_option('avfr_total_vote_limit_'.$featuregroups[0]->slug,'avfr_settings_groups');
		?>
			<a class="avfr-like avfr-vote-calc avfr-vote-up" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" id="<?php echo (int) $post_id;?>" href="#"></a>
			<a class="avfr-like avfr-vote-calc avfr-vote-down" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" id="<?php echo (int) $post_id;?>" href="#"></a>
			<div class="avfr-tooltip">
				<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="<?php _e('Enter your email to vote.', 'feature-request'); ?>">
						</p>
					<?php
						}?>
					<a class="avfr-submit" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" href="#"><?php _e('Vote it!','feature-request') ?></a>
				</div>
				<p class="small-text"><?php _e('You have <span>...</span> votes left in this category for this ', 'feature-request'); echo strtolower(avfr_get_option('avfr_votes_limitation_time','avfr_settings_main')); ?>!</p>
			</div>
		<?php

		} else {
		
		$voting_limit = avfr_get_option('avfr_vote_limit_'.$featuregroups[0]->slug,'avfr_settings_groups');
			if ( $voting_limit == '1' ) {
			?>
				<a class="avfr-like avfr-vote-calc avfr-vote-up" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>" href="#"></a>
				<div class="avfr-tooltip">
					<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="<?php _e('Enter your email to vote.', 'feature-request'); ?>">
						</p>
					<?php
						}?>
						<?php 
						for ( $i=1; $i <= $voting_limit ; $i++ ) { 
							if ($i == 1) {
								$ivote = __('1 vote', 'feature-request');
							} else {
								$ivote = $i.__(' votes', 'feature-request');
							}
							echo "<a class='avfr-votes-value' data-current-group=".$featuregroups[0]->slug." data-post-id=". (int) $post_id." data-vote=".$i." href='#'>".$ivote."</a>";
						}
					 ?>
					</div>
					<p class="small-text"><?php _e('You have <span>...</span> votes left in this category for this ', 'feature-request'); echo strtolower(avfr_get_option('avfr_votes_limitation_time','avfr_settings_main')); ?>!</p>
				</div>
			<?php
			} else {
				?>
				<button class="avfr-vote-now avfr-vote-calc" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>"><?php _e('Vote', 'feature-request') ?></button>
				<div class="avfr-tooltip">
					<div class="voting-buttons">
					<?php 
						if ( !is_user_logged_in() ) { ?>
						<p class="voting-buttons-title">
							<input type="text" name="voter-email" class="voter-email" placeholder="<?php _e('Enter your email to vote.', 'feature-request'); ?>">
						</p>
					<?php
						}?>
						<?php 
						for ( $i=1; $i <= $voting_limit ; $i++ ) { 
							if ($i == 1) {
								$ivote = __('1 vote', 'feature-request');
							} else {
								$ivote = $i.__(' votes', 'feature-request');
							}
							echo "<a class='avfr-votes-value' data-current-group=".$featuregroups[0]->slug." data-post-id=". (int) $post_id." data-vote=".$i." href='#'>".$ivote."</a>";
						}
					 ?>
					</div>
					<p class="small-text"><?php _e('You have <span>...</span> votes left in this category for this ', 'feature-request');  echo strtolower(avfr_get_option('avfr_votes_limitation_time','avfr_settings_main')); ?>!</p>
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
				<?php echo '<span class="avfr-status-'.sanitize_html_class( $status ).'">'.esc_attr( $status ).'</span>';?>
			</div>
		<?php }
	}

endif;

						
/**
 * Flag control
 * @since 1.0
 */

if ( !function_exists('avfr_flag_control') ):

	function avfr_flag_control( $post_id, $ip, $userid ) {
		global $avfr_db;
		//getting group of features.
		$featuregroups = get_the_terms( $post_id, 'groups' );

		//flag option applying
		$flag_show = avfr_get_option('avfr_flag','avfr_settings_main');
		if ( 'on' == $flag_show ) { ?>
			<div class="flag-show">
				<span class="dashicons dashicons-flag"></span>
				<?php
				if ( !$avfr_db->avfr_has_vote_flag( $post_id, $ip, $userid, 'flag' ) ) { ?>
					<a href="#" class="avfr-flag" data-current-group="<?php echo $featuregroups[0]->slug; ?>" data-post-id="<?php echo (int) $post_id;?>"> <?php _e('Report this feature request','feature-request'); ?></a>
				<?php
				} else { ?>
					<span href="#"> <?php _e('Reported!','feature-request'); ?></span>
		  <?php } ?>
			</div>
		<?php
		}
	}

endif;


/**
 * Trim string by character length
 *
 * @param string  $string  The string to trim
 * @param integer $max_length  The width of the desired trim.
 * @param $string $more  A string that is added to the end of string when string is truncated.
 *
 * @return string The trimmed string
 */
if ( ! function_exists( 'avfr_get_trimmed_string') ) {

    function avfr_get_trimmed_string( $string, $max_length = 1000, $more = ' ...' ){
        return function_exists( 'mb_strimwidth' ) ? mb_strimwidth( $string, 0, $max_length, '' ) . $more : substr( $string, 0, $max_length ) . $more;
    }

}

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
			$avfr_get_file_type = avfr_get_option('avfr_allowed_file_types', 'avfr_settings_features')
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

		$author_email 	= get_post_meta( $post_id, '_avfr_author_email', true );

		if ( '' == $author_email ) {

			$author_email = get_the_author_meta('email');
			$author_link  = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ).'?post_type=avfr' );
		
		} else {

			$author_link  = esc_url( add_query_arg( array( 'meta' => 'my', 'val' => base64_encode($author_email) ), get_post_type_archive_link( 'avfr' ) ) );
		
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

		$author_email = get_post_meta( $post_id, '_avfr_author_email', true );
		
		if ( '' == $author_email ) {

			$author_email = get_the_author_meta('email');
			$author_name  = esc_html( get_the_author_meta('display_name') );
			$author_link  = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ).'?post_type=avfr' );
		
		} else {

			$author_hash  = md5(strtolower($author_email));
			$author_link  = esc_url( add_query_arg( array( 'meta' => 'my', 'val' => base64_encode($author_email) ), get_post_type_archive_link( 'avfr' ) ) );

			if ( FALSE !== avfr_validate_gravatar( $author_email ) ) {

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

if ( !function_exists('avfr_show_filters') ) {

	function avfr_show_filters() {
		$all_terms    	 = get_terms( 'groups', array( 'hide_empty' => false ) );
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
									echo "<option value=".esc_url( add_query_arg( array( 'groups' => $all_term->slug ), get_post_type_archive_link( 'avfr' ) ) ).">".$all_term->name."</option>";
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
							<option value="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_status', 'val' => 'all' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('All','feature_request') ?></option>
							<option value="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_status', 'val' => 'open' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Open','feature_request') ?></option>
							<option value="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_status', 'val' => 'approved' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Approve','feature_request') ?></option>
							<option value="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_status', 'val' => 'completed' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Completed','feature_request') ?></option>
							<option value="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_status', 'val' => 'declined' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Decline','feature_request') ?></option>
						</select>
					</span>
					</li>
					<?php if ( is_user_logged_in() ) { ?>
						<li class="avfr-filter-control-item"><a href="<?php echo esc_url( add_query_arg( array( 'meta' => 'my' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('My Features','feature_request') ?></a></li>
					<?php
					} ?>
					<li class="avfr-filter-control-item"><a href="<?php echo esc_url( add_query_arg( array( 'meta' => 'hot' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Hot','feature_request') ?></a></li>
					<li class="avfr-filter-control-item"><a href="<?php echo esc_url( add_query_arg( array( 'meta' => '_avfr_votes' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('Top','feature_request') ?></a></li>
					<li class="avfr-filter-control-item"><a href="<?php echo esc_url( add_query_arg( array( 'meta' => 'date' ), get_post_type_archive_link( 'avfr' ) ) ); ?>"><?php _e('New','feature_request') ?></a></li>
					<?php
					if ( current_user_can('manage_options') && is_single() ) { 
						$id = get_the_ID();
						?>
						<div class="status-changing">
						<span class="triangle-down">
							<select name="statusChanging" class="change-status-select" data-post-id="<?php echo (int) $id;?>">
								<option value="open"><?php _e('Open','feature_request') ?></option>
								<option value="approved"><?php _e('Approve','feature_request') ?></option>
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


	}

}

function avfr_save_option_for_group($term_id, $tt_id) {

	$groups_option =  get_option('avfr_settings_groups', '0');
	if (!is_array($groups_option)) { $groups_option = array(); }
	$catinfo = get_term_by( 'id', $term_id, 'groups' );
	$new_group_slug = $catinfo->slug;
	// Default options for default category that added above
	$new_options = array( 'avfr_vote_limit_'.$new_group_slug => '3',
		'avfr_total_vote_limit_'.$new_group_slug => '30',
		'avfr_disable_comment_for'.$new_group_slug => 'off',
		'avfr_disable_new_for'.$new_group_slug => 'off',
	);

	update_option('avfr_settings_groups', array_merge($groups_option, $new_options), 'no');

}

add_action('create_groups', 'avfr_save_option_for_group', 10, 3);