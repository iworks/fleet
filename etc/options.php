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
				'name'              => 'boad_add_owners',
				'type'              => 'checkbox',
				'th'                => __( 'Add boat owners', 'fleet' ),
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

	$iworks_fleet_options['colors'] = array(
		'aliceblue'            => array(
			'name' => __( 'Alice Blue', 'fleet' ),
			'code' => '#f0f8ff',
		),
		'antiquewhite'         => array(
			'name' => __( 'Antique White', 'fleet' ),
			'code' => '#faebd7',
		),
		'aqua'                 => array(
			'name' => __( 'Aqua', 'fleet' ),
			'code' => '#00ffff',
		),
		'aquamarine'           => array(
			'name' => __( 'Aquamarine', 'fleet' ),
			'code' => '#7fffd4',
		),
		'azure'                => array(
			'name' => __( 'Azure', 'fleet' ),
			'code' => '#f0ffff',
		),
		'beige'                => array(
			'name' => __( 'Beige', 'fleet' ),
			'code' => '#f5f5dc',
		),
		'bisque'               => array(
			'name' => __( 'Bisque', 'fleet' ),
			'code' => '#ffe4c4',
		),
		'black'                => array(
			'name' => __( 'Black', 'fleet' ),
			'code' => '#000000',
		),
		'blanchedalmond'       => array(
			'name' => __( 'Blanched Almond', 'fleet' ),
			'code' => '#ffebcd',
		),
		'blue'                 => array(
			'name' => __( 'Blue', 'fleet' ),
			'code' => '#0000ff',
		),
		'blueviolet'           => array(
			'name' => __( 'Blue Violet', 'fleet' ),
			'code' => '#8a2be2',
		),
		'brown'                => array(
			'name' => __( 'Brown', 'fleet' ),
			'code' => '#a52a2a',
		),
		'burlywood'            => array(
			'name' => __( 'Burly Wood', 'fleet' ),
			'code' => '#deb887',
		),
		'cadetblue'            => array(
			'name' => __( 'Cadet Blue', 'fleet' ),
			'code' => '#5f9ea0',
		),
		'chartreuse'           => array(
			'name' => __( 'Chartreuse', 'fleet' ),
			'code' => '#7fff00',
		),
		'chocolate'            => array(
			'name' => __( 'Chocolate', 'fleet' ),
			'code' => '#d2691e',
		),
		'coral'                => array(
			'name' => __( 'Coral', 'fleet' ),
			'code' => '#ff7f50',
		),
		'cornflowerblue'       => array(
			'name' => __( 'Cornflower Blue', 'fleet' ),
			'code' => '#6495ed',
		),
		'cornsilk'             => array(
			'name' => __( 'Cornsilk', 'fleet' ),
			'code' => '#fff8dc',
		),
		'crimson'              => array(
			'name' => __( 'Crimson', 'fleet' ),
			'code' => '#dc143c',
		),
		'cyan'                 => array(
			'name' => __( 'Cyan', 'fleet' ),
			'code' => '#00ffff',
		),
		'darkblue'             => array(
			'name' => __( 'Dark Blue', 'fleet' ),
			'code' => '#00008b',
		),
		'darkcyan'             => array(
			'name' => __( 'Dark Cyan', 'fleet' ),
			'code' => '#008b8b',
		),
		'darkgoldenrod'        => array(
			'name' => __( 'Dark Golden Rod', 'fleet' ),
			'code' => '#b8860b',
		),
		'darkgrey'             => array(
			'name' => __( 'Dark Grey', 'fleet' ),
			'code' => '#a9a9a9',
		),
		'darkgreen'            => array(
			'name' => __( 'Dark Green', 'fleet' ),
			'code' => '#006400',
		),
		'darkkhaki'            => array(
			'name' => __( 'Dark Khaki', 'fleet' ),
			'code' => '#bdb76b',
		),
		'darkmagenta'          => array(
			'name' => __( 'Dark Magenta', 'fleet' ),
			'code' => '#8b008b',
		),
		'darkolivegreen'       => array(
			'name' => __( 'Dark Olive Green', 'fleet' ),
			'code' => '#556b2f',
		),
		'darkorange'           => array(
			'name' => __( 'Dark Orange', 'fleet' ),
			'code' => '#ff8c00',
		),
		'darkorchid'           => array(
			'name' => __( 'Dark Orchid', 'fleet' ),
			'code' => '#9932cc',
		),
		'darkred'              => array(
			'name' => __( 'Dark Red', 'fleet' ),
			'code' => '#8b0000',
		),
		'darksalmon'           => array(
			'name' => __( 'Dark Salmon', 'fleet' ),
			'code' => '#e9967a',
		),
		'darkseagreen'         => array(
			'name' => __( 'Dark Sea Green', 'fleet' ),
			'code' => '#8fbc8f',
		),
		'darkslateblue'        => array(
			'name' => __( 'Dark Slate Blue', 'fleet' ),
			'code' => '#483D8B',
		),
		'DarkSlateGray'        => array(
			'name' => __( 'DarkSlateGray', 'fleet' ),
			'code' => '#2F4F4F',
		),
		'DarkSlateGrey'        => array(
			'name' => __( 'DarkSlateGrey', 'fleet' ),
			'code' => '#2F4F4F',
		),
		'DarkTurquoise'        => array(
			'name' => __( 'DarkTurquoise', 'fleet' ),
			'code' => '#00CED1',
		),
		'DarkViolet'           => array(
			'name' => __( 'DarkViolet', 'fleet' ),
			'code' => '#9400D3',
		),
		'DeepPink'             => array(
			'name' => __( 'DeepPink', 'fleet' ),
			'code' => '#FF1493',
		),
		'DeepSkyBlue'          => array(
			'name' => __( 'DeepSkyBlue', 'fleet' ),
			'code' => '#00BFFF',
		),
		'DimGray'              => array(
			'name' => __( 'DimGray', 'fleet' ),
			'code' => '#696969',
		),
		'DimGrey'              => array(
			'name' => __( 'DimGrey', 'fleet' ),
			'code' => '#696969',
		),
		'DodgerBlue'           => array(
			'name' => __( 'DodgerBlue', 'fleet' ),
			'code' => '#1E90FF',
		),
		'FireBrick'            => array(
			'name' => __( 'FireBrick', 'fleet' ),
			'code' => '#B22222',
		),
		'FloralWhite'          => array(
			'name' => __( 'FloralWhite', 'fleet' ),
			'code' => '#FFFAF0',
		),
		'ForestGreen'          => array(
			'name' => __( 'ForestGreen', 'fleet' ),
			'code' => '#228B22',
		),
		'Fuchsia'              => array(
			'name' => __( 'Fuchsia', 'fleet' ),
			'code' => '#FF00FF',
		),
		'Gainsboro'            => array(
			'name' => __( 'Gainsboro', 'fleet' ),
			'code' => '#DCDCDC',
		),
		'GhostWhite'           => array(
			'name' => __( 'GhostWhite', 'fleet' ),
			'code' => '#F8F8FF',
		),
		'Gold'                 => array(
			'name' => __( 'Gold', 'fleet' ),
			'code' => '#FFD700',
		),
		'GoldenRod'            => array(
			'name' => __( 'GoldenRod', 'fleet' ),
			'code' => '#DAA520',
		),
		'Gray'                 => array(
			'name' => __( 'Gray', 'fleet' ),
			'code' => '#808080',
		),
		'Grey'                 => array(
			'name' => __( 'Grey', 'fleet' ),
			'code' => '#808080',
		),
		'Green'                => array(
			'name' => __( 'Green', 'fleet' ),
			'code' => '#008000',
		),
		'GreenYellow'          => array(
			'name' => __( 'GreenYellow', 'fleet' ),
			'code' => '#ADFF2F',
		),
		'HoneyDew'             => array(
			'name' => __( 'HoneyDew', 'fleet' ),
			'code' => '#F0FFF0',
		),
		'HotPink'              => array(
			'name' => __( 'HotPink', 'fleet' ),
			'code' => '#FF69B4',
		),
		'IndianRed#CD5C5C'     => array( 'name' => __( 'IndianRed#CD5C5C', 'fleet' ) ),
		'Indigo#4B0082'        => array( 'name' => __( 'Indigo#4B0082', 'fleet' ) ),
		'Ivory'                => array(
			'name' => __( 'Ivory', 'fleet' ),
			'code' => '#FFFFF0',
		),
		'Khaki'                => array(
			'name' => __( 'Khaki', 'fleet' ),
			'code' => '#F0E68C',
		),
		'Lavender'             => array(
			'name' => __( 'Lavender', 'fleet' ),
			'code' => '#E6E6FA',
		),
		'LavenderBlush'        => array(
			'name' => __( 'LavenderBlush', 'fleet' ),
			'code' => '#FFF0F5',
		),
		'LawnGreen'            => array(
			'name' => __( 'LawnGreen', 'fleet' ),
			'code' => '#7CFC00',
		),
		'LemonChiffon'         => array(
			'name' => __( 'LemonChiffon', 'fleet' ),
			'code' => '#FFFACD',
		),
		'LightBlue'            => array(
			'name' => __( 'LightBlue', 'fleet' ),
			'code' => '#ADD8E6',
		),
		'LightCoral'           => array(
			'name' => __( 'LightCoral', 'fleet' ),
			'code' => '#F08080',
		),
		'LightCyan'            => array(
			'name' => __( 'LightCyan', 'fleet' ),
			'code' => '#E0FFFF',
		),
		'LightGoldenRodYellow' => array(
			'name' => __( 'LightGoldenRodYellow', 'fleet' ),
			'code' => '#FAFAD2',
		),
		'LightGray'            => array(
			'name' => __( 'LightGray', 'fleet' ),
			'code' => '#D3D3D3',
		),
		'LightGrey'            => array(
			'name' => __( 'LightGrey', 'fleet' ),
			'code' => '#D3D3D3',
		),
		'LightGreen'           => array(
			'name' => __( 'LightGreen', 'fleet' ),
			'code' => '#90EE90',
		),
		'LightPink'            => array(
			'name' => __( 'LightPink', 'fleet' ),
			'code' => '#FFB6C1',
		),
		'LightSalmon'          => array(
			'name' => __( 'LightSalmon', 'fleet' ),
			'code' => '#FFA07A',
		),
		'LightSeaGreen'        => array(
			'name' => __( 'LightSeaGreen', 'fleet' ),
			'code' => '#20B2AA',
		),
		'LightSkyBlue'         => array(
			'name' => __( 'LightSkyBlue', 'fleet' ),
			'code' => '#87CEFA',
		),
		'LightSlateGray'       => array(
			'name' => __( 'LightSlateGray', 'fleet' ),
			'code' => '#778899',
		),
		'LightSlateGrey'       => array(
			'name' => __( 'LightSlateGrey', 'fleet' ),
			'code' => '#778899',
		),
		'LightSteelBlue'       => array(
			'name' => __( 'LightSteelBlue', 'fleet' ),
			'code' => '#B0C4DE',
		),
		'LightYellow'          => array(
			'name' => __( 'LightYellow', 'fleet' ),
			'code' => '#FFFFE0',
		),
		'Lime'                 => array(
			'name' => __( 'Lime', 'fleet' ),
			'code' => '#00FF00',
		),
		'LimeGreen'            => array(
			'name' => __( 'LimeGreen', 'fleet' ),
			'code' => '#32CD32',
		),
		'Linen'                => array(
			'name' => __( 'Linen', 'fleet' ),
			'code' => '#FAF0E6',
		),
		'Magenta'              => array(
			'name' => __( 'Magenta', 'fleet' ),
			'code' => '#FF00FF',
		),
		'Maroon'               => array(
			'name' => __( 'Maroon', 'fleet' ),
			'code' => '#800000',
		),
		'MediumAquaMarine'     => array(
			'name' => __( 'MediumAquaMarine', 'fleet' ),
			'code' => '#66CDAA',
		),
		'MediumBlue'           => array(
			'name' => __( 'MediumBlue', 'fleet' ),
			'code' => '#0000CD',
		),
		'MediumOrchid'         => array(
			'name' => __( 'MediumOrchid', 'fleet' ),
			'code' => '#BA55D3',
		),
		'MediumPurple'         => array(
			'name' => __( 'MediumPurple', 'fleet' ),
			'code' => '#9370D8',
		),
		'MediumSeaGreen'       => array(
			'name' => __( 'MediumSeaGreen', 'fleet' ),
			'code' => '#3CB371',
		),
		'MediumSlateBlue'      => array(
			'name' => __( 'MediumSlateBlue', 'fleet' ),
			'code' => '#7B68EE',
		),
		'MediumSpringGreen'    => array(
			'name' => __( 'MediumSpringGreen', 'fleet' ),
			'code' => '#00FA9A',
		),
		'MediumTurquoise'      => array(
			'name' => __( 'MediumTurquoise', 'fleet' ),
			'code' => '#48D1CC',
		),
		'MediumVioletRed'      => array(
			'name' => __( 'MediumVioletRed', 'fleet' ),
			'code' => '#C71585',
		),
		'MidnightBlue'         => array(
			'name' => __( 'MidnightBlue', 'fleet' ),
			'code' => '#191970',
		),
		'MintCream'            => array(
			'name' => __( 'MintCream', 'fleet' ),
			'code' => '#F5FFFA',
		),
		'MistyRose'            => array(
			'name' => __( 'MistyRose', 'fleet' ),
			'code' => '#FFE4E1',
		),
		'Moccasin'             => array(
			'name' => __( 'Moccasin', 'fleet' ),
			'code' => '#FFE4B5',
		),
		'NavajoWhite'          => array(
			'name' => __( 'NavajoWhite', 'fleet' ),
			'code' => '#FFDEAD',
		),
		'Navy'                 => array(
			'name' => __( 'Navy', 'fleet' ),
			'code' => '#000080',
		),
		'OldLace'              => array(
			'name' => __( 'OldLace', 'fleet' ),
			'code' => '#FDF5E6',
		),
		'Olive'                => array(
			'name' => __( 'Olive', 'fleet' ),
			'code' => '#808000',
		),
		'OliveDrab'            => array(
			'name' => __( 'OliveDrab', 'fleet' ),
			'code' => '#6B8E23',
		),
		'Orange'               => array(
			'name' => __( 'Orange', 'fleet' ),
			'code' => '#FFA500',
		),
		'OrangeRed'            => array(
			'name' => __( 'OrangeRed', 'fleet' ),
			'code' => '#FF4500',
		),
		'Orchid'               => array(
			'name' => __( 'Orchid', 'fleet' ),
			'code' => '#DA70D6',
		),
		'PaleGoldenRod'        => array(
			'name' => __( 'PaleGoldenRod', 'fleet' ),
			'code' => '#EEE8AA',
		),
		'PaleGreen'            => array(
			'name' => __( 'PaleGreen', 'fleet' ),
			'code' => '#98FB98',
		),
		'PaleTurquoise'        => array(
			'name' => __( 'PaleTurquoise', 'fleet' ),
			'code' => '#AFEEEE',
		),
		'PaleVioletRed'        => array(
			'name' => __( 'PaleVioletRed', 'fleet' ),
			'code' => '#D87093',
		),
		'PapayaWhip'           => array(
			'name' => __( 'PapayaWhip', 'fleet' ),
			'code' => '#FFEFD5',
		),
		'PeachPuff'            => array(
			'name' => __( 'PeachPuff', 'fleet' ),
			'code' => '#FFDAB9',
		),
		'Peru'                 => array(
			'name' => __( 'Peru', 'fleet' ),
			'code' => '#CD853F',
		),
		'Pink'                 => array(
			'name' => __( 'Pink', 'fleet' ),
			'code' => '#FFC0CB',
		),
		'Plum'                 => array(
			'name' => __( 'Plum', 'fleet' ),
			'code' => '#DDA0DD',
		),
		'PowderBlue'           => array(
			'name' => __( 'PowderBlue', 'fleet' ),
			'code' => '#B0E0E6',
		),
		'Purple'               => array(
			'name' => __( 'Purple', 'fleet' ),
			'code' => '#800080',
		),
		'Red'                  => array(
			'name' => __( 'Red', 'fleet' ),
			'code' => '#FF0000',
		),
		'RosyBrown'            => array(
			'name' => __( 'RosyBrown', 'fleet' ),
			'code' => '#BC8F8F',
		),
		'RoyalBlue'            => array(
			'name' => __( 'RoyalBlue', 'fleet' ),
			'code' => '#4169E1',
		),
		'SaddleBrown'          => array(
			'name' => __( 'SaddleBrown', 'fleet' ),
			'code' => '#8B4513',
		),
		'Salmon'               => array(
			'name' => __( 'Salmon', 'fleet' ),
			'code' => '#FA8072',
		),
		'SandyBrown'           => array(
			'name' => __( 'SandyBrown', 'fleet' ),
			'code' => '#F4A460',
		),
		'SeaGreen'             => array(
			'name' => __( 'SeaGreen', 'fleet' ),
			'code' => '#2E8B57',
		),
		'SeaShell'             => array(
			'name' => __( 'SeaShell', 'fleet' ),
			'code' => '#FFF5EE',
		),
		'Sienna'               => array(
			'name' => __( 'Sienna', 'fleet' ),
			'code' => '#A0522D',
		),
		'Silver'               => array(
			'name' => __( 'Silver', 'fleet' ),
			'code' => '#C0C0C0',
		),
		'SkyBlue'              => array(
			'name' => __( 'SkyBlue', 'fleet' ),
			'code' => '#87CEEB',
		),
		'SlateBlue'            => array(
			'name' => __( 'SlateBlue', 'fleet' ),
			'code' => '#6A5ACD',
		),
		'SlateGray'            => array(
			'name' => __( 'SlateGray', 'fleet' ),
			'code' => '#708090',
		),
		'SlateGrey'            => array(
			'name' => __( 'SlateGrey', 'fleet' ),
			'code' => '#708090',
		),
		'Snow'                 => array(
			'name' => __( 'Snow', 'fleet' ),
			'code' => '#FFFAFA',
		),
		'SpringGreen'          => array(
			'name' => __( 'SpringGreen', 'fleet' ),
			'code' => '#00FF7F',
		),
		'SteelBlue'            => array(
			'name' => __( 'SteelBlue', 'fleet' ),
			'code' => '#4682B4',
		),
		'Tan'                  => array(
			'name' => __( 'Tan', 'fleet' ),
			'code' => '#D2B48C',
		),
		'Teal'                 => array(
			'name' => __( 'Teal', 'fleet' ),
			'code' => '#008080',
		),
		'Thistle'              => array(
			'name' => __( 'Thistle', 'fleet' ),
			'code' => '#D8BFD8',
		),
		'Tomato'               => array(
			'name' => __( 'Tomato', 'fleet' ),
			'code' => '#FF6347',
		),
		'Turquoise'            => array(
			'name' => __( 'Turquoise', 'fleet' ),
			'code' => '#40E0D0',
		),
		'Violet'               => array(
			'name' => __( 'Violet', 'fleet' ),
			'code' => '#EE82EE',
		),
		'Wheat'                => array(
			'name' => __( 'Wheat', 'fleet' ),
			'code' => '#F5DEB3',
		),
		'White'                => array(
			'name' => __( 'White', 'fleet' ),
			'code' => '#FFFFFF',
		),
		'WhiteSmoke'           => array(
			'name' => __( 'WhiteSmoke', 'fleet' ),
			'code' => '#F5F5F5',
		),
		'Yellow'               => array(
			'name' => __( 'Yellow', 'fleet' ),
			'code' => '#FFFF00',
		),
		'YellowGreen'          => array(
			'name' => __( 'YellowGreen', 'fleet' ),
			'code' => '#9ACD32',
		),
	);

	return$iworks_fleet_options;
}

