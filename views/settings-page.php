<?php
/**
 * Liveticker: Settings page.
 *
 * This file contains the view model for the Plugin settings oage.
 *
 * @package Liveticker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
