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

			// Load the template files for "Download" post type
			require_once( GCEDD_LIB_DIR . 'gcedd-template-loader.php' );

			// Include needed frontend logic/functions
			require_once( GCEDD_LIB_DIR . 'gcedd-frontend.php' );

			// Adjust post meta info for "Downloads"
			add_filter( 'genesis_post_meta', 'gcedd_post_meta', 20 );

			// Take control of the "Download" template loading
			add_filter( 'template_include', 'template_loader', 20 );

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
	 * Load the Genesis-fied templates, instead of the EDD defaults.
	 * This template loader determines which template file will be used for the
	 * requested page, and uses the following hierarchy to find the template:
	 * 1. First looks in the child theme's root folder.			// great for nativ EDD-supporting themes
	 * 2. Secondly looks in the child theme's 'edd' folder.		// great for adding EDD support afterwards
	 * 3. If no template found, falls back to the packaged Genesis Connect EDD
	 *    templates.
	 *
	 * @author  Ade Walker
	 * @link    http://www.studiograsshopper.ch/
	 *
	 * @author  David Decker
	 * @link    http://deckerweb.de/
	 *
	 * @param   string $template Template file as per template hierarchy.
	 * @return  string $template Specific Genesis Connect EDD template if a download
	 *                           page (single or archive) or a download taxonomy
	 *                           term, or returns original template.
	 */
	public function template_loader( $template ) {

		$download_cpt = ddw_gcedd_download_cpt();

		// Download single pages
		if ( is_single() && $download_cpt == get_post_type() ) {

			// Use custom template via child theme (child root)
			$template = locate_template( array( 'single-' . $download_cpt . '.php' ) );

			// Use custom template via child theme (edd subfolder)
			if ( ! $template ) {
				$template = locate_template( array( 'edd/single-' . $download_cpt . '.php' ) );
			}

			// Fallback to GCEDD template (plugin) - filterable
			if ( ! $template ) {
				$template = apply_filters( 'genesis_compatibility_template_single', GCEDD_TEMPLATE_DIR . '/single-' . $download_cpt . '.php' );
			}

		}

		// Download archive pages
		elseif ( is_post_type_archive( $download_cpt ) ) {

			// Use custom template via child theme (child root)
			$template = locate_template( array( 'archive-' . $download_cpt . '.php' ) );

			// Use custom template via child theme (edd subfolder)
			if ( ! $template ) {
				$template = locate_template( array( 'edd/archive-' . $download_cpt . '.php' ) );
			}

			// Fallback to GCEDD template (plugin) - filterable
			if ( ! $template ) {
				$template = apply_filters( 'genesis_compatibility_template_archive', GCEDD_TEMPLATE_DIR . '/archive-' . $download_cpt . '.php' );
			}

		}

		// Download taxonomy pages
		elseif ( is_tax() && $download_cpt == get_post_type() ) {

			$term = get_query_var( 'term' );

			$tax = get_query_var( 'taxonomy' );

			// Get an array of all relevant taxonomies
			$taxonomies = get_object_taxonomies( $download_cpt, 'names' );

			if ( in_array( $tax, $taxonomies ) ) {

				$tax = sanitize_title( $tax );
				$term = sanitize_title( $term );

				// Use custom template via child theme (child root)
				$templates = array(
					'taxonomy-' . $tax . '-' . $term . '.php',
					'taxonomy-' . $tax . '.php',
					'taxonomy.php',
				);

				$template = locate_template( $templates );

				// Use custom template via child theme (edd subfolder)
				if ( ! $template ) {

					$templates = array(
						'edd/taxonomy-' . $tax . '-' . $term . '.php',
						'edd/taxonomy-' . $tax . '.php',
						'edd/taxonomy.php',
					);

					$template = locate_template( $templates );

				}

				// Fallback to GCEDD template (plugin)
				if ( ! $template ) {
					$template = apply_filters( 'genesis_compatibility_template_taxonomy', GCEDD_TEMPLATE_DIR . '/taxonomy.php' );
				}

			}

		}

		// Finally, return the template file - filterable
		return apply_filters( 'genesis_compatibility_template_loader', $template );

	}

}
new Genesis_EDD_Compatibility;
