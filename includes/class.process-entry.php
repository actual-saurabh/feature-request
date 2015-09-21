<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class FeatureRequestProcessEntry {

	function __construct(){

		add_action( 'wp_ajax_process_entry', 				array($this, 'process_entry' ));
		add_action( 'wp_ajax_my_action',              		array($this, 'avfr_ajax_upload'));
		add_action( 'avfr_entry_submitted',			        array($this, 'send_mail'), 10, 2);
		add_action( 'wp_ajax_nopriv_process_entry', 		array($this, 'process_entry' ));
		add_action( 'wp_ajax_nopriv_my_action',      		array($this, 'avfr_ajax_upload'));
		
	}				
	/**
	*
	*	Process the form submission
	*
	*/
	function process_entry(){
		echo $avfr_image_filter;
		$public_can_vote = avfr_get_option('if_public_voting','if_settings_main');
		$allowed_type	= explode(",",avfr_get_option('avfr_allowed_file_types','avfr_settings_features'));
		$allowed_size   = avfr_get_option('avfr_max_file_size','avfr_settings_features');
		$title 			= isset( $_POST['avfr-title'] ) ? $_POST['avfr-title'] : null;
		$desc 			= isset( $_POST['avfr-description'] ) ? $_POST['avfr-description'] : null;
		$uploadajx 		= isset( $_POST['avfr-upload'] ) ? $_POST['avfr-upload'] : null;
		$uploadOk       = 1;
		$must_approve 	= 'on' == avfr_get_option('avfr_settings_features','if_settings_main') ? 'pending' : 'publish';
		session_start();
		
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_entry' ) {

							// only run for logged in users or if public is allowed
			if( !is_user_logged_in() && 'on' !== $public_can_vote )
				return;

							// ok security passes so let's process some data
			if ( wp_verify_nonce( $_POST['nonce'], 'if-entry-nonce' ) ) {

								// bail if we dont have rquired fields
				if ( empty( $title ) || empty( $desc ) ) {

					printf(('<div class="error">%s</div>'), __('Whoopsy! Looks like you forgot the Title and/or description.', 'feature-request'));

				} else if ( '1' == avfr_get_option('avfr_disable_captcha', 'avfr_settings_main') || isset ($_POST["captcha"]) && $_POST["captcha"] != "" && $_SESSION["code"] == $_POST["captcha"]  )	{

					if ( is_user_logged_in() ) {

						$userid = get_current_user_ID();

					} elseif ( !is_user_logged_in() && $public_can_vote ) {

						$userid = apply_filters('avfr_default_public_author', 1 );
					}


					//get array of inserted tags in front-end
					$tags = str_replace(array('[',']','"','\\'), '', $_POST['avfr-tags'] );
					$groups = $_POST['group'];
					$tags_array = explode(',', $tags);
					// create an feature-request post type
					$post_args = array(
						'post_title'    => wp_strip_all_tags( $title ),
						'post_content'  => avfr_media_filter( $desc ),
						'the_post_thumbnail'  => avfr_image_filter( $uploadajx ),
						'post_status'   => $must_approve,
						'post_type'	  	=> 'avfr',
						'post_author'   => (int) $userid
						);


					if ( $_FILES ) {
						$convert_byte_kb = $allowed_size * 1024 ;
						if ( $_FILES["avfr-upload"]["size"] > $convert_byte_kb ) {


							$response_array = array('success' => 'false' , 'message' => __('<span class="dashicons dashicons-warning"></span>'.' Your image size is greater than acceptable !','feature-request'));
							echo json_encode($response_array);
							die();
						}

						if ( in_array( $_FILES ["avfr-upload"]["type"],$allowed_type) ) {
						//continue 
						} else {

							$response_array = array('success' => 'false' , 'message' => __('<span class="dashicons dashicons-warning"></span>'.' Please upload acceptable image format !','feature-request'));
							echo json_encode($response_array);	
							die();
						}

						if (  $_FILES['avfr-upload']['error'] !== UPLOAD_ERR_OK  ) {

							$response_array = array('success' => 'false' , 'message' => __('<span class="dashicons dashicons-dismiss"></span>'.' upload error :'. $_FILES['avfr-upload']['error'],'feature-request'));
							echo json_encode($response_array);
							die();

						} else {
							$entry_id = wp_insert_post( $post_args );
							$attach_id = media_handle_upload( 'avfr-upload',  $entry_id );
							update_post_meta($entry_id,'_thumbnail_id',$attach_id);

						}	  
					} else {
						$entry_id = wp_insert_post( $post_args );
					}

					
					$entry_groups = wp_set_object_terms($entry_id, $groups,'groups');
					$entry_avfrtags = wp_set_object_terms($entry_id, $tags_array,'avfrtags');

					update_post_meta( $entry_id, '_avfr_votes', 0 );
					update_post_meta( $entry_id, '_avfr_total_votes', 0 );
					update_post_meta( $entry_id, '_avfr_status', 'open' );
					update_post_meta( $entry_id, '_flag', 0 );


					do_action('avfr_entry_submitted', $entry_id, $userid );

					$response_array = array('success' => 'true' , 'message' => __('<span class="dashicons dashicons-yes"></span>'.' Thanks for your entry!','feature-request'));
					echo json_encode($response_array);

					if( $must_approve == 'pending' ){
						echo "<br/>";

						$response_array = array('success' => 'true' , 'message' => __('<span class="dashicons dashicons-flag"></span>'.' You suggestion is awaiting moderation.','feature-request'));
						echo json_encode($response_array);

					}
				} else {

					$response_array = array('success' => 'false' , 'message' => __('<span class="dashicons dashicons-warning"></span>'.' Captcha code is not correct!','feature-request'));
					echo json_encode($response_array);

				}

			}

		}	

		exit(); // ajax
	}

	/**
	*
	*	Send email to the admin notifying of a new submission
	*
	*	@param $entry_id int postid object
	*	@param $userid int userid object
	*
	*/
	function send_mail( $entry_id, $userid ) {

		$user 		 	= get_userdata( $userid );
		$admin_email 	= get_bloginfo('admin_email');
		$entry       	= get_post( $entry_id );
		$mail_disabled 	= avfr_get_option('if_disable_mail','if_settings_advanced');

		$message = sprintf(__("Submitted by: %s", 'feature-request'), $user->display_name) .".\n\n";
		$message .= __("Title:", 'feature-request') . "\n";
		$message .= $entry->post_title."\n\n";
		$message .= __("Description:", 'feature-request') . "\n";
		$message .= $entry->post_content."\n\n";
		$message .= __("Manage all request at", 'feature-request') . "\n";
		$message .= admin_url('edit.php?post_type=feature');

		if ( !isset($mail_disabled) || $mail_disabled == 'off' )
			wp_mail( $admin_email, sprintf(__('New Feature Request Submission - %s', 'feature-request'), $entry_id), $message );

	}

}
new FeatureRequestProcessEntry;
