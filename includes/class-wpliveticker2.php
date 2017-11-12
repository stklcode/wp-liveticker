<?php
/**
 * WP Liveticker 2: Plugin main class.
 *
 * This file contains the plugin's base class.
 *
 * @package WPLiveticker2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * WP Liveticker 2.
 */
class WPLiveticker2 {
	/**
	 * Options tag.
	 *
	 * @var string OPTIONS
	 */
	const VERSION = '1.0.0';

	/**
	 * Options tag.
	 *
	 * @var string OPTIONS
	 */
	const OPTION = 'wplt2';

	/**
	 * Plugin options.
	 *
	 * @var array $_options
	 */
	protected static $_options;

	/**
	 * Plugin options.
	 *
	 * @var boolean $shortcode_present
	 */
	protected static $shortcode_present = false;

	/**
	 * Plugin initialization.
	 *
	 * @return void
	 */
	public static function init() {
		// Skip on autosave or AJAX.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// Load plugin options.
		self::update_options();

		// Load Textdomain.
		load_plugin_textdomain( 'wplt2', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		// Allow shortcodes in widgets.
		add_filter( 'widget_text', 'do_shortcode' );

		// Add shortcode.
		add_shortcode( 'liveticker', array( 'WPLiveticker2', 'shortcode_ticker_show' ) );

		// Enqueue styles.
		add_action( 'wp_footer', array( 'WPLiveticker2', 'enqueue_styles' ) );

		// Admin only actions.
		if ( is_admin() ) {
			// Add dashboard "right now" functionality.
			add_action( 'right_now_content_table_end', array( 'WPLiveticker2_Admin', 'dashboard_right_now' ) );

			// Settings.
			add_action( 'admin_init', array( 'WPLiveticker2_Admin', 'register_settings' ) );
			add_action( 'admin_menu', array( 'WPLiveticker2_Admin', 'register_settings_page' ) );
		}
	}

	/**
	 * Register tick post type.
	 *
	 * @return void
	 */
	public static function register_types() {
		// Add new taxonomy, make it hierarchical (like categories).
		$labels = array(
			'name'              => _x( 'Ticker', 'taxonomy general name' ),
			'singular_name'     => _x( 'Ticker', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Tickers', 'wplt2' ),
			'all_items'         => __( 'All Tickers', 'wplt2' ),
			'parent_item'       => __( 'Parent Ticker', 'wplt2' ),
			'parent_item_colon' => __( 'Parent Ticker:', 'wplt2' ),
			'edit_item'         => __( 'Edit Ticker', 'wplt2' ),
			'update_item'       => __( 'Update Ticker', 'wplt2' ),
			'add_new_item'      => __( 'Add New Ticker', 'wplt2' ),
			'new_item_name'     => __( 'New Ticker', 'wplt2' ),
			'menu_name'         => __( 'Ticker', 'wplt2' ),
		);

		register_taxonomy(
			'wplt2_ticker',
			array( 'wplt2_tick' ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
			)
		);

		// Post type arguments.
		$args = array(
			'labels'          => array(
				'name'               => __( 'Ticks', 'wplt2' ),
				'singular_name'      => __( 'Tick', 'wplt2' ),
				'add_new'            => __( 'Add New', 'wplt2' ),
				'add_new_item'       => __( 'Add New Tick', 'wplt2' ),
				'edit_item'          => __( 'Edit Tick', 'wplt2' ),
				'new_item'           => __( 'New Tick', 'wplt2' ),
				'all_items'          => __( 'All Ticks', 'wplt2' ),
				'view_item'          => __( 'View Tick', 'wplt2' ),
				'search_items'       => __( 'Search Ticks', 'wplt2' ),
				'not_found'          => __( 'No Ticks found', 'wplt2' ),
				'not_found_in_trash' => __( 'No Ticks found in Trash', 'wplt2' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Liveticker', 'wplt2' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-rss',
			'capability_type' => 'post',
			'supports'        => array( 'title', 'editor', 'author' ),
			'taxonomies'      => array( 'wplt2_ticker' ),
		);

		register_post_type( 'wplt2_tick', $args );
	}

	/**
	 * Output Liveticker
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public static function shortcode_ticker_show( $atts ) {
		// Indicate presence of shortcode (to enqueue styles/scripts later).
		self::$shortcode_present = true;

		// Initialize output.
		$output = '';

		// Check if first attribute is filled.
		if ( $atts['ticker'] ) {
			// Set limit to infinite, if not set explicitly.
			if ( ! isset( $atts['limit'] ) ) {
				$atts['limit'] = - 1;
			}

			$output = '<ul class="wplt2_ticker">';

			$args = array(
				'post_type'      => 'wplt2_tick',
				'posts_per_page' => $atts['limit'],
				'tax_query'      => array(
					array(
						'taxonomy' => 'wplt2_ticker',
						'field'    => 'slug',
						'terms'    => $atts['ticker'],
					),
				),
			);

			$wp_query = new WP_Query( $args );

			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();
				$output .= '<li class="wplt2_tick">
						  <p><span class="wplt2_tick_time">' . get_the_time( 'd.m.Y H.i' ) . '</span>
						  <span class="wplt2_tick_title">' . get_the_title() . '</span></p>
						  <p class="wplt2_tick_content">' . get_the_content() . '</p></li>';
			}

			$output .= '</ul>';

			// Show RSS feed link, if configured.
			if ( 1 === self::$_options['show_feed'] ) {
				$output .= '<a href="/feed/liveticker/' . esc_html( $atts['ticker'] ) . '"><img class="wplt2_rss" src="/wp-content/plugins/wp-liveticker2/images/rss.jpg" alt="RSS" /></a>';
			}
		}// End if().

		return $output;
	}

	/**
	 * Register frontend CSS.
	 */
	public static function enqueue_styles() {
		// Only add if shortcode is present.
		if ( self::$shortcode_present ) {
			wp_enqueue_style( 'wplt-css', WPLT2_BASE . 'styles/wp-liveticker2.min.css', '', self::VERSION, 'all' );
		}
	}

	/**
	 * Update options.
	 *
	 * @param array $options Optional. New options to save.
	 */
	protected static function update_options( $options = null ) {
		self::$_options = wp_parse_args(
			get_option( self::OPTION ),
			self::default_options()
		);
	}

	/**
	 * Create default plugin configuration.
	 *
	 * @return array The options array.
	 */
	protected static function default_options() {
		return array(
			'enable_ajax'    => 1,
			'enable_css'     => 1,
			'show_feed'      => 0,
			'reset_settings' => 0,
		);
	}
}
