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

}
