<?php
/**
 * Liveticker: Widget form.
 *
 * This file contains the view model for the Widget settings form.
 *
 * @package Liveticker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
			<label for="<?php echo esc_html( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Ticker:', 'stklcode-liveticker' ); ?></label>
		</td>
		<td>
			<select id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
				<?php
				foreach ( $categories as $c ) {
					echo '<option value="' . esc_attr( $c->slug ) . '"';
					if ( $category === $c->slug ) {
						echo ' selected="selected"';
					}
					echo '>' . esc_html( $c->name ) . '</option>';
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of Ticks', 'stklcode-liveticker' ); ?>:</label>
		</td>
		<td>
			<select id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>">
				<option value="-1"
					<?php
					if ( '-1' === $count ) {
						echo ' selected="selected"';
					}
					?>
				>
					<?php esc_html_e( 'all', 'stklcode-liveticker' ); ?>
				</option>
				<?php
				for ( $i = 1; $i <= 10; $i ++ ) {
					printf(
						'<option value="%d"%s>%d</option>',
						$i,
						( $i === $count ) ? ' selected' : '',
						intval( $i )
					);
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link (optional):', 'stklcode-liveticker' ); ?></label>
		</td>
		<td>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo esc_attr( $this->get_field_id( 'highlight' ) ); ?>"><?php esc_html_e( 'Highlight new:', 'stklcode-liveticker' ); ?></label>
		</td>
		<td>
			<?php
			echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'highlight' ) ) . '"
							   name="' . esc_attr( $this->get_field_name( 'highlight' ) ) . '"
							   type="checkbox" 
							   value="1" 
							   ' . ( ( '1' === $highlight ) ? ' checked' : '' ) . '/>';
			?>
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo esc_attr( $this->get_field_id( 'highlight_time' ) ); ?>"><?php esc_html_e( 'Highlight time [s]:', 'stklcode-liveticker' ); ?></label>
		</td>
		<td>
			<input type="number" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'highlight_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'highlight_time' ) ); ?>" type="text" value="<?php echo esc_html( $highlight_time ); ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="<?php echo esc_attr( $this->get_field_id( 'ajax' ) ); ?>"><?php esc_html_e( 'Auto refresh:', 'stklcode-liveticker' ); ?></label>
		</td>
		<td>
			<?php
			echo '<input class="widefat" 
                         id="' . esc_attr( $this->get_field_id( 'ajax' ) ) . '" 
                         name="' . esc_attr( $this->get_field_name( 'ajax' ) ) . '" 
                         type="checkbox" 
                         value="1"
                         ' . ( ( '1' === $ajax ) ? ' checked' : '' ) . '/>';

			?>
			<small><?php esc_html_e( '(enables ajax)', 'stklcode-liveticker' ); ?></small>
		</td>
	</tr>
</table>
