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

}

global $avfr_db;
$avfr_db = new Avfr_DB;