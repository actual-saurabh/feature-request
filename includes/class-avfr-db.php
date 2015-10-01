<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

// no direct access allowed
if ( ! defined('ABSPATH') ) {
    die();
}

require_once dirname( __FILE__ ) . '/class-axiom-table.php';

/**
 * Create and manipulate custom tables in WordPress database.
 */
class Avfr_DB extends Axiom_Table {

	function __construct() {

		global $wpdb;
		$this->table_name   = $wpdb->base_prefix . 'feature_request';

	}

	const DB_VERSION	= AVFR_VERSION;

	/**
	 * Add vote to database
	 * @since 1.0
	 */
	public function avfr_insert_vote_flag( $fields ) {

		$defaults = array(
			'postid' => absint( get_the_ID() ),
			'time'   => date_i18n( 'Y-m-d H:i:s', current_time('timestamp'), true ),
			'ip'   	 => filter_var( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0, FILTER_VALIDATE_IP ),
			'userid' => 0,
			'type'   => 'vote',
			);

		$add = $this->insert( $this->table_name, $fields, $defaults );

	}

	/**
	 * Get total votes in this week
	 * @since 1.0
	 */
	public function avfr_total_votes_WEEK( $ip, $userid = 0, $email, $voted_group ) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    // global $wpdb;

	   	$sql =  $wpdb->prepare('SELECT votes FROM '.$this->table_name.' WHERE userid="%s" OR ip ="%s" OR email="%s" AND groups ="%s" AND type="vote" AND YEARWEEK(time)=YEARWEEK(CURDATE()) AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $userid, $ip, $email, $voted_group );

	   	$total =  $wpdb->get_col( $sql );

		return array_sum($total);
	}


	/**
	 * Get total votes in this month
	 * @since 1.0
	 */
	public function avfr_total_votes_MONTH( $ip, $userid = 0, $email, $voted_group ) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	   	$sql 	=  $wpdb->prepare('SELECT votes FROM '.$this->table_name.' WHERE userid="%s" OR ip ="%s" OR email="%s" AND groups ="%s" AND type="vote" AND MONTH(time)=MONTH(CURDATE()) AND YEAR(time)=YEAR(CURDATE())', $userid, $ip, $email, $voted_group );

	   	$total 	=  $wpdb->get_col( $sql );

		return array_sum($total);
	}


	/**
	 * Get total votes in this year
	 * @since 1.0
	 */
	public function avfr_total_votes_YEAR( $ip, $userid = 0, $email, $voted_group ) {

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	   	$sql =  $wpdb->prepare('SELECT votes FROM '.$this->table_name.' WHERE userid="%s" AND ip ="%s" OR email="%s" AND groups ="%s" AND type="vote" AND YEAR(time)=YEAR(CURDATE())', $userid, $ip, $email, $voted_group );

	   	$total =  $wpdb->get_col( $sql );

		return array_sum($total);
	}


	/**
	 * Get users email's that voted to request with $post_id id
	 * @since 1.0
	 */
	function avfr_get_voters_email( $post_id ) {

		if ( empty( $post_id ) )
				return;

		global $wpdb;

	   	$sql   	=  $wpdb->prepare('SELECT email FROM '.$this->table_name.' WHERE postid ="%d" AND type="vote"', $post_id );

	   	$result =  $wpdb->get_col( $sql );

	   	return $result;
			
	}


	/**
	 * Check if feature has voted by current user
	 * @since 1.0
	 */
	function avfr_has_voted( $post_id, $ip, $userid = '0' ) {

		if ( empty( $post_id ) )
			return;

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	   	$sql =  $wpdb->prepare('SELECT * FROM '.$this->table_name.' WHERE ip ="%s" AND userid="%s" AND postid ="%d" AND type="vote"', $ip, $userid, $post_id );

	   	$result =  $wpdb->get_results( $sql );

		if ( $result ) {

			return true;

		} else {

			return false;

		}
	}


	/**
	 * If current user (visitor) can vote return true
	 * @since 1.0
	 */
	public function avfr_is_voting_active( $post_id, $ip, $userid = '0' ) {

		$status      	 = avfr_get_status( $post_id );

		$public_can_vote = avfr_get_option('avfr_public_voting','avfr_settings_main');

		if ( ( ( false == $this->avfr_has_voted( $post_id, $ip, $userid) && is_user_logged_in() ) || ( ( false == $this->avfr_has_voted( $post_id, $ip, $userid ) ) && !is_user_logged_in() && 'on' == $public_can_vote) ) && 'open' === $status ){

			return true;

		} else {

			return false;
		}
	}


	/**
	 * Check if post flagged by user
	 * @since 1.0
	 */
	function avfr_has_flag( $post_id, $ip, $userid ) {

		if ( empty( $post_id ) )
			return;

		if ( empty( $ip ) )
			$ip =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;

	    global $wpdb;

	   	$sql =  $wpdb->prepare('SELECT * FROM '.$this->table_name.' WHERE ip ="%s" AND userid="%s" AND postid ="%d" AND type="flag"', $ip, $userid, $post_id );

	   	$result =  $wpdb->get_results( $sql );

		if ( $result ) {

			return true;

		} else {

			return false;

		}
	}


	/**
	*	Create public database tabes on upgrade
	*	@since 1.0
	*/
	function upgrade_install_db(){

		$avfr_table_name = $wpdb->prefix . 'feature_request';

		$sql = "CREATE TABLE $avfr_table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			postid bigint(20) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			ip varchar(20) NOT NULL,
			userid varchar(20) NOT NULL,
			email varchar(100) NOT NULL,
			groups varchar(100) DEFAULT 'none group' NOT NULL,
			type varchar(20) DEFAULT 'vote' NOT NULL,
			votes smallint(5) DEFAULT '1' NOT NULL,
			UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option('avfr_version', $version );

	}

}

global $avfr_db;
$avfr_db = new Avfr_DB;