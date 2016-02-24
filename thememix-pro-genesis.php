<?php
/**
 * ThemeMix Pro for Genesis
 *
 * A plugin that enhances, adds, modifies or removes certain elements
 * of the Genesis Framework.
 *
 *
 * @package   ThemeMix Pro for Genesis
 * @author    ThemeMix <hello@thememix.com>
 * @license   GPL-2.0+
 * @link      https://thememix.com
 * @copyright 2016 ThemeMix
 *
 * @wordpress-plugin
 * Plugin Name:       ThemeMix Pro for Genesis
 * Plugin URI:        https://thememix.com/plugins/thememix-pro-genesis
 * Description:       A plugin that enhances, adds, modifies or removes certain elements
 * Version:           0.3
 * Author:            ThemeMix
 * Author URI:        https://thememix.com
 * Text Domain:       thememix-pro-genesis
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/thememix/thememix-pro-genesis
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require( 'inc/thememix-page-templates.php' );
require( 'modules/featured-content-widget/featured-content-widget.php' );

/**
 * Load the text domain for translation of the plugin
 *
 * @since 0.1.0
 */
load_plugin_textdomain( 'thememix-pro-genesis', false, '/languages' );

register_activation_hook( __FILE__, 'thememix_genesis_translations_activation_check' );
/**
 * Checks for activated Genesis Framework and its minimum version before allowing plugin to activate
 *
 * @author Nathan Rice, Remkus de Vries
 * @since 0.1.0
 * @version 0.1.0
 */
function thememix_genesis_translations_activation_check() {
    // Find Genesis Theme Data
    $theme = wp_get_theme( 'genesis' );
    // Get the version
    $version = $theme->get( 'Version' );
    // Set what we consider the minimum Genesis version
    $minimum_genesis_version = '2.2.6';
    // Restrict activation to only when the Genesis Framework is activated
    if ( basename( get_template_directory() ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, but the ThemeMix Pro for Genesis plugin only works if you have the  %1$sGenesis Framework%2$s or a Genesis Child Theme activated as your current theme.', 'thememix-pro-genesis' ), '<a href="https://remkusdevries.com/out/genesis/" target="_new">', '</a>' ) );
    }
    // Set a minimum version of the Genesis Framework to be activated on
    if ( version_compare( $version, $minimum_genesis_version, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( __( 'We need you to be on %1$sGenesis Framework %2$s%3$s or greater for this plugin to work.', 'thememix-pro-genesis' ), '<a href="https://remkusdevries.com/out/genesis/" target="_new">', $latest, '</a>' ) );
    }
}
