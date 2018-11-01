<?php
/**
 * WP Liveticker 2: Plugin system class.
 *
 * This file contains the derived class for the plugin's system operations.
 *
 * @package WPLiveticker2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Liveticker 2 system configuration.
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

	/**
	 * Plugin uninstall handler.
	 *
	 * @return void
	 */
	public static function uninstall() {
		// Delete all ticks.
		$ticks = new WP_Query( array( 'post_type' => 'wplt2_tick' ) );
		foreach ( $ticks->get_posts() as $tick ) {
			wp_delete_post( $tick->ID, true );
		}

		// Temporarily register taxonomy to delete it.
		register_taxonomy( 'wplt2_ticker', array( 'wplt2_tick' ) );

		// Delete tickers.
		$tickers = get_terms(
			array(
				'taxonomy'   => 'wplt2_ticker',
				'hide_empty' => false,
			)
		);
		foreach ( $tickers as $ticker ) {
			wp_delete_term( $ticker->term_id, 'wplt2_ticker' );
		}

		// Unregister taxonomy again.
		unregister_taxonomy( 'wplt2_ticker' );

		// Delete the option.
		delete_option( self::OPTION );
	}
}
