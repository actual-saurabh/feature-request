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
			'name'                		=> _x( 'Features','feature-request' ),
			'singular_name'       		=> _x( 'Feature','feature-request' ),
			'menu_name'           		=> __( 'Feature Request', 'feature-request' ),
			'name_admin_bar'            => _x( 'Feature', 'add new on admin bar', 'feature-request' ),
			'add_new'             		=> __( 'New Feature', 'feature-request' ),
			'add_new_item'        		=> __( 'Add New Feature', 'feature-request' ),
			'new_item'                  => __( 'New Feature', 'feature-request' ),
			'edit_item'           		=> __( 'Edit Feature', 'feature-request' ),
			'view_item'           		=> __( 'View Feature', 'feature-request' ),
			'all_items'           		=> __( 'All Features', 'feature-request' ),
			'search_items'        		=> __( 'Search Feature', 'feature-request' ),		
			'update_item'         		=> __( 'Update Feature', 'feature-request' ),
			'parent_item_colon'   		=> __( 'Parent Feature:', 'feature-request' ),
			'not_found'           		=> __( 'No Feature found', 'feature-request' ),
			'not_found_in_trash'  		=> __( 'No Feature found in Trash', 'feature-request' ),
		);
		$args = array(
			'labels'              		=> $labels,
			'label'               		=> __( 'Feature', 'feature-request' ),
			'description'         		=> __( 'Create votes', 'feature-request' ),
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
	 

	 	// Check that this plugin was installed bfore this or not
		if ( '' == get_option( 'avfr_installed_before' ) ) {

			// Defaults for adding post (feature request)
			$default_post = array(
				'post_type'	  	=> 'avfr',
				'post_title'	=> wp_strip_all_tags( __('New feature request', 'feature-request') ),
				'post_status'   => 'publish',
				'post_content' 	=> __('Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh
				 		euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud 
				 		exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure
				 		dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at
				 		vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te
				 		feugait nulla facilisi.', 'feature-request'),

				);
			// Insert post
			$entry_id = wp_insert_post( $default_post );
			//Insert group (category)
			wp_set_object_terms( $entry_id, __('Example group 1', 'feature-request') ,'groups');
			// Insert tags (featureTags)
			wp_set_object_terms( $entry_id, __('Example tag 1', 'feature-request') ,'featureTags');


			// Defaults for page with shortcode
			$avfr_page_def = array(
				'post_title'            =>    'Sample Feature Request',
				'post_status'           =>    'publish',
				'post_type'             =>    'page',
				'post_content'          =>    '[feature_request hide_submit="off" hide_votes="off" hide_voting="off"]'
				);
			// Insert as page
			$page_id = wp_insert_post ($avfr_page_def);

			// Default options for default category that added above
			$args = array( 'avfr_vote_limit_example-group-1' => '3',
				'avfr_total_vote_limit_example-group-1' => '30',
				'avfr_disable_comment_forexample-group-1' => 'off',
				'avfr_disable_new_forexample-group-1' => 'off',
			);
			//Insert options to database
			update_option( 'avfr_settings_groups', $avfr_settings_main, '', 'no' );

			//Update this option from 0 to 1, so the codes will runs only 1 time.
			add_option( 'avfr_installed_before', '1', '', 'no');

		}

		// Flush rewrite rules and update option.
		// The option wil be checked to flushing again.
		if ( '0' === get_option( 'avfr_post_registered' ) || '' == get_option( 'avfr_post_registered' ) ) {
			flush_rewrite_rules(false);
			update_option( 'avfr_post_registered', '1', '', 'no' );
		}

	}

}

new Avfr_Post_Type;
