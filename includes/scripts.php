<?php
/**
 * @package Scripts
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Enqueue scripts and styles if shortcode present
 * @return bool
 */
function wplt_enqueue_scripts() {
	global $post;
	// Register frontend CSS
	if ( has_shortcode( $post->post_content, 'liveticker') )
		wp_enqueue_style( 'wplt-css', WPLT_PLUGIN_URL . 'includes/css/wp-liveticker2.css', '', WPLT_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'wplt_enqueue_scripts' );