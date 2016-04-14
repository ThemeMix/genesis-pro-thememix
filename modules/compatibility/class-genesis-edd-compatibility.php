<?php

/**
 * Main EDD Genesis compatibility class.
 *
 * Uses code from Genesis EDD Connect (https://wordpress.org/plugins/genesis-connect-edd/) by David Decker (http://deckerweb.de/).
 */
class Genesis_EDD_Compatibility extends Genesis_Compatibility {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
	}

	/**
	 * Setup Genesis Connect for Easy Digital Downloads.
	 *
	 * Checks whether Easy Digital Downloads and Genesis Framework are active.
	 * Once past these checks, loads the necessary files, actions and filters
	 * for the plugin to do its thing.
	 */
	public function setup() {

		// Bail out now, if EDD not activated
		if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
			return;
		}

		// Load stuff only for the frontend
		if ( ! is_admin() ) {

			/** Load the template files for "Download" post type */
			require_once( GCEDD_LIB_DIR . 'gcedd-template-loader.php' );

			/** Include needed frontend logic/functions */
			require_once( GCEDD_LIB_DIR . 'gcedd-frontend.php' );

			/** Adjust post meta info for "Downloads" */
			add_filter( 'genesis_post_meta', 'gcedd_post_meta', 20 );

			/** Take control of the "Download" template loading */
			add_filter( 'template_include', 'ddw_gcedd_template_loader', 20 );

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
