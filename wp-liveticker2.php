<?php
/**
  * Plugin Name: WP Liveticker 2
  * Description: A simple Liveticker for Wordpress.
  * Version: 0.4.1 beta
  * Author: Stefan Kalscheuer
  * Author URI:	http://www.stklblog.de
  * Text Domain:	wplt2
  * Domain Path:	/lang
  * License: GPL2
  * 
  * This program is free software; you can redistribute it and/or modify
  *  it under the terms of the GNU General Public License, version 2, as
  *  published by the Free Software Foundation.
  *  
  * This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *  GNU General Public License for more details.
  *  
  * You should have received a copy of the GNU General Public License
  *  along with this program; if not, write to the Free Software
  *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */

/**
 * @package Main
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Constants
 */
if( !defined( 'WPLT_VERSION' ) )
	define( 'WPLT_VERSION', '0.4' );

if( !defined( 'WPLT_PLUGIN_URL' ) )
	define( 'WPLT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if( !defined( 'WPLT_PLUGIN_DIR' ) )
	define( 'WPLT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Localization
 */
function WPLT_localization() {
	load_plugin_textdomain( 'wplt2', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'WPLT_localization' );

/**
 * Options
 */
global $wplt_options;
$wplt_options = get_option( 'wplt2' );

/**
 * Include required plugin files
 */
include_once( WPLT_PLUGIN_DIR . 'includes/functions.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/post-types.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/scripts.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/rss.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/shortcodes.php' );
include_once( WPLT_PLUGIN_DIR . 'includes/widget.php' );
if( is_admin() ) {
//	include_once( WPLT_PLUGIN_DIR . 'includes/admin/ajax.php' );
//	include_once( WPLT_PLUGIN_DIR . 'includes/admin/dashboard.php' );
//	include_once( WPLT_PLUGIN_DIR . 'includes/admin/media-button.php' );
//	include_once( WPLT_PLUGIN_DIR . 'includes/admin/meta-boxes.php' );
	include_once( WPLT_PLUGIN_DIR . 'includes/admin/page-settings.php' );
	include_once( WPLT_PLUGIN_DIR . 'includes/admin/post-types-columns.php' );
}

/**
 * On activation
 */
function WPLT_activation() {
	global $WPLT_options;
	
	// Add default settings to database
	$defaults = WPLT_get_default_options();
	
	if( $WPLT_options['reset_settings'] ) {
		update_option( 'wplt2', $defaults );
	}
	else {
		add_option( 'wplt2', $defaults );
	}
	
}
register_activation_hook( __FILE__, 'WPLT_activation' );