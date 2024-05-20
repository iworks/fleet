<?php
/*

Copyright 2023-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_fleet_integration_og' ) ) {
	return;
}

include_once dirname( dirname( __FILE__ ) ) . '/class-iworks-fleet-integration.php';

class iworks_fleet_integration_og extends iworks_fleet_integration {

	public function __construct() {

		add_filter( 'og_image_init', array( $this, 'action_og_image_init_maybe_add_boat_manufacturer' ) );

	}

	public function action_og_image_init_maybe_add_boat_manufacturer( $data ) {
		if ( is_singular( 'iworks_fleet_boat' ) ) {
			$terms = apply_filters(
				'fleet/boat/get_manufacturer',
				array(),
				get_the_ID()
			);
		}
		return $data;
	}

}


