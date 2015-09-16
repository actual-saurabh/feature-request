<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class RequestFeatureColumnMods {

	function __construct(){

			add_filter('manage_avfr_posts_columns', 		array($this,'col_head'));
			add_action('manage_avfr_posts_custom_column', 	array($this,'col_content'), 10, 2);

	}

	/**
	*
	*	Log the columns
	*
	*/
	function col_head( $item ) {

	    unset(
	    	$item['title'],
			$item['date'],
			$item['author'],
			$item['comments'],
			$item['taxonomy-groups'],
			$item['taxonomy-featuretags'],
			$item['flags']
		);

	    $item['title'] 		= __('Title','feature-request');
	    $item['author'] 	= __('Author','feature-request');
	    $item['comments']   = __('<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>','feature-request');
	    $item['idea_status'] = __('Feature Status','feature-request');
	    $item['taxonomy-groups']     = __('Groups','feature-request');
	    $item['taxonomy-featuretags']   = __('<span class="dashicons-before dashicons-tag" title="Tags"><span class="screen-reader-text">Idea Tags</span></span>','feature-request');
	    $item['flags']   = __('<span class="dashicons-before dashicons-flag" title="Flags"><span class="screen-reader-text">Flags</span></span>','feature-request');
		$item['date'] 		= __('Date Published','feature-request');

	    return $item;
	}

	/**
	* Callback for col_head
	* Show the status of an idea
	*/
	function col_content( $column_name, $post_ID ) {

	    if ( 'avfr_status' == $column_name ) {

	       	$status = get_post_meta( $post_ID,'_avfr_status', true );

	       	if ( 'approved' == $status ) {
	       		$color = '#5cb85c';
	       	} elseif ('declined' == $status ) {
	       		$color = '#d9534f';
	       	} elseif ('completed' == $status ) {
	       		$color = '#000000';
	       	} else {
				$status = __('open', 'feature_request');
	       		$color = '#5bc0de';
	       	}

	        echo '<strong style="color:'.esc_attr( $color ).';">'.esc_html( ucfirst( $status ) ).'</strong>';

	    }
	    if ( 'flags' == $column_name ) {

	       echo get_post_meta( $post_ID,'_flag', true );

	    }
	}

}
new RequestFeatureColumnMods;