<?php
/**
 * Liveticker: Plugin API tests.
 *
 * This file contains unit tests for the plugin's REST API extensions.
 *
 * @package SCLiveticker
 */

namespace SCLiveticker;

use DateInterval;
use DateTime;
use WP_REST_Request;
use WP_REST_Server;
use WP_UnitTestCase;

/**
 * Class Test_API.
 */
class Test_API extends WP_UnitTestCase {
	/**
	 * Initialize WP REST API for tests.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		do_action( 'rest_api_init' );
	}

	/**
	 * Test presence of registered routes for ticks and tickers.
	 *
	 * @return void
	 */
	public function test_register_route() {
		global $wp_rest_server;

		$routes = $wp_rest_server->get_routes();

		self::assertArrayHasKey( '/wp/v2/scliveticker_tick', $routes, 'Ticks not exposed in API' );
		self::assertArrayHasKey( '/wp/v2/scliveticker_tick/(?P<id>[\d]+)', $routes, 'Specific ticks not exposed in API' );
		self::assertArrayHasKey( '/wp/v2/scliveticker_ticker', $routes, 'Tickers not exposed in API' );
		self::assertArrayHasKey( '/wp/v2/scliveticker_ticker/(?P<id>[\d]+)', $routes, 'Specific tickers not exposed in API' );
	}

	/**
	 * Test fetching ticks and tickers via the REST API.
	 *
	 * @return void
	 */
	public function test_get_ticks() {
		global $wp_rest_server;

		$request  = new WP_REST_Request( 'GET', '/wp/v2/scliveticker_tick' );
		$response = $wp_rest_server->dispatch( $request );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code' );
		self::assertEmpty( $response->get_data(), 'No data expected on empty database' );

		// Create two tickers with 10 ticks each.
		wp_set_current_user( 1 );
		$ticker_id = array(
			1 => self::factory()->term->create(
				array(
					'name'        => 'Ticker 1',
					'description' => 'Test Liveticker 1',
					'slug'        => 'ticker1',
					'taxonomy'    => 'scliveticker_ticker',
				)
			),
			2 >= self::factory()->term->create(
				array(
					'name'        => 'Ticker 2',
					'description' => 'Test Liveticker 2',
					'slug'        => 'ticker2',
					'taxonomy'    => 'scliveticker_ticker',
				)
			),
		);

		$dt = new DateTime( '2021-05-22 16:17:18' );
		foreach ( range( 1, 20 ) as $n ) {
			$t = 0 === $n % 2 ? '1' : '2';
			$i = ceil( $n / 2 );
			$p = self::factory()->post->create(
				array(
					'post_type'     => 'scliveticker_tick',
					'post_date_gmt' => $dt->format( 'Y-m-d H_i_s' ),
					'post_title'    => 'Tick ' . $t . '.' . $i,
					'post_status'   => 'publish',
					'post_content'  => 'Content of Tick ' . $t . '.' . $i,
				)
			);
			wp_set_object_terms( $p, $ticker_id[ $t ], 'scliveticker_ticker' );
			$dt->add( new DateInterval( 'PT1M' ) );
		}
		wp_set_current_user( 0 );

		// Verify ticker presence via API.
		$response = $wp_rest_server->dispatch( new WP_REST_Request( 'GET', '/wp/v2/scliveticker_ticker' ) );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code' );
		self::assertEquals( 2, count( $response->get_data() ), 'Unexpected number of tickers' );

		// Query all entries.
		$response = $wp_rest_server->dispatch( $request );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code' );
		self::assertEquals( 20, count( $response->get_data() ), 'Unexpected number of ticks without filter' );

		// Limit number of entries.
		$request->set_param( 'limit', 12 );
		$response = $wp_rest_server->dispatch( $request );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code with limit' );
		self::assertEquals( 12, count( $response->get_data() ), 'Unexpected number of ticks with limit' );

		// Filter by time.
		$request->set_param( 'limit', null );
		$request->set_param( 'last', $response->get_data()[5]['date_gmt'] );
		$response = $wp_rest_server->dispatch( $request );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code with time filter' );
		self::assertEquals( 5, count( $response->get_data() ), 'Unexpected number of ticks with time filter' );

		// Filter by ticker.
		$request->set_param( 'last', null );
		$request->set_param( 'ticker', 'ticker1' );
		$response = $wp_rest_server->dispatch( $request );
		self::assertEquals( 200, $response->get_status(), 'Unexpected status code with ticker filter' );
		self::assertEquals( 10, count( $response->get_data() ), 'Unexpected number of ticks with ticker filter' );
		self::assertEmpty(
			array_filter(
				$response->get_data(),
				function ( $t ) use ( $ticker_id ) {
					return 1 !== count( $t['scliveticker_ticker'] ) || ! in_array( $ticker_id[1], $t['scliveticker_ticker'], true );
				}
			),
			'No tick from ticker 2 should be present filtering for ticker1'
		);
	}
}
