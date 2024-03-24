<?php
/**
 * Liveticker: Plugin admin class.
 *
 * This file contains the derived class for the plugin's administration features.
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
 */
class Admin extends SCLiveticker {
	/**
	 * Add to Right Now Widget
	 *
	 * @return void
	 */
	public static function dashboard_right_now(): void {
		$total_files = wp_count_posts( 'scliveticker_tick' );

		echo '<tr>';
		echo '<td class="first b b-tags"><a href="edit.php?post_type=scliveticker_tick">' . esc_html( $total_files->publish ) . '</a></td>';
		echo '<td class="t tags"><a href="edit.php?post_type=scliveticker_tick">' . esc_html__( 'Ticks', 'stklcode-liveticker' ) . '</a></td>';
		echo '</tr>';
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public static function register_settings_page(): void {
		add_submenu_page(
			'edit.php?post_type=scliveticker_tick',
			'Liveticker ' . __( 'Settings', 'stklcode-liveticker' ),
			__( 'Settings', 'stklcode-liveticker' ),
			'manage_options',
			'scliveticker_settings',
			array( Settings::class, 'render_settings_page' )
		);
	}

	/**
	 * Register custom Gutenberg block type.
	 *
	 * @return void
	 * @since 1.1
	 */
	public static function register_block(): void {
		wp_register_script(
			'scliveticker-editor',
			SCLIVETICKER_BASE . 'scripts/block.min.js',
			array( 'wp-blocks', 'wp-element' ),
			self::VERSION,
			true
		);

		wp_register_style(
			'scliveticker-editor',
			SCLIVETICKER_BASE . 'styles/block.min.css',
			array(),
			self::VERSION
		);

		register_block_type(
			'scliveticker-block/liveticker',
			array(
				'editor_script' => 'scliveticker-editor',
				'editor_style'  => 'scliveticker-editor',
			)
		);
	}
}
