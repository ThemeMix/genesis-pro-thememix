<?php

add_action( 'admin_init', 'font_awesome_generate' );
/**
 * Script for regeneration of the Font Awesome JS icons.
 * Accessed whilst logged in as an admin via /wp-admin/?generate-font-awesome=yes
 */
function font_awesome_generate() {
	if (
		is_admin()
		&&
		current_user_can( 'manage_options' )
		&&
		isset( $_GET['generate-font-awesome'] )
		&&
		'yes' == $_GET['generate-font-awesome']
	) {
		require('generate-font-awesome.php');
	}
}

/**
 * Enqueue font awesome picker scripts.
 */
function font_awesome_picker_scripts() {

	// Only load when on an widgets admin page
	if (
		! ThemeMix_Featured_Content::is_widgets_page()
		&&
		'/wp-admin/customize.php' != $_SERVER['PHP_SELF']
	) {
		return;
	}

	$plugin_url = plugin_dir_url( __FILE__ );

	wp_enqueue_style( 'font-awesome-picker',  $plugin_url . 'css/font-awesome-picker.css', array(), '1.0', false );
	wp_enqueue_script( 'fontawesome-icons', $plugin_url . 'js/font-awesome-icons.js',   array(), '1.0', false  );
	wp_enqueue_script( 'font-awesome-picker', $plugin_url . 'js/font-awesome-picker.js',   array( 'jquery', 'fontawesome-icons' ), '1.1', true  );
}
add_action( 'admin_enqueue_scripts', 'font_awesome_picker_scripts' );

function thememix_featured_content_get_span_fontawesome( $text ) {
	global $thememix_featured_content_key;

	$settings = get_option( 'widget_featured-content' );
	$key = $thememix_featured_content_key;

	// Bail out if Font Awesome not on
	if (
		isset( $settings[$key]['font-awesome'] ) && 1 != $settings[$key]['font-awesome']
		||
		! isset( $settings[$key]['font-awesome'] )
	) {
		return $text;
	}


	if ( isset( $settings[$key]['fontawesome-position'] ) ) {
		$position = $settings[$key]['fontawesome-position'];
	} else {
		$position = 'after_title';
	}

	if ( isset( $settings[$key]['fontawesome-icon'] ) ) {
		$icon = $settings[$key]['fontawesome-icon'];
	} else {
		$icon = 'fa-camera-retro';
	}

	$block_icon_code = '[thememix_featured_content_block_title]' . $icon . '|||' . thememix_featured_content_get_size_fontawesome( $key ) . '[/thememix_featured_content_block_title]';
	$inline_icon_code = '[thememix_featured_content_inline_title]' . $icon . '|||' . thememix_featured_content_get_size_fontawesome( $key ) . '[/thememix_featured_content_inline_title]';

	if ( 'before_title' == $position ) {
		$text = $block_icon_code . '<h2%s>%s%s%s</h2>';
	} elseif ( 'inline_before_title' == $position ) {
		$text = '<h2%s>%s' . $inline_icon_code . '%s</h2>';
	} elseif ( 'inline_after_title' == $position ) {
		$text = '<h2%s>%s%s%s' . $inline_icon_code . '</h2>';
	} elseif ( 'after_title' == $position ) {
		$text = '<h2%s>%s%s%s</h2>' . $block_icon_code;
	} else {
		$text = $content;
	}

	return $text;
}

add_filter( 'thememix_featured_content_post_title_add_extra', 'thememix_featured_content_modify_title' );
/**
 * [thememix_featured_content_modify_title description]
 * @param  [type] $title [description]
 * @return [type]        [description]
 * @global int  The widget key
 */
function thememix_featured_content_modify_title( $title ) {
	global $thememix_featured_content_key;

	$settings = get_option( 'widget_featured-content' );
	$key = $thememix_featured_content_key;
	$colour = $settings[$key]['background'];

	$title = str_replace( '[thememix_featured_content_block_title]', '<div style="' . esc_attr( 'width:100%;text-align:center;color: ' . $colour ) . '"><span style="font-size: ' . thememix_featured_content_get_size_fontawesome( $key ) . '" class="fa fa-', $title );
	$title = str_replace( '[thememix_featured_content_inline_title]', '<span style="' . esc_attr( 'color:' . $colour ) . '" class="fa fa-', $title );
	$title = str_replace( '|||', ' fa-', $title );
	$title = str_replace( '[/thememix_featured_content_block_title]', '"></span></div>', $title );
	$title = str_replace( '[/thememix_featured_content_inline_title]', '"></span>', $title );

	return $title;
}

function thememix_featured_content_span_fontawesome( $key, $inline = false ) {

	$settings = get_option( 'widget_featured-content' );
	if ( ! isset( $settings[$key]['font-awesome'] ) || '' == $settings[$key]['font-awesome'] ) {
		return;
	}

	if ( isset( $settings[$key]['fontawesome-icon'] ) ) {
		$icon = $settings[$key]['fontawesome-icon'];
	} else {
		$icon = 'fa-camera-retro';
	}

	if ( false == $inline ) {
		echo '<div style="width:100%;text-align:center;">';
	}

	echo '<span class="fa fa-' . $icon . '" style="font-size: ' . thememix_featured_content_get_size_fontawesome( $key ) . '" aria-hidden="true"></span>';

	if ( false == $inline ) {
		echo '</div>';
	}

}

function thememix_featured_content_get_size_fontawesome( $key ) {

	$settings = get_option( 'widget_featured-content' );
	if ( ! isset( $settings[$key]['fontawesome-size'] ) ) {
		$size = '20px';
	} else {
		$size = $settings[$key]['fontawesome-size'];
	}

	return $size;
}

add_action( 'admin_enqueue_scripts', 'thememix_featured_content_fontawesome_styles' );
add_action( 'wp_enqueue_scripts', 'thememix_featured_content_fontawesome_styles', 11 );
/**
 * Add Font Awesome stylesheet.
 */
function thememix_featured_content_fontawesome_styles() {
	$handle = 'font-awesome';
	$plugin_url = plugin_dir_url( __FILE__ );
	wp_dequeue_style( $handle );
	wp_enqueue_style( $handle,  $plugin_url . 'css/font-awesome.min.css', array(), '1.0', false );
}

add_action( 'admin_print_scripts-widgets.php', 'thememix_featured_content_fontawesome_color_picker_style' );
/**
 * Add Font Awesome related stylesheets.
 */
function thememix_featured_content_fontawesome_color_picker_style() {
	wp_enqueue_style( 'farbtastic' );
}

add_action( 'admin_print_scripts-widgets.php', 'thememix_featured_content_fontawesome_color_picker_script' );
/**
 * Add Farbtastic colour picker script.
 */
function thememix_featured_content_fontawesome_color_picker_script() {
	wp_enqueue_script( 'farbtastic' );
}
