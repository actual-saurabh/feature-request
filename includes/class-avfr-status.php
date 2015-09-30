<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class Avfr_Status {

	function __construct(){

		add_action( 'avfr_vote_up', 					array($this, 'process_status' ), 10, 2);
		add_action( 'avfr_vote_down', 					array($this, 'process_status' ), 10, 2);
		add_action( 'wp_ajax_process_change_status', 	array($this, 'process_change_status' ));
		add_action( 'avfr_status', 						array($this, 'mail_status' ), 10, 2);
	}

	/**
	*
	*	Process the status of an individual feature with an action fired when a user votes up or down
	*
	*	@param $postid int id of the post
	*	@param $userid int id of the user who voted
	*
	*/
	function process_status( $postid, $userid ) {

		// get threashold
		$threshold = avfr_get_option('avfr_threshold','avfr_settings_main');

		// bail if no user threshold set
		if ( empty( $threshold ) )
			return;

		// get total number of votes
		$total     = avfr_get_total_votes( $postid );

		// get total number of vote ups
		$votes     = avfr_get_votes( $postid );

		// if total votes are greater than the threshold
		if ( $total >= $threshold ) {

			// if up votes are passing
			if ( $votes >= $threshold ) {

				update_post_meta( $postid, '_avfr_status', 'approved');

				do_action('avfr_status', 'approved', $postid );

			// up votes failed
			} else {

				update_post_meta( $postid, '_avfr_status', 'declined');

				do_action('avfr_status', 'declined', $postid );

			}

		// not enough votes to calculate yet
		} else {

			update_post_meta( $postid, '_avfr_status', 'open');

		}

	}

	/**
	*
	*	Change status of votes
	*
	*/
	function process_change_status() {

		$post_id = $_POST['post_id'];
		$current_status = get_post_meta( $post_id,'_avfr_status', true );
		$new_status = $_POST['new_status'];

		if ( ($new_status != $current_status) && current_user_can('manage_options') ) {
			update_post_meta( $post_id, '_avfr_status', $new_status );
			echo 'success';
		}

		// for email
		$post_author_id = get_post_field( 'post_author', $post_id );
		$reciever_info 	= get_userdata($post_author_id);
		$entry       	= get_post( $post_id );
		$search			= array('{{writer-name}}','{{avfr-title}}','{{votes}}');
		$replace 		= array($reciever_info->user_login, $entry->post_title, avfr_get_votes( $post_id ));

		if ( 'on' == avfr_get_option('send_mail_'.$new_status.'_writer','avfr_settings_mail') ) {
			
			$reciever_email = get_the_author_meta( 'user_email' , $post_author_id );
			$content		= avfr_get_option('mail_content_'.$new_status.'_writer','avfr_settings_mail');
			$mail_content   = str_replace($search, $replace, $content);
			wp_mail( $reciever_email, 'Feature Request '.$entry->post_title.' '.$new_status.'.', $mail_content );

		}

		if ( 'on' == avfr_get_option('send_mail_'.$new_status.'_voters','avfr_settings_mail') ) {

			$reciever_emails		= get_voters_email($post_id);
			$content		= avfr_get_option('avfr_mail_content_'.$new_status.'_voters','avfr_settings_mail');
			$mail_content   = str_replace($search, $replace, $content);
			wp_mail( $reciever_emails, 'Request '.$entry->post_title.' '.$new_status.'.', $mail_content );

		}

		exit(); // ajax
	}

	/**
	*
	*	Send email to the admin notifying of a status change on an feature
	*
	*	@param $status string approved | declined
	*	@param $postid int postid object
	*
	*/
	function mail_status( $status, $postid ) {

		$admin_email 	= get_bloginfo('admin_email');
		$entry       	= get_post( $postid );
		$mail_disabled 	= avfr_get_option('avfr_disable_mail','avfr_settings_advanced');

		$message = "The status of ".$entry->post_title." has been updated to:\n";
		$message .= "".$status."\n\n";
		$message .= "Manage features at link below\n";
		$message .= "".wp_login_url()."\n\n";

		if ( !$mail_disabled )
			wp_mail( $admin_email, 'Feature Request '.$postid.' Approved ', $message );

	}

}
new Avfr_Status;