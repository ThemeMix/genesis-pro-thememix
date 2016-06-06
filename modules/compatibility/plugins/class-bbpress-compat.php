<?php

/**
 * bbPress Genesis compatibility class.
 *
 * Uses code from bbPress Genesis Extend (http://wordpress.org/extend/plugins/bbpress-genesis-extend/) by Jared Atchison (http://jaredatchison.com/).
 */
class Genesis_BBPress_Compatibility extends Genesis_Compatibility {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'bbp_ready',  array( $this, 'setup_actions' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Setup the Genesis actions.
	 */
	public function setup_actions() {

		// Register forum sidebar if needed
		$this->register_genesis_forum_sidebar();

		// Remove Genesis profile fields from front end
		$this->remove_profile_fields();

		// We hook into 'genesis_before' because it is the most reliable hook
		// available to bbPress in the Genesis page load process.
		add_action( 'genesis_before',     array( $this, 'genesis_post_actions'        ) );
		add_action( 'genesis_before',     array( $this, 'check_genesis_forum_sidebar' ) );

		// Configure which Genesis layout to apply
		add_filter( 'genesis_pre_get_option_site_layout', array( $this, 'genesis_layout' ) );

		// Add Layout and SEO options to Forums
		add_post_type_support( bbp_get_forum_post_type(), array( 'genesis-layouts', 'genesis-seo', 'genesis-scripts', 'genesis-ss' ) );

	}

	/**
	 * Tweak problematic Genesis post actions.
	 */
	public function genesis_post_actions() {

		/**
		 * If the current theme is a child theme of Genesis that also includes
		 * the template files bbPress needs, we can leave things how they are.
		 */
		if ( is_bbpress() ) {

			/**
			 * Remove genesis breadcrumbs.
			 *
			 * bbPress packs its own breadcrumbs, so we don't need the G version.
			 */
			remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

			/**
			 * Remove post info & meta.
			 */
			$this->remove_post_meta();

			/**
			 * Remove Genesis post image and content
			 *
			 * bbPress heavily relies on the_content() so if Genesis is
			 * modifying it unexpectedly, we need to un-unexpect it.
			 */
			remove_action( 'genesis_post_content',  'genesis_do_post_image'     );
			remove_action( 'genesis_post_content',  'genesis_do_post_content'   );
			remove_action( 'genesis_entry_content', 'genesis_do_post_image',  8 );
			remove_action( 'genesis_entry_content', 'genesis_do_post_content'   );

			/**
			 * Remove authorbox
			 *
			 * In some odd cases the Genesis authorbox could appear
			 */
			remove_action( 'genesis_after_post',   'genesis_do_author_box_single' );
			remove_action( 'genesis_entry_footer', 'genesis_do_author_box_single' );

			/**
			 * Remove the navigation
			 *
			 * Make sure the Genesis navigation doesn't try to show after the loop.
			 */
			remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );

			/** Add Actions ***************************************************/

			/**
			 * Re-add the_content back
			 *
			 * bbPress doesn't play nice with the Genesis formatted content, so
			 * we remove it above and reapply the normal version bbPress expects.
			 */
			add_action( 'genesis_post_content',  'the_content' );
			add_action( 'genesis_entry_content', 'the_content' );

			/** Filters *******************************************************/

			/**
			 * Remove forum/topic descriptions
			 *
			 * Many people, myself included, are not a fan of the bbPress
			 * descriptions, e.g. "This forum contains 2 topics and 4 replies".
			 * So we provided an simple option in the settings to remove them.
			 */
			if ( genesis_get_option( 'bbp_forum_desc' ) ) {
				add_filter( 'bbp_get_single_forum_description', '__return_false' );
				add_filter( 'bbp_get_single_topic_description', '__return_false' );
			}
		}
	}

	/**
	 * Register forum specific sidebar if enabled.
	 */
	public function register_genesis_forum_sidebar() {

		if ( function_exists( 'genesis_get_option' ) && genesis_get_option( 'bbp_forum_sidebar' ) ) {
			genesis_register_sidebar( array(
				'id'          => 'sidebar-genesis-bbpress',
				'name'        => __( 'Forum Sidebar', 'genesis-compatibility' ),
				'description' => __( 'This is the primary sidebar used on the forums.', 'genesis-compatibility' )
			) );
		}

	}

	/**
	 * Setup forum specific sidebar on bbPress pages if enabled.
	 */
	public function check_genesis_forum_sidebar() {

		if ( is_bbpress() && genesis_get_option( 'bbp_forum_sidebar' ) ) {

			// Remove the default Genesis sidebar
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar'     );

			// If Genesis Simple Sidebar plugin is in place, nuke it
			remove_action( 'genesis_sidebar', 'ss_do_sidebar'          );

			// Nuke Genesis Genesis Connect for WooCommerce sidebar
			remove_action( 'genesis_sidebar', 'gencwooc_ss_do_sidebar' );

			// Load up the Genisis-bbPress sidebar
			add_action( 'genesis_sidebar', array( $this, 'load_genesis_forum_sidebar' ) );
		}
	}

	/**
	 * Loads the forum specific sidebar.
	 */
	public function load_genesis_forum_sidebar() {

		// Throw up placeholder content if the sidebar is active but empty
		if ( ! dynamic_sidebar( 'sidebar-genesis-bbpress' ) ) {
			echo '
			<div class="widget widget_text">
				<div class="widget-wrap">
					<h4 class="widgettitle">' . __( 'Forum Sidebar Widget Area', 'genesis-compatibility' ) . '</h4>
					<div class="textwidget">
						<p>' . sprintf( __( 'This is the Forum Sidebar Widget Area. You can add content to this area by visiting your <a href="%s">Widgets Panel</a> and adding new widgets to this area.', 'genesis-compatibility' ), admin_url( 'widgets.php' ) ) . '</p>
					</div>
				</div>
			</div>';
		}
	}

	/**
	 * Genesis bbPress layout control.
	 *
	 * If you set a specific layout for a forum, that will be used for that forum
	 * and it's topics. If you set one in the Genesis-bbPress setting, that gets
	 * checked next. Otherwise bbPress will display itself in Genesis default layout.
	 *
	 * @param string $layout
	 * @return bool layout to use
	 */
	public function genesis_layout( $layout ) {

		// Bail if no bbPress
		if ( ! is_bbpress() ) {
			return $layout;
		}

		// Set some defaults
		$forum_id = bbp_get_forum_id();
		// For some reason, if we use the cached version, weird things seem to happen.
		// This needs more investigation, for now we pass false as a work around.
		$settings = get_option( GENESIS_SETTINGS_FIELD, null );
		$retval   = isset( $settings['site_layout'] ) ? $settings['site_layout'] : null;
		$parent   = false;

		// Check and see if a layout has been set for the parent forum
		if ( ! empty( $forum_id ) ) {
			$parent = esc_attr( get_post_meta( $forum_id, '_genesis_layout' , true ) );

			if ( ! empty( $parent ) ) {
				return apply_filters( 'bbp_genesis_layout', $parent );
			}
		}

		// Second, see if a layout has been defined in the bbPress Genesis settings
		if ( empty( $parent ) || ( genesis_get_option( 'bbp_forum_layout' ) !== 'genesis-default' ) ) {
			$retval = genesis_get_option( 'bbp_forum_layout' );
		}

		// Filter the return value
		return apply_filters( 'bbp_genesis_layout', $retval, $forum_id, $parent );
	}

	/**
	 * Remove Genesis profile fields
	 *
	 * In some use cases the Genesis fields were showing (incorrectly)
	 * on the bbPress profile edit pages, so we remove them entirely.
	 */
	public function remove_profile_fields() {

		if ( ! is_admin() ) {
			remove_action( 'show_user_profile', 'genesis_user_options_fields' );
			remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
			remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
			remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
			remove_action( 'show_user_profile', 'genesis_user_seo_fields'     );
			remove_action( 'edit_user_profile', 'genesis_user_seo_fields'     );
			remove_action( 'show_user_profile', 'genesis_user_layout_fields'  );
			remove_action( 'edit_user_profile', 'genesis_user_layout_fields'  );
		}

	}

	/**
	 * Initialising the admin sections.
	 */
	public function admin_init() {
		add_filter( 'genesis_theme_settings_defaults',  array( $this, 'options_defaults'      ) );
		add_action( 'genesis_settings_sanitizer_init',  array( $this, 'sanitization_filters'  ) );
		add_action( 'genesis_theme_settings_metaboxes', array( $this, 'register_settings_box' ) );
	}

	/**
	 * Set defaults.
	 *
	 * @param array $defaults
	 * @return array new defaults
	 */
	public function options_defaults( $defaults ) {
		$defaults['bbp_forum_sidebar'] = '';
		$defaults['bbp_forum_layout']  = 'genesis-default';
		$defaults['bbp_forum_desc']    = '';
		return $defaults;
	}

	/**
	 * Set sanitizations.
	 */
	public function sanitization_filters() {
		genesis_add_option_filter( 'no_html', GENESIS_SETTINGS_FIELD,  array( 'bbp_forum_layout'  ) );
		genesis_add_option_filter( 'one_zero', GENESIS_SETTINGS_FIELD, array( 'bbp_forum_sidebar' ) );
		genesis_add_option_filter( 'one_zero', GENESIS_SETTINGS_FIELD, array( 'bbp_forum_desc'    ) );
	}

	/**
	 * Register the settings metabox.
	 *
	 * @param string The Genesis theme settings page hook
	 */
	public function register_settings_box( $_genesis_theme_settings_pagehook ) {
		add_meta_box( 'bbpress-genesis-options', 'bbPress', array( $this, 'settings_box' ), $_genesis_theme_settings_pagehook, 'main', 'high' );
	}

	/**
	 * Render the settings metabox.
	 */
	public function settings_box() {
		?>
		<p>
			<label for="bbp_forum_layout"><?php _e( 'Forum Layout: ', 'genesis-compatibility' ); ?></label>
			<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[bbp_forum_layout]" id="bbp_forum_layout">
				<option value="genesis-default" <?php selected( genesis_get_option( 'bbp_forum_layout' ), 'genesis-default' ); ?>><?php _e( 'Genesis default', 'genesis-compatibility' ); ?></option><?php

				foreach ( genesis_get_layouts() as $id => $data ) {
					echo "\n\t\t\t\t";
					echo '<option value="' . esc_attr( $id ) . '" ' . selected( genesis_get_option( 'bbp_forum_layout' ), esc_attr( $id ) ) . '>' . esc_attr( $data['label'] ) . '</option>';
				}

				?>

			</select>
		</p>
		<p>
			<input type="checkbox" id="bbp_forum_sidebar" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[bbp_forum_sidebar]" value="1" <?php checked( genesis_get_option( 'bbp_forum_sidebar' ) ); ?> />
			<label for="bbp_forum_sidebar"><?php _e( 'Register a forum specific sidebar that will be used on all forum pages', 'genesis-compatibility' ); ?></label>
		</p>
		<p>
			<input type="checkbox" id="bbp_forum_desc" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[bbp_forum_desc]" value="1" <?php checked( genesis_get_option( 'bbp_forum_desc' ) ); ?> />
			<label for="bbp_forum_desc"><?php _e( 'Remove forum and topic descriptions. E.g. "This forum contains [&hellip;]" notices.', 'genesis-compatibility' ); ?></label>
		</p><?php
	}

}
new Genesis_BBPress_Compatibility;
