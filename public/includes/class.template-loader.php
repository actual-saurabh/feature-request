<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class FeatureRequestTemplateLoader {

	function __construct() {

		add_filter( 'template_include', array($this,'template_loader'));

	}


	function template_loader( $template ) {

		$disable_archive = avfr_get_option('if_disable_archive','if_settings_advanced');

	   	if ( avfr_is_archive() && 'on' !== $disable_archive ):

	    	if ( $overridden_template = locate_template( 'template-features.php', true ) ) {

			   $template = load_template( $overridden_template );

			} else {

			   	$template = AVFR_DIR.'templates/template-features.php';
			}

	    endif;

	    return $template;

	}
}
new FeatureRequestTemplateLoader;