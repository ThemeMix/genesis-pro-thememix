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
		add_action( 'template_redirect', array( $this, 'post_meta' ) );
	}

	/**
	 * Remove default post info & meta.
	 */
	public function post_meta() {

		// Only remove meta on download post-type
		if ( 'download' == get_post_type() ) {
			remove_action( 'genesis_before_post_content', 'genesis_post_info'     );
			remove_action( 'genesis_after_post_content',  'genesis_post_meta'     );
			remove_action( 'genesis_entry_header',        'genesis_post_info', 12 );
			remove_action( 'genesis_entry_footer',        'genesis_post_meta'     );
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

		// Bail out now, if EDD not activated
		if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
			return;
		}

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

	/**
	 * Check and retrieve the correct ID/tag of the registered post type 'Download' by EDD.
	 *
	 * Based on work by David Decker.
	 * @author     David Decker - DECKERWEB
	 * @link       http://genesisthemes.de/en/wp-plugins/genesis-connect-edd/
	 * @link       http://deckerweb.de/twitter
	 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
	 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
	 *
	 * @return string "Downloads" post type slug.
	 */
	public function download_cpt() {

		// Get the proper 'Download' post type ID/tag
		if ( post_type_exists( 'edd_download' ) ) {

			$gcedd_download_cpt = 'edd_download';

		} elseif ( post_type_exists( 'download' ) ) {

			$gcedd_download_cpt = 'download';

		}

		// EDD "Downloads" post type slug
		return $gcedd_download_cpt;

	}

}
new Genesis_EDD_Compatibility;
