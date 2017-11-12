<?php
/**
 * WP Liveticker 2: Plugin admin class.
 *
 * This file contains the derived class for the plugin's administration features.
 *
 * @package WPLiveticker2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

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
	function dashboard_right_now() {
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
		register_setting( 'wplt2_settings', 'wplt2', array( 'WPLiveticker2_Admin', 'validate_settings' ) );

		// Form sections.
		add_settings_section( 'wplt2_settings_general', __( 'General', 'wplt2' ), array( 'WPLiveticker2_Admin', 'settings_general_section' ), __FILE__ );
		add_settings_section( 'wplt2_settings_uninstall', __( 'Uninstall', 'wplt2' ), array( 'WPLiveticker2_Admin', 'settings_uninstall_section' ), __FILE__ );

		// Form fields.
		add_settings_field( 'enable_css', __( 'Default CSS Styles', 'wplt2' ), array( 'WPLiveticker2_Admin', 'settings_enable_css_field' ), __FILE__, 'wplt2_settings_general' );
		add_settings_field( 'reset_settings', __( 'Reset Settings', 'wplt2' ), array( 'WPLiveticker2_Admin', 'settings_reset_settings_field' ), __FILE__, 'wplt2_settings_uninstall' );
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
	 * Render enable css field.
	 *
	 * @return void
	 */
	public static function settings_enable_css_field() {
		$checked = self::$_options['enable_css'];

		echo '<label for="wp-liveticker2[enable_css]">';
		echo '<input type="checkbox" name="wp-liveticker2[enable_css]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'wplt2' );
		echo '</label>';
		echo '<p class="description">' . esc_html__( 'Disable this option to remove the default button styling and the Delightful Downloads CSS file.', 'wplt2' ) . '</p>';
	}

	/**
	 * Render default style field.
	 *
	 * @return void
	 */
	public static function settings_default_style_field() {
		$styles        = wplt_get_shortcode_styles();
		$default_style = self::$_options['default_style'];

		echo '<select name="simple-downloads[default_style]">';
		foreach ( $styles as $key => $value ) {
			$selected = ( $default_style === $key ? ' selected="selected"' : '' );
			echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select>';
		echo '<p class="description">' . esc_html__( 'The default display style.', 'wplt2' ) . '</p>';
	}

	/**
	 * Render reset settings field
	 *
	 * @return void
	 */
	public static function settings_reset_settings_field() {
		$checked = self::$_options['reset_settings'];

		echo '<label for="simple-downloads[reset_settings]">';
		echo '<input type="checkbox" name="simple-downloads[reset_settings]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'wplt2' );
		echo '<p class="description">' . esc_html__( 'Reset plugin settings on re-activation.', 'wplt2' ) . '</p>';
		echo '</label>';
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function settings_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Liveticker <?php esc_html_e( 'Settings', 'wplt2' ); ?></h2>
			<?php if ( isset( $_GET['settings-updated'] ) ) {
				echo '<div class="updated"><p>' . esc_html__( 'Settings updated successfully.', 'wplt2' ) . '</p></div>';
} ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wplt2_settings' );
				do_settings_sections( __FILE__ );
				?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
			</form>
		</div>
		<?php
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

		return wp_parse_args( $input, $defaults );
	}
}
