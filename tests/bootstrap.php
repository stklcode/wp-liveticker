<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package SCLiveticker
 */

use Yoast\WPTestUtils\WPIntegration;

require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

// Get access to tests_add_filter() function.
require_once $_tests_dir . 'includes/functions.php';

// Add plugin to active mu-plugins to make sure it gets loaded.
tests_add_filter(
	'muplugins_loaded',
	function() {
		require dirname( __DIR__ ) . '/stklcode-liveticker.php';
	}
);

/*
 * Bootstrap WordPress. This will also load the Composer autoload file, the PHPUnit Polyfills
 * and the custom autoloader for the TestCase and the mock object classes.
 */
WPIntegration\bootstrap_it();
