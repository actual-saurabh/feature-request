<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class FeatureRequestProcessVote {
	function __construct(){

		add_action( 'wp_ajax_avfr_add_vote', 				        array($this, 'avfr_add_vote' ));
		add_action( 'wp_ajax_avfr_minus_vote', 			        	array($this, 'avfr_minus_vote' ));
		add_action( 'wp_ajax_process_multi_vote', 			        array($this, 'process_multi_vote' ));
		add_action( 'wp_ajax_process_flag', 				        array($this, 'process_flag' ));
		add_action( 'wp_ajax_avfr_calc_remaining_votes', 		    array($this, 'avfr_calc_remaining_votes' ));
		add_action( 'wp_ajax_nopriv_avfr_add_vote', 				array($this, 'avfr_add_vote' ));
		add_action( 'wp_ajax_nopriv_avfr_minus_vote', 				array($this, 'avfr_minus_vote' ));
		add_action( 'wp_ajax_nopriv_process_multi_vote', 			array($this, 'process_multi_vote' ));
		add_action( 'wp_ajax_nopriv_avfr_calc_remaining_votes', 	array($this, 'avfr_calc_remaining_votes' ));
				
	}

	/**
	*
	*	Process the form submission
	*
	*/

	function avfr_add_vote(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$voted_group 		= $_POST['cfg'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option('avfr_public_voting','avfr_settings_main');
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('avfr_total_vote_limit_'.$voted_group,'avfr_settings_groups');
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$get_voter_email 	= get_userdata($userid);
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;
			if ( !is_email( $voter_email ) ) {
				$response_array = array('response' => 'email-warning', 'warning' => __('Please enter a valid email address.','idea-factory'), 'email' => $voter_email );
				echo json_encode($response_array);
				die();
			}
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $voter_email, $idea_voted_group );
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
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $idea_voted_group, 'votes' => '1', 'userid' => $userid, 'email' => $voter_email );
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

	function avfr_minus_vote(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$voted_group 		= $_POST['cfg'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option('avfr_public_voting','avfr_settings_main');
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('avfr_total_vote_limit_'.$voted_group,'avfr_settings_groups');
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;
			if ( !is_email( $voter_email ) ) {
				$response_array = array('response' => 'email-warning', 'warning' => __('Please enter a valid email address.','idea-factory') );
				echo json_encode($response_array);
				die();
			}
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $voter_email, $idea_voted_group );
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
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $idea_voted_group, 'votes' => '1', 'userid' => $userid, 'email' => $voter_email );
				avfr_add_vote( $args );
				//increase votes
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) - 1 );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + 1 );
				do_action('avfr_vote_up', $postid, $userid );
				$response_array = array('response' => 'success' , 'total_votes' => intval( $total_votes ) - 1, 'remaining' => $remaining_votes - 1 );
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
			$voted_group 		= $_POST['cfg'];
			// public voting enabled
			$public_can_vote 	= avfr_get_option( 'avfr_public_voting','avfr_settings_main' );
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('avfr_total_vote_limit_'.$voted_group,'avfr_settings_groups');
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			//Get user ID
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;
			if ( !is_email( $voter_email ) ) {
				$response_array = array('response' => 'email-warning', 'warning' => __('Please enter a valid email address.','idea-factory') );
				echo json_encode($response_array);
				die();
			}
			// get vote statuses
			$has_voted  		= avfr_has_voted( $postid ,$ip, $userid );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $voter_email, $idea_voted_group );
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
				$args = array( 'postid' => $postid, 'type' => 'vote', 'groups' => $idea_voted_group, 'votes' => intval($votes_num), 'userid' => $userid, 'email' => $voter_email );
				avfr_add_vote( $args );
				//increase votes
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) + intval($votes_num) );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + 1 );
				do_action('avfr_vote_up', $postid, $userid );
				$response_array = array('response' => 'success' , 'total_votes' => intval( $total_votes ) + intval($votes_num), 'remaining' => $remaining_votes - intval($votes_num) );
				echo json_encode($response_array);
			}
		}
		die();
	}

	function avfr_calc_remaining_votes(){

		check_ajax_referer('feature_request','nonce');

		// if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$voted_group 		= $_POST['cfg'];
			// Get limit for users from option in voted category
			$user_vote_limit	= avfr_get_option('avfr_total_vote_limit_'.$voted_group,'avfr_settings_groups');
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			//Get user ID
			$userid = get_current_user_ID();

			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$get_voter_email 	= get_userdata($userid);
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $fun( $ip, $userid, $voter_email, $idea_voted_group );

			if ( !$user_total_voted ) {
				$user_total_voted = 0;
			}

			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			$response_array = array('response' => $remaining_votes );
			echo json_encode($response_array);

		// }
		die();
	}

	/**
	*
	*	Process the form submission
	*
	*/

	function avfr_add_flag(){
		// public voting enabled
		$can_flag = avfr_get_option('avfr_flag','avfr_settings_main');

		if ($can_flag == "on") {
		
			check_ajax_referer('feature_request','nonce');

			if ( isset( $_POST['post_id'] ) ) {
				$postid 		= $_POST['post_id'];
			}

			$voted_group 	= $_POST['cfg'];
			$userid = get_current_user_ID();
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

			// get flag statuses
			$has_flag 	= avfr_has_flag( $postid, $ip, $userid );

			// get flags
			$flags 				= get_post_meta( $postid, '_flag', true );

			if ( $has_flag ) {
				echo 'already-flagged';
			} else {
				update_post_meta( $postid, '_flag', (int) $flags + 1 );
				$args = array( 'postid' => $postid, 'ip' => $ip, 'userid' => $userid, 'groups' => $voted_group, 'type' => 'flag' );
		        avfr_add_flag( $args );
			}
		}
			die();
	}
}
new FeatureRequestProcessVote;