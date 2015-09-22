<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class FeatureRequestDB {

	private $table_name;
	private $db_version;

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
					`votes`		= '%s'
				;",
				absint( $args['postid'] ),
				date_i18n( 'Y-m-d H:i:s', $args['time'], true ),
				filter_var( $args['ip'], FILTER_VALIDATE_IP ),
				$args['userid'],
				$args['groups'],
				$args['type'],
				absint($args['votes'])
			)
		);

		if( $add )
			return $wpdb->insert_id;
		return false;
	}

}