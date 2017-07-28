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
 * @package           Asse\Plugin
 * @author            Sebastian Döll <sebastian.doell@axelspringer.de>
 *
 * @wordpress-plugin
 * Plugin Name:       Asse HTTP
 * Plugin URI:        https://as-stash.axelspringer.de/projects/WPPL/repos/asse-http
 * Description:       Asse HTTP WordPress Plgin.
 * Version:           1.1.13
 * Author:            Axel Springer
 * Author URI:        https://www.axelspringer.de
 * Text Domain:       asse-http
 */

defined( 'ABSPATH' ) || exit;

use \Asse\Plugin\Http;

// composer
require_once( __DIR__ . '/vendor/autoload.php');

// activate
register_activation_hook( __FILE__, '\Asse\Plugin\Http::activate' );

// deactivate
register_deactivation_hook( __FILE__, '\Asse\Plugin\Http::deactivate' );

// run
$asse_http = new Http( 'asse_http', '1.1.13', __FILE__ );
