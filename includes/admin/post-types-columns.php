<?php
/**
 * @package Post Types Columns
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Download post type column headings
 *
 * @param array $columns default columns registered by WordPress
 *
 * @return void
 */
function wplt_tick_column_headings( $columns ) {
	return array(
        'cb' 			=> '<input type="checkbox" />',
        'title' 		=> __( 'Title', 'wplt2' ),
        'author'		=> __( 'Author', 'wplt2' ),
		'wplt_ticker'	=> __( 'Ticker', 'wplt2'),
        'date' 			=> __( 'Date', 'wplt2' )
    );
}
//add_filter( 'manage_wplt_tick_posts_columns', 'wplt_tick_column_headings' );

/**
 * Download post type column contents
 *
 * @param array $column_name current column
 * @param int $post_id current post id provided by WordPress
 *
 * @return void
 */
function wplt_tick_column_contents( $column_name, $post_id ) {
	// Title column
	if( $column_name == 'file' ) {
		$path = get_post_meta( $post_id, '_wplt_file_url', true );
		echo wplt_download_filename( $path );
	}
}
add_action( 'manage_wplt_tick_posts_custom_column', 'wplt_tick_column_contents', 10, 2 );

/**
 * Download post type sortable columns filter
 *
 * @param array $columns as set above
 *
 * @return void
 */
function wplt_tick_column_sortable( $columns ) {
	$columns['ticks'] = 'ticks';

	return $columns;
}
add_filter( 'manage_edit-wplt_tick_sortable_columns', 'wplt_tick_column_sortable' );

/**
 * Download post type sortable columns action
 *
 * @param array $query
 *
 * @return void
 */
function wplt_tick_column_orderby( $query ) {
	$orderby = $query->get( 'orderby');  
  
    if( $orderby == 'ticks' ) {  
        $query->set('meta_key','_wplt_file_count');  
        $query->set('orderby','meta_value_num');  
    } 
}
add_action( 'pre_get_posts', 'wplt_tick_column_orderby' );