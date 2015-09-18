<?php
/**
 * @package Post Types
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register tick post type
 *
 * @return void
 */
function wplt_tick_post_type() {
  $args = array(
    'labels' 			=> array( 
     	'name' 					=> __( 'Ticks', 'wplt2' ),
	    'singular_name' 		=> __( 'Tick', 'wplt2' ),
	    'add_new' 				=> __( 'Add New', 'wplt2' ),
	    'add_new_item' 			=> __( 'Add New Tick', 'wplt2' ),
	    'edit_item' 			=> __( 'Edit Tick', 'wplt2' ),
	    'new_item' 				=> __( 'New Tick', 'wplt2' ),
	    'all_items' 			=> __( 'All Ticks', 'wplt2' ),
	    'view_item' 			=> __( 'View Tick', 'wplt2' ),
	    'search_items' 			=> __( 'Search Ticks', 'wplt2' ),
	    'not_found' 			=> __( 'No Ticks found', 'wplt2' ),
	    'not_found_in_trash' 	=> __( 'No Ticks found in Trash', 'wplt2' ), 
	    'parent_item_colon' 	=> '',
	    'menu_name' 			=> __( 'Liveticker', 'wplt2' )
	    						),
    'public' 			=> false,
    'show_ui' 			=> true, 
    'show_in_menu' 		=> true, 
  	'menu_icon'			=> 'dashicons-rss',  
    'capability_type' 	=> 'post', 
    'supports' 			=> array( 'title', 'editor', 'author'),
  	'taxonomies' 		=> array('wplt_ticker')
  ); 

  register_post_type( 'wplt_tick', $args );
}
add_action( 'init', 'wplt_tick_post_type' );


/**
 * Register custom taxamony (category)
 * 
 * @return void
 */
//hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'wplt_ticker_taxonomy', 0 );

//create two taxonomies, genres and writers for the post type "book"
function wplt_ticker_taxonomy()
{
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
			'name' => _x( 'Ticker', 'taxonomy general name' ),
			'singular_name' => _x( 'Ticker', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Tickers', 'wplt2' ),
			'all_items' => __( 'All Tickers', 'wplt2' ),
			'parent_item' => __( 'Parent Ticker', 'wplt2' ),
			'parent_item_colon' => __( 'Parent Ticker:', 'wplt2' ),
			'edit_item' => __( 'Edit Ticker', 'wplt2' ),
			'update_item' => __( 'Update Ticker', 'wplt2' ),
			'add_new_item' => __( 'Add New Ticker', 'wplt2' ),
			'new_item_name' => __( 'New Ticker', 'wplt2' ),
			'menu_name' => __( 'Ticker', 'wplt2' ),
	);

	register_taxonomy('wplt_ticker',array('wplt_tick'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
	));
}