<?php

class Genesis_Compatibility {

	/**
	 * Load the translation component.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'translate' ) );
	}

	/**
	 * Load the textdomain so we can support other languages
	 */
	public function translate() {
		load_plugin_textdomain( 'genesis-compatibility', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
