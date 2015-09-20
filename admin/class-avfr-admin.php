<?php
/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class AVFR_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0
	 */
	private function __construct() {

		$plugin = Feature_Request::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		require_once(AVFR_DIR.'/admin/includes/class.settings.php');
		require_once(AVFR_DIR.'/admin/includes/class.meta.php');
		require_once(AVFR_DIR.'/admin/includes/class.column-mods.php');
		wp_enqueue_style('idea-factory-admin-css', AVFR_URL.'/admin/assets/css/avfr-admin.css', AVFR_VERSION, true );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
