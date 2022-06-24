<?php
/*
Copyright 2017-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_fleet_posttypes_boat' ) ) {
	return;
}

require_once dirname( dirname( __FILE__ ) ) . '/posttypes.php';

class iworks_fleet_posttypes_boat extends iworks_fleet_posttypes {

	protected $post_type_name             = 'iworks_fleet_boat';
	protected $taxonomy_name_manufacturer = 'iworks_fleet_boat_manufacturer';
	protected $taxonomy_name_sails        = 'iworks_fleet_sails_manufacturer';
	protected $taxonomy_name_mast         = 'iworks_fleet_mast_manufacturer';
	/**
	 * Sinle crew meta field name
	 */
	private $single_crew_field_name = 'iworks_fleet_boat_crew';
	/**
	 * Single boat meta field name
	 */
	private $single_boat_field_name = 'iworks_fleet_boat_boat';

	/**
	 * Boat owners meta field name (single fields)
	 *
	 * @since 1.3.0
	 */
	private $owners_field_name = 'iworks_fleet_boat_owners';

	/**
	 * Boat owner ID meta field name (multiple field)
	 *
	 * @since 1.3.0
	 */
	private $owners_index_field_name = 'iworks_fleet_boot_owner_id';

	public function __construct() {
		parent::__construct();
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 10, 2 );
		add_filter( 'the_content', array( $this, 'the_content' ), 10 );
		add_filter( 'the_content', array( $this, 'add_media' ), 999 );
		add_filter( 'international_fleet_posted_on', array( $this, 'get_manufacturer' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby_post_title' ), 10, 2 );
		/**
		 * save post
		 */
		add_action( 'save_post', array( $this, 'add_thumbnail' ), 10, 3 );
		add_action( 'save_post', array( $this, 'save_post_owners_save' ), 10, 3 );
		/**
		 * change default columns
		 */
		add_filter( "manage_{$this->get_name()}_posts_columns", array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
		/**
		 * apply default sort order
		 */
		add_action( 'pre_get_posts', array( $this, 'apply_default_sort_order' ) );
		add_action( 'pre_get_posts', array( $this, 'apply_countries_selector' ) );
		/**
		 * sort next/previous links by title
		 */
		add_filter( 'get_previous_post_sort', array( $this, 'adjacent_post_sort' ), 10, 3 );
		add_filter( 'get_next_post_sort', array( $this, 'adjacent_post_sort' ), 10, 3 );
		add_filter( 'get_previous_post_where', array( $this, 'adjacent_post_where' ), 10, 5 );
		add_filter( 'get_next_post_where', array( $this, 'adjacent_post_where' ), 10, 5 );
		/**
		 * add crew to a boat
		 */
		add_action( 'international_fleet_content_template_overlay_end', array( $this, 'add_crew_to_boat' ), 10, 1 );
		/**
		 * get boat data
		 */
		add_filter( 'iworks_fleet_boat_get_flag', array( $this, 'get_flag_filter' ), 10, 2 );
		add_filter( 'iworks_fleet_boat_get_hull', array( $this, 'get_hull_filter' ), 10, 2 );
		add_filter( 'iworks_fleet_boat_get_last_owner', array( $this, 'filter_get_last_owner' ), 10, 2 );
		/**
		 * replace names to proper
		 */
		if ( is_a( $this->options, 'iworks_options' ) ) {
			/**
			 * Sinle crew meta field name
			 */
			$this->single_crew_field_name = $this->options->get_option_name( 'crew' );
			/**
			 * Single boat meta field name
			 */
			$this->single_boat_field_name = $this->options->get_option_name( 'boat', true );
		}
		/**
		 * get owner boats
		 */
		add_filter( 'iworks_fleet_boat_get_by_owner_id', array( $this, 'get_content_table_by_owner_id' ), 10, 3 );
		/**
		 * fields
		 */
		$this->fields = array(
			'crew'   => array(),
			'boat'   => array(
				'nation'               => array(
					'label'   => __( 'Current nation', 'fleet' ),
					'type'    => 'select2',
					'args'    => array(
						'options' => $this->get_nations(),
					),
					'twitter' => 'yes',
				),
				'build_year'           => array(
					'label'   => __( 'Year of building', 'fleet' ),
					'twitter' => 'yes',
				),
				'hull_number'          => array(
					'label'   => __( 'Hull number', 'fleet' ),
					'twitter' => 'yes',
				),
				'name'                 => array(
					'label'   => __( 'Boat name', 'fleet' ),
					'twitter' => 'yes',
				),
				'color_top'            => array(
					'label' => __( 'Color top', 'fleet' ),
					'type'  => 'select2',
					'args'  => array(
						'options' => $this->get_colors(),
					),
				),
				'color_side'           => array(
					'label' => __( 'Color side', 'fleet' ),
					'type'  => 'select2',
					'args'  => array(
						'options' => $this->get_colors(),
					),
				),
				'color_bottom'         => array(
					'label' => __( 'Color bottom', 'fleet' ),
					'type'  => 'select2',
					'args'  => array(
						'options' => $this->get_colors(),
					),
				),
				'location'             => array( 'label' => __( 'Location', 'fleet' ) ),
				'hull_material'        => array(
					'label' => __( 'Hull material', 'fleet' ),
					'type'  => 'select',
					'args'  => array(
						'options' => array(
							''           => __( '--- select hull material ---', 'fleet' ),
							'mixed'      => __( 'Mixed', 'fleet' ),
							'carbon'     => __( 'Carbon', 'fleet' ),
							'plywood'    => __( 'Plywood', 'fleet' ),
							'gpr'        => __( 'GPR', 'fleet' ),
							'fibreglass' => __( 'Fiberglass', 'fleet' ),
							'composite'  => __( 'Composite', 'fleet' ),
							'wood'       => __( 'Wood', 'fleet' ),
							'kevlar'     => __( 'Kevlar', 'fleet' ),
							''           => __( '', 'fleet' ),
						),
					),
				),
				'first_certified_date' => array(
					'type'    => 'date',
					'label'   => __( 'First Certified', 'fleet' ),
					'twitter' => 'yes',
				),
			),
			'social' => array(
				'website'   => array( 'label' => __( 'Web site', 'fleet' ) ),
				'facebook'  => array( 'label' => __( 'Facebook', 'fleet' ) ),
				'twitter'   => array( 'label' => __( 'Twitter', 'fleet' ) ),
				'instagram' => array( 'label' => __( 'Instagram', 'fleet' ) ),
			),
		);
		/**
		 * add class to metaboxes
		 */
		foreach ( array_keys( $this->fields ) as $name ) {
			if ( 'basic' == $name ) {
				continue;
			}
			$key = sprintf( 'postbox_classes_%s_%s', $this->get_name(), $name );
			add_filter( $key, array( $this, 'add_defult_class_to_postbox' ) );
		}
		/**
		 * shortcodes
		 */
		add_shortcode( 'fleet_boats_list', array( $this, 'shortcode_list' ) );
		add_shortcode( 'boat', array( $this, 'shortcode_boat' ) );
	}

	public function shortcode_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'location'     => null,
				'show_counter' => true,
			),
			$atts,
			'fleet_boats_list'
		);
		/**
		 * params: location
		 */
		$location = $atts['location'];
		/**
		 * WP Query base args
		 */
		$args = array(
			'post_type' => $this->post_type_name,
			'nopaging'  => true,
			'orderby'   => 'post_title',
		);
		/**
		 * location
		 */
		if ( ! empty( $location ) ) {
			if ( preg_match( '/^[\d+, ]$/', $location ) ) {
				$locations         = array_map( 'trim', explode( ',', $location ) );
				$args['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy_name_location,
						'terms'    => $locations,
					),
				);
			} else {
				$locations         = array_map( 'trim', explode( ',', $location ) );
				$args['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy_name_location,
						'field'    => 'name',
						'terms'    => $locations,
					),
				);
			}
		}
		/**
		 * start
		 */
		$format  = get_option( 'date_format' );
		$content = '';
		/**
		 * WP_Query
		 */
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			$content .= '<div class="iworks-fleet-location">';
			if ( $atts['show_counter'] ) {
				$content .= sprintf(
					'<span class="iworks-fleet-list-count">%s</span>',
					sprintf(
						esc_html_x( 'Number of boats: %1$d.', 'number of boats', 'fleet' ),
						$the_query->found_posts
					)
				);
			}
			$content .= '<ul class="iworks-fleet-list">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$content .= sprintf(
					'<li><a href="%s">%s</a></li>',
					get_permalink(),
					get_the_title()
				);
			}
			$content .= '</ul>';
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		return $content;
	}

	/**
	 * Add default class to postbox,
	 */
	public function add_defult_class_to_postbox( $classes ) {
		$classes[] = 'iworks-type';
		return $classes;
	}

	public function register() {
		/**
		 * Check iworks_options object
		 */
		if ( ! is_a( $this->options, 'iworks_options' ) ) {
			return;
		}
		global $iworks_fleet;
		/**
		 * taxonomies configuration
		 */
		$taxonomies            = $this->options->get_option( 'boat_taxonomies' );
		$show_in_menu          = add_query_arg( 'post_type', $iworks_fleet->get_post_type_name( 'person' ), 'edit.php' );
		$labels                = array(
			'name'                  => _x( 'Boats', 'Boat General Name', 'fleet' ),
			'singular_name'         => _x( 'Boat', 'Boat Singular Name', 'fleet' ),
			'menu_name'             => __( '5O5', 'fleet' ),
			'name_admin_bar'        => __( 'Boat', 'fleet' ),
			'archives'              => __( 'Boats', 'fleet' ),
			'attributes'            => __( 'Item Attributes', 'fleet' ),
			'all_items'             => __( 'Boats', 'fleet' ),
			'add_new_item'          => __( 'Add New Boat', 'fleet' ),
			'add_new'               => __( 'Add New', 'fleet' ),
			'new_item'              => __( 'New Boat', 'fleet' ),
			'edit_item'             => __( 'Edit Boat', 'fleet' ),
			'update_item'           => __( 'Update Boat', 'fleet' ),
			'view_item'             => __( 'View Boat', 'fleet' ),
			'view_items'            => __( 'View Boats', 'fleet' ),
			'search_items'          => __( 'Search Boat', 'fleet' ),
			'not_found'             => __( 'Not found', 'fleet' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'fleet' ),
			'featured_image'        => __( 'Featured Image', 'fleet' ),
			'set_featured_image'    => __( 'Set featured image', 'fleet' ),
			'remove_featured_image' => __( 'Remove featured image', 'fleet' ),
			'use_featured_image'    => __( 'Use as featured image', 'fleet' ),
			'insert_into_item'      => __( 'Insert into item', 'fleet' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'fleet' ),
			'items_list'            => __( 'Items list', 'fleet' ),
			'items_list_navigation' => __( 'Items list navigation', 'fleet' ),
			'filter_items_list'     => __( 'Filter items list', 'fleet' ),
		);
		$fleet_boat_taxonomies = array(
			$this->taxonomy_name_location,
		);
		foreach ( $fleet_boat_taxonomies as $key => $value ) {
			switch ( $key ) {
				case 'hull':
					$fleet_boat_taxonomies[] = $this->taxonomy_name_manufacturer;
					break;
				case 'sails':
					$fleet_boat_taxonomies[] = $this->taxonomy_name_sails;
					break;
				case 'mast':
					$fleet_boat_taxonomies[] = $this->taxonomy_name_mast;
					break;
			}
		}
		$args = array(
			'label'                => __( 'Boat', 'fleet' ),
			'labels'               => $labels,
			'supports'             => array( 'title', 'editor', 'thumbnail', 'revision' ),
			'taxonomies'           => $fleet_boat_taxonomies,
			'hierarchical'         => false,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => $show_in_menu,
			'show_in_admin_bar'    => true,
			'show_in_nav_menus'    => true,
			'can_export'           => true,
			'has_archive'          => _x( 'fleet-boats', 'slug for archive', 'fleet' ),
			'exclude_from_search'  => false,
			'publicly_queryable'   => true,
			'capability_type'      => 'page',
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
			'rewrite'              => array(
				'slug' => _x( 'fleet-boat', 'slug for single boat', 'fleet' ),
			),
		);
		register_post_type( $this->post_type_name, $args );
		/**
		 * Boat hull Manufacturer Taxonomy.
		 */
		if ( isset( $taxonomies['hull'] ) && $taxonomies['hull'] ) {
			$labels = array(
				'name'                       => _x( 'Hull Manufacturers', 'Taxonomy General Name', 'fleet' ),
				'singular_name'              => _x( 'Hull Manufacturer', 'Taxonomy Singular Name', 'fleet' ),
				'menu_name'                  => __( 'Hull Manufacturer', 'fleet' ),
				'all_items'                  => __( 'All Hull Manufacturers', 'fleet' ),
				'new_item_name'              => __( 'New Hull Manufacturer Name', 'fleet' ),
				'add_new_item'               => __( 'Add New Hull Manufacturer', 'fleet' ),
				'edit_item'                  => __( 'Edit Hull Manufacturer', 'fleet' ),
				'update_item'                => __( 'Update Hull Manufacturer', 'fleet' ),
				'view_item'                  => __( 'View Hull Manufacturer', 'fleet' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'fleet' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'fleet' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
				'popular_items'              => __( 'Popular Hull Manufacturers', 'fleet' ),
				'search_items'               => __( 'Search Hull Manufacturers', 'fleet' ),
				'not_found'                  => __( 'Not Found', 'fleet' ),
				'no_terms'                   => __( 'No items', 'fleet' ),
				'items_list'                 => __( 'Hull Manufacturers list', 'fleet' ),
				'items_list_navigation'      => __( 'Hull Manufacturers list navigation', 'fleet' ),
			);
			$args   = array(
				'labels'             => $labels,
				'hierarchical'       => false,
				'public'             => true,
				'show_admin_column'  => true,
				'show_in_nav_menus'  => true,
				'show_tagcloud'      => true,
				'show_ui'            => true,
				'show_in_quick_edit' => true,
				'rewrite'            => array(
					'slug' => 'fleet-manufacturer',
				),
			);
			register_taxonomy( $this->taxonomy_name_manufacturer, array( $this->post_type_name ), $args );
		}
		/**
		 * Sails Sails Manufacturer Taxonomy.
		 */
		if ( isset( $taxonomies['sails'] ) && $taxonomies['sails'] ) {
			$labels = array(
				'name'                       => _x( 'Sails Manufacturers', 'Taxonomy General Name', 'fleet' ),
				'singular_name'              => _x( 'Sails Manufacturer', 'Taxonomy Singular Name', 'fleet' ),
				'menu_name'                  => __( 'Sails Manufacturer', 'fleet' ),
				'all_items'                  => __( 'All Sails Manufacturers', 'fleet' ),
				'new_item_name'              => __( 'New Sails Manufacturer Name', 'fleet' ),
				'add_new_item'               => __( 'Add New Sails Manufacturer', 'fleet' ),
				'edit_item'                  => __( 'Edit Sails Manufacturer', 'fleet' ),
				'update_item'                => __( 'Update Sails Manufacturer', 'fleet' ),
				'view_item'                  => __( 'View Sails Manufacturer', 'fleet' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'fleet' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'fleet' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
				'popular_items'              => __( 'Popular Sails Manufacturers', 'fleet' ),
				'search_items'               => __( 'Search Sails Manufacturers', 'fleet' ),
				'not_found'                  => __( 'Not Found', 'fleet' ),
				'no_terms'                   => __( 'No items', 'fleet' ),
				'items_list'                 => __( 'Sails Manufacturers list', 'fleet' ),
				'items_list_navigation'      => __( 'Sails Manufacturers list navigation', 'fleet' ),
			);
			$args   = array(
				'labels'             => $labels,
				'hierarchical'       => false,
				'public'             => true,
				'show_admin_column'  => true,
				'show_in_nav_menus'  => true,
				'show_tagcloud'      => true,
				'show_ui'            => true,
				'show_in_quick_edit' => true,
				'rewrite'            => array(
					'slug' => 'fleet-sails-manufacturer',
				),
			);
			register_taxonomy( $this->taxonomy_name_sails, array( $this->post_type_name ), $args );
		}
		/**
		 * Masts Manufacturer Taxonomy.
		 */
		if ( isset( $taxonomies['mast'] ) && $taxonomies['mast'] ) {
			$labels = array(
				'name'                       => _x( 'Masts Manufacturers', 'Taxonomy General Name', 'fleet' ),
				'singular_name'              => _x( 'Masts Manufacturer', 'Taxonomy Singular Name', 'fleet' ),
				'menu_name'                  => __( 'Masts Manufacturer', 'fleet' ),
				'all_items'                  => __( 'All Masts Manufacturers', 'fleet' ),
				'new_item_name'              => __( 'New Masts Manufacturer Name', 'fleet' ),
				'add_new_item'               => __( 'Add New Masts Manufacturer', 'fleet' ),
				'edit_item'                  => __( 'Edit Masts Manufacturer', 'fleet' ),
				'update_item'                => __( 'Update Masts Manufacturer', 'fleet' ),
				'view_item'                  => __( 'View Masts Manufacturer', 'fleet' ),
				'separate_items_with_commas' => __( 'Separate items with commas', 'fleet' ),
				'add_or_remove_items'        => __( 'Add or remove items', 'fleet' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
				'popular_items'              => __( 'Popular Masts Manufacturers', 'fleet' ),
				'search_items'               => __( 'Search Masts Manufacturers', 'fleet' ),
				'not_found'                  => __( 'Not Found', 'fleet' ),
				'no_terms'                   => __( 'No items', 'fleet' ),
				'items_list'                 => __( 'Masts Manufacturers list', 'fleet' ),
				'items_list_navigation'      => __( 'Masts Manufacturers list navigation', 'fleet' ),
			);
			$args   = array(
				'labels'             => $labels,
				'hierarchical'       => false,
				'public'             => true,
				'show_admin_column'  => true,
				'show_in_nav_menus'  => true,
				'show_tagcloud'      => true,
				'show_ui'            => true,
				'show_in_quick_edit' => true,
				'rewrite'            => array(
					'slug' => 'fleet-masts-manufacturer',
				),
			);
			register_taxonomy( $this->taxonomy_name_mast, array( $this->post_type_name ), $args );
		}
	}

	public function save_post_meta( $post_id, $post, $update ) {
		$result = $this->save_post_meta_fields( $post_id, $post, $update, $this->fields );
		if ( ! $result ) {
			return;
		}
		/**
		 * save crews
		 */
		if (
			$this->options->get_option( 'boat_add_crew_manually' )
			&& isset( $_POST[ $this->single_crew_field_name ] )
		) {
			$value = $_POST[ $this->single_crew_field_name ];
			if ( ! isset( $value['crew'] ) ) {
				$value['crew'] = array();
			}
			/**
			 * clear empty data
			 */
			foreach ( $value['crew'] as $key => $data ) {
				if ( isset( $data['helmsman'] ) && ! empty( $data['helmsman'] ) ) {
					continue;
				}
				if ( isset( $data['crew'] ) && ! empty( $data['crew'] ) ) {
					continue;
				}
				unset( $value['crew'][ $key ] );
			}
			/**
			 * prepare to add to persons
			 */
			$before = $this->get_crews_data( $post_id );
			if ( ! isset( $before['crew'] ) ) {
				$before['crew'] = array();
			}
			$added_users = array();
			/**
			 * delete if empty
			 */
			if ( empty( $value['crew'] ) ) {
				delete_post_meta( $post_id, $this->single_crew_field_name );
			} else {
				$result = add_post_meta( $post_id, $this->single_crew_field_name, $value, true );
				if ( ! $result ) {
					update_post_meta( $post_id, $this->single_crew_field_name, $value );
				}
				foreach ( $value['crew'] as $key => $data ) {
					if ( isset( $data['helmsman'] ) && ! empty( $data['helmsman'] ) ) {
						$added_users[] = $data['helmsman'];
					}
					if ( isset( $data['crew'] ) && ! empty( $data['crew'] ) ) {
						$added_users[] = $data['crew'];
					}
				}
			}
			/**
			 * remove users
			 */
			foreach ( $before['crew'] as $key => $data ) {
				foreach ( array( 'helmsman', 'crew' ) as $key ) {
					if ( isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ) {
						$user_post_id = $data[ $key ];
						if ( ! in_array( $user_post_id, $added_users ) ) {
							delete_post_meta( $user_post_id, $this->single_boat_field_name, $post_id );
						}
					}
				}
			}
			/**
			 * add boat meta to user
			 */
			foreach ( $added_users as $user_post_id ) {
				$assigned_boats = get_post_meta( $user_post_id, $this->single_boat_field_name );
				if ( ! in_array( $post_id, $assigned_boats ) ) {
					add_post_meta( $user_post_id, $this->single_boat_field_name, $post_id, false );
				}
			}
		}
	}

	/**
	 * Change "Enter title here" to "Enter boat number"
	 *
	 * @since 1.0
	 */
	public function enter_title_here( $title, $post ) {
		if ( $post->post_type == $this->post_type_name ) {
			return __( 'Enter boat number eg. POL 7020', 'fleet' );
		}
		return $title;
	}

	/**
	 *
	 * @since 1.0
	 */
	public function the_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		$post_type = get_post_type();
		if ( $post_type != $this->post_type_name ) {
			return $content;
		}
		global $iworks_fleet;
		$post_id    = get_the_ID();
		$text       = '';
		$options    = array(
			'boat_build_year'           => __( 'Year of building', 'fleet' ),
			'manufacturer'              => __( 'Hull manufacturer', 'fleet' ),
			'boat_first_certified_date' => __( 'First certified date', 'fleet' ),
			'boat_hull_material'        => __( 'Hull material', 'fleet' ),
			'boat_in_poland_date'       => __( 'In Poland', 'fleet' ),
			'boat_name'                 => __( 'Name', 'fleet' ),
			'colors'                    => __( 'Colors', 'fleet' ),
			'sails'                     => __( 'Sails manufacturer', 'fleet' ),
			'mast'                      => __( 'Mast manufacturer', 'fleet' ),
			'boat_location'             => __( 'Location', 'fleet' ),
			'social_website'            => __( 'Web site', 'fleet' ),
			'social'                    => __( 'Social Media', 'fleet' ),
		);
		$separator  = _x( ', ', 'Custom taxonomies separator.', 'fleet' );
		$dateformat = get_option( 'date_format' );
		foreach ( $options as $key => $label ) {
			$name  = $this->options->get_option_name( $key );
			$value = get_post_meta( $post_id, $name, true );
			if ( empty( $value ) ) {
				switch ( $key ) {
					/**
					 * handle colors
					 */
					case 'colors':
						$colors_keys = array( 'top', 'side', 'bottom' );
						$value       = '<style>';
						foreach ( $colors_keys as $ckey ) {
							$cname = $this->options->get_option_name( 'boat_color_' . $ckey );
							$x     = get_post_meta( $post_id, $cname, true );
							if ( empty( $x ) ) {
								$x = '#fff';
							}
							$value .= sprintf(
								'path.boat-hull-%s { fill: %s }',
								$ckey,
								$x
							);
						}
						$value .= '</style>';
						$file   = sprintf(
							'%s/%s/assets/images/hull.svg',
							plugins_url(),
							plugin_basename( dirname( $this->base ) )
						);
						$value .= apply_filters( 'iworks_fleet_boat_hull_image', file_get_contents( dirname( $this->base ) . '/assets/images/hull.svg' ) );
						break;
					case 'manufacturer':
						$value = get_the_term_list(
							$post_id,
							$this->taxonomy_name_manufacturer,
							sprintf( '<span class="%s">', esc_attr( $this->taxonomy_name_manufacturer ) ),
							$separator,
							'</span>'
						);
						break;
					case 'sails':
						$value = get_the_term_list(
							$post_id,
							$this->taxonomy_name_sails,
							sprintf( '<span class="%s">', esc_attr( $this->taxonomy_name_sails ) ),
							$separator,
							'</span>'
						);
						break;
					case 'mast':
						$value = get_the_term_list(
							$post_id,
							$this->taxonomy_name_mast,
							sprintf( '<span class="%s">', esc_attr( $this->taxonomy_name_mast ) ),
							$separator,
							'</span>'
						);
						break;
					case 'social':
						$social_content = '';
						foreach ( $this->fields['social'] as $social_key => $social ) {
							if ( 'website' == $social_key ) {
								continue;
							}
							$name         = $this->options->get_option_name( 'social_' . $social_key );
							$social_value = get_post_meta( $post_id, $name, true );
							if ( empty( $social_value ) ) {
								continue;
							}
							if ( false === filter_var( $social_value, FILTER_VALIDATE_URL ) ) {
								switch ( $social_key ) {
									case 'facebook':
										$social_value = sprintf( 'https://facebook.com/%s', $social_value );
										break;
									case 'instagram':
										$social_value = sprintf( 'https://www.instagram.com/%s', $social_value );
										break;
									case 'twitter':
										$social_value = sprintf( 'https://twitter.com/%s', $social_value );
										break;
								}
							}
							$social_content .= sprintf(
								'<li><a href="%s" target="_blank"><span class="dashicons dashicons-%s"></span></a></li>',
								esc_url( $social_value ),
								esc_attr( $social_key )
							);
						}
						if ( ! empty( $social_content ) ) {
							$value .= sprintf( '<ul class="iworks-fleet-boat-social-media">%s</ul>', $social_content );
						}
						break;
				}
				if ( empty( $value ) ) {
					$value = _x( 'unknown', 'value of boat', 'fleet' );
					$value = '-';
				}
			} else {
				switch ( $key ) {
					/**
					 * handle date
					 */
					case 'boat_first_certified_date':
						$value = date_i18n( $dateformat, $value );
						break;
					case 'social_website':
						$value = sprintf( '<a href="%s" class="boat-website">%s</a>', esc_url( $value ), esc_html( $value ) );
						break;
				}
			}
			$text .= $this->boat_single_row( $key, $label, $value );
		}
		/**
		 * Owners
		 */
		if ( $this->options->get_option( 'boat_show_owners' ) ) {
			$owners_text = '';
			$owners      = get_post_meta( $post_id, $this->owners_field_name, true );
			if ( ! empty( $owners ) ) {
				foreach ( $owners as $owner_key => $one ) {
					$classes = array( $one['kind'] );
					if ( $one['first'] ) {
						$classes[] = 'first';
					}
					if ( $one['current'] ) {
						$classes[] = 'current';
					}
					$owners_text .= sprintf( '<li class="%s">', esc_attr( implode( ' ', $classes ) ) );
					if ( 'person' === $one['kind'] ) {
						$print_amp = false;
						foreach ( $one['users_ids'] as $user_id ) {
							if ( $print_amp ) {
								$owners_text .= ' &amp; ';
							}
							$owners_text .= sprintf(
								'<a href="%s">%s</a>',
								get_permalink( $user_id ),
								$iworks_fleet->get_person_name( $user_id )
							);
							$print_amp    = true;
						}
					} else {
						$owners_text .= $one['organization'];
					}
					if (
						! empty( $one['date_from'] )
						|| ! empty( $one['date_to'] )
					) {
						$owners_text .= sprintf(
							' <span class="dates">%s - %s</span>',
							empty( $one['date_from'] ) ? '?' : substr( $one['date_from'], 0, 4 ),
							empty( $one['date_to'] ) ? '?' : substr( $one['date_to'], 0, 4 )
						);
					}
					$owners_text .= '</li>';
				}
				$owners_text .= '</ul>';
			}
			$text .= $this->boat_single_row( 'owners', __( 'Owners', 'fleet' ), $owners_text );
		}
		$section = current_theme_supports( 'html5' ) ? 'section' : 'div';
		if ( ! empty( $text ) ) {
			$content .= sprintf( '<%s class="fleet-boat-details">', $section );
			$content .= sprintf(
				'<h2>%s</h2><table class="boat-data">%s</table>',
				esc_html__( 'Boat details', 'fleet' ),
				$text
			);
			$content .= sprintf( '</%s>', $section );
		}

		/**
		 * crews data
		 */
		if ( $this->options->get_option( 'boat_add_crew_manually' ) ) {
			$text  = '';
			$crews = $this->get_crews_data( $post_id );
			if ( ! empty( $crews ) ) {
				global $iworks_fleet;
				$current = isset( $crews['current'] ) ? $crews['current'] : 'no';
				if ( isset( $crews['crew'][ $current ] ) ) {
					$crew  = $crews['crew'][ $current ];
					$text .= '<div class="iworks-fleet-current-crew">';
					$text .= sprintf( '<h2>%s</h2>', esc_html__( 'Current crew', 'fleet' ) );
					$text .= '<dl>';
					$text .= sprintf( '<dt>%s</dt>', esc_html__( 'Helmsman', 'fleet' ) );
					$text .= sprintf( '<dd>%s</dd>', $iworks_fleet->get_person_name( $crew['helmsman'] ) );
					$text .= sprintf( '<dt>%s</dt>', esc_html__( 'Crew', 'fleet' ) );
					$text .= sprintf( '<dd>%s</dd>', $iworks_fleet->get_person_name( $crew['crew'] ) );
					$text .= '</dl>';
					$text .= '<div>';
					unset( $crews['crew'][ $current ] );
				}
				$crews = $crews['crew'];
				if ( ! empty( $crews ) ) {
					$text .= '<div class="iworks-fleet-past-crews">';
					$text .= sprintf( '<h2>%s</h2>', esc_html( _nx( 'Past crew', 'Past crews', count( $crews ), 'Past crews number', 'fleet' ) ) );
					$text .= '<table>';
					$text .= '<thead>';
					$text .= '<tr>';
					$text .= sprintf( '<th>%s</th>', esc_html__( 'Helmsman', 'fleet' ) );
					$text .= sprintf( '<th>%s</th>', esc_html__( 'Crew', 'fleet' ) );
					$text .= '</tr>';
					$text .= '<thead>';
					$text .= '<tbody>';
					foreach ( $crews as $data ) {
						$text .= '<tr>';
						$text .= sprintf( '<td class="helmsman">%s</td>', $iworks_fleet->get_person_name( $data['helmsman'] ) );
						$text .= sprintf( '<td class="crew">%s</td>', $iworks_fleet->get_person_name( $data['crew'] ) );
						$text .= '</tr>';
					}
					$text .= '<tbody>';
					$text .= '</table>';
				}
				if ( ! empty( $text ) ) {
					$content = sprintf(
						'<div class="iworks-fleet-crews-container">%s</div>%s',
						$text,
						$content
					);
				}
			}
		}

		/**
		 * regatta
		 */
		$content .= apply_filters( 'iworks_fleet_result_boat_regatta_list', '', $post_id );
		/**
		 * return content
		 */
		return $content;
	}

		/**
		 * attach gallery
		 */
	public function add_media( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		$post_type = get_post_type();
		if ( $post_type != $this->post_type_name ) {
			return $content;
		}
		$post_id = get_the_ID();
		$ids     = $this->get_media( $post_id );
		if ( ! empty( $ids ) ) {
			$content  .= sprintf( '<h2>%s</h2>', esc_html__( 'Gallery', 'fleet' ) );
			$shortcode = sprintf( '[gallery ids="%s" link="file"]', implode( ',', $ids ) );
			$content  .= do_shortcode( $shortcode );
			/**
			 * check feature image
			 */
			if ( ! has_post_thumbnail( $post_id ) ) {
				$add = $this->options->get_option( 'boat_auto_add_feature_image' );
				if ( $add ) {
					set_post_thumbnail( $post_id, $ids[0] );
				}
			}
		}
		return $content;
	}

	private function boat_single_row( $key, $label, $value ) {
		if ( empty( $value ) || '-' == $value || is_wp_error( $value ) ) {
			return '';
		}
		$text  = '';
		$text .= sprintf( '<tr class="boat-%s">', esc_attr( $key ) );
		$text .= sprintf( '<td>%s</td>', esc_html( $label ) );
		$text .= sprintf( '<td>%s</td>', $value );
		$text .= '</tr>';
		return $text;
	}

	public function register_meta_boxes( $post ) {
		if ( $this->options->get_option( 'boat_add_crew_manually' ) ) {
			add_meta_box( 'crew', __( 'Crews data', 'fleet' ), array( $this, 'crew' ), $this->post_type_name );
		}
		if ( $this->options->get_option( 'boat_add_extra_data' ) ) {
			add_meta_box( 'boat', __( 'Boat data', 'fleet' ), array( $this, 'boat' ), $this->post_type_name );
		}
		if ( $this->options->get_option( 'boad_add_social_media' ) ) {
			add_meta_box( 'social', __( 'Social Media', 'fleet' ), array( $this, 'social' ), $this->post_type_name );
		}
		if ( $this->options->get_option( 'boad_add_owners' ) ) {
			add_meta_box( 'owners', __( 'Owners', 'fleet' ), array( $this, 'owners' ), $this->post_type_name );
		}
	}

	public function crew( $post ) {
		add_action( 'admin_footer', array( $this, 'print_js_templates' ) );
		?>
	<table class="iworks-crews-list-container">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Current', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Helmsman', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Crew', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Action', 'fleet' ); ?></th>
			</tr>
		</thead>
		<tbody id="iworks-crews-list">
		<?php
		$crews   = $this->get_crews_data( $post->ID );
		$current = isset( $crews['current'] ) ? $crews['current'] : 'no';
		if ( isset( $crews['crew'] ) ) {
			$persons = array();
			foreach ( $crews['crew'] as $key => $data ) {
				foreach ( array( 'helmsman', 'crew' ) as $role ) {
					if ( ! isset( $data[ $role ] ) || empty( $data[ $role ] ) ) {
						continue;
					}
					if ( isset( $persons[ $data[ $role ] ] ) ) {
						continue;
					}
					$persons[ $data[ $role ] ] = get_the_title( $data[ $role ] );
				}
				?>
<tr class="iworks-crew-single-row" id="iworks-crew-<?php echo esc_attr( $key ); ?>">
<td class="iworks-crew-current">
<input type="radio" name="<?php echo $this->single_crew_field_name; ?>[current]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current, $key ); ?> />
</td>
<td class="iworks-crew-helmsman">
<select name="<?php echo $this->single_crew_field_name; ?>[crew][<?php echo esc_attr( $key ); ?>][helmsman]">
	<option value=""><?php esc_html_e( 'Select or remove a helmsman', 'fleet' ); ?></option>
				<?php
				if ( isset( $data['helmsman'] ) && ! empty( $data['helmsman'] ) && isset( $persons[ $data['helmsman'] ] ) ) {
					printf(
						'<option value="%d" selected>%s</option>',
						esc_attr( $data['helmsman'] ),
						esc_html( $persons[ $data['helmsman'] ] )
					);
				}
				?>
</select>
</td>
<td class="iworks-crew-crew">
<select name="<?php echo $this->single_crew_field_name; ?>[crew][<?php echo esc_attr( $key ); ?>][crew]">
	<option value=""><?php esc_html_e( 'Select or remove a  crew', 'fleet' ); ?></option>
				<?php
				if ( isset( $data['crew'] ) && ! empty( $data['crew'] ) && isset( $persons[ $data['crew'] ] ) ) {
					printf(
						'<option value="%d" selected>%s</option>',
						esc_attr( $data['crew'] ),
						esc_html( $persons[ $data['crew'] ] )
					);
				}
				?>
</select>
</td>
<td>
<a href="#" class="iworks-crew-single-delete" data-id="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Delete', 'fleet' ); ?></a>
</td>
</tr>
				<?php
			}
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<label>
					<input type="radio" name="<?php echo $this->single_crew_field_name; ?>[current]" value="no" <?php checked( 'no', $current ); ?> />
						<?php esc_html_e( 'There is no current team', 'fleet' ); ?>
					</label>
				</td>
			</tr>
		</tfoot>
	</table>
	<button class="iworks-add-crew"><?php esc_html_e( 'Add a crew', 'fleet' ); ?></button>
		<?php
	}

	public function print_js_templates() {
		?>
<script type="text/html" id="tmpl-iworks-boat-crew">
<tr class="iworks-crew-single-row" id="iworks-crew-{{{data.id}}}">
	<td class="iworks-crew-current">
<input type="radio" name="<?php echo $this->single_crew_field_name; ?>[current]" value="{{{data.id}}}" />
	</td>
	<td class="iworks-crew-helmsman">
		<select name="<?php echo $this->single_crew_field_name; ?>[crew][{{{data.id}}}][helmsman]">
			<option value=""><?php esc_html_e( 'Select a helmsman', 'fleet' ); ?></option>
		</select>
	</td>
	<td class="iworks-crew-crew">
		<select name="<?php echo $this->single_crew_field_name; ?>[crew][{{{data.id}}}][crew]">
			<option value=""><?php esc_html_e( 'Select a crew', 'fleet' ); ?></option>
		</select>
	</td>
	<td>
		<a href="#" class="iworks-crew-single-delete" data-id="{{{data.id}}}"><?php esc_html_e( 'Delete', 'fleet' ); ?></a>
	</td>
</tr>
</script>
		<?php
	}


	public function boat( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	public function social( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	/**
	 * Meta box "Owners"
	 *
	 * since 1.3.0
	 */
	public function owners( $post ) {
		?>
	<table class="iworks-owners-list-container wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width:4em"><?php esc_html_e( 'First', 'fleet' ); ?></th>
				<th style="width:4em"><?php esc_html_e( 'Current', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Type', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Owner', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Date from', 'fleet' ); ?></th>
				<th><?php esc_html_e( 'Date to', 'fleet' ); ?></th>
				<th><span class="dashicons dashicons-trash"></span></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><a href="#" id="iworks-owners-list-add" class="button"><?php esc_html_e( 'Add owner', 'fleet' ); ?></a></td>
			</tr>
		</tfoot>
		<tbody id="iworks-owners-list">
		<?php
		$owners = get_post_meta( $post->ID, $this->owners_field_name, true );
		if ( is_array( $owners ) ) {
			foreach ( $owners as $id => $owner ) {
				$owner = wp_parse_args(
					$owner,
					array(
						'id'        => $id,
						'current'   => '',
						'first'     => '',
						'date_from' => '',
						'date_to'   => '',
						'users_ids' => array(),
						'kind'      => 'person',
					)
				);
				if ( isset( $owner['user_id'] ) ) {
					$owner['users_ids'][] = $owner['user_id'];
				}
				$this->owners_one_row_helper( $owner );
			}
		}

		?>
		</tbody>
	</table>
		<?php
		$name = esc_attr( $this->owners_field_name );
		?>
<script type="text/html" id="tmpl-iworks-fleet-boat-owner-user">
	  <select name="<?php echo $name; ?>[{{{data.id}}}][users_ids][]" class="select2 empty"></select>
</script>
<script type="text/html" id="tmpl-iworks-fleet-boat-owner">
		<?php
		echo $this->owners_one_row_helper();
		?>
</script>
		<?php
	}

	/**
	 * One row owner helper
	 *
	 * @since 1.3.0
	 */
	public function owners_one_row_helper( $data = array() ) {
		$data = wp_parse_args(
			$data,
			array(
				'id'           => '{{{data.id}}}',
				'current'      => '{{{data.current}}}',
				'first'        => '{{{data.first}}}',
				'user_id'      => '{{{data.user_id}}}',
				'date_from'    => '{{{data.date_from}}}',
				'date_to'      => '{{{data.date_to}}}',
				'users_ids'    => array(),
				'kind'         => '{{{data.kind}}}',
				'organization' => '{{{data.organization}}}',
			)
		);
		/**
		 * remove empty
		 */
		if (
			true
			&& empty( $data['date_to'] )
			&& empty( $data['date_from'] )
			&& empty( $data['users_ids'] )
			&& empty( $data['organization'] )
		) {
			return;
		}
		$name = esc_attr( $this->owners_field_name );
		?>
			<tr data-id="<?php echo esc_attr( $data['id'] ); ?>" data-kind="<?php echo esc_attr( $data['kind'] ); ?>">
	<td><input type="radio" name="<?php echo $name; ?>_first" value="<?php echo esc_attr( $data['id'] ); ?>" <?php echo true === $data['first'] ? ' checked="checked"' : ''; ?> /></td>
	<td><input type="radio" name="<?php echo $name; ?>_current" value="<?php echo esc_attr( $data['id'] ); ?>" <?php echo true === $data['current'] ? ' checked="checked"' : ''; ?> /></td>
	<td class="boat-kind">
		<ul>
			<li><label><input type="radio" <?php echo 'person' === $data['kind'] ? ' checked="checked"' : ''; ?> name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][kind]" value="person"> <?php esc_html_e( 'Person', 'fleet' ); ?></label></li>
			<li><label><input type="radio" <?php echo 'organization' === $data['kind'] ? ' checked="checked"' : ''; ?>  name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][kind]" value="organization"> <?php esc_html_e( 'Organization', 'fleet' ); ?></label></li>
		</ul>
	</td>
	<td>
		<div class="person<?php echo 'person' === $data['kind'] ? '' : ' hidden'; ?>">
			<div class="persons">
		<?php
		if ( is_array( $data['users_ids'] ) ) {
			global $iworks_fleet;
			foreach ( $data['users_ids'] as $user_id ) {
				?>
			<select name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][users_ids][]" class="select2">
				<?php printf( '<option value="%d">%s</option>', $user_id, $iworks_fleet->get_person_name( $user_id ) ); ?>
			</select>
				<?php
			}
		}

		?>
</div>
			<a href="#" class="add-person-selector"><span class="dashicons dashicons-plus"></span></a>
		</div>
		<div class="organization<?php echo 'organization' === $data['kind'] ? '' : ' hidden'; ?>">
		<input type="text" name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][organization]" value="<?php echo esc_attr( $data['organization'] ); ?>" />
		</div>
	</td>
	<td><input type="date_from" class="datepicker" name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][date_from]" value="<?php echo esc_attr( $data['date_from'] ); ?>" /></td>
	<td><input type="date_to" class="datepicker" name="<?php echo $name; ?>[<?php echo esc_attr( $data['id'] ); ?>][date_to]" value="<?php echo esc_attr( $data['date_to'] ); ?>" /></td>
	<td><a href="#" class="iworks-fleet-boat-delete"><span class="dashicons dashicons-trash"></span></a></td>
</tr>
		<?php
	}

	/**
	 * Get custom column values.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $column Column name,
	 * @param integer $post_id Current post id (Boat),
	 */
	public function custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'builder':
				$id = get_post_meta( $post_id, $this->get_custom_field_basic_manufacturer_name(), true );
				if ( empty( $id ) ) {
					echo '-';
				} else {
					printf(
						'<a href="%s">%s</a>',
						add_query_arg(
							array(
								'builder'   => $id,
								'post_type' => 'iworks_fleet_boat',
							),
							admin_url( 'edit.php' )
						),
						get_post_meta( $id, 'iworks_fleet_manufacturer_data_full_name', true )
					);
				}
				break;
			case 'build_year':
				$name = $this->options->get_option_name( 'boat_build_year' );
				echo get_post_meta( $post_id, $name, true );
				break;
			case 'location':
				$name = $this->options->get_option_name( 'boat_location' );
				echo get_post_meta( $post_id, $name, true );
				break;
		}
	}

	/**
	 * change default columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns list of columns.
	 * @return array $columns list of columns.
	 */
	public function add_columns( $columns ) {
		unset( $columns['date'] );
		$columns['build_year'] = __( 'Year of building', 'fleet' );
		$columns['location']   = __( 'Location', 'fleet' );
		$columns['title']      = __( 'Boat Number', 'fleet' );
		return $columns;
	}

	/**
	 * Add default sorting
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Query $query WP Query object.
	 */
	public function apply_default_sort_order( $query ) {
		/**
		 * do not change if it is already set by request
		 */
		if ( isset( $_REQUEST['orderby'] ) ) {
			return $query;
		}
		/**
		 * do not change outsite th admin area
		 */
		if ( ! is_admin() ) {
			if ( ! $query->is_main_query() ) {
				return $query;
			}
			$post_type = get_query_var( 'post_type' );
			if ( ! empty( $post_type ) && $post_type === $this->post_type_name ) {
				$query->set( 'orderby', 'post_title' );
				return $query;
			}
			$taxonomy = get_query_var( $this->taxonomy_name_manufacturer );
			if ( ! empty( $taxonomy ) ) {
				$query->set( 'orderby', 'post_title' );
				return $query;
			}
			$taxonomy = get_query_var( $this->taxonomy_name_sails );
			if ( ! empty( $taxonomy ) ) {
				$query->set( 'orderby', 'post_title' );
				return $query;
			}
			$taxonomy = get_query_var( $this->taxonomy_name_sails );
			if ( ! empty( $taxonomy ) ) {
				$query->set( 'orderby', 'post_title' );
				return $query;
			}
			return $query;
		}
		/**
		 * check get_current_screen()
		 */
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $query;
		}
		/**
		 * check screen post type
		 */
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $query;
		}
		/**
		 * query post type
		 */
		if ( isset( $query->query['post_type'] ) && $this->get_name() != $query->query['post_type'] ) {
			return $query;
		}
		/**
		 * screen post type
		 */
		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && $this->get_name() == $screen->post_type ) {
			$query->set( 'orderby', 'post_title' );
		}
		return $query;
	}

	public function posts_orderby_post_title( $orderby, $query ) {
		/**
		 * query post type
		 */
		if ( isset( $query->query['post_type'] ) && $this->get_name() != $query->query['post_type'] ) {
			return $orderby;
		}
		global $wpdb;
		$re = sprintf(
			'/^(%s.post_title) (DESC|ASC)$/',
			$wpdb->posts
		);
		if ( preg_match( $re, $orderby, $matches ) ) {
			return sprintf(
				'CAST( %s as unsigned ) %s',
				$matches[1],
				$matches[2]
			);
		}
		return $orderby;
	}

	public function get_manufacturer( $content, $post_id ) {
		$valid_post_type = $this->check_post_type_by_id( $post_id );
		if ( ! $valid_post_type ) {
			return $content;
		}
		$terms = wp_get_post_terms( $post_id, $this->taxonomy_name_manufacturer );
		$t     = array();
		foreach ( $terms as $term ) {
			$t[] = $term->name;
		}
		return implode( ', ', $t );
	}

	public function add_thumbnail( $post_id, $post, $update ) {
		$valid_post_type = $this->check_post_type_by_id( $post_id );
		if ( ! $valid_post_type ) {
			return;
		}
		$has_post_thumbnail = has_post_thumbnail();
		if ( $has_post_thumbnail ) {
			return;
		}
		$ids = $this->get_media( $post_id );
		if ( empty( $ids ) ) {
			return;
		}
		set_post_thumbnail( $post_id, $ids[0] );
	}

	private function get_media( $post_id ) {
		$media     = get_attached_media( 'image', $post_id );
		$ids       = array_keys( $media );
		$args      = array(
			'nopaging'    => true,
			'fields'      => 'ids',
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'tax_query'   => array(
				array(
					'taxonomy' => 'boat_number',
					'field'    => 'name',
					'terms'    => trim( preg_replace( '/POL/', '', get_the_title() ) ),
				),
			),
		);
		$the_query = new WP_Query( $args );
		// The Loop
		if ( $the_query->have_posts() ) {
			$ids = array_merge( $ids, $the_query->posts );
		}
		return $ids;
	}

	private function get_crews_data( $post_id ) {
		return get_post_meta( $post_id, $this->single_crew_field_name, true );
	}

	public function add_crew_to_boat( $post_id ) {
		global $iworks_fleet;
		$content = '';
		$crews   = $this->get_crews_data( $post_id );
		if ( ! isset( $crews['current'] ) || ! isset( $crews['crew'] ) || empty( $crews['crew'] ) ) {
			return;
		}
		if ( ! isset( $crews['crew'][ $crews['current'] ] ) ) {
			return;
		}
		$crew = $crews['crew'][ $crews['current'] ];
		if ( isset( $crew['helmsman'] ) ) {
			$user = $iworks_fleet->get_person_avatar( $crew['helmsman'] );
			if ( ! empty( $user ) ) {
				$content .= sprintf( '<div class="iworks-fleet-crew-avatar iworks-fleet-helmsman">%s</div>', $user );
			}
		}
		if ( isset( $crew['crew'] ) ) {
			$user = $iworks_fleet->get_person_avatar( $crew['crew'] );
			if ( ! empty( $user ) ) {
				$content .= sprintf( '<div class="iworks-fleet-crew-avatar iworks-fleet-crew">%s</div>', $user );
			}
		}
		if ( ! empty( $content ) ) {
			printf( '<div class="iworks-fleet-crews-container">%s</div>', $content );
		}
	}

	/**
	 * Shortcode "BOAT" to show single boat.
	 *
	 * @sonce 1.2.7
	 */
	public function shortcode_boat( $atts, $content = '' ) {
		$atts = shortcode_atts(
			array(
				'show_gallery' => false,
				'getby'        => 'number',
				'id'           => 0,
				'type'         => 'link',
			),
			$atts,
			'fleet_boat'
		);
		/**
		 * params: id
		 */
		if ( empty( $atts['id'] ) ) {
			return $content;
		}
		$atts['type'] = explode( ',', $atts['type'] );
		/**
		 * WP Query base args
		 */
		$args      = array(
			'post_type'      => $this->post_type_name,
			'meta_key'       => $this->options->get_option_name( 'boat_hull_number' ),
			'meta_value'     => $atts['id'],
			'posts_per_page' => 1,
		);
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				foreach ( $atts['type'] as $type ) {
					switch ( $type ) {
						case 'gallery':
							$content = $this->add_media( '' );
							break;
						case 'link':
							$content = sprintf(
								'<a href="%s" title="%s">%s</a>',
								get_permalink(),
								esc_attr( get_the_title() ),
								$content
							);
					}
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		return $content;
	}

	public function save_post_owners_save( $post_id, $post, $update ) {
		$valid_post_type = $this->check_post_type_by_id( $post_id );
		if ( ! $valid_post_type ) {
			return;
		}
		if ( ! isset( $_POST[ $this->owners_field_name ] ) ) {
			return;
		}
		$first   = filter_input( INPUT_POST, $this->owners_field_name . '_first', FILTER_DEFAULT );
		$current = filter_input( INPUT_POST, $this->owners_field_name . '_current', FILTER_DEFAULT );
		$owners  = array();
		delete_post_meta( $post_id, $this->owners_field_name );
		delete_post_meta( $post_id, $this->owners_index_field_name );
		foreach ( $_POST[ $this->owners_field_name ] as $key => $value ) {
			$kind         = ( isset( $value['kind'] ) && preg_match( '/^(person|organization)$/', $value['kind'] ) ) ? $value['kind'] : 'person';
			$users_ids    = array();
			$organization = '';
			if ( 'person' === $kind ) {
				foreach ( $value['users_ids'] as $user_id ) {
					$user_id = intval( $user_id );
					if ( 0 < $user_id ) {
						add_post_meta( $post_id, $this->owners_index_field_name, $user_id );
						$users_ids[] = $user_id;
					}
				}
			} else {
				$organization = filter_var( $value['organization'], FILTER_DEFAULT );
			}
			$owner = wp_parse_args(
				array(
					'users_ids'    => $users_ids,
					'organization' => $organization,
					'first'        => $key === $first,
					'current'      => $key === $current,
					'date_to'      => preg_replace( '/[^0-9^\-]/', '', $value['date_to'] ),
					'date_from'    => preg_replace( '/[^0-9^\-]/', '', $value['date_from'] ),
					'kind'         => $kind,
				),
				array(
					'users_ids'    => array(),
					'organization' => '',
					'first'        => false,
					'current'      => false,
					'date_to'      => false,
					'date_from'    => false,
					'kind'         => $kind,
				)
			);
			$add   = false;
			foreach ( $owner as $value ) {
				if ( $add ) {
					continue;
				}
				if ( ! empty( $value ) && $value ) {
					$add = true;
				}
			}
			if ( $add ) {
				$owners[ md5( serialize( $owner ) ) ] = $owner;
			}
		}
		if ( ! empty( $owners ) ) {
			$result = update_post_meta( $post_id, $this->owners_field_name, $owners );
			if ( ! $result ) {
				add_post_meta( $post_id, $this->owners_field_name, $owners, true );
			}
		}
	}

	/**
	 * get list of boats by post ID filter
	 *
	 * @since 1.2
	 * @since 2.0 array $atts Settings
	 */
	public function get_content_table_by_owner_id( $content, $owner_id, $atts ) {
		$settings  = wp_parse_args(
			array(
				'show_title' => true,
				'separator'  => 'ul-list',
			),
			$atts
		);
		$args      = array(
			'post_type'      => $this->post_type_name,
			'posts_per_page' => -1,
			'meta_key'       => $this->owners_index_field_name,
			'meta_value'     => $owner_id,
		);
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			$list = array();
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$list[ get_the_title() ] = sprintf(
					'<a href="%s">%s</a>',
					get_permalink(),
					get_the_title()
				);
			}
			if ( ! empty( $list ) ) {
				$boat_list = '';
				ksort( $list );
				if ( 'ul-list' === $settings['separator'] ) {
					$boat_list .= '<ul class="iworks-fleet-owner-boats-list">';
					foreach ( $list as $boat => $boat_link ) {
						$boat_list .= sprintf(
							'<li class="iworks-fleet-boat iworks-fleet-boat-%d">%s</li>',
							$boat,
							$boat_link
						);
					}
					$boat_list .= '</ul>';
				} else {
					$boat_list = implode( $settings['separator'], $list );
				}
				if ( isset( $attr['show_title'] ) && $attr['show_title'] ) {
					$content .= sprintf(
						'<p>%s: %s</p>',
						esc_html__( 'Boats list', 'fleet' ),
						$boat_list
					);
				} else {
					$content .= $boat_list;
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		return $content;
	}

	public function get_flag_filter( $content, $post_ID ) {
		$meta_key = $this->options->get_option_name( 'boat_nation' );
		$code     = get_post_meta( $post_ID, $meta_key, true );
		if ( ! empty( $code ) ) {
			return sprintf( '<span class="flag flag-%s">%s</span>', esc_attr( strtolower( $code ) ), $content );
		}
		return $content;
	}

	public function get_hull_filter( $content, $post_ID ) {
		return get_the_term_list( $post_ID, $this->taxonomy_name_manufacturer );
	}

	/**
	 * Add OpenGraph data.
	 *
	 * @since 1.3.0
	 */
	public function og_array( $og ) {
		if ( is_singular( $this->post_type_name ) ) {
			return $this->og_array_add( $og, 'boat' );
		}
		return $og;
	}

	private function filter_get_last_owner_helper( $one ) {
		if ( ! isset( $one['users_ids'] ) ) {
			return;
		}
		if ( ! is_array( $one['users_ids'] ) ) {
			return;
		}
		global $iworks_fleet;
		$t = array();
		foreach ( $one['users_ids'] as $user_ID ) {
			$name = $iworks_fleet->get_person_name( $user_ID );
			if ( ! empty( $name ) ) {
				$t[] = sprintf(
					'<a href="%s">%s</a>',
					get_permalink( $user_ID ),
					$name
				);
			}
		}
		$t = array_filter( $t );
		if ( empty( $t ) ) {
			return;
		}
		return implode( _x( ' ', 'user list separator', 'fleet' ), $t );
	}

	public function filter_get_last_owner( $content, $post_ID ) {
		$data = get_post_meta( $post_ID, $this->owners_field_name, true );
		if ( is_array( $data ) ) {
			foreach ( $data as $one ) {
				if ( isset( $one['current'] ) ) {
					$t = $this->filter_get_last_owner_helper( $one );
					if ( ! empty( $t ) ) {
						return $t;
					}
				}
			}
			$t = $this->filter_get_last_owner_helper( $data[ sizeof( $data ) - 1 ] );
			if ( ! empty( $t ) ) {
				return $t;
			}
		}
		return $content;
	}
}

