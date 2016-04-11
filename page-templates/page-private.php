<?php
/**
 * ThemeMix.
 *
 * @package ThemeMix\Templates
 * @author  ThemeMix
 * @license GPL-2.0+
 * @link    http://thememix.com/
 */

//* Template Name: Log in Required

add_filter( 'body_class', 'thememix_private_page_body_class' );
/**
 * Adds a css class to the body element
 *
 * @param  array $classes the current body classes
 * @return array $classes modified classes
 */
function thememix_private_page_body_class( $classes ) {
	$classes[] = 'log-in-required';
	return $classes;
}

//remove the content by default
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
add_action( 'genesis_entry_content', 'thememix_private_page_content' );
/**
 * Adding the content back in when the user is logged in
 * @return [type] [description]
 */
function thememix_private_page_content() {
	if ( is_user_logged_in() ) {
		genesis_do_post_content();
	} else {
		echo '<p>'. __( 'Please log in to view this content.', 'thememix-pro-genesis' ). '</p>';

		$args = array(
			'form_id'			=> 'loginform',
			'redirect'			=> ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'id_username'		=> 'user_login',
			'id_password'		=> 'user_pass',
			'id_remember'		=> 'rememberme',
			'id_submit'			=> 'wp-submit',
			'label_username'	=> __( 'Username', 'thememix-pro-genesis' ),
			'label_password'	=> __( 'Password', 'thememix-pro-genesis' ),
			'label_remember'	=> __( 'Remember Me', 'thememix-pro-genesis' ),
			'label_log_in'		=> __( 'Log In', 'thememix-pro-genesis' ),
		);
		wp_login_form( $args );

	}
}


genesis();