<?php
/**
 * WP Liveticker 2: Widget form.
 *
 * This file contains the view model for the Widget settings form.
 *
 * @package WPLiveticker2
 */

defined( 'ABSPATH' ) || exit;
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
				<option value="0"<?php if ( '0' === $count ) {
					echo ' selected="selected"';
				} ?>><?php esc_html_e( 'all', 'wplt2' ); ?></option>
				<option value="1"<?php if ( '1' === $count ) {
					echo ' selected="selected"';
				} ?>>1
				</option>
				<option value="2"<?php if ( '2' === $count ) {
					echo ' selected="selected"';
				} ?>>2
				</option>
				<option value="3"<?php if ( '3' === $count ) {
					echo ' selected="selected"';
				} ?>>3
				</option>
				<option value="4"<?php if ( '4' === $count ) {
					echo ' selected="selected"';
				} ?>>4
				</option>
				<option value="5"<?php if ( '5' === $count ) {
					echo ' selected="selected"';
				} ?>>5
				</option>
				<option value="6"<?php if ( '6' === $count ) {
					echo ' selected="selected"';
				} ?>>6
				</option>
				<option value="7"<?php if ( '7' === $count ) {
					echo ' selected="selected"';
				} ?>>7
				</option>
				<option value="8"<?php if ( '8' === $count ) {
					echo ' selected="selected"';
				} ?>>8
				</option>
				<option value="9"<?php if ( '9' === $count ) {
					echo ' selected="selected"';
				} ?>>9
				</option>
				<option value="10"<?php if ( '10' === $count ) {
					echo ' selected="selected"';
				} ?>>10
				</option>
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
			<input type="number" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'highlight_time' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'highlight_time' ) ); ?>" type="text" value="<?php echo esc_html( $highlight_time ); ?>" />
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
