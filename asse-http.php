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
 * Version:           0.0.2
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-akamai
 */

defined( 'ABSPATH' ) || exit;

// composer
require_once( __DIR__ . '/vendor/autoload.php');

// globals
if ( ! defined( 'ASSE_HTTP_VERSION' ) ) {
  define( 'ASSE_HTTP_VERSION', '0.0.2' );
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
