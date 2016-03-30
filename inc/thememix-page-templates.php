<?php
/**
 * ThemeMix Page Templates.
 *
 * @copyright Copyright (c), ThemeMix
 * @author  Remkus de Vries <remkus@forsite.media>
 * @author  Ryan Hellyer <ryan@forsite.media>
 * @since 0.1.0
 */
class ThemeMix_Page_Templates {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_filter( 'theme_page_templates', array( $this, 'add_page_templates' ) );
		add_action( 'template_redirect',    array( $this, 'use_page_template' )  );
	}

	/**
	 * Get the template name from template file.
	 *
	 * @param  string  $template_file  The template file
	 * @return string  $template_name  The template name
	 */
	public function get_template_name( $template_file ) {

		$template_contents = file_get_contents( $template_file ) ;
		preg_match_all( "(Template Name:(.*)\n)siU", $template_contents, $template_name );
		$template_name = trim( $template_name[1][0] );
		return $template_name;
	}

	/**
	 * Get dynamic page templates.
	 * Scans /page-templates/ dir within plugins folder.
	 * Returns array of all extra page templates.
	 *
	 * @return array  $templates  The modified list of page templates
	 */
	public function get_dynamic_page_templates() {

		$current_dir = dirname( dirname( __FILE__ ) );
		$dir = $current_dir . '/page-templates/';
		$dynamic_templates = array_diff( scandir( $dir ), array( '..', '.' ) );

		return $dynamic_templates;
	}

	/**
	 * Add page templates to the editor.
	 *
	 * @param  array  $templates  The list of page templates
	 * @return array  $templates  The modified list of page templates
	 */
	public function add_page_templates( $templates ) {
		$current_dir = dirname( dirname( __FILE__ ) );
		$dir = $current_dir . '/page-templates/';
		foreach ( $this->get_dynamic_page_templates() as $key => $template_file ) {
			$templates[$template_file] = $this->get_template_name( $dir . $template_file );
		}

		return $templates;
	}

	/**
	 * Outputs the new page template.
	 */
	public function use_page_template() {
		$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );

		// Loop through all possible dynamic page templates
		foreach ( $this->get_dynamic_page_templates() as $key => $template_file ) {

			// If current page template matches available dynamic page template, then load it.
			if ( $template_file == $page_template ) {
				require( plugin_dir_path( dirname( __FILE__ ) ) . 'page-templates/' . $template_file );
				exit;
			}
		}
	}

}
new ThemeMix_Page_Templates();