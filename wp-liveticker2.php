<?php
/**
 * WP Liveticker 2
 *
 * @package     WPLiveticker2
 * @author      Stefan Kalscheuer <stefan@stklcode.de>
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: WP Liveticker 2
 * Description: A simple Liveticker for Wordpress.
 * Version:     1.0.0 alpha
 * Author:      Stefan Kalscheuer
 * Author URI:  http://www.stklcode.de
 * Text Domain: wplt2
 * Domain Path: /lang
 * License:     GPLv2 or later
 *
 * WP Liveticker 2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Liveticker 2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Liveticker 2. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Constants.
define( 'WPLT_VERSION', '0.4' );
define( 'WPLT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPLT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPLT_TEXTDOM', 'wplt2' );
define( 'WPLT_OPTIONS', 'wplt2' );

/**
 * Localization.
 */
function wplt2_localization() {
	load_plugin_textdomain( WPLT_TEXTDOM, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

add_action( 'plugins_loaded', 'wplt2_localization' );

/**
 * Options.
 */
global $wplt_options;
$wplt_options = get_option( WPLT_OPTIONS );

/**
 * Include required plugin files.
 */
include_once( WPLT_PLUGIN_DIR . 'includes/functions.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/post-types.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/scripts.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/rss.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/shortcodes.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/widget.php' );
if ( is_admin() ) {
	include_once( WPLT_PLUGIN_DIR . 'includes/admin/page-settings.php' );
	include_once( WPLT_PLUGIN_DIR . 'includes/admin/post-types-columns.php' );
}

/**
 * On activation.
 */
function wplt2_activation() {
	global $wplt_options;

	// Add default settings to database.
	$defaults = WPLT_get_default_options();

	if ( $wplt_options['reset_settings'] ) {
		update_option( WPLT_OPTIONS, $defaults );
	} else {
		add_option( WPLT_OPTIONS, $defaults );
	}

}

register_activation_hook( __FILE__, 'wplt2_activation' );
