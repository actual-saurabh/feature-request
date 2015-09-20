<?php

/**
*
*	Class responsible for adding columns into the edit posts screen in the ideas post type
*	used for showing admins the status of an idea
*	@since 1.1
*/
class ideaFactoryColumnMods {

	function __construct(){

			add_filter('manage_ideas_posts_columns', 		array($this,'col_head'));
			add_action('manage_ideas_posts_custom_column', 	array($this,'col_content'), 10, 2);

	}

	/**
	*
	*	Log the columns
	*
	* 	@since    1.1
	*/
	function col_head( $item ) {

	    unset(
	    	$item['title'],
			$item['date'],
			$item['author'],
			$item['comments'],
			$item['taxonomy-groups'],
			$item['taxonomy-ideatags'],
			$item['flags']
		);

	    $item['title'] 		= __('Title','idea-factory');
	    $item['author'] 	= __('Author','idea-factory');
	    $item['comments']   = __('<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>','idea-factory');
	    $item['idea_status'] = __('Idea Status','idea-factory');
	    $item['taxonomy-groups']     = __('Groups','idea-factory');
	    $item['taxonomy-ideatags']   = __('<span class="dashicons-before dashicons-tag" title="Tags"><span class="screen-reader-text">Idea Tags</span></span>','idea-factory');
	    $item['flags']   = __('<span class="dashicons-before dashicons-flag" title="Flags"><span class="screen-reader-text">Flags</span></span>','idea-factory');
		$item['date'] 		= __('Date Published','idea-factory');

	    return $item;
	}

	/**
	* Callback for col_head
	* Show the status of an idea
	*
	* @since    1.1
	*/
	function col_content( $column_name, $post_ID ) {

	    if ( 'idea_status' == $column_name ) {

	       	$status = get_post_meta( $post_ID,'_idea_status', true );

	       	if ( 'approved' == $status ) {
	       		$color = '#5cb85c';
	       	} elseif ('declined' == $status ) {
	       		$color = '#d9534f';
	       	} elseif ('completed' == $status ) {
	       		$color = '#000000';
	       	} else {
				$status = __('open', 'idea_factory');
	       		$color = '#5bc0de';
	       	}

	        echo '<strong style="color:'.esc_attr( $color ).';">'.esc_html( ucfirst( $status ) ).'</strong>';

	    }
	    if ( 'flags' == $column_name ) {

	       echo get_post_meta( $post_ID,'_flag', true );

	    }
	}

}
new ideaFactoryColumnMods;