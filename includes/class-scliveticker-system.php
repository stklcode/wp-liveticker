<?php
/**
 * Liveticker: Plugin system class.
 *
 * This file contains the derived class for the plugin's system operations.
 *
 * @package Liveticker
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Liveticker system configuration.
 */
class SCLiveticker_System extends SCLiveticker {

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
		$ticks = new WP_Query( array( 'post_type' => 'scliveticker_tick' ) );
		foreach ( $ticks->get_posts() as $tick ) {
			wp_delete_post( $tick->ID, true );
		}

		// Temporarily register taxonomy to delete it.
		register_taxonomy( 'scliveticker_ticker', array( 'scliveticker_tick' ) );

		// Delete tickers.
		$tickers = get_terms(
			array(
				'taxonomy'   => 'scliveticker_ticker',
				'hide_empty' => false,
			)
		);
		foreach ( $tickers as $ticker ) {
			wp_delete_term( $ticker->term_id, 'scliveticker_ticker' );
		}

		// Unregister taxonomy again.
		unregister_taxonomy( 'scliveticker_ticker' );

		// Delete the option.
		delete_option( self::OPTION );
	}
}
