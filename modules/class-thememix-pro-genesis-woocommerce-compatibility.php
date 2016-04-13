<?php

/**
 *
 * Integrate WooCommerce with Genesis.
 *
 * Unhook WooCommerce wrappers and
 * Replace with Genesis wrappers.
 *
 * Reference Genesis file:
 * genesis/lib/framework.php
 *
 * @author AlphaBlossom / Tony Eppright
 * @link http://www.alphablossom.com
 *
 */
class ThemeMix_Pro_Genesis_WooCommerce_Compatibility {

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

	}

	/**
	 * Add opening wrapper before WooCommerce loop
	 */
	public function theme_wrapper_start() {

		do_action( 'genesis_before_content_sidebar_wrap' );
		genesis_markup( array(
			'html5' => '<div %s>',
			'xhtml' => '<div id="content-sidebar-wrap">',
			'context' => 'content-sidebar-wrap',
		) );

		do_action( 'genesis_before_content' );
		genesis_markup( array(
			'html5' => '<main %s>',
			'xhtml' => '<div id="content" class="hfeed">',
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

}
new ThemeMix_Pro_Genesis_WooCommerce_Compatibility;
