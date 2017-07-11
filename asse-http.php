<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://as-stash.axelspringer.de/projects/WPPL/repos/asse-http
 * @since             0.0.1
 * @package           AsseHttp
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       Asse HTTP
 * Plugin URI:        https://as-stash.axelspringer.de/projects/WPPL/repos/asse-http
 * Description:       Asse HTTP WordPress Plgin.
 * Version:           1.0.5
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-akamai
 */

defined( 'ABSPATH' ) || exit;

// composer
require_once( __DIR__ . '/vendor/autoload.php');

// globals
if ( ! defined( 'ASSE_HTTP_VERSION' ) ) {
  define( 'ASSE_HTTP_VERSION', '1.0.5' );
}

if ( ! defined( 'ASSE_HTTP_MIN_WORDPRESS' ) ) {
  define( 'ASSE_HTTP_MIN_WORDPRESS', '4.7-alpha' );
}

if ( ! defined( 'ASSE_HTTP_MIN_PHP' ) ) {
  define( 'ASSE_HTTP_MIN_PHP', '5.5' );
}

if ( ! defined( 'ASSE_HTTP_PLUGIN_URL' ) ) {
  define( 'ASSE_HTTP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'ASSE_HTTP_PLUGIN_DIR' ) ) {
  define( 'ASSE_HTTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'ASSE_HTTP_PLUGIN_NAME' ) ) {
  define( 'ASSE_HTTP_PLUGIN_NAME', 'asse_http' );
}

if ( ! defined( 'ASSE_HTTP_CACHE_CONTROL_HEADERS' ) ) {
  $allowed_cache_control_headers = array(
    'max-age',
    's-maxage',
    'min-fresh',
    'must-revalidate',
    'no-cache',
    'no-store',
    'no-transform',
    'public',
    'private',
    'proxy-revalidate',
    'stale-while-revalidate',
    'stale-if-error'
  );
  define( 'ASSE_HTTP_CACHE_CONTROL_HEADERS', $allowed_cache_control_headers );
}

if ( ! defined( 'ASSE_HTTP_CACHE_CONTROL_DEFAULTS' ) ) {
  $cache_control_defaults = [
    'front_page'  => [
      'max-age'                 => 300,           //                5 min
      's-maxage'                => 150,            //                2 min 30 sec
      'public'                  => true,
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'single'      => [
      'max-age'                 => 600,           //               10 min
      's-maxage'                => 60,            //                1 min
      'mmulti'                  => 1,              // enabled,
      'public'                  => true,
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'page'        => [
      'max-age'                 => 1200,          //               20 min
      's-maxage'                => 300,            //                5 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'home'         => [
      'max-age'                 => 180,           //                3 min
      's-maxage'                => 45,            //                      45 sec
      'paged'                   => 5,              //                       5 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'category'   => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,           //                5 min
      'paged'                   => 8,              //                       8 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'tag'         => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,           //                5 min            //                       8 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'author'      => [
      'max-age'                 => 1800,          //               30 min
      's-maxage'                => 600,           //               10 min
      'paged'                   => 10,             //                      10 sec
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'date'        =>  [
      'max-age'                 => 10800,         //      3 hours
      's-maxage'                => 2700,          //               45 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'feed'        => [
      'max-age'                 => 5400,          //       1 hours 30 min
      's-maxage'                => 600,            //               10 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'attachment'   => [
      'max-age'                 => 10800,         //       3 hours
      's-maxage'                => 2700,          //               45 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    'search'       => [
      'max-age'                 => 1800,          //               30 min
      's-maxage'                => 600,            //               10 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ],
    '404'     => [
      'max-age'                 => 900,           //               15 min
      's-maxage'                => 300,            //                5 min
      'stale-while-revalidate'  => 3600 * 24,
      'stale-if-error'          => 3600 * 24 * 3
    ]
  ];
  define( 'ASSE_HTTP_CACHE_CONTROL_DEFAULTS', $cache_control_defaults );
}

if ( ! defined( 'ASSE_HTTP_ACCEPT_ENCODING' ) ) {
  $accepted_encoding = []; // weighted

  if ( function_exists( 'brotli_compress' ) ) {
    $accepted_encoding[] = 'br';
  }

  if ( function_exists( 'ob_gzhandler' ) ) {
    $accepted_encoding[] = 'gzip';

    ini_set( 'zlib.output_compression_level', 6 );
  }

  if ( function_exists( 'gzcompress' ) ) {
    $accepted_encoding[] = 'deflate';
  }

  define( 'ASSE_HTTP_ACCEPT_ENCODING', $accepted_encoding );
}

if ( ! defined( 'ASSE_HTTP_ZLIB_LEVEL' ) ) {
  define( 'ASSE_HTTP_ZLIB_LEVEL', 6 );
}

if ( ! defined( 'ASSE_HTTP_BROTLI_LEVEL' ) ) {
  define( 'ASSE_HTTP_BROTLI_LEVEL', 4 );
}

// timber
$timber               = new \Timber\Timber();
$timber_context       = array();
\Timber::$locations[] = ASSE_HTTP_PLUGIN_DIR . 'templates/';

if ( version_compare( $GLOBALS['wp_version'], ASSE_HTTP_MIN_WORDPRESS, '<' ) ) {
  add_action( 'admin_notices', function () {
    $timber_context = array(
      'wp_version'      => $GLOBALS['wp_version'],
      'wp_version_min'  => ASSE_HTTP_MIN_WORDPRESS
    );
    Timber::render( 'notice-wp-version.twig', $timber_context );
  } );

	return false;
}

// includes
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http-abstract.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http-detect.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http-settings-section.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-asse-http-settings-field.php';

// activate
register_activation_hook( __FILE__, 'AsseHttp::activate' );

// deactivate
register_deactivation_hook( __FILE__, 'AsseHttp::deactivate' );

// run
$asse_akamai = new AsseHttp();
