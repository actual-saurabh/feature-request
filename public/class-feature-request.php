<?php
/**
 	*
 	* 	@package   			Feature-request
 	* 	@author    			Averta
 	* 	@license   			GPL-2.0+
 	* 	@link      			http://averta.net
 	*	@copyright 			2015 Averta
 	*
 **/	
 	
if(isset($_POST['submit'])){

    $post_id = wp_insert_post( array(
                    'post_status'     => 'publish',
                    'post_name'       => 'feature-request-st',
                    'post_type'       => 'page',
                    'post_title'      => 'Start Feature Request',
                    'post_content'    => '[feature_request hide_submit="off" hide_votes="off" hide_voting="off"]'
                ) );

$post_type = 'custom_type';

$query = "UPDATE {$wpdb->prefix}posts SET post_type='".$post_type."' WHERE id='".$post_id."' LIMIT 1";

GLOBAL $wpdb; 

$wpdb->query($query);

}
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
	private function __construct() {

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

		// Load plugin text domain
		add_action( 'init', 			array( $this, 'load_plugin_textdomain' ) );
		add_action('plugins_loaded', 	array($this,'upgrade'));
	}

	/**
	 * Return the plugin slug.
	 * @since    1.0
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 * @since     1.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 * @since    1.0
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

			$avfr_settings_main = array(
					'avfr_domain' => 'suggestions',
					'avfr_welcome' => __( 'Submit and vote for new features!', 'feature-request' ),
					'avfr_approve_features' => 'on',
					'avfr_voting_type' => 'like',
					'avfr_votes_limitation_time' => 'MONTH',
				);
			$avfr_settings_features = array(
					'avfr_allowed_file_types' => 'image/jpeg,image/jpg,image/png,image/gif',
					'avfr_max_file_size' => '1024',
					'avfr_echo_type_size' => __( 'Please uplaod image file with jpg/jpeg/png/gif format >1024 KB size', 'feature-request' ),
					'avfr_related_feature_num' => '3'
				);

		if ( '' == get_option( 'avfr_settings_main' ) ) {
			update_option( 'avfr_settings_main', $avfr_settings_main, '', 'no' );
		}

		if ( '' == get_option( 'avfr_settings_features' ) ) {
			update_option( 'avfr_settings_features', $avfr_settings_features, '', 'no' );
		}

		flush_rewrite_rules();

	}
	 private static function single_deactivate() {

	 }
	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}


	/**
	 * Fired when a new site is activated with a WPMU environment.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids
	 * @since    1.0
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}


	private static function single_activate() {


		global $wpdb;

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
	}

	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$out = load_textdomain( $domain, trailingslashit( AVFR_DIR ). 'languages/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	*	Run on plugin upgrade
	*	@since 1.0
	*/
	function upgrade(){

		$version = get_option('feature_request_version', true );

		if ( $version != AVFR_VERSION ) {

			self::upgrade_install_db();

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