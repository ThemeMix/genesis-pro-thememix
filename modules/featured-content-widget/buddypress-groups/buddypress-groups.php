<?php

add_filter( 'thememix_featured_content_form_fields', 'themefix_buddypress_groups_settings_extension' );
function themefix_buddypress_groups_settings_extension( $args ) {

	if ( ! class_exists( 'BP_Groups_Group' ) ) {
		return $args;
	}

	$groups = BP_Groups_Group::get(array(
		'type'     => 'alphabetical',
		'per_page' => 100
	));
	$the_groups = array();
	foreach ( $groups['groups'] as $key => $group ) {
		$the_groups[$group->id] = $group->name;
	}

	$extra = array(
		'buddypress-group' => array(
			'label'       => __( 'Display a BuddyPress Group activity instead of post(s)', 'thememix-pro-genesis' ),
			'description' => '',
			'type'        => 'checkbox',
		),
		'buddypress-group-group' => array(
			'label'       => __( 'BuddyPress Group', 'thememix-pro-genesis' ),
			'description' => '',
			'type'        => 'select',
			'options'     => $the_groups,
			'requires'    => array(
				'buddypress-group',
				'',
				true
			),
		),
		'buddypress-group-count' => array(
			'label'       => __( 'Number of activities to show', 'thememix-pro-genesis' ),
			'description' => '',
			'type'        => 'select',
			'options'     => array( 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 9 ),
			'requires'    => array(
				'buddypress-group',
				'',
				true
			),
		),
	);

	$args['col1'][0] = array_slice( $args['col1'][0], 0, 1, true ) +
		$extra +
		array_slice( $args['col1'][0], 1, count( $args['col1'][0] ) - 1, true) ;

	return $args;
}

function themefix_buddypress_groups_widget( $settings, $key, $group ) {
	global $gs_counter, $processed_activities;

	if ( ! isset( $processed_activities ) ) {
		$processed_activities = array();
	}

	$group_id = $settings[$key]['buddypress-group-group'];
	$group = groups_get_group( array( 'group_id' => $group_id ) );

	if ( bp_has_activities( bp_ajax_querystring( 'activity' ) . '&primary_id=' . $group_id ) ) {
		while ( bp_activities() ) {
			bp_the_activity();
			$url = trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/' );
			$fontawesome_position = $settings[$key]['fontawesome-position'];

			$activity_id = bp_get_activity_id();

			if ( ! in_array( $activity_id, $processed_activities ) && ! isset( $done ) ) {

				// Get image HTML
				if ( isset( $settings[$key]['show_image'] ) && 1 == $settings[$key]['show_image'] ) {
					$size = $settings[$key]['image_size'];
					$image_html = bp_get_activity_avatar( 'type=' . $size );

					// Add image link to image HTML
					if ( isset( $settings[$key]['link_image'] ) && 1 == $settings[$key]['link_image'] ) {
						if ( '' == $settings[$key]['gravatar_alignment'] ) {
							$alignment = 'alignnone';
						} else {
							$alignment = $settings[$key]['gravatar_alignment'];
						}
						$image_html = '<a href="' . esc_attr( bp_get_activity_user_link() ) . '"><span class="' . esc_attr( $alignment ) . '">' . $image_html . '</span></a>';
					}

				}

				echo '
				<article itemscope="itemscope" itemtype="http://schema.org/Event">';

				if ( isset( $settings[$key]['image_position'] ) && 'before-title' == $settings[$key]['image_position'] ) {
					echo $image_html;
				}

				if ( 'before_title' == $fontawesome_position ) {
					thememix_featured_content_span_fontawesome( $key );
				}

				echo '
					<h2 class="entry-title">';


				echo '
						<a href="' . esc_url( $url ) . '" title="' . esc_attr( $group->name ) . '">';

				if ( 'inline_before_title' == $fontawesome_position ) {
					thememix_featured_content_span_fontawesome( $key, true );
					echo ' ';
				}

				echo esc_html( $group->name );

				if ( 'inline_after_title' == $fontawesome_position ) {
					echo ' ';
					thememix_featured_content_span_fontawesome( $key, true );
				}

				echo '</a>';

				echo '
					</h2>';

				if ( 'after_title' == $fontawesome_position ) {
					thememix_featured_content_span_fontawesome( $key );
				}

				if ( isset( $settings[$key]['image_position'] ) && 'after-title' == $settings[$key]['image_position'] ) {
					echo $image_html;
				}

				if ( bp_activity_has_content() ) {
					bp_activity_content_body();
				}

				if ( isset( $settings[$key]['image_position'] ) && 'after-content' == $settings[$key]['image_position'] ) {
					echo $image_html;
				}

				echo '
				</article>';

				$processed_activities[] = $activity_id;
				$done = true;
			}
		}
	}
}
