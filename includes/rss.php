<?php
/**
 * @package Scripts
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Hook RSS function
 * @return void
 */
if (strpos($_SERVER['REQUEST_URI'], '/feed/liveticker/') !== false) {
	$args = array();
	$args['ticker_slug'] = substr($_SERVER['SCRIPT_NAME'], 1);
	wplt_print_feed($args);
	exit;
}


function wplt_print_feed( $arguments ) {
	
	$args = array(	'post_type' => 'wplt_tick',
					'tax_query' => array(
						array(	'taxonomy' => 'wplt_ticker',
							'field' => 'slug',
							'terms' => $arguments['ticker_slug']
						)
			)
	);


	global $wpdb;
	
	$sql = "SELECT `ID`, DATE_FORMAT(`post_date`,'%a, %d %b %Y %T') AS `post_date_rfc`, `post_content`, `post_title` FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'wplt_tick' AND `post_status` = 'publish' ORDER BY `post_date` DESC;";
	$entries = $wpdb->get_results($sql);

	date_default_timezone_set("Europe/Berlin");
	
	// modify header information
	header("Content-Type: application/rss+xml; charset=UTF-8");
	// generate file head
	$rss = '<?xml version="1.0" encoding="UTF-8"?>';
	$rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
	$rss .= '<channel><title>Lager Live</title>';
	$rss .= '<link>http://'.$_SERVER['SERVER_NAME'].'/lagerticker</link>';
	$rss .= '<atom:link href="http://'.$_SERVER['SERVER_NAME'].''.$_SERVER['REQUEST_URI'].'" rel="self" type="application/rss+xml" />';
	$rss .= '<description></description>';
	$rss .= '<language>de-de</language>';
	$rss .= '<pubDate>'.date("r").'</pubDate>';

	// build entries
	foreach ( $entries as $entry ) {
		//print_r($entry);
		$rss .= '<item><title>'.$entry->post_title.'</title>';
		$rss .= '<link>http://www.dpsg-hardenberg.org/lagerticker</link>';
		$rss .= '<pubDate>'.$entry->post_date_rfc.' '.date('O').'</pubDate>';
		$rss .= '<description><![CDATA['.$entry->post_content.']]></description></item>';
	}
		
	// generate document foot
	$rss .= '</channel></rss>';
	
	print $rss;
}