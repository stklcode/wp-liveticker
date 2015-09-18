<?php
/**
 * @package Dashboard
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add to Right Now Widget
 *
 * @return void
 */
function wplt_dashboard_right_now() {
	$total_files = wp_count_posts( 'wplt_tick' );
	
	echo '<tr>';
	echo '<td class="first b b-tags"><a href="edit.php?post_type=wplt_tick">' . $total_files->publish . '</a></td>';
	echo '<td class="t tags"><a href="edit.php?post_type=wplt_tick">' . __( 'Ticks', 'wplt2' ) . '</a></td>';
	echo '</tr>';
}
add_action( 'right_now_content_table_end' , 'wplt_dashboard_right_now' );

/**
 * Register dashboard widgets
 *
 * @return void
 */
function wplt_register_dashboard_widgets() {
	wp_add_dashboard_widget( 'wplt_dashboard_downloads', __( 'Download Stats', 'wplt2' ), 'wplt_dashboard_downloads_widget' );
}
//add_action( 'wp_dashboard_setup', 'wplt_register_dashboard_widgets' );

/**
 * Ticks Dashboard Widget
 *
 * @access      private
 * @since       1.0 
 * @return      void
*/
function wplt_dashboard_ticks_widget() {
	echo 'Content to follow...';
}