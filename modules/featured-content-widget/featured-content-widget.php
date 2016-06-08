<?php
/**
 * Based on "Genesis Sandbox Featured Content Widget" by Travis Smith
 * https://wpsmith.net/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

/**
 * Genesis Featured Content by ThemeMix Widget
 *
 * @category   ThemeMix_Pro_for_Genesis_Featured_Content
 * @package    Featured Content Widget
 * @author     Travis Smith
 * @author     ThemeMix
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       https://thememix.com/
 * @since      1.0.0
 */

/** Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

define( 'THEMEMIX_FEATURED_CONTENT_PLUGIN_NAME', basename( dirname( __FILE__ ) ) );
define( 'THEMEMIX_FEATURED_CONTENT_PLUGIN_VERSION', '1.0.0' );

add_action( 'genesis_init', 'thememix_featured_content_widget_init', 50 );
/**
 * Initializes Widget & Admin Settings
 * @since 1.0.0
 */
function thememix_featured_content_widget_init() {
	if ( is_admin() ) {
		require_once( 'thememix-featured-content-settings.php' );

		global $_thememix_featured_content_settings;
		$_thememix_featured_content_settings = new THEMEMIX_FEATURED_CONTENT_Settings();
	}

}

require( 'widget.php' );
require( 'font-awesome/font-awesome.php' );
require( 'buddypress-groups/buddypress-groups.php' );
require( 'column-grid/column-grid.php' );

add_action( 'widgets_init', 'thememix_featured_content_widgets_init', 50 );
/**
 * Register THEMEMIXFC for use in the Genesis theme.
 *
 * @since 1.0.0
 */
function thememix_featured_content_widgets_init() {
	if ( ! function_exists( 'genesis_get_option' ) || ( class_exists( 'Premise_Base' ) && !is_admin() ) ) {
		return;
	}
	$gfwa = genesis_get_option( 'thememix_featured_content_gfwa' );
	if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) && $gfwa ) {
		unregister_widget( 'Genesis_Featured_Widget_Amplified' );
	}
	register_widget( 'ThemeMix_Featured_Content' );
}

add_action( 'save_post', 'thememix_featured_content_save_post', 10, 3 );
/**
 * Hooks into save_post to remove all THEMEMIXFC Transients.
 *
 * Contains a filter thememix_featured_content_save_post_query for anyone to modify the query.
 *
 * @since  1.0.0
 * @author Travis Smith
 *
 * @param  int            $post_ID                Post ID.
 * @param  WP_Post        $post                   Post object.
 * @param  bool           $update                 Whether post is being updated
 */
function thememix_featured_content_save_post( $post_ID, $post, $update ) {
	global $wpdb;

	$query = apply_filters( 'thememix_featured_content_save_post_query', "DELETE FROM $wpdb->options WHERE 'option_name' LIKE '%transient_thememix_featured_content%'", $post_ID, $post, $update );
	$wpdb->query( $query );

}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function thememixprofc_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}
