<?php

/**
 * Theme setup.
 *
 * @copyright Copyright (c), Moodlerooms
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@forsite.nu>
 * @since 1.0
 */
class ThemeMix_Pro_Genesis_Theme_Customizer {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'customizer' ) );
	}

	/**
	 * Adds header options.
	 *
	 * @param $wp_customize Theme Customizer object
	 */
	public function customizer( $wp_customize ) {

		// Add Header section
		$wp_customize->add_section(
			'thememix-pro-genesis', array(
				'title'       => __( 'ThemeMix Pro Genesis', 'thememix-pro-genesis' ),
				'priority'    => 10,
				'description' => __( 'Settings for the ThemeMix Pro Genesis plugin.', 'thememix-pro-genesis' ),
			)
		);

	}

}
new ThemeMix_Pro_Genesis_Theme_Customizer;
