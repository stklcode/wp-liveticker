const gulp = require('gulp');
const clean = require('gulp-clean');
const copy = require('gulp-copy');
const zip = require('gulp-zip');
const composer = require('gulp-composer');
const phpunit = require('gulp-phpunit');
const exec = require('child_process').exec;
const phpcs = require('gulp-phpcs');
const cleanCSS = require('gulp-clean-css');
const argv = require('yargs').argv;

const config = require('./package.json');
const dev = argv.dev;
const finalName = config.name + '.' + config.version;

// Clean the target directory.
gulp.task('clean', function () {
	console.log('Cleaning up target directory  ...');
	return gulp.src('dist', {read: false})
		.pipe(clean());
});

// Prepare composer.
gulp.task('compose', function () {
	console.log('Preparing Composer ...');
	return composer('install');
});

// Execute unit tests.
gulp.task('test', ['compose'], function () {
	console.log('Running PHPUnit tests ...');
	return gulp.src('phpunit.xml')
		.pipe(phpunit('./vendor/bin/phpunit', {debug: false}));
});

// Execute PHP Code Sniffer.
gulp.task('test-cs', function (cb) {
	return exec('./vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs', function (err, stdout, stderr) {
		console.log(stdout);
		console.log(stderr);
		if (null === err) {
			console.log('Running PHP Code Sniffer tests ...');
			gulp.src(['statify-blacklist.php', 'inc/**/*.php'])
				.pipe(phpcs({bin: './vendor/bin/phpcs', standard: 'phpcs.xml'}))
				.pipe(phpcs.reporter('log'));
		}
		cb(err);
	});
});


// Bundle files as required for plugin distribution.
gulp.task('bundle', ['clean'], function () {
	console.log('Collecting files for package dist/' + config.name + config.version + ' ...');
	return gulp.src(['**/*.php', 'styles/*.css', '!test/**', '!vendor/**', 'README.md', 'LICENSE.md'], {base: './'})
		.pipe(copy('./dist/' + finalName + '/' + config.name));
});


// Minify CSS.
gulp.task('minify-css', function () {
	if (!dev) {
		console.log('Minifying CSS ...');
		return gulp.src('./dist/' + finalName + '/' + config.name + '/styles/*.css')
			.pipe(cleanCSS({compatibility: 'ie9'}))
			.pipe(gulp.dest('./dist/' + finalName + '/' + config.name + '/styles/'));
	} else {
		console.log('Development flag detected, not minifying CSS ...');
	}
});

// Create a ZIP package of the relevant files for plugin distribution.
gulp.task('package', ['bundle'], function () {
	console.log('Building package dist/' + config.name + config.version + '.zip ...');
	return gulp.src('./dist/' + config.name + '.' + config.version + '/**')
		.pipe(zip(finalName + '.zip'))
		.pipe(gulp.dest('./dist'));
});

gulp.task('default', ['clean', 'compose', 'test', 'test-cs', 'bundle', 'minify-css', 'package']);
