<?php

function iworks_fleet_options() {
	$iworks_fleet_options = array();
	/**
	 * main settings
	 */
	$parent = add_query_arg( 'post_type', 'iworks_fleet_person', 'edit.php' );

	$iworks_fleet_options['index'] = array(
		'version'  => '0.0',
		'use_tabs' => true,
		'page_title' => __( 'Configuration', 'fleet' ),
		'menu' => 'submenu',
		'parent' => $parent,
		'options'  => array(
			array(
				'type'              => 'heading',
				'label'             => __( 'General', 'fleet' ),
			),
			array(
				'type'              => 'heading',
				'label'             => __( 'Results', 'fleet' ),
			),
			array(
				'name'              => 'results_show_points',
				'type'              => 'checkbox',
				'th'                => __( 'Show points', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes' => array( 'switch-button' ),
			),
			array(
				'type'              => 'heading',
				'label'             => __( 'Persons', 'fleet' ),
			),
			array(
				'name'              => 'person_show_social_media',
				'type'              => 'checkbox',
				'th'                => __( 'Show social media links', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes' => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_boats_table',
				'type'              => 'checkbox',
				'th'                => __( 'Show boats table', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes' => array( 'switch-button' ),
			),
		),
		//      'metaboxes' => array(),
		'pages' => array(
			'new-boat' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Add New Boat', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'post_type' => 'iworks_fleet_boat',
						),
						'post-new.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'series' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Series', 'fleet' ),
				'menu_title'  => __( 'Series', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'taxonomy' => 'iworks_dinghy_serie',
							'post_type' => 'iworks_fleet_result',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'hull' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Hulls Manufaturers', 'fleet' ),
				'menu_title'  => __( 'Hulls Manufaturers', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'taxonomy' => 'iworks_fleet_boat_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'sail' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Sails Manufaturers', 'fleet' ),
				'menu_title'  => __( 'Sails Manufaturers', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'taxonomy' => 'iworks_fleet_sails_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'mast' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Masts Manufaturers', 'fleet' ),
				'menu_title'  => __( 'Masts Manufaturers', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'taxonomy' => 'iworks_fleet_mast_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'location' => array(
				'menu' => 'submenu',
				'parent' => $parent,
				'page_title'  => __( 'Locations', 'fleet' ),
				'menu_title'  => __( 'Locations', 'fleet' ),
				'menu_slug' => htmlentities(
					add_query_arg(
						array(
							'taxonomy' => 'iworks_dinghy_location',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
		),
	);
	return $iworks_fleet_options;
}

