<?php
/**
 * @package Scripts
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register scripts and styles
 * @return bool
 */
function wplt_register_scripts() {
	// Register frontend CSS
	wp_register_style( 'wplt-css', WPLT_PLUGIN_URL . 'includes/css/wp-liveticker2.css', '', WPLT_VERSION, 'all' );
}
add_action( 'init', 'wplt_register_scripts' );

/**
 * Print scripts and styles if shortcode is present.
 * @return bool
 */
function wplt_print_scripts() {
	global $wplt_options;

	if( $wplt_options['enable_css'] && $wplt_options['shortcode_present'] ) {
	  wp_print_styles( 'wplt-css' );
	}
}
add_action( 'wp_footer', 'wplt_print_scripts' );

/**
 * Register admin scripts and style
 *
 * @param string $page current page
 *
 * @return bool
 */
function wplt_admin_enqueue_scripts( $page ) {
	// Register scripts
	wp_register_script( 'wplt-admin-js-post', WPLT_PLUGIN_URL . 'includes/js/admin-post.js', array( 'jquery', 'jquery-ui-selectable' ), '1.0', true );
	wp_register_script( 'wplt-admin-js-post-download', WPLT_PLUGIN_URL . 'includes/js/admin-post-download.js', array( 'jquery', 'plupload-all' ), '1.0', true );
	
	// Enqueue on all admin pages
	wp_enqueue_style( 'wplt-admin-css', WPLT_PLUGIN_URL . 'includes/css/admin.css' );
	
	// Enqueue on wplt_download post add/edit screen
	if( in_array( $page, array( 'post.php', 'post-new.php', 'post-edit.php' ) ) && get_post_type() == 'wplt_download' ) {
		wp_enqueue_script( 'plupload-all' );
		wp_enqueue_script( 'wplt-admin-js-post-tick' );
	}
	
	// Enqueue on all other add/edit screen
	if( in_array( $page, array( 'post.php', 'post-new.php', 'post-edit.php', 'page.php' ) ) && get_post_type() != 'wplt_download' ) {
		wp_enqueue_script( 'wplt-admin-js-post' );
	}
}
//add_action( 'admin_enqueue_scripts', 'wplt_admin_enqueue_scripts' );