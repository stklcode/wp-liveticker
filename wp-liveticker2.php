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
define( 'WPLT2_FILE', __FILE__ );
define( 'WPLT2_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPLT2_BASE', plugin_dir_url( __FILE__ ) );

// System Hooks.
add_action( 'init', array( 'WPLiveticker2', 'register_types' ) );
add_action( 'plugins_loaded', array( 'WPLiveticker2', 'init' ) );
register_activation_hook( WPLT2_FILE, array( 'WPLiveticker2_System', 'activate' ) );

// Allow shortcodes in widgets.
add_filter( 'widget_text', 'do_shortcode' );

// Add shortcode.
add_shortcode( 'liveticker', array( 'WPLiveticker2', 'shortcode_ticker_show' ) );

// Add Widget.
add_action( 'widgets_init', array( 'WPLiveticker2_Widget', 'register' ) );

// Autoload.
spl_autoload_register( 'wplt2_autoload' );

/**
 * Autoloader for StatifyBlacklist classes.
 *
 * @param string $class  Name of the class to load.
 *
 * @since 1.0.0
 */
function wplt2_autoload( $class ) {
	$plugin_classes = array(
		'WPLiveticker2',
		'WPLiveticker2_Admin',
		'WPLiveticker2_System',
		'WPLiveticker2_Widget',
	);
	if ( in_array( $class, $plugin_classes, true ) ) {
		require_once(
			sprintf(
				'%s/includes/class-%s.php',
				WPLT2_DIR,
				strtolower( str_replace( '_', '-', $class ) )
			)
		);
	}
}
