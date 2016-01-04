<?php
/**
 * Main plugin file
 * 
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */	

/**
 * Main plugin class
 */
class Feature_Request {

	/**
	 * Unique identifier
	 * @since    1.0
	 */
	protected $plugin_slug = 'feature-request';

	/**
	 * Instance of this class.
	 * @since    1.0
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 * @since     1.0
	 */
	function __construct() {

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		require_once(AVFR_DIR.'/includes/class-avfr-post-type.php');
		require_once(AVFR_DIR.'/includes/class-avfr-entry.php');
		require_once(AVFR_DIR.'/includes/class-avfr-votes.php');
		require_once(AVFR_DIR.'/includes/class-avfr-status.php');
		require_once(AVFR_DIR.'/public/includes/class-avfr-template.php');
		require_once(AVFR_DIR.'/public/includes/class-avfr-assets.php');
		require_once(AVFR_DIR.'/public/includes/avfr-functions.php');
		require_once(AVFR_DIR.'/public/includes/class-avfr-shortcodes.php');
		require_once(AVFR_DIR.'/includes/class-avfr-db.php');
		require_once(AVFR_DIR.'/includes/class-avfr-upgrade.php');
		// Load plugin text domain
		add_action( 'init', array( $this, 'avfr_load_textdomain' ) );
	}

	/**
	 * Return the plugin slug.
	 * @since     1.0
	 * @return    string    plugin slug
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 * @since     1.0
	 * @return    Instance of class
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
    
    /**
     * Initiate database class
     * @since     1.0
     * @return    Instance of database class
     */
	public static function initiate_db_class() {

		require_once(AVFR_DIR.'/includes/class-avfr-db.php');

		global $avfr_db;
		if ( null == $avfr_db) {
			$avfr_db = new Avfr_DB;
		}
		
		return $avfr_db;
	}


	/**
	 * Fired when the plugin is activated.
	 * @param    bool    $network_wide    Check is network wide or not
	 * @since    1.0
	 */
	public static function activate( $network_wide ) {

		$avfr_db = self::initiate_db_class();

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = $avfr_db->get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$avfr_db->single_activate();
				}

				restore_current_blog();

			} else {
				$avfr_db->single_activate();
			}

		} else {
			$avfr_db->single_activate();
		}

		$avfr_settings_main = array(
				'avfr_domain' => 'suggestions',
				'avfr_welcome' => __( 'Submit and vote for new features!', 'feature-request' ),
				'avfr_approve_features' => 'on',
				'avfr_voting_type' => 'like',
				'avfr_votes_limitation_time' => 'MONTH',
			);

		if ( '' == get_option( 'avfr_settings_main' ) ) {
			update_option( 'avfr_settings_main', $avfr_settings_main, 'no' );
		}


		$avfr_settings_features = array(
				'avfr_allowed_file_types' => 'image/jpeg,image/jpg,image/png,image/gif',
				'avfr_max_file_size' => '1024',
				'avfr_echo_type_size' => __( 'Please uplaod image file with jpg/jpeg/png/gif format >1024 KB size', 'feature-request' ),
				'avfr_related_feature_num' => '3'
			);

		if ( '' == get_option( 'avfr_settings_features' ) ) {
			update_option( 'avfr_settings_features', $avfr_settings_features, 'no' );
		}

	}

	/**
	 * Empty function, not used
	 * @since    1.0
	 */
	 private static function single_deactivate() {

	 }

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0
	 */
	public static function deactivate( $network_wide ) {

		$avfr_db = self::initiate_db_class();

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				$blog_ids = $avfr_db->get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					$avfr_db->single_deactivate();

				}

				restore_current_blog();

			} else {
				$avfr_db->single_deactivate();
			}

		} else {
			$avfr_db->single_deactivate();
		}

		update_option( 'avfr_post_registered', '0', 'no' );
		flush_rewrite_rules(false);

	}


	/**
	 * Fired when a new site is activated with a WPMU environment.
	 */
	public function activate_new_site( $blog_id ) {

		$avfr_db = self::initiate_db_class();

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		$avfr_db->single_activate();
		restore_current_blog();

	}

	/**
	 * Load text domain of plugin
	 * @since 1.0
	 */
	public function avfr_load_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$out = load_textdomain( $domain, trailingslashit( AVFR_DIR ). 'languages/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'feature-request', false, plugin_basename( dirname( __FILE__ ) ) . '../languages/' );
	}

}