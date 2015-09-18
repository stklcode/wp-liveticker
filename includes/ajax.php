<?php
/**
 * @package Ajax
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Process Ajax upload file
 *
 * @return void
 */
function wplt_ajax_get_new_ticks() {
	check_ajax_referer( 'wplt_ajax_get_new_ticks' );

	// timestamp for request
	$slug = $_REQUEST['sl'];
  $time = $_REQUEST['ts'];
  
  if($slug) {
    // get new ticks from database
    $args = array(	'post_type' => 'wplt_tick',
        'posts_per_page' => '-1',
        'tax_query' => array(
            array(	'taxonomy' => 'wplt_ticker',
                'field' => 'slug',
                'terms' => $slug
            )
        )
    );
    
    $wp_query = new WP_Query($args);
    
    while ($wp_query->have_posts()) : $wp_query->the_post();
    $output .= '<li class="wplt_tick">
  						  <p><span class="wplt_tick_time">'.get_the_time('d.m.Y H.i').'</span>
  						  <span class="wplt_tick_title">'.get_the_title().'</span></p>
  						  <p class="wplt_tick_content">'.get_the_content().'</p></li>';
    endwhile;
  	
    // Echo success response
    echo $output;
  }
  die();
}
//add_action( 'wp_ajax_wplt_download_upload', 'wplt_download_upload_ajax' );