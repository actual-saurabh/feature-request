<?php
/**
 *
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */
	
class Avfr_Template {

	function __construct() {

		add_filter( 'template_include', array($this,'template_loader'));

	}

	/**
	*
	* @since version 1.0
	* @param $template - return based on view
	* @return page template based on view regardless if the post type doesnt even exist yet due to no posts
	*/
	function template_loader( $template ) {

	   	if ( ( is_post_type_archive( 'avfr' ) || 'avfr' == get_post_type() || has_shortcode( isset( $post->post_content ) ? $post->post_content : null, 'feature_request') ) ):

	    	if ( $overridden_template = locate_template( 'template-avfr.php', true ) ) {

			   $template = load_template( $overridden_template );

			} else {

			   	$template = AVFR_DIR.'templates/template-avfr.php';
			}

	    endif;

	    return $template;

	}
}
new Avfr_Template;