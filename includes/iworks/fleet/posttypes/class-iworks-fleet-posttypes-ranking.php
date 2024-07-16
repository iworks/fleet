<?php
/*

Copyright 2024-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_fleet_posttypes_person' ) ) {
	return;
}

require_once( dirname( dirname( __FILE__ ) ) . '/posttypes.php' );

class iworks_fleet_posttypes_ranking extends iworks_fleet_posttypes {

	protected $post_type_name     = 'iworks_fleet_ranking';

	public function __construct() {
		parent::__construct();
		/**
		 * change default columns
		 */
		add_filter( "manage_{$this->get_name()}_posts_columns", array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
		/**
		 * fields
		 */
		$this->fields = array(
			'personal' => array(
				'sailor_id'  => array(
					'label' => __( 'World Sailing Sailor ID', 'fleet' ),
				),
				'nation'     => array(
					'label'   => __( 'Nation', 'fleet' ),
					'type'    => 'select2',
					'args'    => array(
						'options' => $this->get_nations(),
					),
					'twitter' => 'yes',
				),
				'birth_year' => array(
					'label'   => __( 'Birth Year', 'fleet' ),
					'twitter' => 'yes',
				),
				'birth_date' => array(
					'type'  => 'date',
					'label' => __( 'Birth Date', 'fleet' ),
				),
			),
			'social'   => array(
				'website'   => array( 'label' => __( 'Website', 'fleet' ) ),
				'facebook'  => array( 'label' => __( 'Facebook', 'fleet' ) ),
				'twitter'   => array( 'label' => __( 'Twitter', 'fleet' ) ),
				'instagram' => array( 'label' => __( 'Instagram', 'fleet' ) ),
				'endomondo' => array( 'label' => __( 'Endomondo', 'fleet' ) ),
				'skype'     => array( 'label' => __( 'Skype', 'fleet' ) ),
			),
			'contact'  => array(
				'mobile' => array( 'label' => __( 'Mobile', 'fleet' ) ),
				'email'  => array( 'label' => __( 'E-mail', 'fleet' ) ),
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
		 * Change tag link to person
		 */
		add_filter( 'term_link', array( $this, 'change_tag_link_to_person_link' ), 10, 3 );
	}

	/**
	 * Add default class to postbox,
	 */
	public function add_defult_class_to_postbox( $classes ) {
		$classes[] = 'iworks-type';
		return $classes;
	}

	public function register() {
		$parent       = true;
		$this->labels = array(
			'name'                  => _x( 'Rankings', 'Ranking General Name', 'fleet' ),
			'singular_name'         => _x( 'Ranking', 'Ranking Singular Name', 'fleet' ),
			'menu_name'             => __( 'Fleet', 'fleet' ),
			'name_admin_bar'        => __( 'Ranking', 'fleet' ),
			'archives'              => __( 'Rankings', 'fleet' ),
			'attributes'            => __( 'Item Attributes', 'fleet' ),
			'all_items'             => __( 'Rankings', 'fleet' ),
			'add_new_item'          => __( 'Add New Ranking', 'fleet' ),
			'add_new'               => __( 'Add New Ranking', 'fleet' ),
			'new_item'              => __( 'New Ranking', 'fleet' ),
			'edit_item'             => __( 'Edit Ranking', 'fleet' ),
			'update_item'           => __( 'Update Ranking', 'fleet' ),
			'view_item'             => __( 'View Ranking', 'fleet' ),
			'view_items'            => __( 'View Rankings', 'fleet' ),
			'search_items'          => __( 'Search person', 'fleet' ),
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
		$args         = array(
			'label'                => __( 'person', 'fleet' ),
			'labels'               => $this->labels,
			'supports'             => array( 'title', 'editor', 'thumbnail', 'revision' ),
			'taxonomies'           => array(
				$this->taxonomy_name_club,
				$this->taxonomy_name_location,
			),
			'hierarchical'         => false,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => $parent,
			'show_in_admin_bar'    => true,
			'show_in_nav_menus'    => true,
			'can_export'           => true,
			'has_archive'          => _x( 'fleet-persons', 'slug for archive', 'fleet' ),
			'exclude_from_search'  => false,
			'publicly_queryable'   => true,
			'capability_type'      => 'page',
			'menu_icon'            => 'dashicons-sos',
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
			'rewrite'              => array(
				'slug' => _x( 'fleet-person', 'slug for single person', 'fleet' ),
			),
		);
		$args         = apply_filters( 'fleet_register_person_post_type_args', $args );
		register_post_type( $this->post_type_name, $args );
		/**
		 * person hull club Taxonomy.
		 */
		$labels = array(
			'name'                       => _x( 'Clubs', 'Club General Name', 'fleet' ),
			'singular_name'              => _x( 'Club', 'Club Singular Name', 'fleet' ),
			'menu_name'                  => __( 'Clubs', 'fleet' ),
			'all_items'                  => __( 'Clubs', 'fleet' ),
			'new_item_name'              => __( 'New Club Name', 'fleet' ),
			'add_new_item'               => __( 'Add New Club', 'fleet' ),
			'edit_item'                  => __( 'Edit Club', 'fleet' ),
			'update_item'                => __( 'Update Club', 'fleet' ),
			'view_item'                  => __( 'View Club', 'fleet' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'fleet' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'fleet' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
			'popular_items'              => __( 'Popular Clubs', 'fleet' ),
			'search_items'               => __( 'Search Clubs', 'fleet' ),
			'not_found'                  => __( 'Not Found', 'fleet' ),
			'no_terms'                   => __( 'No items', 'fleet' ),
			'items_list'                 => __( 'Clubs list', 'fleet' ),
			'items_list_navigation'      => __( 'Clubs list navigation', 'fleet' ),
		);
		$args   = array(
			'labels'             => $labels,
			'hierarchical'       => false,
			'public'             => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_quick_edit' => true,
			'rewrite'            => array( 'slug' => 'fleet-club' ),
		);
		$args   = apply_filters( 'fleet_register_person_taxonomy_args', $args );
		register_taxonomy( $this->taxonomy_name_club, array( $this->post_type_name ), $args );
	}

	public function save_post_meta( $post_id, $post, $update ) {
		$result = $this->save_post_meta_fields( $post_id, $post, $update, $this->fields );
	}

	public function register_meta_boxes( $post ) {
		add_meta_box( 'personal', __( 'Rankingal data', 'fleet' ), array( $this, 'personal' ), $this->post_type_name );
		add_meta_box( 'social', __( 'Social Media', 'fleet' ), array( $this, 'social' ), $this->post_type_name );
		add_meta_box( 'contact', __( 'Contact data', 'fleet' ), array( $this, 'contact' ), $this->post_type_name );
	}

	public function contact( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	public function social( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	public function personal( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	/**
	 * Get custom column values.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column Column name,
	 * @param integer $post_id Current post id (person),
	 *
	 */
	public function custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'birth_year':
				$meta_name = $this->options->get_option_name( 'personal_' . $column );
				echo get_post_meta( $post_id, $meta_name, true );
				break;
			case 'email':
				$meta_name = $this->options->get_option_name( 'contact_' . $column );
				$email     = get_post_meta( $post_id, $meta_name, true );
				if ( ! empty( $email ) ) {
					printf( '<a href="mailto:%s">%s</a>', esc_attr( $email ), esc_html( $email ) );
				}
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
		$columns['title']      = __( 'Name', 'fleet' );
		$columns['birth_year'] = __( 'Birth year', 'fleet' );
		$columns['email']      = __( 'E-mail', 'fleet' );
		return $columns;
	}
}

