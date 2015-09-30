<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class Avfr_DB {

	private $avfr_table_name;
	private $avfr_db_ver;

	function __construct() {

		global $wpdb;


		$this->table_name   = $wpdb->base_prefix . 'feature_request';
		$this->db_version 	= AVFR_VERSION;

	}


	// insert events into db
	public function insert( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'postid'	=> '',
			'time'		=> '',
			'ip'		=> '',
			'userid'	=> '',
			'groups' 	=> '',
			'type'		=> '',
			'email'		=> '',
			'votes' 	=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		$add = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$this->table_name} SET
					`postid`	= '%s',
					`time`		= '%s',
					`ip`		= '%s',
					`userid`	= '%s',
					`groups`	= '%s',
					`type`		= '%s',
					`votes`		= '%s',
					`email`		= '%s'
				;",
				absint( $args['postid'] ),
				date_i18n( 'Y-m-d H:i:s', $args['time'], true ),
				filter_var( $args['ip'], FILTER_VALIDATE_IP ),
				$args['userid'],
				$args['groups'],
				$args['type'],
				absint($args['votes']),
				$args['email']
			)
		);

		if( $add )
			return $wpdb->insert_id;
		return false;
	}

}