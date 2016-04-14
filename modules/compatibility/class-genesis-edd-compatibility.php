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

			// Adjust post meta info for "Downloads"
			add_filter( 'genesis_post_meta', array( $this, 'post_meta' ), 20 );

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
	 * Sets the Genesis Post Meta for the Download post type
	 * to use "edd_download_category" and "edd_download_tag" taxonomies
	 * and for backward compatibility to use "download_category" and "download_tag" taxonomies
	 *
	 * Based on work by David Decker.
	 * @author     David Decker - DECKERWEB
	 * @link       http://genesisthemes.de/en/wp-plugins/genesis-connect-edd/
	 * @link       http://deckerweb.de/twitter
	 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
	 * @copyright  Copyright (c) 2012-2013, David Decker - DECKERWEB
	 *
	 * @param  $post_meta
	 * @global $post
	 * @return strings Post meta info for "Download" post type taxonomies.
	 */
	public function post_meta( $post_meta ) {
		global $post;

		// Bail early, if we are not on a page, and, if EDD taxonomies do not exist.
		if ( is_page() && ! taxonomy_exists( array( 'download_category', 'download_tag' ) ) ) {
			return;
		}

		// Get "Download" CPT slug
		$download_cpt = ddw_gcedd_download_cpt();

		$terms_edd_categories = wp_get_object_terms( get_the_ID(), 'download_category' );
		$terms_edd_tags = wp_get_object_terms( get_the_ID(), 'download_tag' );

		// Modify Post Meta for EDD Downloads
		if ( $download_cpt == get_post_type() ) {

			// Case I: post has terms for both tax
			if ( ( count( $terms_edd_categories ) > 0 ) && ( count( $terms_edd_tags ) > 0 ) ) {
				$post_meta = do_shortcode( '[post_terms taxonomy="' . $download_cpt . '_category"] <span class="post-meta-sep">' . _x( '&#x00B7;', 'Translators: Taxonomy separator for Genesis child themes (default: &#x00B7; = &middot;)', 'genesis-connect-edd' ) . '</span> [post_terms before="' . __( 'Tagged:', 'genesis-connect-edd' ) . ' " taxonomy="' . $download_cpt . '_tag"]<br /><br />' );
			}

			// Case II: post has terms only for category
			elseif ( ( count( $terms_edd_categories ) > 0 ) && ! $terms_edd_tags ) {
				$post_meta = do_shortcode( '[post_terms taxonomy="' . $download_cpt . '_category"]<br /><br />' );
			}

			// Case III: post has terms only for tag
			elseif ( ! $terms_edd_categories && ( count( $terms_edd_tags ) > 0 ) ) {
				$post_meta = do_shortcode( '[post_terms before="' . __( 'Tagged:', 'genesis-connect-edd' ) . ' " taxonomy="' . $download_cpt . '_tag"]<br /><br />' );
			}

			// Case IV: post has no terms for both tax
			elseif ( ! $terms_edd_categories && ! $terms_edd_tags ) {

				$post_meta = '';

			}

		}

		// Return altered Post Meta string to Genesis filter
		return $post_meta;
	}

}
new Genesis_EDD_Compatibility;
