<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

wp_enqueue_style( 'corecss', AVFR_URL. ('/public/assets/css/textext.core.css') );
wp_enqueue_style( 'arrowcss', AVFR_URL. ('/public/assets/css/textext.plugin.arrow.css ') );
wp_enqueue_style( 'autocompleteecss', AVFR_URL. ('/public/assets/css/textext.plugin.autocomplete.css') );
wp_enqueue_style( 'clearcss', AVFR_URL. ('/public/assets/css/textext.plugin.clear.css') );
wp_enqueue_style( 'focusscss', AVFR_URL. ('/public/assets/css/textext.plugin.focus.css') );
wp_enqueue_style( 'promptcss', AVFR_URL.('/public/assets/css/textext.plugin.prompt.css') );
wp_enqueue_style( 'tagscss', AVFR_URL. ('/public/assets/css/textext.plugin.tags.css') );

function avfr_get_status( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$status = get_post_meta( $postid, '_avfr_status', true );

	return !empty( $status ) ? $status : false;
}

function get_voters_id( $postid ) {
	if ( empty( $postid ) )
			return;

	global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT userid FROM '.$table.' WHERE userid!="0" AND postid ="%d" AND type="vote"', $postid );

   	$result =  $wpdb->get_col( $sql );

   	return $result;
		
}

/**
*
*	Get the total number of votes for a specific idea
*
*/
function avfr_get_total_votes( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$total_votes = get_post_meta( $postid, '_avfr_total_votes', true );

	return !empty( $total_votes ) ? $total_votes : false;
}

/**
*
*	Get the number of vote up votes for a specific idea
*
*/
function avfr_get_votes( $postid = 0 ) {

	if ( empty( $postid ) )
		return;

	$votes = get_post_meta( $postid, '_avfr_votes', true );

	return !empty( $votes ) ? $votes : false;
}

/**
*
*	Grab an optoin from our settings
*
*/
function avfr_get_option( $option, $section, $default = '' ) {

	if ( empty( $option ) )
		return;

    $options = get_option( $section );


    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/**
*	Used on the front end to properly escape attributes where users have control over what input is entered
*	as well as through a callback upon saving in the backend
*/
function avfr_media_filter( $input = '' ) {

	// bail if no input
	if ( empty( $input ) )
		return;

	// setup our array of allowed content to pass
	$allowed_html = array(
		'a' 			=> array(
		    'href' 		=> array(),
		    'title' 	=> array(),
		    'rel'		=> array(),
		    'target'	=> array(),
		    'name' 		=> array()
		),
		'img'			=> array(
			'src' 		=> array(),
			'alt'		=> array(),
			'title'		=> array()
		),
		'p'				=> array(),
		'br' 			=> array(),
		'em' 			=> array(),
		'strong' 		=> array()
	);

	$out = wp_kses( $input, apply_filters('avfr_allowed_html', $allowed_html ) );

	return $out;
}

function order_avfr_hot()
{
	global $wpdb;
	$final_array 	= [];
    $table 			= $wpdb->base_prefix.'av_feature_request';
    $type 			= 'vote';
   	$sql 			=  $wpdb->prepare( 'SELECT postid, SUM(votes) as sum FROM '.$table.' WHERE type="%s" AND time>=(CURDATE() - INTERVAL 14 DAY) GROUP BY postid', $type );
   	$postids 		=  $wpdb->get_col( $sql, 0 );
   	$post_votes 	= $wpdb->get_col( $sql, 1 );
   	$initial_array 	= array_combine($postids, $post_votes);
   	foreach ( $initial_array as $key => $value ) {
   		$final_array[$key] = $value /= 336;
   	}
   	arsort( $final_array );
   	return array_keys($final_array);
}

/**
*
*	Modify the post type archive to return results based on number of votes
*
*/
add_action( 'pre_get_posts', 'avfr_archive_query');
function avfr_archive_query( $query ) {

	if ( is_admin() || ! $query->is_main_query() )
        return;

 	if ( is_post_type_archive( 'avfr' ) ) {

 		$order_by = 'meta_value_num';

 		if ( 'my' === $_GET['meta'] ) {

 			$query->set( 'author', get_current_user_id() );

 		} elseif ( '_avfr_status' === $_GET['meta'] ) {
 			if ('all' === $_GET['val'] ) {
 				//continue
 			} else {
	 			$query->set( 'meta_key', $_GET['meta'] );
	 			$query->set( 'meta_value', $_GET['val'] );
 			}

 		} elseif ( '_avfr_votes' === $_GET['meta'] ) {

 			$query->set( 'meta_key', $_GET['meta'] );

 		} elseif ( 'hot' === $_GET['meta'] ) {

 			$orderby_hot = order_ideas_hot();
 			$query->set( 'post__in' , $orderby_hot );
 			$order_by = 'post__in';
 		}

        $query->set( 'orderby', $order_by );
        $query->set( 'order', 'DESC' );

        return;
    }
}

/**
*
*	Determine if we're on the avfr post type and also account for their being no entries
*	as our post type archive still has to work regardless
*
*/
function avfr_is_archive(){

	global $post;

	$label 			= avfr_get_option('if_domain','if_settings_main','avfr');
	$url 			= isset($_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) ? $_SERVER['REQUEST_URI'] : '';
	$is_empty_idea 	= $url ? substr($url,-6) == '/'.esc_attr( trim( $label ) ).' ' || substr($url,-7) == '/'.esc_attr( trim( $label ) ).'/' : null;

	if ( 'avfr' == get_post_type() || $is_empty_idea ):

		return true;

	else:

		return false;

	endif;

}

/**
*
*	Determines if the voting controls should be shown or not based on if the
*	user has voted, is logged in, and status is approved
*
*/
function avfr_is_voting_active( $postid = '' , $ip , $userid ) {


	$status      	 = avfr_get_status( $postid );

	$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');


	if ( ( ( false == avfr_has_voted( $postid, $ip, $userid ) && is_user_logged_in() ) || ( ( false == avfr_has_voted( $postid, $ip, $userid ) ) && !is_user_logged_in() && "on" == $public_can_vote) ) && 'open' === $status ){

		return true;

	} else {

		return false;
	}
}

/**
* 	Adds a vote entry into the databse
*/
function avfr_add_vote( $args = array() ) {

	$db = new featurerequestDB;

	$defaults = array(
		'postid' => get_the_ID(),
		'time'   => current_time('timestamp'),
		'ip'   	 => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
		'userid' => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
		'type'   => 'vote',
	);

	$args = array_merge( $defaults, $args );

	$db->insert( $args );

}

/**
*
*	Has the public user voted
*
*/
function avfr_has_voted( $postid = '', $ip = '' , $userid = '0' ) {

	if ( empty( $postid ) )
		return;

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	if ( empty( $userid ) )
		$userid = '0';


    global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT * FROM '.$table.' WHERE ip ="%s" AND userid="%s" AND postid ="%d" AND type="vote"', $ip, $userid, $postid );

   	$result =  $wpdb->get_results( $sql );

	if ( $result ) {

		return true;

	} else {

		return false;

	}
}
/**
*
*	Has flagged by user that not logged in
*
*/
function avfr_has_public_flag( $postid = '', $ip = '' ) {

	if ( empty( $postid ) )
		return;

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;


    global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT * FROM '.$table.' WHERE ip ="%s" AND postid ="%d" AND type="flag"', $ip, $postid );

   	$result =  $wpdb->get_results( $sql );

	if ( $result ) {

		return true;

	} else {

		return false;

	}
}
/**
*
*	Calculate total votes for each ip
*
*/
function avfr_total_votes_MONTH( $ip = '', $userid = '', $avfr_voted_group = '' ) {

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

    global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ip ="%s" AND userid="%s" AND groups ="%s" AND type="vote" AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $ip, $userid, $avfr_voted_group );

   	$total =  $wpdb->get_col( $sql );

		return array_sum($total);
}

function avfr_total_votes_YEAR( $ip = '', $userid = '', $avfr_voted_group = '' ) {

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

    global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ip ="%s" AND userid="%s" AND groups ="%s" AND type="vote" AND YEAR(time)=YEAR(CURDATE())', $ip, $userid, $avfr_voted_group );

   	$total =  $wpdb->get_col( $sql );

		return array_sum($total);
}

function avfr_total_votes_WEEK( $ip = '', $userid = '', $avfr_voted_group = '' ) {

	if ( empty( $ip ) )
		$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

    global $wpdb;

    $table = $wpdb->base_prefix.'av_feature_request';

   	$sql =  $wpdb->prepare('SELECT votes FROM '.$table.' WHERE ip ="%s" AND userid="%s" AND groups ="%s" AND type="vote" AND YEARWEEK(time)=YEARWEEK(CURDATE()) AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $ip, $userid, $avfr_voted_group );

   	$total =  $wpdb->get_col( $sql );

		return array_sum($total);
}

/**
* 	Adds a public flag entry into the databse
*/
function avfr_add_public_flag( $args = array() ) {

	$db = new featurerequestDB;

	$defaults = array(
		'postid' => get_the_ID(),
		'time'   => current_time('timestamp'),
		'ip'   	 => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0,
		'type'   	 => 'flag',
	);

	$args = array_merge( $defaults, $args );

	$db->insert( $args );

}

/**
*
*	The variables being localized
*
*/
function avfr_localized_args( $max = '', $paged = '' ){

	global $wp_query, $post;

	$args = array(
		'ajaxurl' 		  => admin_url( 'admin-ajax.php' ),
		'nonce'			  => wp_create_nonce('feature_request'),
		'error_message'   => apply_filters('avfr_error',__('Awww snap, something went wrong!','Feature-request')),
		'label'			  => apply_filters('avfr_loadmore_label',__('Load more ideas','Feature-request')),
		'label_loading'   => apply_filters('avfr_loadmore_loading',__('Loading ideas...','Feature-request')),
		'thanks_voting'   => apply_filters('avfr_thanks_voting',__('Thanks for voting!','Feature-request')),
		'already_voted'   => apply_filters('avfr_already_voted',__('You have already voted!','Feature-request')),
		'already_flagged' => apply_filters('avfr_already_flagged',__('You have already flagged this idea!','Feature-request')),
		'thanks_flag'	  => apply_filters('avfr_thanks_flag',__('Reported!','Feature-request')),
		'reached_limit'   => apply_filters('avfr_reached_limit',__('You are reached voting limit for this groups of ideas.','Feature-request')),
		'startPage'		  => $paged,
		'maxPages' 		  => $max,
		'nextLink' 		  => next_posts($max, false)
	);

	return apply_filters('avfr_localized_args', $args );

}

/**
*
*
*	ALL PLUGGABLE BELOW
*
*/

/**
*
*	The modal used to show the idea submission form
*	
*/
if ( !function_exists('avfr_submit_modal') ):

	function avfr_submit_modal( $groups = '' ) {

		$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');

		$userid 		= $public_can_vote && !is_user_logged_in() ? 1 : get_current_user_ID();

		if ( is_user_logged_in() || 'on' == $public_can_vote ) { 
			
			$allgroups = get_terms('groups', array('hide_empty' => 0, ));
			foreach ( $allgroups as $exclude ) {
				if ( 'on' == avfr_get_option('disable_new_for'.$exclude->slug,'if_settings_groups') ) {
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
			<div class="avfr-factory-modal" tabindex="-1">
				<div class="avfr-modal-dialog ">
				    <div class="avfr-modal-content">
						<button type="button" class="close" data-dismiss="avfr-modal"><span aria-hidden="true">&times;</span></button>

				    	<div class="avfr-modal-header">
				    		<h3 class="avfr-modal-title"><?php apply_filters('avfr_submit_idea_label', _e('Submit Feature','Feature-request'));?></h3>
				    	</div>
				    	<div class="avfr-modal-body">

							<form id="avfr--entry--form" method="post" enctype="multipart/form-data">
								<div id="avfr_form_group"><label for="avfr-title"><?php apply_filters('avfr_form_title', _e('Submit feature for :','Feature-request'));?></label>
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
												    'exclude'           => array(), 
												    'exclude_tree'      => array(), 
												    'include'           => array(),
												    'number'            => '', 
												    'fields'            => 'all', 
												    'slug'              => '',
												    'parent'            => '',
												    'childless'         => false,
												    'get'               => '', 
												    'name__like'        => '',
												    'description__like' => '',
												    'pad_counts'        => false, 
												    'offset'            => '', 
												    'search'            => '', 
												    'cache_domain'      => 'core'
												); 

										        $terms = get_terms('featuretags',$avfr_modal_tag );
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

								<?php do_action('avfr_inside_form_top');?>

								<div id="avfr_form_title"><label for="avfr-title"><?php apply_filters('avfr_form_title', _e('Title','Feature-request'));?></label>
								<input id="avfr--entryform_title" type="text" name="avfr-title" value="" placeholder="My Awesome Submission" ></div>
								<div id="avfr_form_desc"><label for="avfr-description"> <?php apply_filters('avfr_form_description', _e('Description','feature-request'));?></label>
								<textarea id="avfr--entryform_description" form="avfr--entry--form" name="avfr-description" value="" placeholder="Make the description meaningful!"></textarea>		
  								</div>
  								<div id="avfr_form_tags"><label for="avfr-title"><?php apply_filters('avfr_form_title', _e('Feature tags :','feature-request'));?></label>
								<textarea name="avfr-tags" id="tags-data-list" rows="1" ></textarea> </div>
  								<?php $disable_upload = avfr_get_option('if_disable_upload','if_settings_avfr') ?>
  								<?php if ( 'on' != $disable_upload ) : ?>
  								<div id="avfr_form_upload"><label for="jpg-file"><?php _e('Select file to upload:','feature_request'); ?></label>
  								<?php echo  avfr_get_option('avfr_echo_type_size','if_settings_avfr');?>
  								</br>
  								<input id="avfr-upload-form" type="file"  name='avfr-upload'>
  								</div>	
								<!--File Upload-->
								<?php endif; ?>

								<?php if ( 'on' != avfr_captcha() ) : ?>
								<div id="avfr_form_captcha">
								      <label for="captcha"><?php _e('Captcha','feature_request') ?></label>
								      <input id="captcha" type="text" name="captcha" value="" maxlength="4" size="40" />
								      <img id="imgCaptcha" src="<?php echo AVFR_URL; ?>/public/includes/create_image.php" />
								      <img id='reload' src="<?php echo AVFR_URL; ?>/public/assets/image/refresh.png" alt="Refresh" >
								</div>
								<?php endif; ?> 
															
								<?php do_action('avfr_inside_form_bottom');?>
								
								<input type="hidden" name="action" value="process_entry">
								<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('if-entry-nonce'); ?>"/>

								<div class="avfr-modal-footer">
									<input id="avfr--btn" class="avfr-button" type="submit" value="<?php apply_filters('avfr_submit_label', _e('Submit','feature-request'));?>">
									<div id="avfr--entry--form-results"><p></p></div>
								</div>
							</form>

						</div>
					</div>
				</div>
			</div>

		<?php } else { ?>
					<div class="avfr-factory-modal" tabindex="-1">
						<div class="avfr-modal-dialog ">
							<div class="avfr-modal-content">
								<div class="avfr-modal-body">
								<button type="button" class="close" data-dismiss="avfr-modal"><span aria-hidden="true">&times;</span></button>
									<p>Please <a href="<?php echo wp_login_url( home_url() ); ?>">login</a> or <a href="<?php echo wp_registration_url(); ?>">register</a> to submit new feature.</p>
								</div>
							</div>
						</div>
					</div>
				<?php
			  }
	}

endif;
/**
*
*	disable captcha function
*
*/
	function avfr_captcha (){
	  $disable_captcha = avfr_get_option('if_disable_captcha','if_settings_avfr') ; 
	 return $disable_captcha;
	}

/**
*
*	Header area showing intor message and button to click to open submission modal
*
*/
if ( !function_exists('avfr_submit_header') ):

	function avfr_submit_header(){

		$intro_message = avfr_get_option('if_welcome','if_settings_main',apply_filters('idea_factory_default_message', __('Submit and vote for new features!','feature-request')));
		$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');

		if ( is_user_logged_in() || $public_can_vote ): ?>

			<aside class="avfr--layout-submit">

				<div class="avfr--submit-left">

					<?php echo avfr_media_filter( $intro_message );?>

				</div>

				<div class="avfr--submit-right">

					<?php do_action('avfr_before_submit_button'); ?>

						<a href="#" data-toggle="avfr-modal" data-target=".avfr-modal" class="avfr--button avfr-trigger"><?php _e('Submit feature','feature-request');?></a>

					<?php do_action('avfr_after_submit_button'); ?>

				</div>

			</aside>

		<?php endif;
	}

endif;


/**
*	Draw teh actual voting controls
*
*/
if ( !function_exists('avfr_vote_controls') ):

	function avfr_vote_controls( $postid = '' ) {
		
		if ( empty( $postid ) ){
			$postid = get_the_ID();
		}

		//check voting type
		$voting_type = avfr_get_option('voting_type','if_settings_avfr','');
		//getting group of idea.
		$avfrgroups = get_the_terms( $postid, 'groups' );

		if ( $voting_type!=='vote' ){
			//getting like/dislike limit option for each group
			$like_limit = avfr_get_option('like_limit_'.$avfrroups[0]->slug,'if_settings_groups','');
		?>
			<a class="avfr-like avfr-vote-up" data-current-group="<?php echo $avfrgroups[0]->slug; ?>" data-post-id="<?php echo (int) $postid;?>" href="#"></a>
			<a class="avfr-like avfr-vote-down" data-current-group="<?php echo $avfrgroups[0]->slug; ?>" data-post-id="<?php echo (int) $postid;?>" href="#"></a>
			<div class="idea-factory-tooltip">
				<div class="voting-buttons"></div>
				<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','if_settings_avfr')); ?>!</p>
			</div>
		<?php

		} else { //if voting type = votes
		//getting vote limit option
		$voting_limit = avfr_get_option('vote_limit_'.$avfrgroups[0]->slug,'if_settings_groups','');
			if ( $voting_limit==1 ) {
			?>
				<a class="avfr-like avfr-vote-up" data-current-group="<?php echo $avfrroups[0]->slug; ?>" data-post-id="<?php echo (int) $postid;?>" href="#"></a>
				<div class="avfr-tooltip">
					<div class="voting-buttons"></div>
					<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','if_settings_avfr')); ?>!</p>
				</div>
			<?php
			} else {
				?>
				<button class="avfr-vote-now" data-current-group="<?php echo $avfrgroups[0]->slug; ?>" data-post-id="<?php echo (int) $postid;?>">Vote</button>
				<div class="avfr-tooltip">
					<div class="voting-buttons">
					<p class="voting-buttons-title">
						<?php _e('Select your vote','feature_request'); ?>
					</p>
						<?php 
						for ( $i=1; $i <= $voting_limit ; $i++ ) { 
							echo "<a class='avfr-votes-value' data-current-group=".$avfrgroups[0]->slug." data-post-id=". (int) $postid." data-vote=".$i." href='#'>".$i." vote</a>";
						}
					 ?>
					</div>
					<p class="small-text">You have <span>...</span> votes left in this category for this <?php echo strtolower(avfr_get_option('votes_limitation_time','if_settings_avfr')); ?>!</p>
				</div>
				<?php
			}
		}
	}

endif;

/**
*	Draw the voting status
*	@since 1.1
*
*/
if ( !function_exists('avfr_vote_status') ):

	function avfr_vote_status( $postid = '' ) {

		$status      	= avfr_get_status( $postid );

		if ( 'open' !== $status && false !== $status ) { ?>
			<div class="avfr--status">
				<?php echo '<span class="avfr--status_'.sanitize_html_class( $status ).'">'.esc_attr( $status ).'</span>';?>
			</div>
		<?php }
	}

endif;

						
/**
*	Flag control
*
*/
if ( !function_exists('avfr_flag-control') ):

	function avfr_flag_control($postid = '') {
		//getting group of feature.
		$avfrgroups = get_the_terms( $postid, 'groups' );
		//flag option applying
		$if_flag_disabled = avfr_get_option('if_flag','if_settings_avfr');
		if ($if_flag_disabled=="on") {
			?>
			<div class="flag-show">
				<span class="dashicons dashicons-flag"></span>
				<a href="#" class="avfr-flag" data-current-group="<?php echo $avfrgroups[0]->slug; ?>" data-post-id="<?php echo (int) $postid;?>"> <?php _e('Report this idea','feature_request'); ?></a>
			</div>
			<?php
		}

	}
endif;

/*-----------------------------------------------------------------------------------*/
/*  Get trimmed string
/*-----------------------------------------------------------------------------------*/

function avfr_the_trimmed_string( $string, $max_length = 1000, $more = ' ...' ){
    echo avfr_get_trimmed_string( $string, $max_length, $more );
}

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

/*-----------------------------------------------------------------------------------*/
/*  Shortcode enabled excerpts trimmed by character length
/*-----------------------------------------------------------------------------------*/

function avfr_the_trim_excerpt( $post_id = null, $char_length = 250, $exclude_strip_shortcode_tags = null, $read_more = null ){
    echo avfr_get_the_trim_excerpt( $post_id, $char_length, $exclude_strip_shortcode_tags, $read_more );
}

    /**
     * Get trimmed string by character length
     *
     * @param string  $post_id      The post id. Default: current post
     * @param int     $char_length  The length of the desired trim.
     * @param $string $more  A string that is added to the end of string when string is truncated.
     *
     * @return string The trimmed string
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

            if ( $read_more ) {
                $excerpt_more = $read_more;
            }

            // Clean post content
            $excerpt = strip_tags( avfr_strip_shortcodes( $excerpt, $exclude_strip_shortcode_tags ) );
            $text    = avfr_get_trimmed_string( $excerpt, $char_length, $excerpt_more );

            return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
        }

    }

/*-----------------------------------------------------------------------------------*/
/*  Get excerpt by post ID
/*-----------------------------------------------------------------------------------*/

function avfr_the_excerpt_by_id( $post_id = null, $char_length = 250 ){
    echo avfr_get_the_excerpt_by_id( $post_id, $char_length );
}

    if ( ! function_exists( 'avfr_get_the_excerpt_by_id' ) ) {

        /**
         * Get excerpt by post ID
         */
        function avfr_get_the_excerpt_by_id( $post_id = null, $char_length = 250 ) {
            $post = get_post( $post_id );
            if ( ! isset( $post ) ) return "";


            $excerpt      = apply_filters( 'the_excerpt', get_post_field( 'post_excerpt', $post->ID ) );
            $excerpt_more = apply_filters( 'excerpt_more', ' ...');

            // If post have excerpt, return it
            if ( ! empty( $excerpt ) ) {
                // if the max char limit was set, trim the excerpt
                if ( $char_length ) {
                    $excerpt = avfr_get_trimmed_string( $excerpt, $char_length, $excerpt_more );
                }
                return $excerpt;
            }

            // If the excerpt was not created, generate the excerpt by post content
            return avfr_get_the_trim_excerpt( $post, $char_length );
        }

    }

/*-----------------------------------------------------------------------------------*/
/*  Remove just shortcode tags from the given content but keep content of shortcodes
/*-----------------------------------------------------------------------------------*/

/**
 *  Remove just shortcode tags from the given content but keep content of shortcodes
 *
 * @param  string  $content                      The string that we plan to strip the shortcodes from
 * @param  string  $exclude_strip_shortcode_tags List of shortcode names which we want to strip from text
 * @return string                                The text with shortcodes striped
 */
function avfr_strip_shortcodes( $content, $exclude_strip_shortcode_tags = null ) {
    if ( ! $content ) return $content;

    if ( ! $exclude_strip_shortcode_tags )
        $exclude_strip_shortcode_tags = avfr_exclude_strip_shortcode_tags();

    if ( empty( $exclude_strip_shortcode_tags ) || !is_array( $exclude_strip_shortcode_tags ) )
        return preg_replace( '/\[[^\]]*\]/', '', $content );

    $exclude_codes = join( '|', $exclude_strip_shortcode_tags );
    return preg_replace( "~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $content );
}


/*-----------------------------------------------------------------------------------*/
/*  The list of shortcode tags that should not be removed in idea_factory_strip_shortcodes
/*-----------------------------------------------------------------------------------*/

/**
 * List of shortcodes to strip
 * @return array List of shortcodes to be exclude
 */
function avfr_exclude_strip_shortcode_tags(){
    return apply_filters( 'avfr_exclude_strip_shortcode_tags', array() );
}

/*
*
*add setting about the file uploaded type
*
*/
function avfr_image_filter( $input = '' ) {

	if ( empty( $input ) )
		return;
	
	$allowed_image = array(
		$avfr_get_file_type= avfr_get_option('if_settings_avfr','avfr_allowed_file_types')
	);

	return $allowed_image;
}


function avfr_the_author_avatar() {
	echo avfr_get_author_avatar();
}

	function avfr_get_author_avatar() {
		if (is_user_logged_in()) {
        $author =  get_the_author_meta( 'ID' );
		$avatar = get_avatar( $author );
		}else{
		$author =  get_post_meta( $post_id,'_author_email', $single );
		$avatar = get_avatar($author);
		}
		$avatar_link = $avatar ;

		return apply_filters( 'avfr_get_author_avatar', $avatar_link, $author, $avatar, $idea );
	}

	function validate_gravatar($email) {
	// Craft a potential url and test its headers
	$hash = md5(strtolower(trim($email)));
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);
	if (!preg_match("|200|", $headers[0])) {
		$has_valid_avatar = FALSE;
	} else {
		$has_valid_avatar = TRUE;
	}
	return $has_valid_avatar;
}