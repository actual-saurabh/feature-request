<?php
/**
 	* Featrue request WordPress Plugin.
 	*
 	* @package   			Feature-request
 	* @author    			Averta
 	* @license   			GPL-2.0+
 	* @link      			http://averta.net
 	*	@copyright 			2015 Averta
 	*
 	* Plugin Name:       Feature Request
 	* Plugin URI:        http://averta.net
 	* Description:       Featrue request and suggestion submitter with voting system for wordpress.
 	* Version:           1.0.0
 	* GitHub Plugin URI: https://github.com/averta-lab/feature-request
 	*	Author:            Averta
	* Author URI:        http://averta.net
	* Text Domain:       featrue-request
	* License:           license.txt
	* Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set some constants
define('AVFR_VERSION', '1.0');
define('AVFR_DIR', plugin_dir_path( __FILE__ ));
define('AVFR_URL', plugins_url( '', __FILE__ ));

require_once( plugin_dir_path( __FILE__ ) . 'public/class-feature-request.php' );

register_activation_hook( __FILE__, array( 'Feature_Request', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Feature_Request', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Feature_Request', 'get_instance' ) );

if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-avfr-admin.php' );
	add_action( 'plugins_loaded', array( 'AVFR_Admin', 'get_instance' ) );
}
