<?php
/**
 * Plugin uninstall file.
 *
 * @package   MembersPrivacyCaps
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://themehybrid.com/plugins/members-privacy-caps
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Bail if we're not actually uninstalling.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	wp_die( sprintf(
		// Translators: %s is the file name wrapped in a code tag.
		__( '%s should only be called when uninstalling the plugin.', 'members-privacy-caps' ),
		'<code>' . __FILE__ . '</code>'
	) );
}

# Remove capabilities added by the plugin.

$role = get_role( 'administrator' );

# If the administrator role exists, remove added capabilities for the plugin.
if ( ! is_null( $role ) ) {
	$role->remove_cap( 'export_others_personal_data' );
	$role->remove_cap( 'erase_others_personal_data'  );
	$role->remove_cap( 'manage_privacy_options'      );
}
