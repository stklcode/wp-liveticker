<?php
class wplt_widget extends WP_Widget {
  function wplt_widget() {
    parent::WP_Widget( false, $name = 'Liveticker' );
  }

  function widget( $args, $instance ) {
    extract( $args );
    $title = apply_filters( 'wplt_catlit', $instance['title'] );
    $category = apply_filters( 'wplt_catlit', $instance['category'] );
    $count = apply_filters( 'wplt_catlit', $instance['count'] );
    $link = apply_filters( 'wplt_catlit', $instance['link'] );
    $highlight = apply_filters( 'wplt_catlit', $instance['highlight'] );
    $highlight_time = apply_filters( 'wplt_catlit', $instance['highlight_time'] );
    $ajax = apply_filters( 'wplt_catlit', $instance['ajax'] );
    ?>

    <?php
	echo $before_widget;
    ?>

    <?php
      if ($title) {
	    echo $before_title . $title . $after_title;
      }
      
	?>   
	<ul class="wplt_widget">
	<?php
   	$args = array(	'post_type' => 'wplt_tick',
    				'tax_query' => array(
    					array(	'taxonomy' => 'wplt_ticker',
    							'field' => 'slug',
    							'terms' => $category,
    					)
    				)
      			);

      	$wp_query = new WP_Query($args);
      	while ($wp_query->have_posts()) : $wp_query->the_post();
     ?>
	     <li><span class="wplt_widget_time"><?php echo get_the_time('d.m.Y - H.i'); ?></span><span class="wplt_widget_content<?php if($highlight=="1" && get_the_time('U') > (time()-$highlight_time)) echo '_new'; ?>"><br /><?php echo the_title(); ?></span></li>
     <?php
     	if( $count > 0 && ++$cnt == $count ) break;
      	endwhile;
      ?>
      </ul>

     <?php
       if ($link)
         print '<p class="wplt_widget_link"><a href="'.$link.'">'.__( 'show all', 'wplt2' ).'...</a></p>';
         
       echo $after_widget;
     ?>
     <?php
  }

  function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

  function form( $instance ) {
    $title = esc_attr( $instance['title'] );
    $category = esc_attr( $instance['category'] );
    $count = esc_attr( $instance['count'] );
    $link = esc_attr( $instance['link'] );
    $highlight = esc_attr( $instance['highlight'] );
    $highlight_time = esc_attr( $instance['highlight_time'] );
    $ajax = esc_attr( $instance['ajax'] );
    $categories = get_terms('wplt_ticker', 'orderby=name&order=ASC');
    ?>
    
    <table>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label></td>
      <td><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></td>
     </tr>
     <tr>
      <td> <label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Ticker:', 'wplt2' ); ?></label></td>
      <td>
       <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
        <?php foreach ($categories as $cat) {
         echo '<option value="'.$cat->slug.'"'; if($category==$cat->slug) echo ' selected="selected"'; echo '>'.$cat->name.'</option>';
        } ?>
       </select>
      </td>
     </tr>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of Ticks:', 'wplt2' ); ?></label></td>
      <td>
       <select id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>">
        <option value="0"<?php if($count==0) echo ' selected="selected"' ?>><?php _e('all','wplt2');?></option>
        <option value="1"<?php if($count==1) echo ' selected="selected"' ?>>1</option><option value="2"<?php if($count==2) echo ' selected="selected"' ?>>2</option>
        <option value="3"<?php if($count==3) echo ' selected="selected"' ?>>3</option><option value="4"<?php if($count==4) echo ' selected="selected"' ?>>4</option>
        <option value="5"<?php if($count==5) echo ' selected="selected"' ?>>5</option><option value="6"<?php if($count==6) echo ' selected="selected"' ?>>6</option>
        <option value="7"<?php if($count==7) echo ' selected="selected"' ?>>7</option><option value="8"<?php if($count==8) echo ' selected="selected"' ?>>8</option>
        <option value="9"<?php if($count==9) echo ' selected="selected"' ?>>9</option><option value="10"<?php if($count==10) echo ' selected="selected"' ?>>10</option>
       </select>
      </td>
     </tr>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Link (optional):', 'wplt2' ); ?></label></td>
      <td><input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo $link; ?>" /></td>
     </tr>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'highlight' ); ?>"><?php _e( 'Highlight new:', 'wplt2' ); ?></label></td>
      <td><input class="widefat" id="<?php echo $this->get_field_id( 'highlight' ); ?>" name="<?php echo $this->get_field_name( 'highlight' ); ?>" type="checkbox" value="1" <?php if($highlight=="1") echo ' checked="checked"'; ?> /></td>
     </tr>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'highlight_time' ); ?>"><?php _e( 'Highlight time [s]:', 'wplt2' ); ?></label></td>
      <td><input class="widefat" id="<?php echo $this->get_field_id( 'highlight_time' ); ?>" name="<?php echo $this->get_field_name( 'highlight_time' ); ?>" type="text" value="<?php echo $highlight_time; ?>" /></td>
     </tr>
     <tr>
      <td><label for="<?php echo $this->get_field_id( 'ajax' ); ?>"><?php _e( 'Auto refresh:', 'wplt2' ); ?></label></td>
      <td><input class="widefat" id="<?php echo $this->get_field_id( 'ajax' ); ?>" name="<?php echo $this->get_field_name( 'ajax' ); ?>" type="checkbox" value="1"<?php if($ajax=="1") echo ' checked="checked"'; ?> disabled="disabled" /> <small><?php _e( '(enables ajax)', 'wplt2' ); ?></small></td>
     </tr>
    </table>
    
    
     
    <?php
  }
}

add_action( 'widgets_init', 'wplt_widget_init' );
function wplt_widget_init() {
  register_widget( 'wplt_widget' );
}
