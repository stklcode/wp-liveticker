<?php
/**
 * Liveticker Robo build script.
 *
 * This file contains the Robo tasks for building a distributable plugin package.
 * Should not be included in final package.
 *
 * @author    Stefan Kalscheuer <stefan@stklcode.de>
 *
 * @package   Liveticker
 * @version   1.0.0
 */

use Robo\Exception\TaskException;
use Robo\Tasks;
use Symfony\Component\Finder\Finder;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks {
	const PROJECT_NAME = 'stklcode-liveticker';
	const SVN_URL      = 'https://plugins.svn.wordpress.org/stklcode-liveticker';

	const OPT_TARGET    = 'target';
	const OPT_SKIPTEST  = 'skipTests';
	const OPT_SKIPSTYLE = 'skipStyle';
	const OPT_MINIFY    = 'minify';
	const OPT_NODE      = 'node';

	/**
	 * Version tag (read from composer.json).
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Target directory path.
	 *
	 * @var string
	 */
	private $target_dir;

	/**
	 * Final package name.
	 *
	 * @var string
	 */
	private $final_name;

	/**
	 * RoboFile constructor
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function __construct( $opts = array( self::OPT_TARGET => 'dist' ) ) {
		// Read composer configuration and extract version number..
		$composer = json_decode( file_get_contents( __DIR__ . '/composer.json' ) );
		// Extract parameter from options.
		$this->version    = $composer->version;
		$this->target_dir = $opts[ self::OPT_TARGET ];
		$this->final_name = self::PROJECT_NAME . '.' . $this->version;
	}

	/**
	 * Clean up target directory
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function clean( $opts = array( self::OPT_TARGET => 'dist' ) ) {
		$this->say( 'Cleaning target directory...' );
		if ( is_dir( $this->target_dir . '/' . $this->final_name ) ) {
			$this->_deleteDir( array( $this->target_dir . '/' . $this->final_name ) );
		}
		if ( is_file( $this->target_dir . '/' . $this->final_name . '.zip' ) ) {
			$this->_remove( $this->target_dir . '/' . $this->final_name . '.zip' );
		}
	}

	/**
	 * Run PHPUnit tests
	 *
	 * @return void
	 */
	public function test() {
		$this->say( 'Executing PHPUnit tests...' );
		$this->taskPhpUnit()->configFile( __DIR__ . '/phpunit.xml' )->run();
	}

	/**
	 * Run code style tests
	 *
	 * @return void
	 */
	public function testCS(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
			self::OPT_NODE      => false,
		)
	) {
		$this->say( 'Executing PHPCS...' );
		$this->_exec( __DIR__ . '/vendor/bin/phpcs --standard=phpcs.xml -s' );

		if ( $opts[self::OPT_NODE] ) {
			$this->say( 'Executing ESLint...' );
			$this->_exec( __DIR__ . '/node_modules/eslint/bin/eslint.js ' . __DIR__ . '/scripts/liveticker.js' );

			$this->say( 'Executing StyleLint...' );
			$this->_exec( __DIR__ . '/node_modules/stylelint/bin/stylelint.js ' . __DIR__ . '/styles/liveticker.css' );
		}
	}

	/**
	 * Build a distributable bundle.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function build(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
			self::OPT_NODE      => false,
		)
	) {
		$this->clean( $opts );
		if ( isset( $opts[ self::OPT_SKIPTEST ] ) && true === $opts[ self::OPT_SKIPTEST ] ) {
			$this->say( 'Tests skipped' );
		} else {
			$this->test();
		}
		if ( isset( $opts[ self::OPT_SKIPSTYLE ] ) && true === $opts[ self::OPT_SKIPSTYLE ] ) {
			$this->say( 'Style checks skipped' );
		} else {
			$this->testCS($opts);
		}
		$this->bundle();
	}

	/**
	 * Bundle global resources.
	 *
	 * @return void
	 */
	private function bundle() {
		$this->say( 'Bundling resources...' );
		$this->taskCopyDir( array(
			'includes' => $this->target_dir . '/' . $this->final_name . '/includes',
			'scripts'  => $this->target_dir . '/' . $this->final_name . '/scripts',
			'styles'   => $this->target_dir . '/' . $this->final_name . '/styles',
			'views'    => $this->target_dir . '/' . $this->final_name . '/views',
		) )->run();
		$this->_copy( 'stklcode-liveticker.php', $this->target_dir . '/' . $this->final_name . '/stklcode-liveticker.php' );
		$this->_copy( 'README.md', $this->target_dir . '/' . $this->final_name . '/README.md' );
		$this->_copy( 'LICENSE.md', $this->target_dir . '/' . $this->final_name . '/LICENSE.md' );

		// Remove content before title (e.g. badges) from README file.
		$this->taskReplaceInFile( $this->target_dir . '/' . $this->final_name . '/README.md' )
		     ->regex( '/^[^\\#]*/' )
		     ->to( '' )
		     ->run();
	}

	/**
	 * Minify JavaScript and CSS assets in target director.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function minify(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		if ( $opts[ self::OPT_MINIFY ] ) {
			$this->minifyJS( $opts );
			$this->minifyCSS( $opts );
		} else {
			$this->say( 'Minification skipped.' );
		}
	}

	/**
	 * Minify CSS assets.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function minifyCSS(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		if ( ! isset( $opts[ self::OPT_MINIFY ] ) ) {
			$this->say( 'CSS minification skipped.' );

			return;
		}

		$this->say( 'Minifying CSS...' );

		$finder = Finder::create()->name( '*.css*' )
								->notName( '*.min.css' )
								->in( $this->target_dir . '/' . $this->final_name . '/styles' );
		foreach ( $finder as $file ) {
			$this->taskMinify( $file )->run();
			// Replace original file for in-place minification.
			$abspath = $file->getPath() . '/' . $file->getFilename();
			$this->_rename( str_replace( '.css', '.min.css', $abspath ), $abspath, true );
		}
	}

	/**
	 * Minify JavaScript assets.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function minifyJS(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		if ( ! isset( $opts[ self::OPT_MINIFY ] ) ) {
			$this->say( 'JS minification skipped.' );

			return;
		}

		$this->say( 'Minifying JavaScript...' );

		// Minify global JavaScripts files except already minified.
		$finder = Finder::create()->name( '*.js*' )
						->notName( '*.min.js' )
						->in( $this->target_dir . '/' . $this->final_name . '/scripts' );
		foreach ( $finder as $file ) {
			$this->taskMinify( $file )->run();
			// Replace original file for in-place minification.
			$abspath = $file->getPath() . '/' . $file->getFilename();
			$this->_rename( str_replace( '.js', '.min.js', $abspath ), $abspath, true );
		}
	}

	/**
	 * Create ZIP package from distribution bundle.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 */
	public function package(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		$this->build( $opts );
		$this->say( 'Packaging...' );
		$this->taskPack( $this->target_dir . '/' . $this->final_name . '.zip' )
			->addDir( '', $this->target_dir . '/' . $this->final_name )
			->run();
	}

	/**
	 * Deploy development version (trunk).
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	public function deployTrunk(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		// First execute build job.
		$this->build( $opts );

		// Prepare VCS, either checkout or update local copy.
		$this->prepareVCS();

		$this->say( 'Preparing deployment directory...' );
		$this->updateVCStrunk();

		// Update remote repository.
		$this->say( 'Deploying...' );
		$this->commitVCS(
			'--force trunk/*',
			'Updated ' . self::PROJECT_NAME . ' trunk'
		);
	}

	/**
	 * Deploy current version tag.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	public function deployTag(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		// First execute build job.
		$this->build( $opts );

		// Prepare VCS, either checkout or update local copy.
		$this->prepareVCS();

		$this->say( 'Preparing deployment directory...' );
		$this->updateVCStag();

		// Update remote repository.
		$this->say( 'Deploying...' );
		$this->commitVCS(
			'tags/' . $this->version,
			'Updated ' . self::PROJECT_NAME . ' v' . $this->version
		);
	}

	/**
	 * Deploy current version tag.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	public function deployReadme(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		// First execute build job.
		$this->build( $opts );

		// Prepare VCS, either checkout or update local copy.
		$this->prepareVCS();

		$this->updateVCSreadme();

		// Update remote repository.
		$this->say( 'Deploying...' );
		$this->commitVCS(
			'--force trunk/README.md',
			'Updated ' . self::PROJECT_NAME . ' ReadMe'
		);
	}

	/**
	 * Deploy current version tag and trunk.
	 *
	 * @param array $opts Options.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	public function deployAll(
		$opts = array(
			self::OPT_TARGET    => 'dist',
			self::OPT_SKIPTEST  => false,
			self::OPT_SKIPSTYLE => false,
			self::OPT_MINIFY    => true,
		)
	) {
		// First execute build job.
		$this->build( $opts );

		// Prepare VCS, either checkout or update local copy.
		$this->prepareVCS();

		$this->say( 'Preparing deployment directory...' );
		$this->updateVCStrunk();
		$this->updateVCStag();

		// Update remote repository.
		$this->say( 'Deploying...' );
		$this->commitVCS(
			array(
				'--force trunk/*',
				'--force tags/' . $this->version,
			),
			'Updated ' . self::PROJECT_NAME . ' v' . $this->version
		);
	}

	/**
	 * Prepare VCS direcory.
	 *
	 * Checkout or update local copy of SVN repository.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	private function prepareVCS() {
		if ( is_dir( $this->target_dir . '/svn' ) ) {
			$this->taskSvnStack()
				->stopOnFail()
				->dir( $this->target_dir . '/svn/stklcode-liveticker' )
				->update()
				->run();
		} else {
			$this->_mkdir( $this->target_dir . '/svn' );
			$this->taskSvnStack()
				->dir( $this->target_dir . '/svn' )
				->checkout( self::SVN_URL )
				->run();
		}
	}

	/**
	 * Commit VCS changes
	 *
	 * @param string|array $to_add Files to add.
	 * @param string       $msg    Commit message.
	 *
	 * @return void
	 * @throws TaskException On errors.
	 */
	private function commitVCS( $to_add, $msg ) {
		$task = $this->taskSvnStack()
					->stopOnFail()
					->dir( $this->target_dir . '/svn/stklode-liveticker' );

		if ( is_array( $to_add ) ) {
			foreach ( $to_add as $ta ) {
				$task = $task->add( $ta );
			}
		} else {
			$task = $task->add( $to_add );
		}

		$task->commit( $msg )->run();
	}

	/**
	 * Update SVN readme file.
	 *
	 * @return void
	 */
	private function updateVCSreadme() {
		$trunk_dir = $this->target_dir . '/svn/stklcode-liveticker/trunk';
		$this->_copy( $this->target_dir . '/' . $this->final_name . 'README.md', $trunk_dir . 'README.md' );
	}

	/**
	 * Update SVN development version (trunk).
	 *
	 * @return void
	 */
	private function updateVCStrunk() {
		// Clean trunk directory.
		$trunk_dir = $this->target_dir . '/svn/stklcode-liveticker/trunk';
		$this->taskCleanDir( $trunk_dir )->run();

		// Copy built bundle to trunk.
		$this->taskCopyDir( array( $this->target_dir . '/' . $this->final_name => $trunk_dir ) )->run();
	}

	/**
	 * Update current SVN version tag.
	 *
	 * @return void
	 */
	private function updateVCStag() {
		// Clean tag directory if it exists.
		$tag_dir = $this->target_dir . '/svn/stklcode-liveticker/tags/' . $this->version;
		if ( is_dir( $tag_dir ) ) {
			$this->taskCleanDir( $this->target_dir . '/svn/stklcode-liveticker/tags/' . $this->version )->run();
		} else {
			$this->_mkdir( $tag_dir );
		}

		// Copy built bundle to trunk.
		$this->taskCopyDir( array( $this->target_dir . '/' . $this->final_name => $tag_dir ) )->run();
	}
}
