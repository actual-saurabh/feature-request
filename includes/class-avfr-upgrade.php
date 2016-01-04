<?php
/**
 * Plugin upgrade
 *
 * @package             Feature-Request
 * @author              Averta
 * @license             GPL-2.0+
 * @link                http://averta.net
 * @copyright           2015 Averta
 *
 */

/**
* Upgrade class
*/
class AVFR_Upgrade {
	
	function __construct() {
		add_action('plugins_loaded', array($this, 'upgrade' ));
		add_action('init', array($this, 'avfr_upgrade_107_to_110' ));
	}

	/**
	 * Upgrade from version 1.0.7 to 1.1.0
	 * Move data from options table to termmeta
	 */
	public function avfr_upgrade_107_to_110() {

		if ( '1' != get_option('avfr_tax_option_moved') ) {

			$allgroups = get_terms( 'groups', array( 'hide_empty' => 0 ) );

			$moved = array();

			foreach ( $allgroups as $group ) {

				$max_votes = avfr_get_option('avfr_vote_limit_'.$group->slug,'avfr_settings_groups');
			    $total_votes = avfr_get_option('avfr_total_vote_limit_'.$group->slug,'avfr_settings_groups');
			    $comments_disabled = avfr_get_option('avfr_disable_comment_for'.$group->slug,'avfr_settings_groups');
			    $new_disabled = avfr_get_option('avfr_disable_new_for'.$group->slug,'avfr_settings_groups');

			    $term_id = $group->term_id;

			    update_term_meta( $term_id, 'avfr_max_votes', $max_votes );
			    update_term_meta( $term_id, 'avfr_total_votes', $total_votes );
			    update_term_meta( $term_id, 'avfr_comments_disabled', $comments_disabled );
			    update_term_meta( $term_id, 'avfr_new_disabled', $new_disabled );

			}

		    update_option( 'avfr_tax_option_moved', '1' );
		    delete_option( 'avfr_settings_groups' );

		}
	}


	/**
	 * Run on plugin upgrade
	 * @since 1.0 
	 */
	public function upgrade() {

		$version = get_option('feature_request_version', true );

		if ( $version != AVFR_VERSION ) {
			global $avfr_db;
			$avfr_db->upgrade_install_db();

		}
	}

}

new AVFR_Upgrade;