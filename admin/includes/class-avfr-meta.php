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

class Avfr_Meta {

	function __construct(){

		add_action( 'add_meta_boxes', 					array($this,'add_status_box') );
		add_action( 'save_post',						array($this,'save_status_box'), 10, 3 );
	}

	/**
	*
	*
	*	Add a status metabox if the user has opted in for the threshold settings
	*
	*	@since 1.0
	*/
	function add_status_box(){

			add_meta_box('avfr_status',__( 'Feature Status', 'feature-request' ),array($this,'render_status_box'), 'avfr','side','core');

	}

	/**
	* 	Render status metabox
	*
	* 	@param WP_Post $post The post object.
	*	@since 1.0
	*
	*/
	function render_status_box( $post ){

		wp_nonce_field( 'avfr_meta', 'avfr_nonce' );

		$status = get_post_meta( $post->ID, '_avfr_status', true );

		?>
		<select name="avfr_status">
	      	<option value="approved" <?php selected( $status, 'approved' ); ?>><?php _e('Approved','feature-request');?></option>
	      	<option value="declined" <?php selected( $status, 'declined' ); ?>><?php _e('Declined','feature-request');?></option>
	      	<option value="open" <?php selected( $status, 'open' ); ?>><?php _e('Open','feature-request');?></option>
	      	<option value="completed" <?php selected( $status, 'completed' ); ?>><?php _e('Completed','feature-request');?></option>
	    </select>
	    <?php
	}

	/**
	*
	* 	Save the status
	*
	* 	@param int $post_id The ID of the post being saved.
	*	@param post $post the post
	*	@since 1.0
	*
	*/
	function save_status_box( $post_id, $post, $update ) {

		if ( ! isset( $_POST['avfr_nonce'] ) )
			return $post_id;

		$nonce = $_POST['avfr_nonce'];

		if ( !wp_verify_nonce( $nonce, 'avfr_meta' ) || defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || 'avfr' != $post->post_type )
			return $post_id;

		$status 	 = isset( $_POST['avfr_status'] ) ? $_POST['avfr_status'] : false;

		update_post_meta( $post_id, '_avfr_status', sanitize_text_field( trim( $status ) ) );


	}
}
new Avfr_Meta;