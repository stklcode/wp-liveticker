<?php
/**
 * WP Liveticker 2: Widget class.
 *
 * This file contains the liveticker widget.
 *
 * @package WPLiveticker2
 */

/**
 * Class WPLiveticker2_Widget.
 */
class WPLiveticker2_Widget extends WP_Widget {

	/**
	 * WPLiveticker2_Widget constructor.
	 */
	public function __construct() {
		parent::__construct( false, 'Liveticker' );
	}

	/**
	 * Register the widget.
	 */
	public static function register() {
		register_widget( 'WPLiveticker2_Widget' );
	}

	/**
	 * Echoes the widget content.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// Notify WPLT2 class that widget is present.
		WPLiveticker2::mark_widget_present();

		$instance       = self::fill_options_with_defaults( $instance );
		$before_widget  = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$after_widget   = isset( $args['after_widget'] ) ? $args['after_widget'] : '';
		$before_title   = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title    = isset( $args['after_title'] ) ? $args['after_title'] : '';
		$title          = apply_filters( 'wplt2_catlit', $instance['title'] );
		$category       = apply_filters( 'wplt2_catlit', $instance['category'] );
		$count          = apply_filters( 'wplt2_catlit', $instance['count'] );
		$link           = apply_filters( 'wplt2_catlit', $instance['link'] );
		$highlight      = apply_filters( 'wplt2_catlit', $instance['highlight'] );
		$highlight_time = apply_filters( 'wplt2_catlit', $instance['highlight_time'] );
		$ajax           = apply_filters( 'wplt2_catlit', $instance['ajax'] );
		?>

		<?php
		// @codingStandardsIgnoreLine
		echo $before_widget;
		?>

		<?php
		if ( $title ) {
			// @codingStandardsIgnoreLine
			echo $before_title . esc_html( $title ) . $after_title;
		}

		echo '<ul class="wplt2-widget';
		if ( '1' === $ajax ) {
			echo ' wplt2-widget-ajax" '
				. 'data-wplt2-ticker="' . esc_attr( $category ) . '" '
				. 'data-wplt2-limit="' . esc_attr( $count ) . '" '
				. 'data-wplt2-last="' . esc_attr( time() );
		}
		echo '">';

		$args = array(
			'post_type' => 'wplt2_tick',
			'tax_query' => array(
				array(
					'taxonomy' => 'wplt2_ticker',
					'field'    => 'slug',
					'terms'    => $category,
				),
			),
		);

		$wp_query = new WP_Query( $args );
		$cnt      = 0;
		while ( $wp_query->have_posts() && ( $count <= 0 || ++ $cnt < $count ) ) {
			$wp_query->the_post();
			// @codingStandardsIgnoreLine
			echo WPLiveticker2::tick_html_widget(
				esc_html( get_the_time( 'd.m.Y - H.i' ) ),
				get_the_title(),
				( '1' === $highlight && get_the_time( 'U' ) > ( time() - $highlight_time ) )
			);
		}

		echo '</ul>';

		if ( $link ) {
			echo '<p class="wplt2-widget-link">'
				. '<a href="' . esc_attr( $link ) . '">' . esc_html__( 'show all', 'wplt2' ) . '...</a>'
				. '</p>';
		}
		// @codingStandardsIgnoreLine
		echo $after_widget;
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Determine configuration flags with fallback to default.
		$title          = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$category       = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
		$count          = isset( $instance['count'] ) ? esc_attr( $instance['count'] ) : '';
		$link           = isset( $instance['link'] ) ? esc_attr( $instance['link'] ) : '';
		$highlight      = isset( $instance['highlight'] ) ? esc_attr( $instance['highlight'] ) : '0';
		$highlight_time = isset( $instance['highlight_time'] ) ? esc_attr( $instance['highlight_time'] ) : '0';
		$ajax           = isset( $instance['ajax'] ) ? esc_attr( $instance['ajax'] ) : '0';
		$categories     = get_terms( 'wplt2_ticker', 'orderby=name&order=ASC' );

		include WPLT2_DIR . 'views/widget-form.php';
	}

	/**
	 * Fill instance configuration with default options.
	 *
	 * @param array $instance Potentially incomplete instance configuration.
	 *
	 * @return array Complete instance configuration.
	 */
	private static function fill_options_with_defaults( $instance ) {
		$default = array(
			'title'          => '',
			'category'       => '',
			'count'          => '',
			'link'           => '',
			'highlight'      => '0',
			'highlight_time' => '0',
			'ajax'           => '0',
		);

		return array_merge( $default, $instance );
	}
}
