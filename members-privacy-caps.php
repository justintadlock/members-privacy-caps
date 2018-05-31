<?php
/**
 * Plugin Name: Members - Privacy Caps
 * Plugin URI:  https://themehybrid.com/plugins/members-privacy-caps
 * Description: Creates additional capabilities for control over WordPress' privacy and personal data features.
 * Version:     1.0.0-dev
 * Author:      Justin Tadlock
 * Author URI:  https://themehybrid.com
 * Text Domain: members-privacy-caps
 * Domain Path: /resources/lang
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St,
 * Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   MembersPrivacyCaps
 * @version   1.0.0
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2018, Justin Tadlock
 * @link      https://themehybrid.com/plugins/members-privacy-caps
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\AddOns\PrivacyCaps;

# Add actions and filters.
add_action( 'init',                  __NAMESPACE__ . '\load_textdomain',  0    );
add_action( 'members_register_caps', __NAMESPACE__ . '\register_caps'          );
add_filter( 'map_meta_cap',          __NAMESPACE__ . '\map_meta_cap',    95, 2 );

# Register activation/deactivation hooks.
register_activation_hook( __FILE__, __NAMESPACE__ . '\activation' );

/**
 * Adds the privacy capabilities to the administrator role whenever the plugin
 * is activated.
 *
 * On Multisite, we're not adding any caps to a role because these caps are, by
 * default, only allowed for people with the `manage_network` capability, which
 * is a Super Admin cap.  If network owners want sub-site administrators to be
 * able to have thse caps, they should be added to the role via the edit role
 * screen in Members.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function activation() {

	if ( is_multisite() ) {
		return;
	}

	$role = get_role( 'administrator' );

	if ( $role ) {
		$role->add_cap( 'export_others_personal_data' );
		$role->add_cap( 'erase_others_personal_data'  );
		$role->add_cap( 'manage_privacy_options'      );
	}
}

/**
 * Loads the plugin's textdomain.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function load_textdomain() {

 	load_plugin_textdomain(
		'members-privacy-caps',
		false,
		trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . '/resources/lang'
	);
}

/**
 * Registers capabilities with the Members plugin.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function register_caps() {

	members_register_cap( 'export_others_personal_data', [
		'label' => __( "Export Others' Personal Data", 'members-privacy-caps' ),
		'group' => 'user'
	] );

	members_register_cap( 'erase_others_personal_data', [
		'label' => __( "Erase Others' Personal Data", 'members-privacy-caps' ),
		'group' => 'user'
	] );

	members_register_cap( 'manage_privacy_options', [
		'label' => __( 'Manage Privacy Options', 'members-privacy-caps' ),
		'group' => 'general'
 	] );
}

/**
 * The privacy caps are mapped to `manage_options` (or `manage_network` in the
 * case of multisite) by default.
 *
 * @since  1.0.0
 * @access public
 * @param  array   $caps
 * @param  string  $cap
 * @return array
 */
function map_meta_cap( $caps, $cap ) {

 	$privacy_caps = [
 		'export_others_personal_data',
 		'erase_others_personal_data',
 		'manage_privacy_options'
 	];

 	if ( in_array( $cap, $privacy_caps ) ) {

		$caps = [ $cap ];

		// Core WP requires the 'delete_users' cap here, so we're going
		// to add this as a required cap too.
		if ( 'erase_others_personal_data' === $cap ) {
			$caps[] = 'delete_users';
		}
 	}

 	return $caps;
}
