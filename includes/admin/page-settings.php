<?php
/**
 * @package Settings Page
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register settings page
 *
 * @return void
 */
function wplt_register_page_settings() {
	add_submenu_page( 'edit.php?post_type=wplt_tick', 'Liveticker2 ' . __( 'Settings', 'wplt2' ), __( 'Settings', 'wplt2' ), 'manage_options', 'wplt_settings', 'wplt_render_page_settings' );
}
add_action( 'admin_menu', 'wplt_register_page_settings' );

/**
 * Register settings API
 *
 * @return void
 */
function wplt_register_settings() {
	register_setting( 'wplt_settings', 'wplt2', 'wplt_validate_settings' ); 
	
	// Form sections
	add_settings_section( 'wplt_settings_general', __( 'General', 'wplt2' ), 'wplt_settings_general_section', __FILE__ );
	add_settings_section( 'wplt_settings_uninstall', __( 'Uninstall', 'wplt2' ), 'wplt_settings_uninstall_section', __FILE__ );
	
	// Form fields
	add_settings_field( 'enable_css', __( 'Default CSS Styles', 'wplt2' ), 'wplt_settings_enable_css_field', __FILE__, 'wplt_settings_general' );
	add_settings_field( 'reset_settings', __( 'Reset Settings', 'wplt2' ), 'wplt_settings_reset_settings_field', __FILE__, 'wplt_settings_uninstall' );
} 
add_action( 'admin_init', 'wplt_register_settings' );

/**
 * Validate settings callback
 *
 * @return void
 */
function wplt_validate_settings( $input ) {
	 $defaults = wplt_get_default_options();
	 
	 $parsed = wp_parse_args( $input, $defaults );
	 
	 // Fix empty default text textbox
	 if( trim( $input['default_text'] == '' ) ) {
		 $parsed['default_text'] = $defaults['default_text'];
	 }
	 
	 return $parsed;
}

/**
 * Render settings page
 *
 * @return void
 */
function wplt_render_page_settings() {
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Liveticker <?php _e( 'Settings', 'wplt2' ); ?></h2>
		<?php if ( isset( $_GET['settings-updated'] ) ) {
			echo '<div class="updated"><p>' . __( 'Settings updated successfully.', 'wplt2' ) . '</p></div>';
		} ?>
		<form action="options.php" method="post">
			<?php 
				settings_fields( 'wplt_settings' ); 
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
 * Render general section
 *
 * @return void
 */
function wplt_settings_general_section() {
	return;
}

/**
 * Render uninstall section
 *
 * @return void
 */
function wplt_settings_uninstall_section() {
	return;
}

/**
 * Render enable css field
 *
 * @return void
 */
function wplt_settings_enable_css_field() {
	global $wplt_options;
	
	$checked = $wplt_options['enable_css'];

	echo '<label for="wp-liveticker2[enable_css]">';
	echo '<input type="checkbox" name="wp-liveticker2[enable_css]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
	echo __( 'Enable', 'wplt2' );
	echo '</label>';
	echo '<p class="description">' . __( 'Disable this option to remove the default button styling and the Delightful Downloads CSS file.', 'wplt2' ) . '</p>';
}

/**
 * Render default style field
 *
 * @return void
 */
function wplt_settings_default_style_field() {
	global $wplt_options;

	$styles = wplt_get_shortcode_styles();
	$default_style = $wplt_options['default_style'];

	echo '<select name="simple-downloads[default_style]">';
	foreach( $styles as $key => $value ) {
		$selected = ( $default_style == $key ? ' selected="selected"' : '' );
		echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';	
	}
	echo '</select>';
	echo '<p class="description">' . __( 'The default display style.', 'wplt2' ) . '</p>';
}

/**
 * Render reset settings field
 *
 * @return void
 */
function wplt_settings_reset_settings_field() {
	global $wplt_options;
	
	$checked = $wplt_options['reset_settings'];
	
	echo '<label for="simple-downloads[reset_settings]">';
	echo '<input type="checkbox" name="simple-downloads[reset_settings]" value="1" ' . checked( $checked, 1, false ) . ' /> ';
	echo __( 'Enable', 'wplt2' );
	echo '<p class="description">' . __( 'Reset plugin settings on re-activation.', 'wplt2' ) . '</p>';
	echo '</label>';
}