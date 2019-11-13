[![Build Status](https://travis-ci.org/stklcode/wp-liveticker.svg?branch=master)](https://travis-ci.org/stklcode/wp-liveticker)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=de.stklcode.web.wordpress.plugins%3Awp-liveticker&metric=alert_status)](https://sonarcloud.io/dashboard?id=de.stklcode.web.wordpress.plugins%3Awp-liveticker)
[![Packagist Version](https://img.shields.io/packagist/v/stklcode/stklcode-liveticker.svg)](https://packagist.org/packages/stklcode/stklcode-liveticker)
[![License](https://img.shields.io/badge/license-GPL%20v2-blue.svg)](https://github.com/stklcode/wp-liveticker/blob/master/LICENSE.md)

# Liveticker (by stklcode)

* Contributors:      Stefan Kalscheuer
* Tags:              liveticker, feed, rss
* Requires at least: 4.0
* Tested up to:      5.3
* Requires PHP:      5.2
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
* Shortcode to display liveticker
* Add ticker to sidebar widgets
* Ability to customise through CSS
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

Use the shortcode `[liveticker ticker="my-ticker"]`.
If you want to define a custom tick limit, you might also add a limit with `[liveticker ticker="my-ticker" limit="10"]`.

### Can I use my own styles?

Of course.
You can deactivate the default stylesheet on the settings page and include your own instead.

### Does the liveticker work with caching?

It strongly depends on the use case.
If you update your ticker every 5 minutes, a caching time of 12 hours obviously makes no sense.
However the AJAX update will fetch the latest ticks and update cached tickers  depending on the configured interval.


## Screenshots

1. Example liveticker (frontend)
2. Tick management
3. Ticker configuration.
4. Settings page
5. Example shortcode
6. Example widget

## Changelog

### 1.0.0 - 2018-11-02

* Initial release
