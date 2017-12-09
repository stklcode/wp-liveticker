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
		// Skip on autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Load plugin options.
		self::update_options();

		// Skip on AJAX if not enabled disabled.
		if ( ( ! isset( self::$_options['enable_ajax'] ) || 1 !== self::$_options['enable_ajax'] ) && ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// Load Textdomain.
		load_plugin_textdomain( 'wplt2', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		// Allow shortcodes in widgets.
		add_filter( 'widget_text', 'do_shortcode' );

		// Add shortcode.
		add_shortcode( 'liveticker', array( 'WPLiveticker2', 'shortcode_ticker_show' ) );

		// Enqueue styles.
		add_action( 'wp_footer', array( 'WPLiveticker2', 'enqueue_styles' ) );

		// Enqueue JavaScript.
		add_action( 'wp_footer', array( 'WPLiveticker2', 'enqueue_scripts' ) );

		// Add AJAX hook if configured.
		if ( 1 === self::$_options['enable_ajax'] ) {
			add_action( 'wp_ajax_wplt2_update-ticks', array( 'WPLiveticker2', 'ajax_update' ) );
			add_action( 'wp_ajax_nopriv_wplt2_update-ticks', array( 'WPLiveticker2', 'ajax_update' ) );
		}

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

			$output = '<ul class="wplt2-ticker';
			if ( 1 === self::$_options['enable_ajax'] ) {
				$output .= ' wplt2-ticker-ajax" '
							. 'data-wplt2-ticker="' . $atts['ticker'] . '" '
							. 'data-wplt2-limit="' . $atts['limit'] . '" '
							. 'data-wplt2-last="' . time();
			}
			$output .= '">';

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
				$output .= self::tick_html( get_the_time( 'd.m.Y H.i' ), get_the_title(), get_the_content() );
			}

			$output .= '</ul>';

			// Show RSS feed link, if configured.
			if ( 1 === self::$_options['show_feed'] ) {
				// TODO.
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
			wp_enqueue_style(
				'wplt-css',
				WPLT2_BASE . 'styles/wp-liveticker2.css',
				'',
				self::VERSION, 'all'
			);
		}
	}

	/**
	 * Register frontend JS.
	 */
	public static function enqueue_scripts() {
		wp_enqueue_script(
			'wplt2-js',
			WPLT2_BASE . 'scripts/wp-liveticker2.js',
			array( 'jquery' ),
			self::VERSION
		);

		// Add endpoint to script.
		wp_localize_script(
			'wplt2-js',
			'ajax_object',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'wplt2_update-ticks' ),
				'poll_interval' => self::$_options['poll_interval'] * 1000,
			)
		);
	}

	/**
	 * Process Ajax upload file
	 *
	 * @return void
	 */
	public static function ajax_update() {
		// Verify AJAX nonce.
		check_ajax_referer( 'wplt2_update-ticks' );

		// Extract update requests.
		if ( isset( $_POST['update'] ) && is_array( $_POST['update'] ) ) {
			$res = array();
			foreach ( wp_unslash( $_POST['update'] ) as $update_req ) {
				if ( isset( $update_req['s'] ) ) {
					$slug      = $update_req['s'];
					$limit     = ( isset( $update_req['l'] ) ) ? intval( $update_req['l'] ) : - 1;
					$last_poll = ( isset( $update_req['t'] ) ) ? intval( $update_req['t'] ) : 0;

					// Query new ticks from DB.
					$query_args = array(
						'post_type'      => 'wplt2_tick',
						'posts_per_page' => $limit,
						'tax_query'      => array(
							array(
								'taxonomy' => 'wplt2_ticker',
								'field'    => 'slug',
								'terms'    => $slug,
							),
						),
						'date_query'     => array(
							'after' => date( 'c', $last_poll ),
						),
					);

					$query = new WP_Query( $query_args );

					$out = '';
					while ( $query->have_posts() ) {
						$query->the_post();
						$out .= self::tick_html( get_the_time( 'd.m.Y H.i' ), get_the_title(), get_the_content() );
					}
					$res[] = array(
						's' => $slug,
						'h' => $out,
						't' => time(),
					);
				}
			}
			// Echo JSON encoded result set.
			echo json_encode( $res );
		}

		exit;
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
			'poll_interval'  => 60,
			'enable_css'     => 1,
			'show_feed'      => 0,
			'reset_settings' => 0,
		);
	}

	/**
	 * Generate HTML code for a tick element.
	 *
	 * @param string $time    Tick time (readable).
	 * @param string $title   Tick title.
	 * @param string $content Tick content.
	 */
	public static function tick_html( $time, $title, $content = null ) {
		return '<li class="wplt2-tick">'
				. '<p><span class="wplt2-tick_time">' . esc_html( $time ) . '</span>'
				. '<span class="wplt2-tick-title">' . esc_html( $title ) . '</span></p>'
				. '<p class="wplt2-tick-content">' . $content . '</p></li>';
	}
}
