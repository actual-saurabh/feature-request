<?php
/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class Avfr_Columns {

	function __construct(){

			add_filter('manage_avfr_posts_columns', 		array($this,'col_head'));
			add_action('manage_avfr_posts_custom_column', 	array($this,'col_content'), 10, 2);

	}

	/**
	*
	*	Log the columns
	*
	* 	@since    1.0
	*/

	function col_head( $item ) {

	    unset(
	    	$item['title'],
			$item['date'],
			$item['author'],
			$item['comments'],
			$item['taxonomy-groups'],
			$item['taxonomy-featureTags'],
			$item['flags']
		);

	    $item['title'] 		= __('Title','Feature-request');
	    $item['author'] 	= __('Author','Feature-request');
	    $item['comments']   = __('<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>','Feature-request');
	    $item['avfr_status'] = __('Feature Status','Feature-request');
	    $item['taxonomy-groups']     = __('Groups','Feature-request');
	    $item['taxonomy-featureTags']   = __('<span class="dashicons-before dashicons-tag" title="Tags"><span class="screen-reader-text">Tags</span></span>','Feature-request');
	    $item['flags']   = __('<span class="dashicons-before dashicons-flag" title="Flags"><span class="screen-reader-text">Flags</span></span>','Feature-request');
		$item['date'] 		= __('Date Published','Feature-request');

	    return $item;
	}

	/**
	* 
	* Show the status of an feature
	*
	* @since    1.0
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
new Avfr_Columns;