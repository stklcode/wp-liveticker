[![Build Status](https://travis-ci.org/stklcode/wp-liveticker.svg?branch=master)](https://travis-ci.org/stklcode/wp-liveticker)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=de.stklcode.web.wordpress.plugins%3Awp-liveticker&metric=alert_status)](https://sonarcloud.io/dashboard?id=de.stklcode.web.wordpress.plugins%3Awp-liveticker)
[![WP Plugin Version](https://img.shields.io/wordpress/plugin/v/stklcode-liveticker.svg)](https://wordpress.org/plugins/stklcode-liveticker/)
[![Packagist Version](https://img.shields.io/packagist/v/stklcode/stklcode-liveticker.svg)](https://packagist.org/packages/stklcode/stklcode-liveticker)
[![License](https://img.shields.io/badge/license-GPL%20v2-blue.svg)](https://github.com/stklcode/wp-liveticker/blob/master/LICENSE.md)

# Liveticker (by stklcode)

* Contributors:      Stefan Kalscheuer
* Tags:              liveticker, feed, rss
* Requires at least: 4.0
* Tested up to:      5.4
* Requires PHP:      5.6
* Stable tag:        1.0.0
* License:           GPLv2 or later
* License URI:       http://www.gnu.org/licenses/gpl-2.0.html

A simple ajaxified liveticker plugin for WordPress.

## Description

Liveticker is a simple liveticker plugin for WordPress.
Easily add multiple livetickers, add them to posts with shortcode or use them as Widget.

### Features

* Handle multiple Tickers
* Automatic update via AJAX
* RSS feed capability
* Gutenberg block and shortcode to display liveticker
* Add ticker to sidebar widgets
* Ability to customize through CSS
* Localization support


## Installation

1. Upload `stklcode-liveticker` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Liveticker menu to start.

### Requirements ###

* PHP 5.2 or above
* WordPress 4.0 or above

## Frequently asked questions

### How do I display a liveticker on my post/page?

On WordPress 5 sites there is a Gutenberg Block available to embed a liveticker in your post.

You can also use the shortcode  `[liveticker ticker="my-ticker"]` on WordPress 4 or classic-mode sites. 
If you want to define a custom tick limit, you might also add a limit with `[liveticker ticker="my-ticker" limit="10"]`.

### Can I use my own styles?

Of course.
You can deactivate the default stylesheet on the settings page and include your own instead.

### Does the liveticker work with caching?

If you activate AJAX updates (enabled by default), the JavaScript will automatically update the content, even when the 
page is loaded from cached.

If AJAX is disabled, it depends on your update and caching intervals. If you update your ticker every 5 minutes, a 
caching time of 12 hours obviously makes no sense.


## Screenshots

1. Example liveticker (frontend)
2. Tick management
3. Ticker configuration.
4. Settings page
5. Gutenberg block
6. Example shortcode
7. Example widget

## Changelog

### 1.1.0 - unreleased

* Requires PHP 5.6 or above
* Use GMT for automatic updates 
* Gutenberg Block available
* Ticks exposed through REST API

### 1.0.0 - 2018-11-02

* Initial release
