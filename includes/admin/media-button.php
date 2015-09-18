<?php
/**
 * @package Media Button
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Display Media Button
 *
 * @param string $content existing media buttons
 *
 * @return string $content + $output
 */
function wplt_media_button( $context ) {
	
	if( get_post_type() != 'wplt_download' ) {
		return $context . '<a href="#" id="sdm-media-button" class="button add-download" data-editor="content" title="Add Download"><span class="wp-media-buttons-icon"></span>Add Download</a>';
	}	
}
add_filter( 'media_buttons_context', 'wplt_media_button' );

/**
 * Add Modal Window to Footer
 *
 * @return void
 */
function wplt_media_modal() {
	global $wplt_options;
	
	$downloads = new WP_Query( 'post_type=wplt_download&nopaging=true&orderby=title&order=ASC' );
	?>
	<div id="sdm-download-modal" style="display: none">
		<div class="media-modal">
			<a id="sdm-download-modal-close" class="media-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>
			<div class="media-modal-content">
				<div class="media-frame-title">
					<h1><?php _e( 'Insert Download', 'simple-downloads' ); ?></h1>
				</div>
				<div class="left-panel">
					<div class="sdm-download-list">
						<ul id="selectable_list">
							<?php
							while ( $downloads->have_posts() ) {
								$downloads->the_post();
								echo '<li data-ID="' . get_the_ID() . '">';
								echo '<strong>' . get_the_title() . '</strong>';
								echo '<span class="download_url">' . get_post_meta( get_the_ID(), '_wplt_file_url', true ) . '</span>';
								echo '</li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div class="right-panel">
					<div class="download-details" style="display: none">
						<h3><?php _e( 'Download Details', 'simple-downloads' ); ?></h3>
						<label for="sdm-download-text"><?php _e( 'Text', 'simple-downloads' ); ?>:</label>
						<input type="text" name="sdm-download-text" id="sdm-download-text" value="<?php echo $wplt_options['default_text']; ?>"/>
						<label for="sdm-download-style"><?php _e( 'Style', 'simple-downloads' ); ?>:</label>
						<select name="sdm-download-style" id="sdm-download-style">
							<?php
							$styles = wplt_get_shortcode_styles();
							$default_style = $wplt_options['default_style'];
							
							foreach( $styles as $key => $value ) {
								$selected = ( $default_style == $key ? ' selected="selected"' : '' );
								echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';	
							}
							?>
						</select>
						<div class="sdm-download-color-container">
							<label for="sdm-download-color"><?php _e( 'Color', 'simple-downloads' ); ?>:</label>
							<select name="sdm-download-color" id="sdm-download-color">
								<?php
								$colors = wplt_get_shortcode_colors();
								$default_color = $wplt_options['default_color'];

								foreach( $colors as $key => $value ) {
									$selected = ( $default_color == $key ? ' selected="selected"' : '' );
									echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';	
								}
								?>
							</select>
						</div>
						<input id="sdm-download-button" type="button" value="<?php _e( 'Insert Download', 'simple-downloads' ); ?>" class="button-primary" />
						<input id="sdm-filesize-button" type="button" value="<?php _e( 'Insert File Size', 'simple-downloads' ); ?>" class="button" />
						<input id="sdm-count-button"type="button" value="<?php _e( 'Insert Download Count', 'simple-downloads' ); ?>" class="button" />
					</div>
				</div>
						
			</div>
		</div>
		<div class="media-modal-backdrop"></div>
	</div>
	<?php
}
add_action( 'admin_footer', 'wplt_media_modal' );