<?php

/*
*
*	Class responsible for building the template redirect
*
*/
class featurerequestAssetLoader {

	function __construct(){
		add_action('wp_enqueue_scripts', array($this,'scripts'), 99);
	}

	function scripts(){

		global $wp_query, $post;

		$disable_css    = avfr_get_option('if_disable_css','if_settings_advanced');

	 	$max 			=  $wp_query->max_num_pages;
	 	$paged 			= ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

	    if ( avfr_is_archive() || has_shortcode( isset( $post->post_content ) ? $post->post_content : null, 'feature_request') ):

	    	if ( 'on' !== $disable_css ) {
	    		wp_enqueue_style('dashicons');
	    		wp_enqueue_style('feature-request-css', AVFR_URL.'/public/assets/css/feature-request.css', AVFR_VERSION, true );
			}

			wp_enqueue_script('feature-request-script', AVFR_URL.'/public/assets/js/feature-request.js', array('jquery'), AVFR_VERSION, true);
			wp_localize_script('feature-request-script', 'feature_request', avfr_localized_args( $max , $paged) );

		endif;
	}
}
new featurerequestAssetLoader;