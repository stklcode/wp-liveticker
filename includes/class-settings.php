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
		self::render_checkbox(
			'enable-ajax',
			'[enable_ajax]',
			self::$options['enable_ajax'],
			__( 'Disable this option to not use AJAX update. This means all liveticker widgets and shortcodes are only updated once on site load.', 'stklcode-liveticker' ),
			__( 'Enable AJAX updates', 'stklcode-liveticker' )
		);
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
		self::render_checkbox(
			'enable-css',
			'[enable_css]',
			self::$options['enable_css'],
			__( 'Disable this option to remove the default styling CSS file.', 'stklcode-liveticker' ),
			__( 'Enable default stylesheet', 'stklcode-liveticker' )
		);
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
		self::render_checkbox(
			'show-feed',
			'[show_feed]',
			self::$options['show_feed'],
			__( 'Can be overwritten in shortcode.', 'stklcode-liveticker' ),
			__( 'Show RSS feed in shortcode', 'stklcode-liveticker' )
		);
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
		self::render_checkbox(
			'enable-shortcode',
			'[enable_shortcode]',
			self::$options['enable_shortcode'],
			__( 'Enable shortcode processing in tick content.', 'stklcode-liveticker' ),
			__( 'Allow shortcodes in tick content', 'stklcode-liveticker' )
		);
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
		self::render_checkbox(
			'embedded-script',
			'[embedded_script]',
			self::$options['embedded_script'],
			__( 'Allow embedded script evaluation in tick contents. This might be useful for embedded content, e.g. social media integrations.', 'stklcode-liveticker' ),
			__( 'Allow JavaScript in tick content', 'stklcode-liveticker' )
		);
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

	/**
	 * Render a checkbox field.
	 *
	 * @param string $id                 Field ID.
	 * @param string $name               Option name.
	 * @param mixed  $value              Current value.
	 * @param string $description        Description text.
	 * @param string $screen_reader_text Screen reader text.
	 *
	 * @return void
	 */
	private static function render_checkbox(
		string $id,
		string $name,
		$value,
		string $description,
		string $screen_reader_text
	) {
		?>
		<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( $screen_reader_text ); ?></legend>
			<label for="<?php echo esc_attr( self::OPTION . '-' . $id ); ?>">
				<input id="<?php echo esc_attr( self::OPTION . '-' . $id ); ?>" name="<?php echo esc_attr( self::OPTION . $name ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?>>
				<?php esc_html_e( 'Enable', 'stklcode-liveticker' ); ?>
			</label>
			<p class="description"><?php echo esc_html( $description ); ?></p>
		</fieldset>
		<?php
	}
}
