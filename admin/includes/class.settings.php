<?php
/**
* creates setting tabs
*
* @since version 1.0
* @param null
* @return global settings
*/

require_once dirname( __FILE__ ) . '/class.settings-api.php';

if ( !class_exists('AVFR_Settings_Api_Wrap' ) ):
class AVFR_Settings_Api_Wrap {

    private $settings_api;

    const version = '1.0';

    function __construct() {

        $this->dir  		= plugin_dir_path( __FILE__ );
        $this->url  		= plugins_url( '', __FILE__ );
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', 						array($this, 'admin_init') );
        add_action( 'admin_menu', 						array($this, 'submenu_page'));
        add_action( 'admin_head', 						array($this, 'reset_votes'));
        add_action( 'wp_ajax_avfr_reset', 		        array($this, 'avfr_reset' ));

    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

	function submenu_page() {
		add_submenu_page( 'edit.php?post_type=avfr', 'Settings', __('Settings','feature-request'), 'manage_options', 'feature-request-settings', array($this,'submenu_page_callback') );
		add_submenu_page( 'edit.php?post_type=avfr', 'Help', __('Help','feature-request'), 'manage_options', 'feature-request-docs', array($this,'docs_callback') );
		add_submenu_page( 'edit.php?post_type=avfr', 'Reset', __('Reset','feature-request'), 'manage_options', 'feature-request-reset', array($this,'reset_callback') );
	}

	/**
	*
	*	Allow admins to reset the votes
	*
	*/
	function reset_callback(){



		echo '<div class="wrap">';

			?><h2><?php _e('feature request Reset','feature-request');?></h2>

			<label style="display:block;margin-top:20px;"><?php _e('Click the button below to reset votes. Warning, there is no going back!','feature-request');?></label>
			<a style="background:#d9534f;border:none;box-shadow:none;color:white;display:inline-block;margin-top:10px;" class="button idea-factory-reset--votes" href="#"><?php _e('Reset Votes','feature-request');?></a>
			<?php 

		echo '</div>';


	}

	/**
	*
	*	Documentation page callback
	*
	*/
	function docs_callback(){

		$domain = avfr_get_option('avfr_domain','avfr_settings_main','suggestions');

		echo '<div class="wrap">';

			?><h2 style="margin-bottom:0;"><?php _e('Idea Factory Documentation','feature-request');?></h2>
			<hr>

			<h3 style="margin-bottom:0;"><?php _e('The Basics','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('After you activate <em>Idea Factory</em>, it will automatically be available at <a href="'.get_post_type_archive_link( 'suggestions' ).'" target="_blank">'.get_post_type_archive_link( 'suggestions' ).'</a>. You can rename this in the settings or deactivate it all together and use the shortcode instead. By default voting is limited to logged in users, however you can activate public voting that would work (in addition to) logged in voting.','feature-request');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('The Shortcodes','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('You can additionally display the form and ideas via a shortcode as documented below.','feature-request');?></p>

			<code>[idea_factory hide_submit="off" hide_votes="off" hide_voting="off" groups="2,5,12"]</code>
            
			<ul>
				<li><strong><?php _e('Hide Submit','feature-request');?></strong> - <?php _e('Set this to "on" to hide the submission button and form.','feature-request');?></li>
				<li><strong><?php _e('Hide Votes','feature-request');?></strong> - <?php _e('Set this to "on" to hide the votes.','feature-request');?></li>
                <li><strong><?php _e('Hide Voting','feature-request');?></strong> - <?php _e('Set this to "on" to hide the voting features.','feature-request');?></li>
				<li><strong><?php _e('Groups','feature-request');?></strong> - <?php _e('Set groups IDs for show only the specific groups.','feature-request');?></li>
			</ul>

            <code>[idea_factory_user_votes hide_total="off" hide_remaining="off" groups="1,5,12"]</code>

            <ul>
                <li><strong><?php _e('groups','feature-request');?></strong> - <?php _e('Set groups IDs for show only total or remaining votes for specific groups. If not set, all groups will be shown. ','feature-request');?></li>
                <li><strong><?php _e('hide_total','feature-request');?></strong> - <?php _e('Set this to "on" to hide the total votes.','feature-request');?></li>
                <li><strong><?php _e('hide_remaining','feature-request');?></strong> - <?php _e('Set this to "on" to hide the remaining voting.','feature-request');?></li>
            </ul>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('How Voting Works','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('Voting is available to logged in users, and logged out users (with the option enabled). Total votes are stored in the post meta table for (logged in users). Once a user votes, a flag is recorded in the user_meta table (logged in users), preventing this user from being able to vote again on the same idea.</br></br>In the case of public voting, voters IP addresses are recorded into a custom table. From there the logic works the same, only difference is where the data is stored.','feature-request');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('How the Threshold Works','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('The threshold allows individual ideas to automatically be assigned a status based on a grading formula. For example, if you set this threshold to 10, then when the total votes reaches 10 it will trigger the grading. A vote up, and vote down, both count. In the end, if the total votes is over 10, and the total up votes is over 10, it passes. If not, it fails. Otherwise, the status remains open.','feature-request');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('Reset','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('On your left you will see the Reset option. When you click into this menu, and you click the red Reset button, it will reset all the votes back to zero. There is no going back, so be sure this is what you want to do when you click that button.','feature-request');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('Developers','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('Full documentation of hooks, actions, filters, and helper functions are available on the GitHub wiki page located <a href="https://github.com/tmeister/feature-request/wiki">here</a>','feature-request');?>.</p>

			<?php


		echo '</div>';
	}

	/**
	*
	*	Handl the click event for resetting votes
	*
	*/
	function reset_votes() {

		$nonce = wp_create_nonce('feature-request-reset');

		$screen = get_current_screen();

		if ( 'ideas_page_feature-request-reset' == $screen->id ) {

			?>
				<!-- Reset Votes -->
				<script>
					jQuery(document).ready(function($){
						// reset post meta
					  	jQuery('.idea-factory-reset--votes').click(function(e){
                        var r = confirm('Are you sure to reset all votes?');
                            if ( r == false ) {
                                //continue
                            } else {

    					  		e.preventDefault();

    					  		var data = {
    					            action: 'idea_factory_reset',
    					            security: '<?php echo $nonce;?>'
    					        };

    						  	jQuery.post(ajaxurl, data, function(response) {
    						  		if( response ){
    						        	alert(response);
    						        	location.reload();
    						  		}
    						    });
                            }

					    });
					});
				</script>

		<?php }

	}

	/**
	*
	*	Process the votes reste
	*	@since 1.1
	*/
	function idea_factory_reset(){

		check_ajax_referer( 'feature-request-reset', 'security' );

		if ( !current_user_can('manage_options') )
			exit;

		$posts = get_posts( array('post_type' => 'avfr', 'posts_per_page' => -1 ) );

		if ( $posts ):

			foreach ( $posts as $post ) {

				$total_votes = get_post_meta( $post->ID, '_idea_total_votes', true );
				$votes 		 = get_post_meta( $post->ID, '_idea_votes', true );

				if ( !empty( $total_votes ) ) {
					update_post_meta( $post->ID, '_idea_total_votes', 0 );
				}

				if ( !empty( $votes ) ) {
					update_post_meta( $post->ID, '_idea_votes', 0 );
				}
			}

		endif;

        global $wpdb;

        $table = $wpdb->base_prefix.'feature_request';

        $delete = $wpdb->query('TRUNCATE TABLE '.$table.'');

		echo __('All votes reset!','idea-factory');

		exit;

	}

	function submenu_page_callback() {

		echo '<div class="wrap">';
			?><h2><?php _e('Idea Factory Settings','idea-factory');?></h2><?php

			$this->settings_api->show_navigation();
        	$this->settings_api->show_forms();

		echo '</div>';

	}

    function get_settings_sections() {
        $sections = array(
            array(
                'id' 	=> 'avfr_settings_main',
                'title' => __( 'Setup', 'idea-factory' ),
                'desc'  => __( 'Setting up plugin','idea-factory' )
            ),
            array(
                'id' 	=> 'avfr_settings_features',
                'title' => __( 'features', 'idea-factory' ),
                'desc'  => __( 'Ideas settings (file uploading and character limitation)','idea-factory' )
            ),
            array(
                'id' 	=> 'avfr_settings_vote_system',
                'title' => __( 'Groups', 'idea-factory' ),
                'desc'  => __( 'Groups can have different settings','idea-factory' )
            ),
            array(
                'id'    => 'avfr_settings_mail',
                'title' => __( 'E-Mail', 'idea-factory' ),
                'desc'  => __( 'Email settings, you can select when and who should be recieve emails.','idea-factory' )
            ),
           	array(
                'id' 	=> 'avfr_settings_advanced',
                'title' => __( 'Advanced', 'idea-factory' ),
                'desc'  => __( 'Advanced plugin option','idea-factory' )
            )

        );
        return $sections;
    }

    function get_settings_fields() {

		$domain 	= avfr_get_option('avfr_domain','avfr_settings_main','suggestions');

        $settings_fields = array(
            'avfr_settings_main' => array(
            	array(
                    'name' 				=> 'avfr_domain',
                    'label' 			=> __( 'Naming Convention', 'feature-request' ),
                    'desc' 				=> '<a href="'.get_post_type_archive_link( 'suggestions' ).'">'. __( 'Link to ideas page', 'feature-request' ) .'</a> - ' . __( 'By default its called Ideas. You can rename this here.', 'feature-request' ),
                    'type' 				=> 'text',
                    'default' 			=> __('suggestions','idea-factory'),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' 				=> 'avfr_welcome',
                    'label' 			=> __( 'Welcome Message', 'feature-request' ),
                    'desc' 				=> __( 'Enter a message to display to users to vote. Some HTML ok.', 'feature-request' ),
                    'type' 				=> 'textarea',
                    'default' 			=> __('Submit and vote for new features!', 'idea-factory'),
                    'sanitize_callback' => 'avfr_content_filter'
                ),
                array(
                    'name' 				=> 'avfr_approve_ideas',
                    'label' 			=> __( 'Require Idea Approval', 'idea-factory' ),
                    'desc' 				=> __( 'Check this box to enable newly submitted ideas to be put into a pending status instead of automatically publishing.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name' 				=> 'avfr_public_voting',
                    'label' 			=> __( 'Enable Public Voting', 'idea-factory' ),
                    'desc' 				=> __( 'Enable the public (non logged in users) to submit and vote on new ideas.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
            	array(
                    'name' 				=> 'avfr_threshold',
                    'label' 			=> __( 'Voting Threshold', 'idea-factory' ),
                    'desc' 				=> __( 'Specify an optional number of votes that each idea must reach in order for its status to be automatically updated to "approved" , "declined", or "open."', 'idea-factory' ),
                    'type' 				=> 'text',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_int'
                ),
                                 /**
                 *
                 *Disable Upload
                 *
                 */
                   array(
                    'name'              => 'avfr_disable_upload',
                    'label'             => __( 'Disable Uplaod Files', 'idea-factory' ),
                    'desc'              => __( 'Disable upload for idea factory form (if checked).', 'idea-factory' ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),

                /**
                 *
                 *Disable captcha
                 *
                 */
                  array(
                    'name'              => 'avfr_disable_captcha',
                    'label'             => __( 'Disable Captcha ', 'idea-factory' ),
                    'desc'              => __( 'Disable captcha code on submit form (if checked).', 'idea-factory' ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                 /**
                 *Type of voting setting in admin panel
                 */
                array(
                    'name'              => 'avfr_voting_type',
                    'label'             => __( 'Select voting type', 'idea-factory' ),
                    'desc'              => __( 'Users can vote 1-5 star or can score + and - to any idea.', 'idea-factory' ),
                    'type'              => 'radio',
                    'options'           => array('vote' => 'Vote', 'like' => 'Like/Dislike' ),
                    'default'           => 'like'
                ),
                array(
                    'name'              => 'avfr_votes_limitation_time',
                    'label'             => __( 'Select voting limitation time', 'idea-factory' ),
                    'desc'              => __( 'Set limit to user vote (one user can vote only 10 time in month by defults change it in groups section)', 'idea-factory' ),
                    'type'              => 'radio',
                    'options'           => array('YEAR' => 'Year', 'MONTH' => 'Month', 'WEEK' => 'Week' ),
                    'default'           => 'MONTH'
                ),
                 /**
                 *Enable or disable flag in ideas
                 */
                array(
                    'name'              => 'avfr_flag',
                    'label'             => __( 'Show flag in ideas', 'idea-factory' ),
                    'desc'              => __( 'If checked, users can report unpleasant ideas.', 'idea-factory' ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                /**
                 *Enable or disable flag in ideas
                 */
                array(
                    'name'              => 'avfr_single',
                    'label'             => __( 'Single page for each idea', 'idea-factory' ),
                    'desc'              => __( 'If checked, ideas has seprate single page and permalink goes activate!.', 'idea-factory' ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                )
            ),
                /**
                 *Ideas setting:upload,filesize,maximum character in title.
                 */
            'avfr_settings_ideas' 	=> array(
                /**
                 *Allowed file types
                 */
                array(
                    'name' 				=> 'avfr_allowed_file_types',
                    'label' 			=> __( 'Allowed file types', 'idea-factory' ),
                    'desc' 				=> __( 'Enter file upload format that you want with above format', 'idea-factory' ),
                    'type'				=> 'text',
                    'default' 			=> __('image/jpeg,image/jpg','idea-factory'),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                /**
                 *Maximum file size allowed
                 */
                array(
                    'name' 				=> 'avfr_max_file_size',
                    'label' 			=> __( 'Maximum allowed file size', 'idea-factory' ),
                    'desc' 				=> __( 'Please enter maximum file size that user can be upload ! (Size Calcute in KB).', 'idea-factory'  ),
                    'type'				=> 'text',
                    'default' 			=> '1024', // KB
                    'sanitize_callback' => 'avfr_sanitize_int'
                ),
                /**
                 *
                 *echo the explanition about size and type
                 *
                 */
                 array(
                    'name'              => 'avfr_echo_type_size',
                    'label'             => __( 'Tip upload Massage', 'idea-factory' ),
                    'desc'              => __( 'Explain for your customer about image size and type that they can upload!', 'idea-factory'  ),
                    'type'              => 'text',
                    'default'           => 'Please uplaod image file with jpg format > 1024 KB size!',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                 /**
                 *Number of related ideas to show
                 */
                array(
                    'name'              => 'avfr_related_idea_num',
                    'label'             => __( 'Number of related idea', 'idea-factory' ),
                    'desc'              => __( 'Show familiar ideas (Enter 0 for disabling related idea show only in single page.)', 'idea-factory' ),
                    'type'              => 'text',
                    'default'           => __( '3', 'idea-factory' ),
                    'sanitize_callback' => 'avfr_sanitize_int'
                ),
            ),
            // setting for mail options (section : mail)
            // send_mail_status_reciever
            // mail_content_status_reciver
            'avfr_settings_mail' 	=> array(
                array(
                    'name'              => 'avfr_send_mail_approved_writer',
                    'label'             => __( 'Send mail if approved', 'idea-factory' ),
                    'desc'              => __( 'If idea gone be approved, idea submitter will be inform via mail', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_approved_writer',
                    'label'             => __( 'Text will be sent', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'desc'              => __('Above text will sent to idea submitter if idea gone approved!'),
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_approved_voters',
                    'label'             => __( 'Send mail to Voters', 'idea-factory' ),
                    'desc'              => __( 'Send email to idea voters when idea approved.', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_approved_voters',
                    'label'             => __( 'Content (approved idea ) (voters)', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_completed_writer',
                    'label'             => __( 'To writer if completed', 'idea-factory' ),
                    'desc'              => __( 'Send email to idea writer when idea completed.', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_completed_writer',
                    'label'             => __( 'Content (completed idea ) (writer)', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_completed_voters',
                    'label'             => __( 'To voters if approved', 'idea-factory' ),
                    'desc'              => __( 'Send email to idea voters when idea completed.', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_completed_voters',
                    'label'             => __( 'Content (completed idea ) (voters)', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_declined_writer',
                    'label'             => __( 'To writer if declined', 'idea-factory' ),
                    'desc'              => __( 'Send email to idea writer when idea declined.', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_declined_writer',
                    'label'             => __( 'Content (declined idea ) (writer)', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_declined_voters',
                    'label'             => __( 'To voters if declined', 'idea-factory' ),
                    'desc'              => __( 'Send email to idea voters when idea declined.', 'idea-factory'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_declined_voters',
                    'label'             => __( 'Content (declined idea ) (voters)', 'idea-factory' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
            ),
            'avfr_settings_vote_system'    => array(

            ),
            'avfr_settings_advanced' 	=> array(
            	array(
                    'name' 				=> 'avfr_disable_css',
                    'label' 			=> __( 'Disable Core CSS', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the core css file from loading.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                 array(
                    'name' 				=> 'avfr_disable_mail',
                    'label' 			=> __( 'Disable Emails', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the admin email notification of new submissions.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name' 				=> 'avfr_disable_archive',
                    'label' 			=> __( 'Disable Archive', 'idea-factory' ),
                    'desc' 				=> __( 'Disable the automatic archive. This assumes you will be using the shortcode instead to show the ideas on a page that you specify.', 'idea-factory' ),
                    'type'				=> 'checkbox',
                    'default' 			=> '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                )
            )
        );

            /**
            *Add text field in setting for each product
            *Added by hosein71
            *Commited on 8/2/15 3:48
            */
		$taxonomy = 'groups';
		$terms = get_terms($taxonomy,array('hide_empty'=> false,)); // Get all terms of a taxonomy
		if ( $terms && !is_wp_error($terms) ) {
			if ( $this->settings_api->get_option('voting_type','avfr_settings_ideas','')=='vote' ) { // If voting option is set to votes
				foreach ( $terms as $term ) { 
                    $settings_fields['avfr_settings_vote_system'][]=
                        array(
                            'name'              => 'avfr_group_'.$term->slug,
                            'label'              => __( '<h3 style="width:400px;">Settings for '.$term->name.' group</h3>' , 'idea-factory' ),
                            'type'              => 'html',
                            'sanitize_callback' => 'idea_factory_sanitize_html'
                        );
					$settings_fields['avfr_settings_vote_system'][]=
						array(
		                    'name' 				=> 'avfr_vote_limit_'.$term->slug,
		                    'label' 			=> __(' maximum votes :', 'idea-factory' ),
		                    'default' 			=> __( '5', 'idea-factory' ),
		                    'sanitize_callback' => 'avfr_sanitize_int'
		                );
		            $settings_fields['avfr_settings_vote_system'][]=
						array(
		                    'name' 				=> 'avfr_total_vote_limit_'.$term->slug,
		                    'label' 			=> __( 'Total vote:', 'idea-factory' ),
		                    'default' 			=> __( '30', 'idea-factory' ),
		                    'sanitize_callback' => 'avfr_sanitize_int'
		                );
                    $settings_fields['avfr_settings_vote_system'][]=
                        array(
                            'name'              => 'avfr_disable_comment_for'.$term->slug,
                            'label'             => __( 'Disable comments', 'idea-factory' ),
                            'desc'              => __( 'Disable comments for selected group.', 'idea-factory' ),
                            'default'           => '',
                            'type'              => 'checkbox',
                            'sanitize_callback' => 'avfr_sanitize_checkbox'
                        );
                    $settings_fields['avfr_settings_vote_system'][]=
                        array(
                            'name'              => 'avfr_disable_new_for'.$term->slug,
                            'label'             => __( 'Disable submit new idea', 'idea-factory' ),
                            'desc'              => __( 'Disable submitting new idea for selected group.', 'idea-factory' ),
                            'default'           => '',
                            'type'              => 'checkbox',
                            'sanitize_callback' => 'avfr_sanitize_checkbox'
                        );
				}

			}
		}

        return $settings_fields;
    }

    /**
    *
    *	Sanitize checkbox input
    *
    */
    function avfr_sanitize_checkbox( $input ) {

		if ( $input ) {

			$output = '1';

		} else {

			$output = '0';

		}

		return $output;
	}

	/**
	*
	*	Sanitize integers
	*
	*/
	function avfr_sanitize_int( $input ) {

		if ( $input ) {

			$output = absint( $input );

		} else {

			$output = false;

		}

		return $output;
	}
}
endif;

$settings = new AVFR_Settings_Api_Wrap();