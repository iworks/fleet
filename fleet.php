<?php
/**
 * PLUGIN_TITLE
 *
 * @package           PLUGIN_NAME
 * @author            AUTHOR_NAME
 * @copyright         2017-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Fleet Manager Base
 * Plugin URI:        PLUGIN_URI
 * Description:       PLUGIN_DESCRIPTION
 * Version:           PLUGIN_VERSION
 * Requires at least: PLUGIN_REQUIRES_WORDPRESS
 * Requires PHP:      PLUGIN_REQUIRES_PHP
 * Author:            AUTHOR_NAME
 * Author URI:        AUTHOR_URI
 * Text Domain:       fleet
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * static options
 */
define( 'IWORKS_FLEET_VERSION', 'PLUGIN_VERSION' );
define( 'IWORKS_FLEET_PREFIX', 'iworks_fleet_' );
$base     = dirname( __FILE__ );
$includes = $base . '/includes';

/**
 * require: Iworksfleet Class
 */
if ( ! class_exists( 'iworks_fleet' ) ) {
	require_once $includes . '/iworks/class-iworks-fleet.php';
}
/**
 * configuration
 */
require_once $base . '/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $includes . '/iworks/options/options.php';
}

/**
 * load options
 */

global $iworks_fleet_options;
$iworks_fleet_options = null;

function iworks_fleet_get_options_object() {
	global $iworks_fleet_options;
	if ( is_object( $iworks_fleet_options ) ) {
		return $iworks_fleet_options;
	}
	$iworks_fleet_options = new iworks_options();
	$iworks_fleet_options->set_option_function_name( 'iworks_fleet_options' );
	$iworks_fleet_options->set_option_prefix( IWORKS_FLEET_PREFIX );
	if ( method_exists( $iworks_fleet_options, 'set_plugin' ) ) {
		$iworks_fleet_options->set_plugin( basename( __FILE__ ) );
	}
	return $iworks_fleet_options;
}

function iworks_fleet_activate() {
	$iworks_fleet_options = iworks_fleet_get_options_object();
	$iworks_fleet_options->activate();
	/**
	 * install tables
	 */
	$iworks_fleet = new iworks_fleet;
	$iworks_fleet->db_install();
}

function iworks_fleet_deactivate() {
	$iworks_fleet_options = iworks_fleet_get_options_object();
	$iworks_fleet_options->deactivate();
}

global $iworks_fleet;
$iworks_fleet = new iworks_fleet();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_fleet_activate' );
register_deactivation_hook( __FILE__, 'iworks_fleet_deactivate' );
/**
 * Ask for vote
 */
include_once $includes . '//iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'Fleet Manager', 'fleet' ),
	'fleet'
);
