<?php

/**
 * Integrate WooCommerce with Genesis.
 * Partially based on Genesis Connect for WooCommerce by StudioPress (http://www.studiopress.com/plugins/genesis-connect-woocommerce)
 * Partially based on work by AlphaBlossom / Tony Eppright (http://www.alphablossom.com)
 */
class ThemeMix_Pro_Genesis_WooCommerce {

	public function __construct() {

		add_theme_support( 'woocommerce' );

		// Add WooCommerce support for Genesis layouts (sidebar, full-width, etc) - Thank you Kelly Murray/David Wang
		add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-seo' ) );

		// Unhook WooCommerce Sidebar - use Genesis Sidebars instead
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Unhook WooCommerce wrappers
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

		// Hook new functions with Genesis wrappers
		add_action( 'woocommerce_before_main_content', array( $this, 'theme_wrapper_start' ), 10 );
		add_action( 'woocommerce_after_main_content', array( $this, 'theme_wrapper_end' ), 10 );

		// Setup theme
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
	}

	/**
	 * Add opening wrapper before WooCommerce loop
	 */
	public function theme_wrapper_start() {

		do_action( 'genesis_before_content_sidebar_wrap' );
		genesis_markup( array(
			'html5' => '<div %s>',
			'context' => 'content-sidebar-wrap',
		) );

		do_action( 'genesis_before_content' );
		genesis_markup( array(
			'html5' => '<main %s>',
			'context' => 'content',
		) );
		do_action( 'genesis_before_loop' );

	}

	/*
	 * Add closing wrapper after WooCommerce loop.
	 */
	public function theme_wrapper_end() {

		do_action( 'genesis_after_loop' );
		genesis_markup( array(
			'html5' => '</main>', //* end .content
			'xhtml' => '</div>', //* end #content
		) );
		do_action( 'genesis_after_content' );

		echo '</div>'; //* end .content-sidebar-wrap or #content-sidebar-wrap
		do_action( 'genesis_after_content_sidebar_wrap' );

	}

	/**
	 * Setup integration.
	 *
	 * Checks whether WooCommerce is active, then checks if relevant
	 * theme support exists. Once past these checks, loads the necessary
	 * files, actions and filters for the plugin to do its thing.
	 */
	public function setup() {

		/** Fail silently if theme doesn't support GCW */
		if ( ! current_theme_supports( 'genesis-connect-woocommerce' ) ) {
//			return;
		}

		/** Environment is OK, let's go! */

		global $woocommerce;

		// Load modified Genesis breadcrumb filters and callbacks
		if ( ! current_theme_supports( 'gencwooc-woo-breadcrumbs') ) {
//			require( 'genesis-connect-woocommerce/lib/breadcrumb.php' );
		}

		// Ensure WooCommerce 2.0+ compatibility
		add_theme_support( 'woocommerce' );

		// Add Genesis Layout and SEO options to Product edit screen
		add_post_type_support( 'product', array( 'genesis-layouts', 'genesis-seo' ) );

		// Add Studiopress plugins support
		add_post_type_support( 'product', array( 'genesis-simple-sidebars', 'genesis-simple-menus' ) );

		// Integration - Genesis Simple Sidebars
		if ( in_array( 'genesis-simple-sidebars/plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
//			require( 'genesis-connect-woocommerce/genesis-simple-sidebars.php' );
		}

		// Integration - Genesis Simple Menus
		if ( in_array( 'genesis-simple-menus/simple-menu.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
//			require( 'genesis-connect-woocommerce/genesis-simple-menus.php' );
		}

	}

}
new ThemeMix_Pro_Genesis_WooCommerce;
