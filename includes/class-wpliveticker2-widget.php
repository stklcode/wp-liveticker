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
		$instance = self::fill_options_with_defaults( $instance );
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
		echo $before_widget;
		?>

		<?php
		if ( $title ) {
			echo $before_title . esc_html( $title ) . $after_title;
		}

		?>
		<ul class="wplt_widget">
			<?php
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
			$cnt = 0;
			while ( $wp_query->have_posts() ) : $wp_query->the_post();
				?>
				<li>
					<span class="wplt_widget_time"><?php echo esc_html( get_the_time( 'd.m.Y - H.i' ) ); ?></span><span class="wplt_widget_content<?php if ( '1' === $highlight && get_the_time( 'U' ) > ( time() - $highlight_time ) ) {
						echo '_new';
} ?>"><br /><?php echo the_title(); ?></span></li>
				<?php
				if ( $count > 0 && ++ $cnt === $count ) {
					break;
				}
			endwhile;
			?>
		</ul>

		<?php
		if ( $link ) {
			print '<p class="wplt_widget_link"><a href="' . esc_attr( $link ) . '">' . esc_html__( 'show all', 'wplt2' ) . '...</a></p>';
		}

		echo $after_widget;
		?>
		<?php
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
		?>

		<table>
			<tr>
				<td>
					<label for="<?php echo esc_html( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_html( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Ticker:', 'wplt2' ); ?></label>
				</td>
				<td>
					<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
						<?php foreach ( $categories as $cat ) {
							echo '<option value="' . esc_attr( $cat->slug ) . '"';
							if ( $category === $cat->slug ) {
								echo ' selected="selected"';
							}
							echo '>' . esc_html( $cat->name ) . '</option>';
} ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of Ticks:', 'wplt2' ); ?></label>
				</td>
				<td>
					<select id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>">
						<option value="0"<?php if ( '0' === $count ) { echo ' selected="selected"'; } ?>><?php esc_html_e( 'all', 'wplt2' ); ?></option>
						<option value="1"<?php if ( '1' === $count ) { echo ' selected="selected"'; } ?>>1</option>
						<option value="2"<?php if ( '2' === $count ) { echo ' selected="selected"'; } ?>>2</option>
						<option value="3"<?php if ( '3' === $count ) { echo ' selected="selected"'; } ?>>3</option>
						<option value="4"<?php if ( '4' === $count ) { echo ' selected="selected"'; } ?>>4</option>
						<option value="5"<?php if ( '5' === $count ) { echo ' selected="selected"'; } ?>>5</option>
						<option value="6"<?php if ( '6' === $count ) { echo ' selected="selected"'; } ?>>6</option>
						<option value="7"<?php if ( '7' === $count ) { echo ' selected="selected"'; } ?>>7</option>
						<option value="8"<?php if ( '8' === $count ) { echo ' selected="selected"'; } ?>>8</option>
						<option value="9"<?php if ( '9' === $count ) { echo ' selected="selected"'; } ?>>9</option>
						<option value="10"<?php if ( '10' === $count ) { echo ' selected="selected"'; } ?>>10</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link (optional):', 'wplt2' ); ?></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_attr( $this->get_field_id( 'highlight' ) ); ?>"><?php esc_html_e( 'Highlight new:', 'wplt2' ); ?></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'highlight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'highlight' ) ); ?>" type="checkbox" value="1" <?php if ( '1' === $highlight ) {
						echo ' checked="checked"';
} ?> /></td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_attr( $this->get_field_id( 'highlight_time' ) ); ?>"><?php esc_html_e( 'Highlight time [s]:', 'wplt2' ); ?></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'highlight_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'highlight_time' ) ); ?>" type="text" value="<?php echo esc_html( $highlight_time ); ?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo esc_attr( $this->get_field_id( 'ajax' ) ); ?>"><?php esc_html_e( 'Auto refresh:', 'wplt2' ); ?></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ajax' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ajax' ) ); ?>" type="checkbox" value="1"<?php if ( '1' === $ajax ) {
						echo ' checked="checked"';
} ?> disabled="disabled" />
					<small><?php esc_html_e( '(enables ajax)', 'wplt2' ); ?></small>
				</td>
			</tr>
		</table>


		<?php
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
