<?php

$settings = get_option( 'widget_featured-content' );
if ( is_array( $settings ) ) {
	foreach ( $settings as $key => $setting ) {
		$thememix_featured_content_grid_counter[$key] = 0;
	}
}

function thememix_featured_content_grid_after() {
	global $thememix_featured_content_grid_counter;

	$settings = get_option( 'widget_featured-content' );
	foreach ( $settings as $key => $setting ) {
		$thememix_featured_content_grid_counter[$key]++;
	}

}
add_action( 'thememix_featured_content_after_post_content', 'thememix_featured_content_grid_after' );


function thememix_featured_content_grid_styling() {
	global $thememix_featured_content_grid_counter;

	// Find chosen number of columns
	$settings = get_option( 'widget_featured-content' );
	foreach ( $settings as $key => $setting ) {

		if ( isset( $settings[$key]['column-grid'] ) ) {
			$chosen_number_of_columns = $settings[$key]['column-grid'];
		} else {
			$chosen_number_of_columns = 1;
		}

		// Set actual number of columns based on how many posts are being loaded (no point in doing 25% width for a single post)
		if  ( isset( $settings[$key]['buddypress-group'] ) && 1 == $settings[$key]['buddypress-group'] ) {
			$actual_number_of_columns = $chosen_number_of_columns;
		} elseif ( isset( $thememix_featured_content_grid_counter[$key] ) && ( $thememix_featured_content_grid_counter[$key] < $chosen_number_of_columns ) ) {
			$actual_number_of_columns = $thememix_featured_content_grid_counter[$key];
		} else {
			$actual_number_of_columns = $chosen_number_of_columns;
		}

		if ( $actual_number_of_columns > 1 ) {
			echo '<style>.featured-content article.post, .featured-content li {float:left;word-wrap:break-word;width:';

			if ( 2 == $actual_number_of_columns ) {
				echo '50';
			} elseif ( 3 == $actual_number_of_columns ) {
				echo '33.3';
			} elseif ( 4 == $actual_number_of_columns ) {
				echo '25';
			}

			echo '%}</style>';
		}

	}

}
add_action( 'wp_footer', 'thememix_featured_content_grid_styling' );
