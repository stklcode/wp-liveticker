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
		echo '<td class="first b b-tags"><a href="edit.php?post_type=wplt_tick">' . $total_files->publish . '</a></td>';
		echo '<td class="t tags"><a href="edit.php?post_type=wplt_tick">' . __( 'Ticks', 'wplt2' ) . '</a></td>';
		echo '</tr>';
	}
}
