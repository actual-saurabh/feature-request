<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$table = $wpdb->prefix.'feature_request';

// delete optoin
delete_option('feature_request_version');

// drop db table
$wpdb->query("DROP TABLE IF EXISTS $table");
