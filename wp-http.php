<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/axelspringer/wp-http
 * @since             0.0.1
 * @package           AxelSpringer\WP\Http
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       WP HTTP
 * Plugin URI:        https://github.com/axelspringer/wp-http
 * Description:       A companion plugin for WordPress to support HTTP.
 * Version:           1.1.15
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       wp-http
 */

defined( 'ABSPATH' ) || exit;

// make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// respect composer autoload
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	$loader = require_once __DIR__ . '/vendor/autoload.php';
	$loader->addPsr4( 'AxelSpringer\\WP\\HTTP\\', __DIR__ . '/src' );
}

use \AxelSpringer\WP\HTTP\__WP__ as WP;
use \AxelSpringer\WP\HTTP\__PLUGIN__ as Plugin;
use \AxelSpringer\WP\HTTP\Plugin as HTTP;

// bootstrap
if ( ! defined( WP::VERSION ) )
	define( WP::VERSION, Plugin::VERSION );

if ( ! defined( WP::URL ) )
	define( WP::URL, plugin_dir_url( __FILE__ ) );

if ( ! defined( WP::SLUG ) )
    define( WP::SLUG, Plugin::SLUG );

// activation
register_activation_hook( __FILE__, '\AxelSpringer\WP\HTTP\Plugin::activation' );

// deactivation
register_deactivation_hook( __FILE__, '\AxelSpringer\WP\HTTP\Plugin::deactivation' );

// run
global $wp_http; // this bootstraps the plugin, and provides a global accessible helper
$wp_http = new HTTP( WP_HTTP_SLUG, WP_HTTP_VERSION, __FILE__ );
