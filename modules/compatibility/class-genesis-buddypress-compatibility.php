<?php

/**
 * Main BuddyPress Genesis compatibility class.
 */
class Genesis_BuddyPress_Compatibility extends Genesis_Compatibility {

	/**
	 * Class constructor.
	 */
	public function __construct() {
	}

	/**
	 * Remove default post info & meta.
	 */
	public function post_meta() {

		// Only remove meta on download post-type
		if (
			bp_is_page( BP_MEMBERS_SLUG )
			||
			bp_is_page( BP_ACTIVITY_SLUG )
			||
			bp_is_page( BP_GROUPS_SLUG )
			||
			bp_is_page( BP_FORUMS_SLUG )
			||
			bp_is_page( BP_BLOGS_SLUG )
			/*
			bp_is_activity_front_page()
			bp_is_activity_component()
			bp_is_directory()
			bp_is_home()
			*/
		) {
			$this->remove_post_meta();
		}

	}

}
new Genesis_BuddyPress_Compatibility;
