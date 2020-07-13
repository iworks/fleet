<?php
function iworks_fleet_options() {
	$iworks_fleet_options = array();
	/**
	 * main settings
	 */
	$parent                        = add_query_arg( 'post_type', 'iworks_fleet_person', 'edit.php' );
	$iworks_fleet_options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Configuration', 'fleet' ),
		'menu'       => 'submenu',
		'parent'     => $parent,
		'options'    => array(
			array(
				'type'  => 'heading',
				'label' => __( 'General', 'fleet' ),
			),
			array(
				'name'        => 'country',
				'type'        => 'select2',
				'th'          => __( 'Country', 'sierotki' ),
				'options'     => iworks_fleet_get_contries_select2(),
				'multiple'    => true,
				'description' => __( 'Select country or countries if you need to show only boats and sailors from it.', 'fleet' ),
			),
			array(
				'name'              => 'wide_class',
				'type'              => 'checkbox',
				'th'                => __( 'Add wide body class', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'load_frontend_css',
				'type'              => 'checkbox',
				'th'                => __( 'Load frontend CSS', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			/**
			 * Results
			 */
			array(
				'type'  => 'heading',
				'label' => __( 'Results', 'fleet' ),
			),
			array(
				'name'              => 'results_show_points',
				'type'              => 'checkbox',
				'th'                => __( 'Show points', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			/**
			 * Persons
			 */
			array(
				'type'  => 'heading',
				'label' => __( 'Persons', 'fleet' ),
			),
			array(
				'name'              => 'person_show_social_media',
				'type'              => 'checkbox',
				'th'                => __( 'Show social media links', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_boats_table',
				'type'              => 'checkbox',
				'th'                => __( 'Show boats table', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_boats_owned_table',
				'type'              => 'checkbox',
				'th'                => __( 'Show boat owned on person details page', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_tag_to_person',
				'type'              => 'checkbox',
				'th'                => __( 'Tag to person', 'fleet' ),
				'description'       => __( 'Replace person tag by fleet person.', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_articles_with_person_tag',
				'type'              => 'checkbox',
				'th'                => __( 'Add posts list', 'fleet' ),
				'description'       => __( 'Add posts list to person with matching tag.', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_flag_on_single',
				'type'              => 'checkbox',
				'th'                => __( 'Show flag', 'fleet' ),
				'description'       => __( 'Show country flag before person on single person page.', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			/**
			 * Boats
			 */
			array(
				'type'  => 'heading',
				'label' => __( 'Boats', 'fleet' ),
			),
			array(
				'name'              => 'boat_add_crew_manually',
				'type'              => 'checkbox',
				'th'                => __( 'Add crew manually', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'boat_add_extra_data',
				'type'              => 'checkbox',
				'th'                => __( 'Add extra data', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'boat_add_social_media',
				'type'              => 'checkbox',
				'th'                => __( 'Add boat social media', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'boat_add_owners',
				'type'              => 'checkbox',
				'th'                => __( 'Add boat owners', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'boat_show_owners',
				'type'              => 'checkbox',
				'th'                => __( 'Show boat owners on boat details page', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'    => 'boat_taxonomies',
				'type'    => 'checkbox_group',
				'th'      => __( 'Boat taxonomies', 'fleet' ),
				'options' => array(
					'sail' => __( 'Sail manufacturer', 'fleet' ),
					'mast' => __( 'Mast manufacturer', 'fleet' ),
					'hull' => __( 'Hull manufacturer', 'fleet' ),
				),
			),
			array(
				'name'              => 'boat_auto_add_feature_image',
				'type'              => 'checkbox',
				'th'                => __( 'Auto add feature image', 'fleet' ),
				'description'       => __( 'Automagicaly add feature image, if there is some taged with boat number.', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
		),
		//      'metaboxes' => array(),
		'pages'      => array(
			'new-boat' => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Add New Boat', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'post_type' => 'iworks_fleet_boat',
						),
						'post-new.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'series'   => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Series', 'fleet' ),
				'menu_title'           => __( 'Series', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'taxonomy'  => 'iworks_fleet_serie',
							'post_type' => 'iworks_fleet_result',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'hull'     => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Hulls Manufaturers', 'fleet' ),
				'menu_title'           => __( 'Hulls Manufaturers', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'taxonomy'  => 'iworks_fleet_boat_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'sail'     => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Sails Manufaturers', 'fleet' ),
				'menu_title'           => __( 'Sails Manufaturers', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'taxonomy'  => 'iworks_fleet_sails_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'mast'     => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Masts Manufaturers', 'fleet' ),
				'menu_title'           => __( 'Masts Manufaturers', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'taxonomy'  => 'iworks_fleet_mast_manufacturer',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
			'location' => array(
				'menu'                 => 'submenu',
				'parent'               => $parent,
				'page_title'           => __( 'Locations', 'fleet' ),
				'menu_title'           => __( 'Locations', 'fleet' ),
				'menu_slug'            => htmlentities(
					add_query_arg(
						array(
							'taxonomy'  => 'iworks_fleet_location',
							'post_type' => 'iworks_fleet_person',
						),
						'edit-tags.php'
					)
				),
				'set_callback_to_null' => true,
			),
		),
	);
	/**
	 * The World Sailing Member National Authority County Codes
	 * see:
	 * https://www.sailing.org/raceofficials/eventorganizers/mna_codes.php
	 */
	$iworks_fleet_options['mna_codes'] = iworks_fleet_get_contries();

	$iworks_fleet_options['colors'] = array(
		'#f0f8ff' => __( 'Alice Blue', 'fleet' ),
		'#faebd7' => __( 'Antique White', 'fleet' ),
		'#00ffff' => __( 'Aqua', 'fleet' ),
		'#7fffd4' => __( 'Aquamarine', 'fleet' ),
		'#f0ffff' => __( 'Azure', 'fleet' ),
		'#f5f5dc' => __( 'Beige', 'fleet' ),
		'#ffe4c4' => __( 'Bisque', 'fleet' ),
		'#000000' => __( 'Black', 'fleet' ),
		'#ffebcd' => __( 'Blanched Almond', 'fleet' ),
		'#0000ff' => __( 'Blue', 'fleet' ),
		'#8a2be2' => __( 'Blue Violet', 'fleet' ),
		'#a52a2a' => __( 'Brown', 'fleet' ),
		'#deb887' => __( 'Burly Wood', 'fleet' ),
		'#5f9ea0' => __( 'Cadet Blue', 'fleet' ),
		'#7fff00' => __( 'Chartreuse', 'fleet' ),
		'#d2691e' => __( 'Chocolate', 'fleet' ),
		'#ff7f50' => __( 'Coral', 'fleet' ),
		'#6495ed' => __( 'Cornflower Blue', 'fleet' ),
		'#fff8dc' => __( 'Cornsilk', 'fleet' ),
		'#dc143c' => __( 'Crimson', 'fleet' ),
		'#00ffff' => __( 'Cyan', 'fleet' ),
		'#00008b' => __( 'Dark Blue', 'fleet' ),
		'#008b8b' => __( 'Dark Cyan', 'fleet' ),
		'#b8860b' => __( 'Dark Golden Rod', 'fleet' ),
		'#a9a9a9' => __( 'Dark Gray', 'fleet' ),
		'#a9a9a9' => __( 'Dark Grey', 'fleet' ),
		'#006400' => __( 'Dark Green', 'fleet' ),
		'#bdb76b' => __( 'Dark Khaki', 'fleet' ),
		'#8b008b' => __( 'Dark Magenta', 'fleet' ),
		'#556b2f' => __( 'Dark Olive Green', 'fleet' ),
		'#ff8c00' => __( 'Dark Orange', 'fleet' ),
		'#9932cc' => __( 'Dark Orchid', 'fleet' ),
		'#8b0000' => __( 'Dark Red', 'fleet' ),
		'#e9967a' => __( 'Dark Salmon', 'fleet' ),
		'#8fbc8f' => __( 'Dark Sea Green', 'fleet' ),
		'#483d8b' => __( 'Dark Slate Blue', 'fleet' ),
		'#2f4f4f' => __( 'Dark Slate Gray', 'fleet' ),
		'#2f4f4f' => __( 'Dark Slate Grey', 'fleet' ),
		'#00ced1' => __( 'Dark Turquoise', 'fleet' ),
		'#9400d3' => __( 'Dark Violet', 'fleet' ),
		'#ff1493' => __( 'Deep Pink', 'fleet' ),
		'#00bfff' => __( 'Deep Sky Blue', 'fleet' ),
		'#696969' => __( 'Dim Gray', 'fleet' ),
		'#696969' => __( 'Dim Grey', 'fleet' ),
		'#1e90ff' => __( 'Dodger Blue', 'fleet' ),
		'#b22222' => __( 'Fire Brick', 'fleet' ),
		'#fffaf0' => __( 'Floral White', 'fleet' ),
		'#228b22' => __( 'Forest Green', 'fleet' ),
		'#ff00ff' => __( 'Fuchsia', 'fleet' ),
		'#dcdcdc' => __( 'Gainsboro', 'fleet' ),
		'#f8f8ff' => __( 'Ghost White', 'fleet' ),
		'#ffd700' => __( 'Gold', 'fleet' ),
		'#daa520' => __( 'Golden Rod', 'fleet' ),
		'#808080' => __( 'Gray', 'fleet' ),
		'#808080' => __( 'Grey', 'fleet' ),
		'#008000' => __( 'Green', 'fleet' ),
		'#adff2f' => __( 'Green Yellow', 'fleet' ),
		'#f0fff0' => __( 'Honey Dew', 'fleet' ),
		'#ff69b4' => __( 'Hot Pink', 'fleet' ),
		'#cd5c5c' => __( 'Indian Red', 'fleet' ),
		'#4b0082' => __( 'Indigo', 'fleet' ),
		'#fffff0' => __( 'Ivory', 'fleet' ),
		'#f0e68c' => __( 'Khaki', 'fleet' ),
		'#e6e6fa' => __( 'Lavender', 'fleet' ),
		'#fff0f5' => __( 'Lavender Blush', 'fleet' ),
		'#7cfc00' => __( 'Lawn Green', 'fleet' ),
		'#fffacd' => __( 'Lemon Chiffon', 'fleet' ),
		'#add8e6' => __( 'Light Blue', 'fleet' ),
		'#f08080' => __( 'Light Coral', 'fleet' ),
		'#e0ffff' => __( 'Light Cyan', 'fleet' ),
		'#fafad2' => __( 'Light Golden Rod Yellow', 'fleet' ),
		'#d3d3d3' => __( 'Light Gray', 'fleet' ),
		'#d3d3d3' => __( 'Light Grey', 'fleet' ),
		'#90ee90' => __( 'Light Green', 'fleet' ),
		'#ffb6c1' => __( 'Light Pink', 'fleet' ),
		'#ffa07a' => __( 'Light Salmon', 'fleet' ),
		'#20b2aa' => __( 'Light Sea Green', 'fleet' ),
		'#87cefa' => __( 'Light Sky Blue', 'fleet' ),
		'#778899' => __( 'Light Slate Gray', 'fleet' ),
		'#778899' => __( 'Light Slate Grey', 'fleet' ),
		'#b0c4de' => __( 'Light Steel Blue', 'fleet' ),
		'#ffffe0' => __( 'Light Yellow', 'fleet' ),
		'#00ff00' => __( 'Lime', 'fleet' ),
		'#32cd32' => __( 'Lime Green', 'fleet' ),
		'#faf0e6' => __( 'Linen', 'fleet' ),
		'#ff00ff' => __( 'Magenta', 'fleet' ),
		'#800000' => __( 'Maroon', 'fleet' ),
		'#66cdaa' => __( 'Medium Aqua Marine', 'fleet' ),
		'#0000cd' => __( 'Medium Blue', 'fleet' ),
		'#ba55d3' => __( 'Medium Orchid', 'fleet' ),
		'#9370db' => __( 'Medium Purple', 'fleet' ),
		'#3cb371' => __( 'Medium Sea Green', 'fleet' ),
		'#7b68ee' => __( 'Medium Slate Blue', 'fleet' ),
		'#00fa9a' => __( 'Medium Spring Green', 'fleet' ),
		'#48d1cc' => __( 'Medium Turquoise', 'fleet' ),
		'#c71585' => __( 'Medium Violet Red', 'fleet' ),
		'#191970' => __( 'Midnight Blue', 'fleet' ),
		'#f5fffa' => __( 'Mint Cream', 'fleet' ),
		'#ffe4e1' => __( 'Misty Rose', 'fleet' ),
		'#ffe4b5' => __( 'Moccasin', 'fleet' ),
		'#ffdead' => __( 'Navajo White', 'fleet' ),
		'#000080' => __( 'Navy', 'fleet' ),
		'#fdf5e6' => __( 'Old Lace', 'fleet' ),
		'#808000' => __( 'Olive', 'fleet' ),
		'#6b8e23' => __( 'Olive Drab', 'fleet' ),
		'#ffa500' => __( 'Orange', 'fleet' ),
		'#ff4500' => __( 'Orange Red', 'fleet' ),
		'#da70d6' => __( 'Orchid', 'fleet' ),
		'#eee8aa' => __( 'Pale Golden Rod', 'fleet' ),
		'#98fb98' => __( 'Pale Green', 'fleet' ),
		'#afeeee' => __( 'Pale Turquoise', 'fleet' ),
		'#db7093' => __( 'Pale Violet Red', 'fleet' ),
		'#ffefd5' => __( 'Papaya Whip', 'fleet' ),
		'#ffdab9' => __( 'Peach Puff', 'fleet' ),
		'#cd853f' => __( 'Peru', 'fleet' ),
		'#ffc0cb' => __( 'Pink', 'fleet' ),
		'#dda0dd' => __( 'Plum', 'fleet' ),
		'#b0e0e6' => __( 'Powder Blue', 'fleet' ),
		'#800080' => __( 'Purple', 'fleet' ),
		'#663399' => __( 'Rebecca Purple', 'fleet' ),
		'#ff0000' => __( 'Red', 'fleet' ),
		'#bc8f8f' => __( 'Rosy Brown', 'fleet' ),
		'#4169e1' => __( 'Royal Blue', 'fleet' ),
		'#8b4513' => __( 'Saddle Brown', 'fleet' ),
		'#fa8072' => __( 'Salmon', 'fleet' ),
		'#f4a460' => __( 'Sandy Brown', 'fleet' ),
		'#2e8b57' => __( 'Sea Green', 'fleet' ),
		'#fff5ee' => __( 'Sea Shell', 'fleet' ),
		'#a0522d' => __( 'Sienna', 'fleet' ),
		'#c0c0c0' => __( 'Silver', 'fleet' ),
		'#87ceeb' => __( 'Sky Blue', 'fleet' ),
		'#6a5acd' => __( 'Slate Blue', 'fleet' ),
		'#708090' => __( 'Slate Gray', 'fleet' ),
		'#708090' => __( 'Slate Grey', 'fleet' ),
		'#fffafa' => __( 'Snow', 'fleet' ),
		'#00ff7f' => __( 'Spring Green', 'fleet' ),
		'#4682b4' => __( 'Steel Blue', 'fleet' ),
		'#d2b48c' => __( 'Tan', 'fleet' ),
		'#008080' => __( 'Teal', 'fleet' ),
		'#d8bfd8' => __( 'Thistle', 'fleet' ),
		'#ff6347' => __( 'Tomato', 'fleet' ),
		'#40e0d0' => __( 'Turquoise', 'fleet' ),
		'#ee82ee' => __( 'Violet', 'fleet' ),
		'#f5deb3' => __( 'Wheat', 'fleet' ),
		'#ffffff' => __( 'White', 'fleet' ),
		'#f5f5f5' => __( 'White Smoke', 'fleet' ),
		'#ffff00' => __( 'Yellow', 'fleet' ),
		'#9acd32' => __( 'Yellow Green', 'fleet' ),
	);

	return $iworks_fleet_options;
}

function iworks_fleet_get_contries() {
	return array(
		array(
			'nation' => __( 'Algeria', 'fleet' ),
			'code'   => 'ALG',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Finland', 'fleet' ),
			'code'   => 'FIN',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Pakistan', 'fleet' ),
			'code'   => 'PAK',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'American Samoa', 'fleet' ),
			'code'   => 'ASA',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Grenada', 'fleet' ),
			'code'   => 'GRN',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Palestine', 'fleet' ),
			'code'   => 'PLE',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Andorra', 'fleet' ),
			'code'   => 'AND',
			'group'  => 'E',
		),
		array(
			'nation' => __( 'Guam', 'fleet' ),
			'code'   => 'GUM',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Panama', 'fleet' ),
			'code'   => 'PAN',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Angola', 'fleet' ),
			'code'   => 'ANG',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Guatemala', 'fleet' ),
			'code'   => 'GUA',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Papua New Guinea', 'fleet' ),
			'code'   => 'PNG',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Antigua', 'fleet' ),
			'code'   => 'ANT',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Hong Kong, China', 'fleet' ),
			'code'   => 'HKG',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Paraguay', 'fleet' ),
			'code'   => 'PAR',
			'group'  => 'N',
		),
		array(
			'nation' => __( 'Argentina', 'fleet' ),
			'code'   => 'ARG',
			'group'  => 'M',
		),
		array(
			'nation' => __( 'Hungary', 'fleet' ),
			'code'   => 'HUN',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Peru', 'fleet' ),
			'code'   => 'PER',
			'group'  => 'M',
		),
		array(
			'nation' => __( 'Armenia', 'fleet' ),
			'code'   => 'ARM',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'Iceland', 'fleet' ),
			'code'   => 'ISL',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Philippines', 'fleet' ),
			'code'   => 'PHI',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Aruba', 'fleet' ),
			'code'   => 'ARU',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'India', 'fleet' ),
			'code'   => 'IND',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Poland', 'fleet' ),
			'code'   => 'POL',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Australia', 'fleet' ),
			'code'   => 'AUS',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Indonesia', 'fleet' ),
			'code'   => 'INA',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Portugal', 'fleet' ),
			'code'   => 'POR',
			'group'  => 'E',
		),
		array(
			'nation' => __( 'Austria', 'fleet' ),
			'code'   => 'AUT',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Iran', 'fleet' ),
			'code'   => 'IRN',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Puerto Rico', 'fleet' ),
			'code'   => 'PUR',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Azerbaijan', 'fleet' ),
			'code'   => 'AZE',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'Iraq', 'fleet' ),
			'code'   => 'IRQ',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Qatar', 'fleet' ),
			'code'   => 'QAT',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Bahamas', 'fleet' ),
			'code'   => 'BAH',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Ireland', 'fleet' ),
			'code'   => 'IRL',
			'group'  => 'A',
		),
		array(
			'nation' => __( 'Romania', 'fleet' ),
			'code'   => 'ROU',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Bahrain', 'fleet' ),
			'code'   => 'BRN',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Israel', 'fleet' ),
			'code'   => 'ISR',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Russia', 'fleet' ),
			'code'   => 'RUS',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'Barbados', 'fleet' ),
			'code'   => 'BAR',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Italy', 'fleet' ),
			'code'   => 'ITA',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Samoa', 'fleet' ),
			'code'   => 'SAM',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Belarus', 'fleet' ),
			'code'   => 'BLR',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Jamaica', 'fleet' ),
			'code'   => 'JAM',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'San Marino', 'fleet' ),
			'code'   => 'SMR',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Belize', 'fleet' ),
			'code'   => 'BIZ',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Japan', 'fleet' ),
			'code'   => 'JPN',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Senegal', 'fleet' ),
			'code'   => 'SEN',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Belgium', 'fleet' ),
			'code'   => 'BEL',
			'group'  => 'F',
		),
		array(
			'nation' => __( 'Kazakhstan', 'fleet' ),
			'code'   => 'KAZ',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'Serbia', 'fleet' ),
			'code'   => 'SRB',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Bermuda', 'fleet' ),
			'code'   => 'BER',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Kenya', 'fleet' ),
			'code'   => 'KEN',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Seychelles', 'fleet' ),
			'code'   => 'SEY',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Botswana', 'fleet' ),
			'code'   => 'BOT',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Korea', 'fleet' ),
			'code'   => 'KOR',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Singapore', 'fleet' ),
			'code'   => 'SIN',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Brazil', 'fleet' ),
			'code'   => 'BRA',
			'group'  => 'N',
		),
		array(
			'nation' => __( 'Kosovo', 'fleet' ),
			'code'   => 'KOS',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Slovak Republic', 'fleet' ),
			'code'   => 'SVK',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'British Virgin Islands', 'fleet' ),
			'code'   => 'IVB',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Kyrgyzstan', 'fleet' ),
			'code'   => 'KGZ',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'Slovenia', 'fleet' ),
			'code'   => 'SLO',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Brunei', 'fleet' ),
			'code'   => 'BRU',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'DPR Korea', 'fleet' ),
			'code'   => 'PRK',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'South Africa', 'fleet' ),
			'code'   => 'RSA',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Bulgaria', 'fleet' ),
			'code'   => 'BUL',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Kuwait', 'fleet' ),
			'code'   => 'KUW',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Spain', 'fleet' ),
			'code'   => 'ESP',
			'group'  => 'E',
		),
		array(
			'nation' => __( 'Cambodia', 'fleet' ),
			'code'   => 'CAM',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Latvia', 'fleet' ),
			'code'   => 'LAT',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Sri Lanka', 'fleet' ),
			'code'   => 'SRI',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Canada', 'fleet' ),
			'code'   => 'CAN',
			'group'  => 'P',
		),
		array(
			'nation' => __( 'Lebanon', 'fleet' ),
			'code'   => 'LIB',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'St Kitts and Nevis', 'fleet' ),
			'code'   => 'SKN',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Cayman Islands', 'fleet' ),
			'code'   => 'CAY',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Libya', 'fleet' ),
			'code'   => 'LBA',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'St Lucia', 'fleet' ),
			'code'   => 'LCA',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Chile', 'fleet' ),
			'code'   => 'CHI',
			'group'  => 'M',
		),
		array(
			'nation' => __( 'Liechtenstein', 'fleet' ),
			'code'   => 'LIE',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Sudan', 'fleet' ),
			'code'   => 'SUD',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'China, PR', 'fleet' ),
			'code'   => 'CHN',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Lithuania', 'fleet' ),
			'code'   => 'LTU',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Sweden', 'fleet' ),
			'code'   => 'SWE',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Chinese Taipei', 'fleet' ),
			'code'   => 'TPE',
			'group'  => 'J',
		),
		array(
			'nation' => __( 'Luxembourg', 'fleet' ),
			'code'   => 'LUX',
			'group'  => 'F',
		),
		array(
			'nation' => __( 'Switzerland', 'fleet' ),
			'code'   => 'SUI',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Colombia', 'fleet' ),
			'code'   => 'COL',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Macau', 'fleet' ),
			'code'   => '',
			'group'  => 'Assoc',
		),
		array(
			'nation' => __( 'Tahiti', 'fleet' ),
			'code'   => 'TAH',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Cook Islands', 'fleet' ),
			'code'   => 'COK',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Madagascar', 'fleet' ),
			'code'   => 'MAD',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Tanzania', 'fleet' ),
			'code'   => 'TAN',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Croatia', 'fleet' ),
			'code'   => 'CRO',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Malaysia', 'fleet' ),
			'code'   => 'MAS',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Thailand', 'fleet' ),
			'code'   => 'THA',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Cuba', 'fleet' ),
			'code'   => 'CUB',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Malta', 'fleet' ),
			'code'   => 'MLT',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Timor Leste', 'fleet' ),
			'code'   => 'TLS',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Cyprus', 'fleet' ),
			'code'   => 'CYP',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Mauritius', 'fleet' ),
			'code'   => 'MRI',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Trinidad & Tobago', 'fleet' ),
			'code'   => 'TTO',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Czech Republic', 'fleet' ),
			'code'   => 'CZE',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Mexico', 'fleet' ),
			'code'   => 'MEX',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Tunisia', 'fleet' ),
			'code'   => 'TUN',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Denmark', 'fleet' ),
			'code'   => 'DEN',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Moldova', 'fleet' ),
			'code'   => 'MDA',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Turkey', 'fleet' ),
			'code'   => 'TUR',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Djibouti', 'fleet' ),
			'code'   => 'DJI',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Monaco', 'fleet' ),
			'code'   => 'MON',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Turks and Caicos', 'fleet' ),
			'code'   => 'TKS',
			'group'  => 'Assoc',
		),
		array(
			'nation' => __( 'Dominican Republic', 'fleet' ),
			'code'   => 'DOM',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Morocco', 'fleet' ),
			'code'   => 'MAR',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Uganda', 'fleet' ),
			'code'   => 'UGA',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Ecuador', 'fleet' ),
			'code'   => 'ECU',
			'group'  => 'M',
		),
		array(
			'nation' => __( 'Montenegro', 'fleet' ),
			'code'   => 'MNE',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Ukraine', 'fleet' ),
			'code'   => 'UKR',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Egypt', 'fleet' ),
			'code'   => 'EGY',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Montserrat', 'fleet' ),
			'code'   => 'MNT',
			'group'  => 'Assoc',
		),
		array(
			'nation' => __( 'United Arab Emirates', 'fleet' ),
			'code'   => 'UAE',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'El Salvador', 'fleet' ),
			'code'   => 'ESA',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Mozambique', 'fleet' ),
			'code'   => 'MOZ',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'United States of America', 'fleet' ),
			'code'   => 'USA',
			'group'  => 'P',
		),
		array(
			'nation' => __( 'Estonia', 'fleet' ),
			'code'   => 'EST',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Myanmar', 'fleet' ),
			'code'   => 'MYA',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Uruguay', 'fleet' ),
			'code'   => 'URU',
			'group'  => 'M',
		),
		array(
			'nation' => __( 'Fiji', 'fleet' ),
			'code'   => 'FIJ',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Namibia', 'fleet' ),
			'code'   => 'NAM',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'US Virgin Islands', 'fleet' ),
			'code'   => 'ISV',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'France', 'fleet' ),
			'code'   => 'FRA',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Netherlands', 'fleet' ),
			'code'   => 'NED',
			'group'  => 'F',
		),
		array(
			'nation' => __( 'Vanuatu', 'fleet' ),
			'code'   => 'VAN',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'FYRO Macedonia', 'fleet' ),
			'code'   => 'MKD',
			'group'  => 'C',
		),
		array(
			'nation' => __( 'Netherland Antilles (Curacao and St Maarten)', 'fleet' ),
			'code'   => 'AHO',
			'group'  => 'L',
		),
		array(
			'nation' => __( 'Venezuela', 'fleet' ),
			'code'   => 'VEN',
			'group'  => 'O',
		),
		array(
			'nation' => __( 'Georgia', 'fleet' ),
			'code'   => 'GEO',
			'group'  => 'H',
		),
		array(
			'nation' => __( 'New Zealand', 'fleet' ),
			'code'   => 'NZL',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Vietnam', 'fleet' ),
			'code'   => 'VIE',
			'group'  => 'K',
		),
		array(
			'nation' => __( 'Germany', 'fleet' ),
			'code'   => 'GER',
			'group'  => 'B',
		),
		array(
			'nation' => __( 'Nigeria', 'fleet' ),
			'code'   => 'NIG',
			'group'  => 'G',
		),
		array(
			'nation' => __( 'Zimbabwe', 'fleet' ),
			'code'   => 'ZIM',
			'group'  => 'Q',
		),
		array(
			'nation' => __( 'Great Britain', 'fleet' ),
			'code'   => 'GBR',
			'group'  => 'A',
		),
		array(
			'nation' => __( 'Norway', 'fleet' ),
			'code'   => 'NOR',
			'group'  => 'I',
		),
		array(
			'nation' => __( 'Greece', 'fleet' ),
			'code'   => 'GRE',
			'group'  => 'D',
		),
		array(
			'nation' => __( 'Oman', 'fleet' ),
			'code'   => 'OMA',
			'group'  => '',
		),
	);
}

function iworks_fleet_get_contries_select2() {
	$c    = iworks_fleet_get_contries();
	$data = array();
	foreach ( $c as $one ) {
		if (
			empty( $one['code'] )
			|| empty( $one['nation'] )
		) {
			continue;
		}
		$data[ $one['code'] ] = $one['nation'];
	}
	asort( $data );
	return $data;
}


