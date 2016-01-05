<?php

/**
 *
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */

class Avfr_Votes {
	function __construct(){

		add_action( 'wp_ajax_avfr_vote', 				        	array($this, 'avfr_vote' ));
		add_action( 'wp_ajax_avfr_add_flag', 				        array($this, 'avfr_add_flag' ));
		add_action( 'wp_ajax_avfr_calc_remaining_votes', 		    array($this, 'avfr_calc_remaining_votes' ));
		add_action( 'wp_ajax_nopriv_avfr_vote', 					array($this, 'avfr_vote' ));
		add_action( 'wp_ajax_nopriv_avfr_calc_remaining_votes', 	array($this, 'avfr_calc_remaining_votes' ));
		add_action( 'wp_ajax_nopriv_avfr_add_flag', 				array($this, 'avfr_add_flag' ));
				
	}

	/**
	*	Process the form submission
	*/

	function avfr_vote(){

		check_ajax_referer('feature_request','nonce');
		global $avfr_db;
		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			$votes_num			= $_POST['votes'];
			// get votes
			$votes 				= get_post_meta( $postid, '_avfr_votes', true );
			$total_votes 		= get_post_meta( $postid, '_avfr_total_votes', true );
			$voted_group 		= $_POST['cfg'];
			$term = get_term_by( 'slug', $voted_group, $taxonomy = 'groups' );
			$term_id = $term->term_id;
			// public voting enabled
			$public_can_vote 	= avfr_get_option('avfr_public_voting','avfr_settings_main');
			// Get limit for users from option in voted category
			$user_vote_limit 	= get_term_meta( $term_id, 'avfr_total_votes', true);
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			//Get user ID
			$userid 			= get_current_user_id();
			$ip 				= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$get_voter_email 	= get_userdata($userid);
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;

			if ( !is_email( $voter_email ) ) {
				$response_array = array('response' => 'email-warning', 'warning' => __('Please enter a valid email address.','feature-request'), 'email' => $voter_email );
				wp_send_json($response_array);
			}

			// get vote statuses
			$has_voted  		= $avfr_db->avfr_has_vote_flag( $postid ,$ip, $userid, 'vote' );
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			$user_total_voted 	= $avfr_db->$fun( $ip, $userid, $voter_email, $voted_group );
			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			// if the public can vote and the user has already voted or they are logged in and have already voted then bail out
			if ( $public_can_vote && $has_voted ) {
				$response_array = array('response' => 'already-voted');
				wp_send_json($response_array);
			}

			if ( $user_vote_limit < ($user_total_voted + abs( intval($votes_num) ) ) ) {
				wp_send_json( array('response' => $remaining_votes) );
			} else {
				$args = array( 'postid' => $postid, 'ip' => $ip, 'userid' => $userid, 'email' => $voter_email, 'groups' => $voted_group, 'type' => 'vote', 'votes' => abs( intval( $votes_num ) ) );
				$avfr_db->avfr_insert_vote_flag( $args );
				update_post_meta( $postid, '_avfr_votes', intval( $votes ) + intval( $votes_num ) );
				update_post_meta( $postid, '_avfr_total_votes', intval( $total_votes ) + 1 );
				do_action('avfr_add_vote', $postid, $userid );
				$response_array = array( 'response' => 'success' , 'total_votes' => intval( $votes ) + intval( $votes_num ), 'remaining' => $remaining_votes - abs( intval($votes_num) ) );
					
				wp_send_json($response_array);
			}
		}
		wp_die();
	}


	function avfr_calc_remaining_votes(){

		check_ajax_referer('feature_request','nonce');

		if ( isset( $_POST['post_id'] ) ) {

			$postid 			= $_POST['post_id'];
			// get votes
			$voted_group 		= $_POST['cfg'];
			$term = get_term_by( 'slug', $voted_group, $taxonomy = 'groups' );
			$term_id = $term->term_id;
			// Get limit for users from option in voted category
			
			$user_vote_limit 	= get_term_meta( $term_id, 'avfr_total_votes', true);
			$limit_time			= avfr_get_option('avfr_votes_limitation_time','avfr_settings_main');
			
			//Get user ID
			$userid 			= get_current_user_id();
			$ip 				= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
			$get_voter_email 	= get_userdata($userid);
			$voter_email 		= ( !is_user_logged_in() && isset( $_POST['voter_email'] ) ) ? $_POST['voter_email'] : $get_voter_email->user_email;
			//Get related function to time limitation
			$fun 				= 'avfr_total_votes_'.$limit_time;
			global $avfr_db;
			$user_total_voted 	= $avfr_db->$fun( $ip, $userid, $voter_email, $voted_group );

			if ( !$user_total_voted ) {
				$user_total_voted = 0;
			}

			$remaining_votes 	= $user_vote_limit - $user_total_voted;

			$response_array = array('response' => $remaining_votes );
			wp_send_json($response_array);

		}
		wp_die();
	}

	/**
	* Process the form submission
	*/
	function avfr_add_flag(){
		// public voting enabled
		$can_flag = avfr_get_option('avfr_flag','avfr_settings_main');

		if ( $can_flag == "on") {
			
			check_ajax_referer('feature_request','nonce');
			global $avfr_db;
			if ( isset( $_POST['post_id'] ) ) {
				$postid 		= $_POST['post_id'];
			}

			$voted_group 	 = $_POST['cfg'];
			$userid 		 = get_current_user_id();
			$get_voter_email = get_userdata($userid);
			$reporter_email  = $get_voter_email->user_email;
			$ip 			 = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

			// get flag statuses
			$has_flag 		 = $avfr_db->avfr_has_vote_flag( $postid, $ip, $userid, 'flag' );

			// get flags
			$flags 			 = get_post_meta( $postid, '_flag', true );

			if ( $has_flag ) {
				$response_array = array( 'response' => 'already-flagged', 'message' => __('You already flagged this idea.', 'feature-request') );
				wp_send_json($response_array);
			} else {
				update_post_meta( $postid, '_flag', (int) $flags + 1 );
				$args = array( 'postid' => $postid, 'ip' => $ip, 'userid' => $userid, 'email' => $reporter_email, 'groups' => $voted_group, 'type' => 'flag', 'votes' => '0' );
				$avfr_db->avfr_insert_vote_flag( $args );
		        $response_array = array('response' => 'success', 'message' => __('Reported!', 'feature-request') );
				wp_send_json($response_array);
			}
		}
			wp_die();
	}
}
new Avfr_Votes;