<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class featurerequestProcessVote {
	function __construct(){

		add_action( 'wp_ajax_process_vote_up', 				        array($this, 'process_vote_up' ));
		add_action( 'wp_ajax_process_vote_down', 			        array($this, 'process_vote_down' ));
		add_action( 'wp_ajax_process_multi_vote', 			        array($this, 'process_multi_vote' ));
		add_action( 'wp_ajax_process_flag', 				        array($this, 'process_flag' ));
		add_action( 'wp_ajax_calc_remaining_votes', 		        array($this, 'calc_remaining_votes' ));
		add_action( 'wp_ajax_nopriv_process_vote_up', 				array($this, 'process_vote_up' ));
		add_action( 'wp_ajax_nopriv_process_vote_down', 			array($this, 'process_vote_down' ));
		add_action( 'wp_ajax_nopriv_process_multi_vote', 			array($this, 'process_multi_vote' ));
		add_action( 'wp_ajax_nopriv_calc_remaining_votes', 			array($this, 'calc_remaining_votes' ));
				
	}

	/**
	*
	*	Process the form submission
	*
	*/

	function process_vote_up(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$avfr_voted_group 	= $_POST['cig'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option('if_public_voting','if_settings_main');
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('like_limit_'.$avfr_voted_group,'if_settings_groups');
			$limit_time			= avfr_get_option('votes_limitation_time','if_settings_avfr');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $avfr_voted_group );
			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_voted ) {
				$response_array = array('response' => 'already-voted');
				echo json_encode($response_array);
				die();
			}

			if ( $user_vote_limit < ($user_total_voted + 1) ) {
				echo $remaining_votes;
					die();
			} else {
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $avfr_voted_group, 'votes' => '1', 'userid' => $userid );
				avfr_add_vote( $args );
				//increase votes
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) + 1 );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + 1 );
				do_action('avfr_vote_up', $postid, $userid );
				$response_array = array('response' => 'success' , 'total_votes' => intval( $total_votes ) + intval($votes_num), 'remaining' => $remaining_votes - 1 );
				echo json_encode($response_array);
			}
		}
		die();
	}

	/**
	*
	*	Process the form submission
	*
	*/

	function process_vote_down(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$idea_voted_group 	= $_POST['cig'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option('if_public_voting','if_settings_main');
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('like_limit_'.$avfr_voted_group,'if_settings_groups');
			$limit_time			= avfr_get_option('votes_limitation_time','if_settings_avfr');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $idea_voted_group );
			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_voted ) {
				$response_array = array('response' => 'already-voted');
				echo json_encode($response_array);
				die();
			}

			if ( $user_vote_limit < ($user_total_voted + 1) ) {
				echo $remaining_votes;
					die();
			} else {
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $avfr_voted_group, 'votes' => '1', 'userid' => $userid );
				avfr_add_vote( $args );
				//increase votes
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) - 1 );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + 1 );
				do_action('avfr_vote_up', $postid, $userid );
				$response_array = array('response' => 'success' , 'total_votes' => intval( $total_votes ) + intval($votes_num), 'remaining' => $remaining_votes - 1 );
				echo json_encode($response_array);
			}
		}
		die();
	}

		function process_multi_vote(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			$votes_num			= $_POST['votes'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$idea_voted_group 	= $_POST['cig'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option( 'if_public_voting','if_settings_main' );
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('total_vote_limit_'.$avfr_voted_group,'if_settings_groups');
			$limit_time			= avfr_get_option('votes_limitation_time','if_settings_avfr');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $avfr_voted_group );
			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_voted ) {
				$response_array = array('response' => 'already-voted' );
				echo json_encode($response_array);
				die();
			}

			if ( $user_vote_limit < ($user_total_voted + intval($votes_num)) ) {
				$response_array = array('response' => $remaining_votes );
				echo json_encode($response_array);
					die();
			} else {
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $avfr_voted_group, 'votes' => intval($votes_num), 'userid' => $userid );
				avfr_add_vote( $args );
				//increase votes
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) + intval($votes_num) );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + intval($votes_num) );
				do_action('avfr_vote_up', $postid, $userid );
				$response_array = array('response' => 'success' , 'total_votes' => intval( $total_votes ) + intval($votes_num), 'remaining' => $remaining_votes - intval($votes_num) );
				echo json_encode($response_array);
			}
		}
		die();
	}

	function calc_remaining_votes(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$idea_voted_group 	= $_POST['cig'];
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('total_vote_limit_'.$avfr_voted_group,'if_settings_groups');
			$limit_time			= avfr_get_option('votes_limitation_time','if_settings_ideas');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $idea_voted_group );

			if ( !$user_total_voted ) {
				$user_total_voted = 0;
			}

			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			$response_array = array('response' => $remaining_votes );
			echo json_encode($response_array);

		}
		die();
	}

	/**
	*
	*	Process the form submission
	*
	*/

	function process_flag(){
		// public voting enabled
		$can_flag = avfr_get_option('if_flag','if_settings_avfr');
		if ($can_flag == "on") {
		
			check_ajax_referer('feature_request','nonce');

			if ( isset( $_POST['post_id'] ) ) {
				$postid 		= $_POST['post_id'];
			}
			$avfr_voted_group 	= $_POST['cig'];
			// get flag statuses
			$has_public_flag 	= avfr_has_public_flag( $postid );
			// get flags
			$flags 				= get_post_meta( $postid, '_flag', true );

			if ( is_user_logged_in() ) {
				$userid = get_current_user_ID();
				$user_flagged = get_user_meta( $userid, '_avfr'.absint( $postid ).'_has_flagged', true);
				if ($user_flagged) {
					echo 'already-flagged';
					die();
				}else{
					//Update flags count
					update_post_meta( $postid, '_flag', intval($flags) + 1 );
					// update user meta so they can't vote on this again
					update_user_meta( $userid, '_avfr'.$postid.'_has_flagged', true );
					echo 'success';
				}
			}elseif(!$has_public_flag){
				//Update flags count
				update_post_meta( $postid, '_flag', intval($flags) + 1 );
				$userid = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
				$args = array( 'postid' => $postid, 'type' => 'flag', 'groups' => $avfr_voted_group );
		        avfr_add_public_flag( $args );
		        echo 'success';
				}else{
					echo 'already-flagged';
					die();
				}
			}
			die();
		}
}
new featurerequestProcessVote;