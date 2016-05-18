<?php

/**
 * Genesis Compatibility.
 * Fixes problems commonly experienced when using the Genesis Theme Framework
 * with bbPress, Easy Digital Downloads, BuddyPress and WooCommerce.
 */
class Genesis_Compatibility {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_layers' ) );
	}

	/**
	 * Load the compatibility layers.
	 */
	public function load_layers() {

		if ( class_exists( 'bbPress' ) ) {
			require( 'plugins/class-bbpress-compat.php' );
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			require( 'plugins/class-edd-compat.php' );
		}

		if ( class_exists( 'BuddyPress' ) ) {
			require( 'plugins/class-buddypress-compat.php' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			require( 'plugins/class-woocommerce-compat.php' );
		}

	}

	/**
	 * Remove default post info & meta.
	 */
	public function remove_post_meta() {
		remove_action( 'genesis_before_post_content', 'genesis_post_info'     );
		remove_action( 'genesis_after_post_content',  'genesis_post_meta'     );
		remove_action( 'genesis_entry_header',        'genesis_post_info', 12 );
		remove_action( 'genesis_entry_footer',        'genesis_post_meta'     );
	}

}
