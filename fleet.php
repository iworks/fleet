<?php
/*
Plugin Name: Fleet Manager
Text Domain: fleet
Plugin URI: http://iworks.pl/fleet/
Description: Manage the fleet of sailing boats. Add sailors, boats, regatta results and more.
Version: PLUGIN_VERSION
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2017 Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * static options
 */
define( 'IWORKS_FLEET_VERSION', 'PLUGIN_VERSION' );
define( 'IWORKS_FLEET_PREFIX',  'iworks_fleet_' );
$base = dirname( __FILE__ );
$vendor = $base.'/vendor';

/**
 * require: Iworksfleet Class
 */
if ( ! class_exists( 'iworks_fleet' ) ) {
	require_once $vendor.'/iworks/fleet.php';
}
/**
 * configuration
 */
require_once $base.'/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $vendor.'/iworks/options/options.php';
}

/**
 * i18n
 */
load_plugin_textdomain( 'fleet', false, plugin_basename( dirname( __FILE__ ) ).'/languages' );

/**
 * load options
 */
$iworks_fleet_options = new iworks_options();
$iworks_fleet_options->set_option_function_name( 'iworks_fleet_options' );
$iworks_fleet_options->set_option_prefix( IWORKS_FLEET_PREFIX );

function iworks_fleet_options_init() {
	global $iworks_fleet_options;
	$iworks_fleet_options->options_init();
}

function iworks_fleet_activate() {
	$iworks_fleet_options = new iworks_options();
	$iworks_fleet_options->set_option_function_name( 'iworks_fleet_options' );
	$iworks_fleet_options->set_option_prefix( IWORKS_FLEET_PREFIX );
	$iworks_fleet_options->activate();
	/**
	 * install tables
	 */
	$iworks_fleet = new iworks_fleet;
	$iworks_fleet->db_install();
}

function iworks_fleet_deactivate() {
	global $iworks_fleet_options;
	$iworks_fleet_options->deactivate();
}

$iworks_fleet = new iworks_fleet();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_fleet_activate' );
register_deactivation_hook( __FILE__, 'iworks_fleet_deactivate' );
