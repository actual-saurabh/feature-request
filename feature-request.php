<?php
/**
 * Featrue request WordPress Plugin.
 *
 * @package   Feature-Rquest
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://averta.net
 *
 *
 * Plugin Name:     Feature Request
 * Plugin URI:      https://wordpress.org/plugins/feature-request/
 * Description:     Featrue request and suggestion submitter with voting system for wordpress.
 * Version:         1.0.7
 * Author:          averta
 * Author URI:      http://averta.net
 * Text Domain:     feature-request
 * License URI:     license.txt
 * Domain Path:     /languages
 * Tested up to:    4.4.0
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
