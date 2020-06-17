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
				'name'              => 'boad_add_social_media',
				'type'              => 'checkbox',
				'th'                => __( 'Add boat social media', 'fleet' ),
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
	$iworks_fleet_options['mna_codes'] = array(
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
			'group'  => __( 'Assoc', 'fleet' ),
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

	return $iworks_fleet_options;
}

