<?php
/**
 * WP Liveticker 2: Plugin system class.
 *
 * This file contains the derived class for the plugin's system operations.
 *
 * @package WPLiveticker2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * WP Liveticker 2 system configuration.
 *
 */
class WPLiveticker2_System extends WPLiveticker2 {

	/**
	 * Activation hook.
	 *
	 * Initializes default options.
	 *
	 * @return void
	 */
	public static function activate() {
		// Load current options.
		self::update_options();

		// Add default settings to database.
		$defaults = self::default_options();

		if ( self::$_options['reset_settings'] ) {
			// Reset requested, overwrite existing options with default.
			update_option( self::OPTION, $defaults );
		} else {
			// Otherwise add new options.
			add_option( self::OPTION, $defaults );
		}
	}
}
