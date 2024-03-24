<?php
/**
 * Liveticker: Plugin settings class.
 *
 * This file contains the derived class for the plugin's settings.
 *
 * @package SCLiveticker
 */

namespace SCLiveticker;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Liveticker admin configuration.
 *
 * @since 1.3.0 extracted from {@link Admin} class
 */
class Settings extends SCLiveticker {

	/**
	 * Register settings API
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function register_settings(): void {
		register_setting(
			'scliveticker_settings',
			self::OPTION,
			array( __CLASS__, 'validate_settings' )
		);

		// Form sections.
		add_settings_section(
			'scliveticker_settings_general',
			__( 'General', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_general_section' ),
			'scliveticker-settings-page'
		);

		// Form fields.
		add_settings_field(
			'enable_ajax',
			__( 'Use AJAX', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_enable_ajax_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-enable-ajax' )
		);

		add_settings_field(
			'poll_interval',
			__( 'AJAX poll interval', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_poll_interval_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-poll-interval' )
		);

		add_settings_field(
			'enable_css',
			__( 'Default CSS Styles', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_enable_css_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-enable-css' )
		);

		add_settings_field(
			'show_feed',
			__( 'Show RSS feed', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_show_feed_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-show-feed' )
		);

		add_settings_field(
			'enable_shortcode',
			__( 'Shortcode support', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_enable_shortcode_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-enable-shortcode' )
		);

		add_settings_field(
			'embedded_script',
			__( 'Embedded JavaScript', 'stklcode-liveticker' ),
			array( __CLASS__, 'settings_embedded_script_field' ),
			'scliveticker-settings-page',
			'scliveticker_settings_general',
			array( 'label_for' => esc_attr( self::OPTION ) . '-embedded-script' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function render_settings_page(): void {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Liveticker <?php esc_html_e( 'Settings', 'stklcode-liveticker' ); ?></h2>
			<?php
			if ( isset( $_GET['settings-updated'] ) ) {	// phpcs:ignore
				echo '<div class="updated"><p>' . esc_html__( 'Settings updated successfully.', 'stklcode-liveticker' ) . '</p></div>';
			}
			?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'scliveticker_settings' );
				do_settings_sections( 'scliveticker-settings-page' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}


	/**
	 * Render general section.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_general_section(): void {
	}

	/**
	 * Render enable AJAX field.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_enable_ajax_field(): void {
		$checked = self::$options['enable_ajax'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-enable-ajax" type="checkbox" name="' . esc_attr( self::OPTION ) . '[enable_ajax]" value="1" ' . checked( $checked, 1, false ) . '> ';
		esc_html_e( 'Enable', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Disable this option to not use AJAX update. This means all liveticker widgets and shortcodes are only updated once on site load.', 'stklcode-liveticker' ) . '</p>';
	}

	/**
	 * Render AJAX poll interval field.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_poll_interval_field(): void {
		$poll_interval = self::$options['poll_interval'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-poll-interval" type="number" name="' . esc_attr( self::OPTION ) . '[poll_interval]" value="' . esc_attr( $poll_interval ) . '"> ';
		esc_html_e( 'seconds', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Interval (in seconds) to update ticker if AJAX is enabled.', 'stklcode-liveticker' ) . '</p>';
	}


	/**
	 * Render enable css field.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_enable_css_field(): void {
		$checked = self::$options['enable_css'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-enable-css" type="checkbox" name="' . esc_attr( self::OPTION ) . '[enable_css]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Disable this option to remove the default styling CSS file.', 'stklcode-liveticker' ) . '</p>';
	}

	/**
	 * Render enable css field.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_show_feed_field(): void {
		$checked = self::$options['show_feed'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-show-feed" type="checkbox" name="' . esc_attr( self::OPTION ) . '[show_feed]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Can be overwritten in shortcode.', 'stklcode-liveticker' ) . '</p>';
	}

	/**
	 * Render enable shortcode field.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_enable_shortcode_field(): void {
		$checked = self::$options['enable_shortcode'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-enable-shortcode" type="checkbox" name="' . esc_attr( self::OPTION ) . '[enable_shortcode]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Enable shortcode processing in tick content.', 'stklcode-liveticker' ) . '</p>';
	}

	/**
	 * Render embedded script field.
	 *
	 * @return void
	 *
	 * @since 1.2.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function settings_embedded_script_field(): void {
		$checked = self::$options['embedded_script'];

		echo '<input id="' . esc_attr( self::OPTION ) . '-embedded-script" type="checkbox" name="' . esc_attr( self::OPTION ) . '[embedded_script]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
		esc_html_e( 'Enable', 'stklcode-liveticker' );
		echo '<p class="description">' . esc_html__( 'Allow embedded script evaluation in tick contents. This might be useful for embedded content, e.g. social media integrations.', 'stklcode-liveticker' ) . '</p>';
	}

	/**
	 * Validate settings callback.
	 *
	 * @param array $input Input arguments.
	 *
	 * @return array Parsed arguments.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 moved from Admin to Settings class
	 */
	public static function validate_settings( array $input ): array {
		$defaults = self::default_options();

		$result['enable_ajax']      = isset( $input['enable_ajax'] ) ? intval( $input['enable_ajax'] ) : 0;
		$result['poll_interval']    = isset( $input['poll_interval'] ) ? intval( $input['poll_interval'] ) : $defaults['poll_interval'];
		$result['enable_css']       = isset( $input['enable_css'] ) ? intval( $input['enable_css'] ) : 0;
		$result['show_feed']        = isset( $input['show_feed'] ) ? intval( $input['show_feed'] ) : 0;
		$result['enable_shortcode'] = isset( $input['enable_shortcode'] ) ? intval( $input['enable_shortcode'] ) : 0;
		$result['embedded_script']  = isset( $input['embedded_script'] ) ? intval( $input['embedded_script'] ) : 0;

		return $result;
	}
}
