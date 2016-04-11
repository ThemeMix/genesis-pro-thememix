<?php
/**
 * Plugin Name: Genesis Sandbox Featured Content Widget
 * Plugin URI: https://wpsmith.net/
 * Description: Based on the Genesis Featured Widget Amplified for additional functionality which allows support for custom post types, taxonomies, and extends the flexibility of the widget via action hooks to allow the elements to be re-positioned or other elements to be added.
 * Version: 1.1.8
 * Author: Travis Smith
 * Author URI: http://wpsmith.net/
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
 * Genesis Sandbox Featured Post Widget
 *
 * @category   Genesis_Sandbox_Featured_Content
 * @package    Widgets
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */

/** Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit( 'Cheatin&#8217; uh?' );

define( 'THEMEMIXFC_PLUGIN_NAME', basename( dirname( __FILE__ ) ) );
define( 'THEMEMIXFC_PLUGIN_VERSION', '1.1.0' );

add_action( 'genesis_init', 'thememixfc_init', 50 );
/**
 * Initializes Widget & Admin Settings
 * @since 1.1.0
 */
function thememixfc_init() {
	if ( is_admin() ) {
		require_once( 'thememixfc-settings.php' );
		
		global $_thememixfc_settings;
		$_thememixfc_settings = new ThemeMixFC_Settings();
	}
	
}

require( 'widget.php' );
require( 'font-awesome/font-awesome.php' );
require( 'buddypress-groups/buddypress-groups.php' );
require( 'column-grid/column-grid.php' );

add_action( 'widgets_init', 'thememixfc_widgets_init', 50 );
/**
 * Register THEMEMIXFC for use in the Genesis theme.
 *
 * @since 1.1.0
 */
function thememixfc_widgets_init() {
	if ( class_exists( 'Premise_Base' ) && !is_admin() ) {
		return;
	}
	$gfwa = genesis_get_option( 'thememixfc_gfwa' );
	if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) && $gfwa ) {
		unregister_widget( 'Genesis_Featured_Widget_Amplified' );
	}
	register_widget( 'GS_Featured_Content' );
}

add_filter( 'plugin_action_links', 'thememixfc_action_links', 10, 2 );
/**
 * Add Menus & Donate Action Link.
 * 
 * @param array $links Array of links.
 * @param string $file Basename of plugin.
 * @return array $links Maybe modified array of links.
 */
function thememixfc_action_links( $links, $file ) {
	if ( $file == plugin_basename( __FILE__ ) ) {
		if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) )
			array_unshift( $links, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=genesis' ), __( 'Settings', 'thememix-pro-genesis' ) ) );
		array_unshift( $links, sprintf( '<a href="%s">%s</a>', admin_url( 'widgets.php' ), __( 'Widgets', 'thememix-pro-genesis' ) ) );
		array_push( $links, sprintf( '<a href="http://wpsmith.net/donation" target="_blank">%s</a>', __( 'Donate', 'thememix-pro-genesis' ) ) );
	}
	return $links;
}

add_action( 'save_post', 'thememixfc_save_post', 10, 3 );
/**
 * Hooks into save_post to remove all THEMEMIXFC Transients.
 *
 * Contains a filter thememixfc_save_post_query for anyone to modify the query.
 *
 * @since  1.1.5
 * @date   2014-06-24
 * @author Travis Smith <t(at)wpsmith.net>}
 *
 * @param  int            $post_ID                Post ID.
 * @param  WP_Post        $post                   Post object.
 * @param  bool           $update                 Whether post is being updated
 */
function thememixfc_save_post( $post_ID, $post, $update ) {
	global $wpdb;

	$query = apply_filters( 'thememixfc_save_post_query', "DELETE FROM $wpdb->options WHERE 'option_name' LIKE '%transient_thememixfc%'", $post_ID, $post, $update );
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

/**
 * Serve admin panel notice when site is missing functionality.
 */
function thememixprofc_requirements_notice() {

	if ( genesis_html5() && class_exists( 'BP_Groups_Group' ) ) {
		return;
	}

	echo '<div class="notice notice-success is-dismissible">';

	if ( ! genesis_html5() ) {
		echo '<p>' . __( 'The ThemeMix for Genesis plugin requires your Genesis theme to use HTML5 mode.', 'thememix-pro-genesis' ) . '</p>';
	}

	echo '</div>';
}
add_action( 'admin_notices', 'thememixprofc_requirements_notice' );
