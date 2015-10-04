<?php

/**
 * 	@package   			Feature-request
 * 	@author    			Averta
 * 	@license   			GPL-2.0+
 * 	@link      			http://averta.net
 *	@copyright 			2015 Averta
 */

class Avfr_Post_Type {

	public function __construct(){

       	add_action('init',array($this,'avfr_post_type'));
	}
	/**
	 	* 
	 	* Creates a post type
	 	* 
	*/
	function avfr_post_type() {

		$domain = avfr_get_option('avfr_domain','avfr_settings_main','suggestions');

		$labels = array(
			'name'                		=> _x( 'Features','Feature-request' ),
			'singular_name'       		=> _x( 'Feature','Feature-request' ),
			'menu_name'           		=> __( 'Feature Request', 'Feature-request' ),
			'name_admin_bar'            => _x( 'Feature', 'add new on admin bar', 'Feature-request' ),
			'add_new'             		=> __( 'New Feature', 'Feature-request' ),
			'add_new_item'        		=> __( 'Add New Feature', 'Feature-request' ),
			'new_item'                  => __( 'New Feature', 'Feature-request' ),
			'edit_item'           		=> __( 'Edit Feature', 'Feature-request' ),
			'view_item'           		=> __( 'View Feature', 'Feature-request' ),
			'all_items'           		=> __( 'All Features', 'Feature-request' ),
			'search_items'        		=> __( 'Search Feature', 'Feature-request' ),		
			'update_item'         		=> __( 'Update Feature', 'Feature-request' ),
			'parent_item_colon'   		=> __( 'Parent Feature:', 'Feature-request' ),
			'not_found'           		=> __( 'No Feature found', 'Feature-request' ),
			'not_found_in_trash'  		=> __( 'No Feature found in Trash', 'Feature-request' ),
		);
		$args = array(
			'labels'              		=> $labels,
			'label'               		=> __( 'Feature', 'Feature-request' ),
			'description'         		=> __( 'Create votes', 'Feature-request' ),
			'labels'              		=> $labels,
			'supports'            		=> array( 'editor','title', 'comments', 'author','thumbnail' ), //featured image 
			'rewrite' 					=> array( 'slug' => 'suggestions','pages' =>true ),
			'public'					=> true,
			'publicly_queryable'        => true,
			'show_ui' 					=> true,
			'show_in_menu'              => true,
			'query_var'                 => true,
			'rewrite' 					=> array( 'slug' => 'suggestions','pages' =>true ),
			'capability_type' 			=> 'post',
			'has_archive'				=> $domain,
			'menu_icon'					=> 'dashicons-megaphone',
			'can_export' 				=> true,
			'hierarchical'              => false,
		    'menu_position'             => null,
			'supports'            		=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ) 
		);

		register_post_type( 'avfr', apply_filters('avfr_type_args', $args ) );

		// Hierarchical taxonomy for features
		$labels = array(
			'name' 						=> _x( 'Groups','taxonomy general name' ), 
			'singular_name' 			=> _x( 'Groups','taxonomy singular name' ),
			'search_items' 				=> __( 'Search groups' ),
			'all_items' 				=> __( 'All Groups' ),
			'parent_items' 				=> __( 'Parent group' ),
			'parent_item_colon' 		=> __( 'Parent groups' ),
			'edit_item' 				=> __( 'Edit group' ),
			'update_item'				=> __( 'Update group' ),
			'add_new_item'				=> __( 'Add group' ),
			'update_count_callback' 	=> '_update_post_term_count',
			'menu_name' 				=> __( 'Groups' ),
			);
		$args = array(
			'hierarchical' 				=> true ,
			'labels' 					=> $labels ,
			'show_ui'					=> true ,
			'show_admin_column' 		=> true ,
			'query_var' 				=> true ,
			'rewrite' 					=> array( 'slug' => 'groups' ),
			'update_count_callback' 	=> '_update_post_term_count',
			);
		register_taxonomy('groups' , array('avfr') , $args);

		// Non hierarchical taxonomy for features
		$labels = array(
			'name' 						=> _x( 'Feature tags','taxonomy general name' ), 
			'singular_name' 			=> _x( 'Feature tags','taxonomy singular name' ),
			'search_items' 				=> __( 'Search tags' ),
			'all_items' 				=> __( 'All tags' ),
			'edit_item'                 => __( 'Edit tag' ),
			'update_item'               => __( 'Update tag' ),
			'add_new_item'              => __( 'Add New tag' ),
			'new_item_name'             => __( 'New tag Name' ),
			'separate_items_with_commas'=> __( 'Separate tags with commas' ),
			'add_or_remove_items'       => __( 'Add or remove tag' ),
			'choose_from_most_used'     => __( 'Choose from the most used tags' ),
			'not_found'                 => __( 'No tag found.' ),
			'update_item'				=> __( 'Update tags' ),
			'add_new_item'				=> __( 'Add tag' ),
			'menu_name' 				=> __( 'Tags' ),
			);
		$args = array(
			'hierarchical' 				=> false , 
			'labels' 					=> $labels ,
			'show_ui'					=> true ,
			'show_admin_column' 		=> true ,
			'update_count_callback' 	=> '_update_post_term_count',
			'rewrite' 					=> array( 'slug' => 'avfrtags' ),
			);
		register_taxonomy('featureTags' , array('avfr') , $args);
		
		function default_post() {
			if ( get_option($check_installed_bef,0) == "0" ); {
				//default page
			$avfr_page_def = array(
				'post_name'             =>    'first-defualt-feature-request',
				'post_title'            =>    'Sample Feature Request',
				'post_status'           =>    'publish',
				'post_type'             =>    'page',
				'post_content'          =>    '[feature_request hide_submit="off" hide_votes="off" hide_voting="off"]'
				);
			wp_insert_post ($avfr_page_def);
		}

		add_action('admin_init','default_post' );
	  }

}

new Avfr_Post_Type;
