<?php
/**
 * WP Liveticker 2: Plugin admin class.
 *
 * This file contains the derived class for the plugin's administration features.
 *
 * @package WPLiveticker2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Liveticker 2 admin configuration.
 *
 * @since   1.0.0
 */
class WPLiveticker2_Admin extends WPLiveticker2 {
	/**
	 * Add to Right Now Widget
	 *
	 * @return void
	 */
	public static function dashboard_right_now() {
		$total_files = wp_count_posts( 'wplt2_tick' );

		echo '<tr>';
		echo '<td class="first b b-tags"><a href="edit.php?post_type=wplt2_tick">' . esc_html( $total_files->publish ) . '</a></td>';
		echo '<td class="t tags"><a href="edit.php?post_type=wplt2_tick">' . esc_html__( 'Ticks', 'wplt2' ) . '</a></td>';
		echo '</tr>';
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public static function register_settings_page() {
		add_submenu_page(
			'edit.php?post_type=wplt2_tick',
			'Liveticker2 ' . __( 'Settings', 'wplt2' ),
			__( 'Settings', 'wplt2' ),
			'manage_options',
			'wplt2_settings',
			array(
				'WPLiveticker2_Admin',
				'settings_page',
			)
		);
	}

	/**
	 * Register settings API
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting(
			'wplt2_settings',
			'wplt2',
			array( 'WPLiveticker2_Admin', 'validate_settings' )
		);

		// Form sections.
		add_settings_section(
			'wplt2_settings_general',
			__( 'General', 'wplt2' ),
			array( 'WPLiveticker2_Admin', 'settings_general_section' ),
			'wplt2-settings-page'
		);

		// Form fields.
		add_settings_field(
			'enable_ajax',
			__( 'Use AJAX', 'wplt2' ),
			array( 'WPLiveticker2_Admin', 'settings_enable_ajax_field' ),
			'wplt2-settings-page',
			'wplt2_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-enable-ajax' )
		);

		add_settings_field(
			'poll_interval',
			__( 'AJAX poll interval', 'wplt2' ),
			array( 'WPLiveticker2_Admin', 'settings_poll_interval_field' ),
			'wplt2-settings-page',
			'wplt2_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-poll-interval' )
		);

		add_settings_field(
			'enable_css',
			__( 'Default CSS Styles', 'wplt2' ),
			array( 'WPLiveticker2_Admin', 'settings_enable_css_field' ),
			'wplt2-settings-page',
			'wplt2_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-enable-css' )
		);

		add_settings_field(
			'show_feed',
			__( 'Show RSS feed', 'wplt2' ),
			array( 'WPLiveticker2_Admin', 'settings_show_feed_field' ),
			'wplt2-settings-page',
			'wplt2_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-show-feed' )
		);
	}

	/**
	 * Render general section.
	 *
	 * @return void
	 */
	public static function settings_general_section() {
	}

	/**
	 * Render uninstall section.
	 *
	 * @return void
	 */
	public static function settings_uninstall_section() {
	}

	/**
	 * Render enable AJAX field.
	 *
	 * @return void
	 */
	public static function settings_enable_ajax_field() {
		$checked = self::$_options['enable_ajax'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-enable-ajax" type="checkbox" name="' . esc_attr( self::OPTION ) . '[enable_ajax]" value="1" ' . checked( $checked, 1, false ) . '> ';
		esc_html_e( 'Enable', 'wplt2' );
		echo '<p class="description">' . esc_html__( 'Disable this option to not use AJAX update. This means all liveticker widgets and shortcodes are only updated once on site load.', 'wplt2' ) . '</p>';
	}

	/**
	 * Render AJAX poll interval field.
	 *
	 * @return void
	 */
	public static function settings_poll_interval_field() {
		$poll_interval = self::$_options['poll_interval'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-poll-interval" type="number" name="' . esc_attr( self::OPTION ) . '[poll_interval]" value="' . esc_attr( $poll_interval ) . '"> ';
		esc_html_e( 'seconds', 'wplt2' );
		echo '<p class="description">' . esc_html__( 'Interval (in seconds) to update ticker if AJAX is enabled.', 'wplt2' ) . '</p>';
	}


	/**
	 * Render enable css field.
	 *
	 * @return void
	 */
	public static function settings_enable_css_field() {
		$checked = self::$_options['enable_css'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-enable-css" type="checkbox" name="' . esc_attr( self::OPTION ) . '[enable_css]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'wplt2' );
		echo '<p class="description">' . esc_html__( 'Disable this option to remove the default styling CSS file.', 'wplt2' ) . '</p>';
	}

	/**
	 * Render enable css field.
	 *
	 * @return void
	 */
	public static function settings_show_feed_field() {
		$checked = self::$_options['show_feed'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-show-feed" type="checkbox" name="' . esc_attr( self::OPTION ) . '[show_feed]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'wplt2' );
		echo '<p class="description">' . esc_html__( 'Can be overwritten in shortcode.', 'wplt2' ) . '</p>';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function settings_page() {
		include WPLT2_DIR . 'views/settings-page.php';
	}

	/**
	 * Validate settings callback.
	 *
	 * @param array $input Input arguments.
	 *
	 * @return array Parsed arguments.
	 */
	public static function validate_settings( $input ) {
		$defaults = self::default_options();
		$result   = wp_parse_args( $input, $defaults );
		foreach ( $defaults as $k => $v ) {
			if ( is_int( $v ) ) {
				$result[ $k ] = intval( $result[ $k ] );
			}
		}

		return $result;
	}
}
