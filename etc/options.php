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
				'th'          => __( 'Country', 'fleet' ),
				'options'     => iworks_fleet_get_countries_select2(),
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
			array(
				'type'  => 'subheading',
				'label' => __( 'Load Modules', 'fleet' ),
			),
			/*
			array(
				'name'              => 'load_person',
				'type'              => 'checkbox',
				'th'                => __( 'Load Persons', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since' => '2.2.3',
			),
			array(
				'name'              => 'load_result',
				'type'              => 'checkbox',
				'th'                => __( 'Load Results', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since' => '2.2.3',
			),
			array(
				'name'              => 'load_boat',
				'type'              => 'checkbox',
				'th'                => __( 'Load Boats', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since' => '2.2.3',
			),
			 */
			array(
				'name'              => 'load_ranking',
				'type'              => 'checkbox',
				'th'                => __( 'Load Rankings', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'since'             => '2.2.3',
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
				'th'                => __( 'Show Points', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'results_show_trophy',
				'type'              => 'checkbox',
				'th'                => __( 'Show Trophy', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'results_show_country',
				'type'              => 'checkbox',
				'th'                => __( 'Show Country', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'results_serie_show',
				'type'              => 'checkbox',
				'th'                => __( 'Show Series', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'results_serie_link',
				'type'              => 'checkbox',
				'th'                => __( 'Link Series', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'        => 'results_serie_trophy_world',
				'type'        => 'select',
				'th'          => __( 'World Serie', 'fleet' ),
				'options'     => iworks_fleet_get_series(),
				'multiple'    => true,
				'description' => __( 'Select World serie to show trophy', 'fleet' ),
			),
			array(
				'name'        => 'results_serie_trophy_continental',
				'type'        => 'select',
				'th'          => __( 'Continental Serie', 'fleet' ),
				'options'     => iworks_fleet_get_series(),
				'multiple'    => true,
				'description' => __( 'Select continental serie to show trophy', 'fleet' ),
			),
			array(
				'name'        => 'results_serie_trophy_national',
				'type'        => 'select',
				'th'          => __( 'National Serie', 'fleet' ),
				'options'     => iworks_fleet_get_series(),
				'multiple'    => true,
				'description' => __( 'Select national serie to show trophy', 'fleet' ),
			),
			array(
				'name'              => 'result_show_download_link',
				'type'              => 'checkbox',
				'th'                => __( 'Allow download CSV', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'result_show_english_title',
				'type'              => 'checkbox',
				'th'                => __( 'Show English', 'fleet' ),
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
				'description'       => __( 'Allow to show English title on a list.', 'fleet' ),
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
				'th'                => __( 'Show Boats Table', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_boats_owned_table',
				'type'              => 'checkbox',
				'th'                => __( 'Show Boat Owned', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_places_chart',
				'type'              => 'checkbox',
				'th'                => __( 'Show Places Chart', 'fleet' ),
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
			array(
				'name'              => 'person_show_trophy',
				'type'              => 'checkbox',
				'th'                => __( 'Show trophy', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_sailor_id',
				'type'              => 'checkbox',
				'th'                => __( 'Show link to World Sailin with Sailor ID', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'person_show_download_link',
				'type'              => 'checkbox',
				'th'                => __( 'Allow download CSV', 'fleet' ),
				'default'           => 1,
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
				'name'              => 'boat_show_flag',
				'type'              => 'checkbox',
				'th'                => __( 'Show boat flag', 'fleet' ),
				'default'           => 0,
				'description'       => __( 'We recommend to turn off if we show sailors nationality.', 'fleet' ),
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
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
			array(
				'name'              => 'boat_show_trophy',
				'type'              => 'checkbox',
				'th'                => __( 'Show trophy', 'fleet' ),
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'classes'           => array( 'switch-button' ),
			),
			array(
				'name'              => 'boat_show_download_link',
				'type'              => 'checkbox',
				'th'                => __( 'Allow download CSV', 'fleet' ),
				'default'           => 1,
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
							'post_type' => 'iworks_fleet_person',
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
	$iworks_fleet_options['mna_codes'] = iworks_fleet_get_countries();

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
	/**
	 * turn off pages
	 */
	$taxonomies = get_option( 'iworks_fleet_boat_taxonomies' );
	foreach ( array( 'hull', 'sail', 'mast' ) as $key ) {
		if ( ! is_array( $taxonomies ) || empty( $taxonomies ) || ! isset( $taxonomies[ $key ] ) ) {
			unset( $iworks_fleet_options['index']['pages'][ $key ] );
		}
	}
	return $iworks_fleet_options;
}

/**
 * get countries array
 */
function iworks_fleet_get_countries() {
	return array(
		array(
			'nation' => esc_html__( 'Algeria', 'fleet' ),
			'en'     => 'Algeria',
			'code'   => 'ALG',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Finland', 'fleet' ),
			'en'     => 'Finland',
			'code'   => 'FIN',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Pakistan', 'fleet' ),
			'en'     => 'Pakistan',
			'code'   => 'PAK',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'American Samoa', 'fleet' ),
			'en'     => 'American Samoa',
			'code'   => 'ASA',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Grenada', 'fleet' ),
			'en'     => 'Grenada',
			'code'   => 'GRN',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Palestine', 'fleet' ),
			'en'     => 'Palestine',
			'code'   => 'PLE',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Andorra', 'fleet' ),
			'en'     => 'Andorra',
			'code'   => 'AND',
			'group'  => 'E',
		),
		array(
			'nation' => esc_html__( 'Guam', 'fleet' ),
			'en'     => 'Guam',
			'code'   => 'GUM',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Panama', 'fleet' ),
			'en'     => 'Panama',
			'code'   => 'PAN',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Angola', 'fleet' ),
			'en'     => 'Angola',
			'code'   => 'ANG',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Guatemala', 'fleet' ),
			'en'     => 'Guatemala',
			'code'   => 'GUA',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Papua New Guinea', 'fleet' ),
			'en'     => 'Papua New Guinea',
			'code'   => 'PNG',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Antigua', 'fleet' ),
			'en'     => 'Antigua',
			'code'   => 'ANT',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Hong Kong, China', 'fleet' ),
			'en'     => 'Hong Kong, China',
			'code'   => 'HKG',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Paraguay', 'fleet' ),
			'en'     => 'Paraguay',
			'code'   => 'PAR',
			'group'  => 'N',
		),
		array(
			'nation' => esc_html__( 'Argentina', 'fleet' ),
			'en'     => 'Argentina',
			'code'   => 'ARG',
			'group'  => 'M',
		),
		array(
			'nation' => esc_html__( 'Hungary', 'fleet' ),
			'en'     => 'Hungary',
			'code'   => 'HUN',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Peru', 'fleet' ),
			'en'     => 'Peru',
			'code'   => 'PER',
			'group'  => 'M',
		),
		array(
			'nation' => esc_html__( 'Armenia', 'fleet' ),
			'en'     => 'Armenia',
			'code'   => 'ARM',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'Iceland', 'fleet' ),
			'en'     => 'Iceland',
			'code'   => 'ISL',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Philippines', 'fleet' ),
			'en'     => 'Philippines',
			'code'   => 'PHI',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Aruba', 'fleet' ),
			'en'     => 'Aruba',
			'code'   => 'ARU',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'India', 'fleet' ),
			'en'     => 'India',
			'code'   => 'IND',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Poland', 'fleet' ),
			'en'     => 'Poland',
			'code'   => 'POL',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Australia', 'fleet' ),
			'en'     => 'Australia',
			'code'   => 'AUS',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Indonesia', 'fleet' ),
			'en'     => 'Indonesia',
			'code'   => 'INA',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Portugal', 'fleet' ),
			'en'     => 'Portugal',
			'code'   => 'POR',
			'group'  => 'E',
		),
		array(
			'nation' => esc_html__( 'Austria', 'fleet' ),
			'en'     => 'Austria',
			'code'   => 'AUT',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Iran', 'fleet' ),
			'en'     => 'Iran',
			'code'   => 'IRN',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Puerto Rico', 'fleet' ),
			'en'     => 'Puerto Rico',
			'code'   => 'PUR',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Azerbaijan', 'fleet' ),
			'en'     => 'Azerbaijan',
			'code'   => 'AZE',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'Iraq', 'fleet' ),
			'en'     => 'Iraq',
			'code'   => 'IRQ',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Qatar', 'fleet' ),
			'en'     => 'Qatar',
			'code'   => 'QAT',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Bahamas', 'fleet' ),
			'en'     => 'Bahamas',
			'code'   => 'BAH',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Ireland', 'fleet' ),
			'en'     => 'Ireland',
			'code'   => 'IRL',
			'group'  => 'A',
		),
		array(
			'nation' => esc_html__( 'Romania', 'fleet' ),
			'en'     => 'Romania',
			'code'   => 'ROU',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Bahrain', 'fleet' ),
			'en'     => 'Bahrain',
			'code'   => 'BRN',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Israel', 'fleet' ),
			'en'     => 'Israel',
			'code'   => 'ISR',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Russia', 'fleet' ),
			'en'     => 'Russia',
			'code'   => 'RUS',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'Barbados', 'fleet' ),
			'en'     => 'Barbados',
			'code'   => 'BAR',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Italy', 'fleet' ),
			'en'     => 'Italy',
			'code'   => 'ITA',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Samoa', 'fleet' ),
			'en'     => 'Samoa',
			'code'   => 'SAM',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Belarus', 'fleet' ),
			'en'     => 'Belarus',
			'code'   => 'BLR',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Jamaica', 'fleet' ),
			'en'     => 'Jamaica',
			'code'   => 'JAM',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'San Marino', 'fleet' ),
			'en'     => 'San Marino',
			'code'   => 'SMR',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Belize', 'fleet' ),
			'en'     => 'Belize',
			'code'   => 'BIZ',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Japan', 'fleet' ),
			'en'     => 'Japan',
			'code'   => 'JPN',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Senegal', 'fleet' ),
			'en'     => 'Senegal',
			'code'   => 'SEN',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Belgium', 'fleet' ),
			'en'     => 'Belgium',
			'code'   => 'BEL',
			'group'  => 'F',
		),
		array(
			'nation' => esc_html__( 'Kazakhstan', 'fleet' ),
			'en'     => 'Kazakhstan',
			'code'   => 'KAZ',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'Serbia', 'fleet' ),
			'en'     => 'Serbia',
			'code'   => 'SRB',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Bermuda', 'fleet' ),
			'en'     => 'Bermuda',
			'code'   => 'BER',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Kenya', 'fleet' ),
			'en'     => 'Kenya',
			'code'   => 'KEN',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Seychelles', 'fleet' ),
			'en'     => 'Seychelles',
			'code'   => 'SEY',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Botswana', 'fleet' ),
			'en'     => 'Botswana',
			'code'   => 'BOT',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Korea', 'fleet' ),
			'en'     => 'Korea',
			'code'   => 'KOR',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Singapore', 'fleet' ),
			'en'     => 'Singapore',
			'code'   => 'SIN',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Brazil', 'fleet' ),
			'en'     => 'Brazil',
			'code'   => 'BRA',
			'group'  => 'N',
		),
		array(
			'nation' => esc_html__( 'Kosovo', 'fleet' ),
			'en'     => 'Kosovo',
			'code'   => 'KOS',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Slovak Republic', 'fleet' ),
			'en'     => 'Slovak Republic',
			'code'   => 'SVK',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'British Virgin Islands', 'fleet' ),
			'en'     => 'British Virgin Islands',
			'code'   => 'IVB',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Kyrgyzstan', 'fleet' ),
			'en'     => 'Kyrgyzstan',
			'code'   => 'KGZ',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'Slovenia', 'fleet' ),
			'en'     => 'Slovenia',
			'code'   => 'SLO',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Brunei', 'fleet' ),
			'en'     => 'Brunei',
			'code'   => 'BRU',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'DPR Korea', 'fleet' ),
			'en'     => 'DPR Korea',
			'code'   => 'PRK',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'South Africa', 'fleet' ),
			'en'     => 'South Africa',
			'code'   => 'RSA',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Bulgaria', 'fleet' ),
			'en'     => 'Bulgaria',
			'code'   => 'BUL',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Kuwait', 'fleet' ),
			'en'     => 'Kuwait',
			'code'   => 'KUW',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Spain', 'fleet' ),
			'en'     => 'Spain',
			'code'   => 'ESP',
			'group'  => 'E',
		),
		array(
			'nation' => esc_html__( 'Cambodia', 'fleet' ),
			'en'     => 'Cambodia',
			'code'   => 'CAM',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Latvia', 'fleet' ),
			'en'     => 'Latvia',
			'code'   => 'LAT',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Sri Lanka', 'fleet' ),
			'en'     => 'Sri Lanka',
			'code'   => 'SRI',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Canada', 'fleet' ),
			'en'     => 'Canada',
			'code'   => 'CAN',
			'group'  => 'P',
		),
		array(
			'nation' => esc_html__( 'Lebanon', 'fleet' ),
			'en'     => 'Lebanon',
			'code'   => 'LIB',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'St Kitts and Nevis', 'fleet' ),
			'en'     => 'St Kitts and Nevis',
			'code'   => 'SKN',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Cayman Islands', 'fleet' ),
			'en'     => 'Cayman Islands',
			'code'   => 'CAY',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Libya', 'fleet' ),
			'en'     => 'Libya',
			'code'   => 'LBA',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'St Lucia', 'fleet' ),
			'en'     => 'St Lucia',
			'code'   => 'LCA',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Chile', 'fleet' ),
			'en'     => 'Chile',
			'code'   => 'CHI',
			'group'  => 'M',
		),
		array(
			'nation' => esc_html__( 'Liechtenstein', 'fleet' ),
			'en'     => 'Liechtenstein',
			'code'   => 'LIE',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Sudan', 'fleet' ),
			'en'     => 'Sudan',
			'code'   => 'SUD',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'China, PR', 'fleet' ),
			'en'     => 'China, PR',
			'code'   => 'CHN',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Lithuania', 'fleet' ),
			'en'     => 'Lithuania',
			'code'   => 'LTU',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Sweden', 'fleet' ),
			'en'     => 'Sweden',
			'code'   => 'SWE',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Chinese Taipei', 'fleet' ),
			'en'     => 'Chinese Taipei',
			'code'   => 'TPE',
			'group'  => 'J',
		),
		array(
			'nation' => esc_html__( 'Luxembourg', 'fleet' ),
			'en'     => 'Luxembourg',
			'code'   => 'LUX',
			'group'  => 'F',
		),
		array(
			'nation' => esc_html__( 'Switzerland', 'fleet' ),
			'en'     => 'Switzerland',
			'code'   => 'SUI',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Colombia', 'fleet' ),
			'en'     => 'Colombia',
			'code'   => 'COL',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Macau', 'fleet' ),
			'en'     => 'Macau',
			'code'   => '',
			'group'  => 'Assoc',
		),
		array(
			'nation' => esc_html__( 'Tahiti', 'fleet' ),
			'en'     => 'Tahiti',
			'code'   => 'TAH',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Cook Islands', 'fleet' ),
			'en'     => 'Cook Islands',
			'code'   => 'COK',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Madagascar', 'fleet' ),
			'en'     => 'Madagascar',
			'code'   => 'MAD',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Tanzania', 'fleet' ),
			'en'     => 'Tanzania',
			'code'   => 'TAN',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Croatia', 'fleet' ),
			'en'     => 'Croatia',
			'code'   => 'CRO',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Malaysia', 'fleet' ),
			'en'     => 'Malaysia',
			'code'   => 'MAS',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Thailand', 'fleet' ),
			'en'     => 'Thailand',
			'code'   => 'THA',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Cuba', 'fleet' ),
			'en'     => 'Cuba',
			'code'   => 'CUB',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Malta', 'fleet' ),
			'en'     => 'Malta',
			'code'   => 'MLT',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Timor Leste', 'fleet' ),
			'en'     => 'Timor Leste',
			'code'   => 'TLS',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Cyprus', 'fleet' ),
			'en'     => 'Cyprus',
			'code'   => 'CYP',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Mauritius', 'fleet' ),
			'en'     => 'Mauritius',
			'code'   => 'MRI',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Trinidad & Tobago', 'fleet' ),
			'en'     => 'Trinidad & Tobago',
			'code'   => 'TTO',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Czech Republic', 'fleet' ),
			'en'     => 'Czech Republic',
			'code'   => 'CZE',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Mexico', 'fleet' ),
			'en'     => 'Mexico',
			'code'   => 'MEX',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Tunisia', 'fleet' ),
			'en'     => 'Tunisia',
			'code'   => 'TUN',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Denmark', 'fleet' ),
			'en'     => 'Denmark',
			'code'   => 'DEN',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Moldova', 'fleet' ),
			'en'     => 'Moldova',
			'code'   => 'MDA',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Turkey', 'fleet' ),
			'en'     => 'Turkey',
			'code'   => 'TUR',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Djibouti', 'fleet' ),
			'en'     => 'Djibouti',
			'code'   => 'DJI',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Monaco', 'fleet' ),
			'en'     => 'Monaco',
			'code'   => 'MON',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Turks and Caicos', 'fleet' ),
			'en'     => 'Turks and Caicos',
			'code'   => 'TKS',
			'group'  => 'Assoc',
		),
		array(
			'nation' => esc_html__( 'Dominican Republic', 'fleet' ),
			'en'     => 'Dominican Republic',
			'code'   => 'DOM',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Morocco', 'fleet' ),
			'en'     => 'Morocco',
			'code'   => 'MAR',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Uganda', 'fleet' ),
			'en'     => 'Uganda',
			'code'   => 'UGA',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Ecuador', 'fleet' ),
			'en'     => 'Ecuador',
			'code'   => 'ECU',
			'group'  => 'M',
		),
		array(
			'nation' => esc_html__( 'Montenegro', 'fleet' ),
			'en'     => 'Montenegro',
			'code'   => 'MNE',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Ukraine', 'fleet' ),
			'en'     => 'Ukraine',
			'code'   => 'UKR',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Egypt', 'fleet' ),
			'en'     => 'Egypt',
			'code'   => 'EGY',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Montserrat', 'fleet' ),
			'en'     => 'Montserrat',
			'code'   => 'MNT',
			'group'  => 'Assoc',
		),
		array(
			'nation' => esc_html__( 'United Arab Emirates', 'fleet' ),
			'en'     => 'United Arab Emirates',
			'code'   => 'UAE',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'El Salvador', 'fleet' ),
			'en'     => 'El Salvador',
			'code'   => 'ESA',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Mozambique', 'fleet' ),
			'en'     => 'Mozambique',
			'code'   => 'MOZ',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'United States of America', 'fleet' ),
			'en'     => 'United States of America',
			'code'   => 'USA',
			'group'  => 'P',
		),
		array(
			'nation' => esc_html__( 'Estonia', 'fleet' ),
			'en'     => 'Estonia',
			'code'   => 'EST',
			'group'  => 'G',
		),
		array(
			'nation' => esc_html__( 'Myanmar', 'fleet' ),
			'en'     => 'Myanmar',
			'code'   => 'MYA',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Uruguay', 'fleet' ),
			'en'     => 'Uruguay',
			'code'   => 'URU',
			'group'  => 'M',
		),
		array(
			'nation' => esc_html__( 'Fiji', 'fleet' ),
			'en'     => 'Fiji',
			'code'   => 'FIJ',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Namibia', 'fleet' ),
			'en'     => 'Namibia',
			'code'   => 'NAM',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'US Virgin Islands', 'fleet' ),
			'en'     => 'US Virgin Islands',
			'code'   => 'ISV',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'France', 'fleet' ),
			'en'     => 'France',
			'code'   => 'FRA',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Netherlands', 'fleet' ),
			'en'     => 'Netherlands',
			'code'   => 'NED',
			'group'  => 'F',
		),
		array(
			'nation' => esc_html__( 'Vanuatu', 'fleet' ),
			'en'     => 'Vanuatu',
			'code'   => 'VAN',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'FYRO Macedonia', 'fleet' ),
			'en'     => 'FYRO Macedonia',
			'code'   => 'MKD',
			'group'  => 'C',
		),
		array(
			'nation' => esc_html__( 'Netherland Antilles (Curacao and St Maarten)', 'fleet' ),
			'en'     => 'Netherland Antilles (Curacao and St Maarten)',
			'code'   => 'AHO',
			'group'  => 'L',
		),
		array(
			'nation' => esc_html__( 'Venezuela', 'fleet' ),
			'en'     => 'Venezuela',
			'code'   => 'VEN',
			'group'  => 'O',
		),
		array(
			'nation' => esc_html__( 'Georgia', 'fleet' ),
			'en'     => 'Georgia',
			'code'   => 'GEO',
			'group'  => 'H',
		),
		array(
			'nation' => esc_html__( 'New Zealand', 'fleet' ),
			'en'     => 'New Zealand',
			'code'   => 'NZL',
			'group'  => 'Q',
		),
		array(
			'nation' => esc_html__( 'Vietnam', 'fleet' ),
			'en'     => 'Vietnam',
			'code'   => 'VIE',
			'group'  => 'K',
		),
		array(
			'nation' => esc_html__( 'Germany', 'fleet' ),
			'en'     => 'Germany',
			'code'   => 'GER',
			'group'  => 'B',
		),
		array(
			'nation' => esc_html__( 'Nigeria', 'fleet' ),
			'en'     => 'Nigeria',
			'code'   => 'NIG',
			'group'  => 'G',
		),
		array(
			'nation'   => esc_html__( 'Zimbabwe', 'fleet' ),
			'en'       => 'Zimbabwe',
			'code'     => 'ZIM',
			'group'    => 'Q',
			'code_old' => array( 'KR', 'ZB' ),
		),
		array(
			'nation' => esc_html__( 'Great Britain', 'fleet' ),
			'en'     => 'Great Britain',
			'code'   => 'GBR',
			'group'  => 'A',
		),
		array(
			'nation' => esc_html__( 'Norway', 'fleet' ),
			'en'     => 'Norway',
			'code'   => 'NOR',
			'group'  => 'I',
		),
		array(
			'nation' => esc_html__( 'Greece', 'fleet' ),
			'en'     => 'Greece',
			'code'   => 'GRE',
			'group'  => 'D',
		),
		array(
			'nation' => esc_html__( 'Oman', 'fleet' ),
			'en'     => 'Oman',
			'code'   => 'OMA',
			'group'  => '',
		),
	);
}

/**
 * Get nation by code
 *
 * @param string $code The code of the nation
 * @return array|false The nation data or false if not found
 *
 * @access public
 * @since  2.6.0
 */
function iworks_fleet_get_nation_by_code( $code ) {
	$countries = iworks_fleet_get_countries();
	foreach ( $countries as $country ) {
		if ( $country['code'] == $code ) {
			return $country;
		}
	}
	return false;
}

/**
 * Get nation by code
 *
 * Retrieve the nation data array by the given code.
 *
 * @param string $code The code of the nation
 *
 * @return array|false The nation data or false if not found
 *
 * @access public
 * @since  1.0.0
 */
function iworks_fleet_get_us_state_codes() {
	return array(
		'AL' => __( 'Alabama', 'fleet' ),
		'AK' => __( 'Alaska', 'fleet' ),
		'AZ' => __( 'Arizona', 'fleet' ),
		'AR' => __( 'Arkansas', 'fleet' ),
		'AA' => __( 'Armed Forces America', 'fleet' ),
		'AE' => __( 'Armed Forces Europe', 'fleet' ),
		'AP' => __( 'Armed Forces Pacific', 'fleet' ),
		'CA' => __( 'California', 'fleet' ),
		'CO' => __( 'Colorado', 'fleet' ),
		'CT' => __( 'Connecticut', 'fleet' ),
		'DE' => __( 'Delaware', 'fleet' ),
		'DC' => __( 'District of Columbia', 'fleet' ),
		'FL' => __( 'Florida', 'fleet' ),
		'GA' => __( 'Georgia', 'fleet' ),
		'HI' => __( 'Hawaii', 'fleet' ),
		'ID' => __( 'Idaho', 'fleet' ),
		'IL' => __( 'Illinois', 'fleet' ),
		'IN' => __( 'Indiana', 'fleet' ),
		'IA' => __( 'Iowa', 'fleet' ),
		'KS' => __( 'Kansas', 'fleet' ),
		'KY' => __( 'Kentucky', 'fleet' ),
		'LA' => __( 'Louisiana', 'fleet' ),
		'ME' => __( 'Maine', 'fleet' ),
		'MD' => __( 'Maryland', 'fleet' ),
		'MA' => __( 'Massachusetts', 'fleet' ),
		'MI' => __( 'Michigan', 'fleet' ),
		'MN' => __( 'Minnesota', 'fleet' ),
		'MS' => __( 'Mississippi', 'fleet' ),
		'MO' => __( 'Missouri', 'fleet' ),
		'MT' => __( 'Montana', 'fleet' ),
		'NE' => __( 'Nebraska', 'fleet' ),
		'NV' => __( 'Nevada', 'fleet' ),
		'NH' => __( 'New Hampshire', 'fleet' ),
		'NJ' => __( 'New Jersey', 'fleet' ),
		'NM' => __( 'New Mexico', 'fleet' ),
		'NY' => __( 'New York', 'fleet' ),
		'NC' => __( 'North Carolina', 'fleet' ),
		'ND' => __( 'North Dakota', 'fleet' ),
		'OH' => __( 'Ohio', 'fleet' ),
		'OK' => __( 'Oklahoma', 'fleet' ),
		'OR' => __( 'Oregon', 'fleet' ),
		'PA' => __( 'Pennsylvania', 'fleet' ),
		'RI' => __( 'Rhode Island', 'fleet' ),
		'SC' => __( 'South Carolina', 'fleet' ),
		'SD' => __( 'South Dakota', 'fleet' ),
		'TN' => __( 'Tennessee', 'fleet' ),
		'TX' => __( 'Texas', 'fleet' ),
		'UT' => __( 'Utah', 'fleet' ),
		'VT' => __( 'Vermont', 'fleet' ),
		'VA' => __( 'Virginia', 'fleet' ),
		'WA' => __( 'Washington', 'fleet' ),
		'WV' => __( 'West Virginia', 'fleet' ),
		'WI' => __( 'Wisconsin', 'fleet' ),
		'WY' => __( 'Wyoming', 'fleet' ),
	);
}

function iworks_fleet_get_canada_provinces_codes() {
	return array(
		'AB' => __( 'Alberta', 'fleet' ),
		'BC' => __( 'British Columbia', 'fleet' ),
		'MB' => __( 'Manitoba', 'fleet' ),
		'NB' => __( 'New Brunswick', 'fleet' ),
		'NL' => __( 'Newfoundland and Labrador', 'fleet' ),
		'NT' => __( 'Northwest Territories', 'fleet' ),
		'NS' => __( 'Nova Scotia', 'fleet' ),
		'NU' => __( 'Nunavut', 'fleet' ),
		'ON' => __( 'Ontario', 'fleet' ),
		'PE' => __( 'Prince Edward Island', 'fleet' ),
		'QC' => __( 'Quebec', 'fleet' ),
		'SK' => __( 'Saskatchewan', 'fleet' ),
		'YT' => __( 'Yukon', 'fleet' ),
	);
}

function iworks_fleet_get_australia_state_codes() {
	return array(
		'NSW' => __( 'New South Wales', 'fleet' ),
		'NS'  => __( 'New South Wales', 'fleet' ),
		'NT	' => __( 'Northern Territory', 'fleet' ),
		'Qld' => __( 'Queensland', 'fleet' ),
		'QL'  => __( 'Queensland', 'fleet' ),
		'SA	' => __( 'South Australia', 'fleet' ),
		'Tas' => __( 'Tasmania', 'fleet' ),
		'TS'  => __( 'Tasmania', 'fleet' ),
		'Vic' => __( 'Victoria', 'fleet' ),
		'VI'  => __( 'Victoria', 'fleet' ),
		'WA'  => __( 'Western Australia', 'fleet' ),
	);
}


function iworks_fleet_get_countries_select2() {
	$c    = iworks_fleet_get_countries();
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

function iworks_fleet_get_series() {
	return apply_filters( 'iworks_fleet_get_series', array() );
}
