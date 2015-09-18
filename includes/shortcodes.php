<?php
/**
 * @package Shortcodes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Allow shortcodes in widgets
 */
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'wp_ajax_get_new_ticks', array( $this, 'wplt_ajax_get_new_ticks' ) );


/**
 * Output Liveticker
 *
 * @param array atts shortcode options
 *
 * @return string
 */
function wplt_shortcode_ticker_show( $atts ) {
	global $wplt_options;

	$wplt_options['shortcode_present'] = true;

	/*$wplt_ticker_options = array();

	extract(
		shortcode_atts( array(
			'id'	=> $wplt_ticker_options['id'],
			'count'	=> $wplt_ticker_options['count'],
			'order'	=> $wplt_ticker_options['order']
		), $atts )
	);*/

	if($atts[0])
	{
		if(!$atts[1]) $atts[1] = -1;
	
		$output = '<ul class="wplt_ticker">';
	
		$args = array(	'post_type' => 'wplt_tick',
				'posts_per_page' => $atts[1],
				'tax_query' => array(
						array(	'taxonomy' => 'wplt_ticker',
								'field' => 'slug',
								'terms' => $atts[0]
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
	
		$output .= '</ul>';
		$output .= '<a href="/feed/liveticker/lager-live"><img class="wplt_rss" src="/wp-content/plugins/wp-liveticker2/images/rss.jpg" alt="RSS" /></a>';
	}
	
	return $output;
}

add_shortcode( 'liveticker', 'wplt_shortcode_ticker_show' );