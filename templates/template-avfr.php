<?php

/**
 * Feature Request template file
 *
 * This file is used for showing feature requests that submitted.
 * 
 * @package   			Feature-Request
 * @author    			Averta
 * @license   			GPL-2.0+
 * @link      			http://averta.net
 * @copyright 			2015 Averta
 *
 */
if ( current_user_can( 'manage_options' ) ) {

	if ( isset($_GET['action']) ) {
		if ( 'deletepost' === $_GET['action'] ) {
			$id = get_the_id(); 
			wp_trash_post($id);
			wp_safe_redirect( get_post_type_archive_link( 'avfr' ), $status = 302 );
		} 
	}

}

if ( isset( $_GET['redirect'] ) ) {
	
	exit;
}

get_header();
global $avfr_db;
$public_can_vote = avfr_get_option('avfr_public_voting','avfr_settings_main');
$single_allowed  = avfr_get_option('avfr_single','avfr_settings_main');
do_action('avfr_layout_before'); ?>
<div class="container">
	<main class="avfr-wrap site-main"  id="main" role="main">

		<?php
		global $wp_query;
		$term = $wp_query->get_queried_object();

		if (is_tax($term)) {
			if ( 'on' == get_term_meta( $term->term_id, 'avfr_new_disabled', true ) || ( 'on' != is_single() && $single_allowed ) ) { 
				_e('Submitting new feature for this group is closed.','feature-request');
			} else {
				echo avfr_submit_header();
			}
		} else {
			echo avfr_submit_header();
		}
		
		avfr_show_filters();
		
		do_action('avfr_before_entries'); ?>

		<section class="avfr-layout-main site-content">

			<?php

			if ( is_single() && 'on' != $single_allowed && ! current_user_can('manage_options') ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
				exit();
			} else {

				if ( have_posts() ):

					while( have_posts() ) : the_post();

				// setup some vars
				$id             = get_the_ID();
				$userid 		= get_current_user_ID();
				$ip 			= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 0;
				$has_voted 		= $avfr_db->avfr_has_vote_flag( $id, $ip, $userid, 'vote' );
				$total_votes 	= avfr_get_votes( $id );
				$status      	= avfr_get_status( $id );
				$status_class   = $status ? sprintf('avfr-entry-%s', $status ) : false;
				$groups 		= wp_get_post_terms( $id, 'groups', array("fields" => "all") );
				?>
				<article class="avfr-entry-wrap post <?php if ( is_single() ) { echo "single-post";	} ?> <?php echo sanitize_html_class( $status_class );?> <?php echo $has_voted ? 'avfr-hasvoted' : false;?>">
					<?php do_action('avfr_entry_wrap_top', $id ); ?>
					<div class="avfr-votes-area" id="avfr-<?php echo (int) $id;?>">
						<div class="avfr-controls">
							<div class="avfr-totals">
								
								<?php
								if ( $total_votes ) { ?>	
								<?php
								if ( 'on' == $total_votes ) { ?>

								<strong class="avfr-totals-num">1</strong><br>
								<span class="avfr-totals-label"><?php _e( 'vote','feature-request' ); ?></span>

								<?php

							} elseif ( !empty( $total_votes ) ) { ?>

							<strong class="avfr-totals-num"><?php echo $total_votes ?></strong><br>
							<span class="avfr-totals-label"><?php _e( 'votes','feature_request' ); ?></span>

							<?php
						} 
						?>
						<?php 
					} else { ?>

					<strong class="avfr-totals-num">0</strong><br>
					<span class="avfr-totals-label"><?php _e( 'vote','feature_request' ); ?></span>

					<?php
				} ?>

			</div>
			<?php
			if ( $avfr_db->avfr_is_voting_active( $id, $ip, $userid ) ) {
				echo avfr_vote_controls($id);
			} ?>
		</div>

		<?php echo avfr_vote_status( $id ); ?>
	</div>

	<header class="entry-header">
		<h2 class="entry-title">

			<?php 
			if ( ('on' == $single_allowed) || ( ('on' != $single_allowed)  && current_user_can('manage_options') ) ) { ?>
			<a href ="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php
		} else { the_title(); } ?>

	</h2>
	<?php 
	if ( is_single() ) { ?>
	<div class="entry-meta" role="category tag">
		<div class="avfr-short-group">
			<span class="dashicons dashicons-category"></span>
			<?php
			the_terms( $id, 'groups', ' ', ', ' );
			?>
		</div>
		<?php

		if ( false != has_term( '', 'featureTags', $id) ) {?>
		<div class="avfr-short-tags">
			<span class="dashicons dashicons-tag"></span>
			<?php
			the_terms( $id, 'featureTags', ' ', ', ' );
			?>
		</div>

		<?php
	}
	//comments option apply here
	$disabled_comment = get_term_meta( $groups[0]->term_id, 'avfr_comments_disabled', true );
	if ( 'on' == $disabled_comment ) {
		_e('Comments are closed for this feature.','feature-request');
	} else {
		?>
		<div>
			<span class="avfr-short-comment">
				<span class="dashicons dashicons-admin-comments"></span>
				<?php
				comments_popup_link( 'No comment', 'One comment', '% comments', get_permalink() ); ?>
			</span>
		</div>
		<?php echo avfr_flag_control($id, $ip, $userid); ?>
	</div>
	<?php if ( is_single() ) :?>
		<div id="avfr-avatar">
			<?php 
			avfr_get_author_avatar($id); ?>

			<div id="avfr-avatar-name">
				<p>
					<?php 
					avfr_get_author_name($id); _e( " shared this feature", 'feature_request' ); ?>
				</p>
				<p class="date"><?php the_time('F j, Y'); ?></p>
			</div>
		</div>

	<?php endif; ?>
	<?php
}
}
?>

</header>

<div class="avfr-entry entry-content">
	<?php
	the_content(); ?>
	<?php
	if ( has_post_thumbnail() && is_single() ) { 
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

		?>
		<div class="attachments">
			<p class="image-caption"> <?php _e('feature attachments:','feature-request'); ?> </p>
			<figure class="avfr-image post-image">
				<?php 
				echo '<a rel="lightbox" href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '">';
				the_post_thumbnail( 'thumbnail' );
				echo '</a>';
				?>

			</figure>
		</div>
		<?php 
	} ?>
</div>
<?php
if (is_single()) {
	if ( current_user_can( 'manage_options' ) ) {
		?>
		<div id="avfr-delete">	
			<span class="dashicons dashicons-trash"></span><a href="<?php echo add_query_arg( array( 'action' => 'deletepost' ), the_permalink() ); ?>"><?php _e( 'Delete post', 'feature-request' ); ?></a>
			<span class="dashicons dashicons-edit"></span> <?php edit_post_link( 'Edit Post', '', '', '' ); ?>
		</div>
		<?php }
	} ?>

	<?php 
	if ( is_single() ) {

		//Get number of related post from option
		$related_posts_num = avfr_get_option('avfr_related_feature_num','avfr_settings_features');
		if ( isset( $related_posts_num ) && ('0' != $related_posts_num) ) {								
			?>
			<div class="related-avfr-section">
				<h2 class="related-title"><?php _e('Related Features :','feature_request') ?></h2>
				<div class="related-avfr-list">
					<?php 
					//Get array of terms (Groups and featureTags)
					$featuretags 	= wp_get_post_terms( $id, 'featureTags', array("fields" => "all") );
					//Pluck out the IDs to get an array of IDS
					$featuretags_ids = wp_list_pluck($featuretags,'term_id');

					$related_query = new WP_Query (array(
						'post_type'    	 => 'avfr',
						'tax_query'	  	 => array('relation' => 'AND',
							array(
								'taxonomy'	 => 'groups',
								'field'    	 => 'slug',
								'terms'    	 => $groups[0]->slug,
								'operator' 	 => 'IN'
								),
							array(
								'taxonomy'	 => 'featureTags',
								'field'    	 => 'term_id',
								'terms'    	 => $featuretags_ids,
								'operator' 	 => 'IN'
								),
							),
						'posts_per_page'      => $related_posts_num,
						'ignore_sticky_post' => 1,
						'order_by' 			 => 'rand',
						'post__not_in' 		 => array($id)
						));

					if ( $related_query->have_posts() ) {
						while ($related_query->have_posts() ) : $related_query->the_post(); ?>
						<ul class="single-related">
							<li><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						</ul>
					<?php endwhile;
					wp_reset_query();
				} else { _e('No related feature exist.','feature-request'); }
				?>
			</div>	
		</div>
								<?php } // If related post number not set to 0
							}//if is single ?>

							<?php if ( ! is_single() ) { ?>

							<footer class="entry-meta" role="category tag">
								<div class="avfr-short-group">
									<span class="dashicons dashicons-category"></span>
									<?php
									the_terms( $id, 'groups', ' ', ', ' );
									?>
								</div>
								<?php
								if ( false != has_term( '', 'featureTags', $id) ) {?>
								<div class="avfr-short-tags">
									<span class="dashicons dashicons-tag"></span>
									<?php
									the_terms( $id, 'featureTags', ' ', ', ' );
									?>
								</div>

								<?php
							}
							//comments option apply here
							$disabled_comment = get_term_meta( $groups[0]->term_id, 'avfr_comments_disabled', true );
							if ( 'on' == $disabled_comment ) {
								_e('Comments are closed for this feature.','feature-request');
							} else {
								?>
								<div>
									<span class="avfr-short-comment">
										<span class="dashicons dashicons-admin-comments"></span>
										<?php comments_popup_link( 'No comment', 'One comment', '% comments', get_permalink() ); ?>
									</span>
								</div>
								<?php } ?>
							</footer>

							<?php
						}
						comments_template();

						do_action('avfr_entry_bottom', $id ); 
						?>

						<?php do_action('avfr_entry_wrap_bottom', $id ); ?>

					</article>

					<?php

					endwhile;

					wp_reset_query();

					else:

						apply_filters('avfr_no_features', _e('No features found. Why not submit one?','feature-request'));

					endif;
		}//if is single and single allowed
		?>
	</section>

	<?php do_action('avfr_after_entries'); ?>

</main>
</div>

<?php do_action('avfr_layout_after');

if (is_tax($term)) {
	if ( ! ( 'on' == avfr_get_option('disable_new_for'.$term->slug,'if_settings_groups') || ( is_single() && 'on' != $single_allowed ) ) ) { 
		echo avfr_submit_box();
	}
} else {
	echo avfr_submit_box();
}

get_footer();