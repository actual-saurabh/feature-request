<?php
/**
 *  @package            Feature-request
 *  @author             Averta
 *  @license            GPL-2.0+
 *  @link               http://averta.net
 *  @copyright          2015 Averta
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
			<a style="background:#d9534f;border:none;box-shadow:none;color:white;display:inline-block;margin-top:10px;" class="button feature-request-reset--votes" href="#"><?php _e('Reset Votes','feature-request');?></a>
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

			?><h2 style="margin-bottom:0;"><?php _e('Feature Request Documentation','feature-request');?></h2>
			<hr>

			<h3 style="margin-bottom:0;"><?php _e('The Basics','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('After you activate <em>Feature Request</em>, it will automatically be available at <a href="'.get_post_type_archive_link( 'suggestions' ).'" target="_blank">'.get_post_type_archive_link( 'suggestions' ).'</a>. You can rename this in the settings or deactivate it all together and use the shortcode instead. By default voting is limited to logged in users, however you can activate public voting that would work (in addition to) logged in voting.','feature-request');?></p>

			<hr style="margin-top:20px;">

			<h3 style="margin-bottom:0;"><?php _e('The Shortcodes','feature-request');?></h3>
			<p style="margin-top:5px;"><?php _e('You can additionally display the form and Features via a shortcode as documented below.','feature-request');?></p>

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

		if ( 'avfr_page_feature-request-reset' == $screen->id ) {

			?>
				<!-- Reset Votes -->
				<script>
					jQuery(document).ready(function($){
						// reset post meta
					  	jQuery('.feature-request-reset--votes').click(function(e){
                        var r = confirm('Are you sure to reset all votes?');
                            if ( r == false ) {
                                //continue
                            } else {

    					  		e.preventDefault();

    					  		var data = {
    					            action: 'feature-request_reset',
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
	function avfr_reset(){

		check_ajax_referer( 'feature-request-reset', 'security' );

		if ( !current_user_can('manage_options') )
			exit;

		$posts = get_posts( array('post_type' => 'avfr', 'posts_per_page' => -1 ) );

		if ( $posts ):

			foreach ( $posts as $post ) {

				$total_votes = get_post_meta( $post->ID, '_avfr_total_votes', true );
				$votes 		 = get_post_meta( $post->ID, '_avfr_votes', true );

				if ( !empty( $total_votes ) ) {
					update_post_meta( $post->ID, '_avfr_total_votes', 0 );
				}

				if ( !empty( $votes ) ) {
					update_post_meta( $post->ID, '_avfr_votes', 0 );
				}
			}

		endif;

        global $wpdb;

        $table = $wpdb->base_prefix.'feature_request';

        $delete = $wpdb->query('TRUNCATE TABLE '.$table.'');

		echo __('All votes reset!','feature-request');

		exit;

	}

	function submenu_page_callback() {

		echo '<div class="wrap">';
			?><h2><?php _e('Feature Request Settings','feature-request');?></h2><?php

			$this->settings_api->show_navigation();
        	$this->settings_api->show_forms();

		echo '</div>';

	}

    function get_settings_sections() {
        $sections = array(
            array(
                'id' 	=> 'avfr_settings_main',
                'title' => __( 'Setup', 'feature-request' ),
                'desc'  => __( 'Setting up plugin','feature-request' )
            ),
            array(
                'id' 	=> 'avfr_settings_features',
                'title' => __( 'features', 'feature-request' ),
                'desc'  => __( 'Features settings','feature-request' )
            ),
            array(
                'id' 	=> 'avfr_settings_groups',
                'title' => __( 'Groups', 'feature-request' ),
                'desc'  => __( 'Groups can have different settings','feature-request' )
            ),
            array(
                'id'    => 'avfr_settings_mail',
                'title' => __( 'E-Mail', 'feature-request' ),
                'desc'  => __( 'Email settings, you can select when and who should be recieve emails.','feature-request' )
            ),
           	array(
                'id' 	=> 'avfr_settings_advanced',
                'title' => __( 'Advanced', 'feature-request' ),
                'desc'  => __( 'Advanced plugin option','feature-request' )
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
                    'desc' 				=> '<a href="'.get_post_type_archive_link( 'avfr' ).'">'. __( 'Link to ideas page', 'feature-request' ) .'</a> - ' . __( 'By default its called Ideas. You can rename this here.', 'feature-request' ),
                    'type' 				=> 'text',
                    'default' 			=> __('suggestions','feature-request'),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                array(
                    'name' 				=> 'avfr_welcome',
                    'label' 			=> __( 'Welcome Message', 'feature-request' ),
                    'desc' 				=> __( 'Enter a message to display to users to vote. Some HTML ok.', 'feature-request' ),
                    'type' 				=> 'textarea',
                    'default' 			=> __('Submit and vote for new features!', 'feature-request'),
                    'sanitize_callback' => 'avfr_content_filter'
                ),
                array(
                    'name' 				=> 'avfr_approve_features',
                    'label' 			=> __( 'Require Feature Approval', 'feature-request' ),
                    'desc' 				=> __( 'Check this box to enable newly submitted ideas to be put into a pending status instead of automatically publishing.', 'feature-request' ),
                    'type'				=> 'checkbox',
                    'default' 			=> ''
                ),
                array(
                    'name' 				=> 'avfr_public_voting',
                    'label' 			=> __( 'Enable Public Voting', 'feature-request' ),
                    'desc' 				=> __( 'Enable the public (non logged in users) to submit and vote on new ideas.', 'feature-request' ),
                    'type'				=> 'checkbox',
                    'default' 			=> ''
                ),
            	array(
                    'name' 				=> 'avfr_threshold',
                    'label' 			=> __( 'Voting Threshold', 'feature-request' ),
                    'desc' 				=> __( 'Specify an optional number of votes that each feature must reach in order for its status to be automatically updated to "approved" , "declined", or "open."', 'feature-request' ),
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
                    'label'             => __( 'Disable Uplaod Files', 'feature-request' ),
                    'desc'              => __( 'Disable upload for feature factory form (if checked).', 'feature-request' ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),

                /**
                 *
                 *Disable captcha
                 *
                 */
                  array(
                    'name'              => 'avfr_disable_captcha',
                    'label'             => __( 'Disable Captcha ', 'feature-request' ),
                    'desc'              => __( 'Disable captcha code on submit form (if checked).', 'feature-request' ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                 /**
                 *Type of voting setting in admin panel
                 */
                array(
                    'name'              => 'avfr_voting_type',
                    'label'             => __( 'Select voting type', 'feature-request' ),
                    'desc'              => __( 'Users can vote 1-5 star or can score + and - to any feature.', 'feature-request' ),
                    'type'              => 'radio',
                    'options'           => array('vote' => 'Vote', 'like' => 'Like/Dislike' ),
                    'default'           => 'like'
                ),
                array(
                    'name'              => 'avfr_votes_limitation_time',
                    'label'             => __( 'Select voting limitation time', 'feature-request' ),
                    'desc'              => __( 'Set limit to user vote (one user can vote only 10 time in month by defults change it in groups section)', 'feature-request' ),
                    'type'              => 'radio',
                    'options'           => array('YEAR' => 'Year', 'MONTH' => 'Month', 'WEEK' => 'Week' ),
                    'default'           => 'MONTH'
                ),
                 /**
                 *Enable or disable flag in features
                 */
                array(
                    'name'              => 'avfr_flag',
                    'label'             => __( 'Show flag in features', 'feature-request' ),
                    'desc'              => __( 'If checked, users can report unpleasant features.', 'feature-request' ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                /**
                 *Enable or disable flag in features
                 */
                array(
                    'name'              => 'avfr_single',
                    'label'             => __( 'Single page for each feature', 'feature-request' ),
                    'desc'              => __( 'If checked, features has seprate single page and permalink goes activate!.', 'feature-request' ),
                    'type'              => 'checkbox',
                    'default'           => ''
                )
            ),
                /**
                 *features setting:upload,filesize,maximum character in title.
                 */
            'avfr_settings_features' 	=> array(
                /**
                 *Allowed file types
                 */
                array(
                    'name' 				=> 'avfr_allowed_file_types',
                    'label' 			=> __( 'Allowed file types', 'feature-request' ),
                    'desc' 				=> __( 'Enter file upload format that you want with above format', 'feature-request' ),
                    'type'				=> 'text',
                    'default' 			=> __('image/jpeg,image/jpg','feature-request'),
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                /**
                 *Maximum file size allowed
                 */
                array(
                    'name' 				=> 'avfr_max_file_size',
                    'label' 			=> __( 'Maximum allowed file size', 'feature-request' ),
                    'desc' 				=> __( 'Please enter maximum file size that user can be upload ! (Size Calcute in KB).', 'feature-request'  ),
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
                    'label'             => __( 'Tip upload Massage', 'feature-request' ),
                    'desc'              => __( 'Explain for your customer about image size and type that they can upload!', 'feature-request'  ),
                    'type'              => 'text',
                    'default'           => 'Please uplaod image file with jpg format > 1024 KB size!',
                    'sanitize_callback' => 'sanitize_text_field'
                ),
                 /**
                 *Number of related features to show
                 */
                array(
                    'name'              => 'avfr_related_feature_num',
                    'label'             => __( 'Number of related feature', 'feature-request' ),
                    'desc'              => __( 'Show familiar features (Enter 0 for disabling related feature show only in single page.)', 'feature-request' ),
                    'type'              => 'text',
                    'default'           => __( '3', 'feature-request' ),
                    'sanitize_callback' => 'avfr_sanitize_int'
                ),
            ),
            // setting for mail options (section : mail)
            // send_mail_status_reciever
            // mail_content_status_reciver
            'avfr_settings_mail' 	=> array(
                array(
                    'name'              => 'avfr_send_mail_approved_writer',
                    'label'             => __( 'Send mail if approved', 'feature-request' ),
                    'desc'              => __( 'If feature gone be approved, feature submitter will be inform via mail', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                array(
                    'name'              => 'avfr_mail_content_approved_writer',
                    'label'             => __( 'Text will be sent', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'desc'              => __('Above text will sent to feature submitter if feature gone approved!'),
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_approved_voters',
                    'label'             => __( 'Send mail to Voters', 'feature-request' ),
                    'desc'              => __( 'Send email to feature voters when feature approved.', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                array(
                    'name'              => 'avfr_mail_content_approved_voters',
                    'label'             => __( 'Content (approved feature ) (voters)', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_completed_writer',
                    'label'             => __( 'To writer if completed', 'feature-request' ),
                    'desc'              => __( 'Send email to feature writer when feature completed.', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                array(
                    'name'              => 'avfr_mail_content_completed_writer',
                    'label'             => __( 'Content (completed feature ) (writer)', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_completed_voters',
                    'label'             => __( 'To voters if approved', 'feature-request' ),
                    'desc'              => __( 'Send email to feature voters when feature completed.', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                array(
                    'name'              => 'avfr_mail_content_completed_voters',
                    'label'             => __( 'Content (completed feature ) (voters)', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_declined_writer',
                    'label'             => __( 'To writer if declined', 'feature-request' ),
                    'desc'              => __( 'Send email to feature writer when feature declined.', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => '',
                    'sanitize_callback' => 'avfr_sanitize_checkbox'
                ),
                array(
                    'name'              => 'avfr_mail_content_declined_writer',
                    'label'             => __( 'Content (declined feature ) (writer)', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
                array(
                    'name'              => 'avfr_send_mail_declined_voters',
                    'label'             => __( 'To voters if declined', 'feature-request' ),
                    'desc'              => __( 'Send email to feature voters when feature declined.', 'feature-request'  ),
                    'type'              => 'checkbox',
                    'default'           => ''
                ),
                array(
                    'name'              => 'avfr_mail_content_declined_voters',
                    'label'             => __( 'Content (declined feature ) (voters)', 'feature-request' ),
                    'type'              => 'textarea',
                    'default'           => '',
                    'sanitize_callback' => 'esc_textarea'
                ),
            ),
            'avfr_settings_groups'    => array(

            ),
            'avfr_settings_advanced' 	=> array(
            	array(
                    'name' 				=> 'avfr_disable_css',
                    'label' 			=> __( 'Disable Core CSS', 'feature-request' ),
                    'desc' 				=> __( 'Disable the core css file from loading.', 'feature-request' ),
                    'type'				=> 'checkbox',
                    'default' 			=> ''
                ),
                 array(
                    'name' 				=> 'avfr_disable_mail',
                    'label' 			=> __( 'Disable Emails', 'feature-request' ),
                    'desc' 				=> __( 'Disable the admin email notification of new submissions.', 'feature-request' ),
                    'type'				=> 'checkbox',
                    'default' 			=> ''
                ),
                array(
                    'name' 				=> 'avfr_disable_archive',
                    'label' 			=> __( 'Disable Archive', 'feature-request' ),
                    'desc' 				=> __( 'Disable the automatic archive. This assumes you will be using the shortcode instead to show the features on a page that you specify.', 'feature-request' ),
                    'type'				=> 'checkbox',
                    'default' 			=> ''
                )
            )
        );

		$taxonomy = 'groups';
		$terms = get_terms($taxonomy, array('hide_empty'=> false)); // Get all terms of a taxonomy
		if ( $terms && !is_wp_error($terms) ) {
				foreach ( $terms as $term ) { 
                    $settings_fields['avfr_settings_groups'][]=
                        array(
                            'name'              => 'avfr_group_'.$term->slug,
                            'label'              => __( '<h3 style="width:400px;">Settings for '.$term->name.' group</h3>' , 'feature-request' ),
                            'type'              => 'html',
                            'sanitize_callback' => 'avfr_sanitize_html'
                        );
					$settings_fields['avfr_settings_groups'][]=
						array(
		                    'name' 				=> 'avfr_vote_limit_'.$term->slug,
		                    'label' 			=> __( ' maximum votes :', 'feature-request' ),
                            'desc'              => __( 'Works only in vote mode.', 'feature-request' ),
		                    'default' 			=> __( '5', 'feature-request' ),
		                    'sanitize_callback' => 'avfr_sanitize_int'
		                );
		            $settings_fields['avfr_settings_groups'][]=
						array(
		                    'name' 				=> 'avfr_total_vote_limit_'.$term->slug,
		                    'label' 			=> __( 'Total vote:', 'feature-request' ),
		                    'default' 			=> __( '30', 'feature-request' ),
		                    'sanitize_callback' => 'avfr_sanitize_int'
		                );
                    $settings_fields['avfr_settings_groups'][]=
                        array(
                            'name'              => 'avfr_disable_comment_for'.$term->slug,
                            'label'             => __( 'Disable comments', 'feature-request' ),
                            'desc'              => __( 'Disable comments for selected group.', 'feature-request' ),
                            'default'           => '',
                            'type'              => 'checkbox'
                        );
                    $settings_fields['avfr_settings_groups'][]=
                        array(
                            'name'              => 'avfr_disable_new_for'.$term->slug,
                            'label'             => __( 'Disable submit new feature', 'feature-request' ),
                            'desc'              => __( 'Disable submitting new feature for selected group.', 'feature-request' ),
                            'default'           => '',
                            'type'              => 'checkbox'
                        );
				}
		}

        return $settings_fields;
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