<?php

/**
 * EDD Genesis compatibility class.
 *
 * Uses code from Genesis EDD Connect (https://wordpress.org/plugins/genesis-connect-edd/) by David Decker (http://deckerweb.de/).
 */
class Genesis_EDD_Compatibility extends Genesis_Compatibility {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'template_redirect', array( $this, 'post_meta' ) );
	}

	/**
	 * Remove default post info & meta.
	 */
	public function post_meta() {

		// Only remove meta on download post-type
		if ( 'download' == get_post_type() ) {
			$this->remove_post_meta();
		}

	}

	/**
	 * Setup Genesis Connect for Easy Digital Downloads.
	 *
	 * Checks whether Easy Digital Downloads and Genesis Framework are active.
	 * Once past these checks, loads the necessary files, actions and filters
	 * for the plugin to do its thing.
	 */
	public function setup() {

		// Load stuff only for the frontend
		if ( ! is_admin() ) {

		}

		// Add Genesis Layout, SEO, Scripts, Archive Settings options to "Download" edit screen
		add_post_type_support( 'download', array(
			'genesis-layouts',
			'genesis-seo',
			'genesis-scripts',
			'genesis-cpt-archives-settings'
		) );

		add_post_type_support( 'edd_download', array(
			'genesis-layouts',
			'genesis-seo',
			'genesis-scripts',
			'genesis-cpt-archives-settings'
		) );

		// Add plugin support for: Genesis Simple Sidebars, Genesis Simple Menus, Genesis Prose Extras
		add_post_type_support( 'download', array(
			'genesis-simple-sidebars',
			'genesis-simple-menus',
			'gpex-inpost-css'
		) );

		add_post_type_support( 'edd_download', array(
			'genesis-simple-sidebars',
			'genesis-simple-menus',
			'gpex-inpost-css'
		) );

		// Add some additional toolbar items for "EDD Toolbar" plugin
		add_action( 'eddtb_custom_main_items', 'gcedd_toolbar_additions' );

	}

}
new Genesis_EDD_Compatibility;
