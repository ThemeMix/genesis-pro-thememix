<?php
/**
 * ThemeMix.
 *
 * @package ThemeMix\Templates
 * @author  ThemeMix
 * @license GPL-2.0+
 * @link    https://thememix.com/
 */

//* Template Name: Login Form

//* Remove our default page content
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

add_action( 'genesis_entry_content', 'thememix_do_login_form' );
/**
 * Replace genesis_do_post_content with login form
 * @return [type] [description]
 */
function thememix_do_login_form() {

	$user = wp_get_current_user();

	if ( is_user_logged_in() ) { ?>

		<h3>You are already logged in!</h3>
		<p>Hello, <?php echo $user->user_firstname; ?>, it looks like you are already signed in!</p>
		<p><a href="/">Go to Homepage</a> or <a href="<?php echo wp_logout_url( get_permalink() ); ?>">Log Out</a></p>

	<?php
	} else {
		$args = array(
			'form_id'			=> 'loginform',
			'redirect'			=> get_bloginfo( 'url' ),
			'id_username'		=> 'user_login',
			'id_password'		=> 'user_pass',
			'id_remember'		=> 'rememberme',
			'id_submit'			=> 'wp-submit',
			'label_username'	=> __( 'Username' ),
			'label_password'	=> __( 'Password' ),
			'label_remember'	=> __( 'Remember Me' ),
			'label_log_in'		=> __( 'Log In' ),
		);
		wp_login_form( $args );
	}
}
genesis();