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

if ( class_exists( 'iworks_fleet_posttypes_ranking' ) ) {
	return;
}

require_once( dirname( dirname( __FILE__ ) ) . '/posttypes.php' );

class iworks_fleet_posttypes_ranking extends iworks_fleet_posttypes {

	protected $post_type_name = 'iworks_fleet_ranking';

	public function __construct() {
		parent::__construct();
		/**
		 * change default columns
		 */
		add_filter( "manage_{$this->get_name()}_posts_columns", array( $this, 'add_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
		/**
		 * shortcodes
		 */
		add_shortcode( 'iworks_fleet_ranking', array( $this, 'shortcode_ranking' ), 10, 2 );
		/**
		 * fields
		 */
		$this->fields = array(
			'ranking' => array(
				'use_boat'   => array(
					'add_class' => 'key',
					'label'     => esc_html__( 'Use Boat', 'fleet' ),
					'type'      => 'checkbox',
				),
				'use_helm'   => array(
					'add_class' => 'key',
					'label'     => esc_html__( 'Use Helm', 'fleet' ),
					'type'      => 'checkbox',
				),
				'use_crew'   => array(
					'add_class' => 'key',
					'label'     => esc_html__( 'Use Crew', 'fleet' ),
					'type'      => 'checkbox',
				),
				'serie'      => array(
					'add_class' => 'value',
					'label'     => esc_html__( 'Serie', 'fleet' ),
					'type'      => 'select',
					'options'   => array(
						'required' => 'required',
					),
				),
				'no_discard' => array(
					'add_class' => 'value',
					'label'     => esc_html__( 'Not Discardable Serie', 'fleet' ),
					'type'      => 'select',
				),
				'year'       => array(
					'description' => esc_html__( 'Only year matters.', 'fleet' ),
					'label'       => esc_html__( 'Year', 'fleet' ),
					'type'        => 'date',
					'options'     => array(
						'required' => 'required',
					),
				),
				'drop'       => array(
					'label' => esc_html__( 'Allow Discard', 'fleet' ),
					'type'  => 'checkbox',
				),
				'drop_after' => array(
					'label' => esc_html__( 'Discard After', 'fleet' ),
					'type'  => 'number',
					'args'  => array(
						'min'     => 1,
						'default' => 4,
						'class'   => array( 'small-text', 'textright' ),
					),
				),
				'sex'        => array(
					'add_class' => 'value',
					'label'     => esc_html__( 'Sex', 'fleet' ),
					'type'      => 'select',
					'args'      => array(
						'options' => array(
							'any'    => __( '&mdash; Any &mdash;', 'fleet' ),
							'female' => __( 'Female', 'fleet' ),
							'male'   => __( 'Male', 'fleet' ),
						),
					),
				),
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
		 * Fleet
		 */
		add_filter( 'iworks/fleet/register_taxonomy/iworks_fleet_ranking/add', '__return_false' );
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
		$show_in_menu = add_query_arg( 'post_type', $iworks_fleet->get_post_type_name( 'person' ), 'edit.php' );
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
			'search_items'          => __( 'Search ranking', 'fleet' ),
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
			'label'                => __( 'ranking', 'fleet' ),
			'labels'               => $this->labels,
			'supports'             => array( 'title', 'editor', 'thumbnail', 'revision' ),
			'hierarchical'         => false,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => $show_in_menu,
			'show_in_admin_bar'    => false,
			'show_in_nav_menus'    => false,
			'can_export'           => true,
			'has_archive'          => false,
			'exclude_from_search'  => false,
			'publicly_queryable'   => false,
			'capability_type'      => 'page',
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
			'rewrite'              => array(
				'slug' => _x( 'fleet-ranking', 'slug for single ranking', 'fleet' ),
			),
		);
		$args         = apply_filters( 'fleet_register_ranking_post_type_args', $args );
		register_post_type( $this->post_type_name, $args );
	}

	public function save_post_meta( $post_id, $post, $update ) {
		$result = $this->save_post_meta_fields( $post_id, $post, $update, $this->fields );
	}

	public function register_meta_boxes( $post ) {
		add_meta_box( 'ranking', __( 'Ranking Settings', 'fleet' ), array( $this, 'ranking' ), $this->post_type_name );
	}

	public function ranking( $post ) {
		$this->fields['ranking']['serie']['args']['options']      = $this->get_series_array();
		$this->fields['ranking']['no_discard']['args']['options'] = $this->get_series_array();
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
			case 'shortcode':
				printf( '[iworks_fleet_ranking id="%d"]', $post_id );
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
		$columns['shortcode'] = __( 'Shortcode', 'fleet' );
		return $columns;
	}

	/**
	 * OpenGraph data.
	 *
	 * @since 2.2.0
	 */
	public function og_array( $og ) {
		return $og;
	}

	public function shortcode_ranking( $atts, $content = '' ) {
		$args = shortcode_atts(
			array(
				'id'      => null,
				'header'  => 'h2',
				'title'   => 'hide',
				'content' => 'hide',
			),
			$atts,
			'iworks/fleet/ranking/shortcode/atts'
		);
		if ( empty( $args['id'] ) ) {
			if ( current_user_can( 'administrator' ) ) {
				$content .= $this->get_template(
					'ranking-missing-id',
					'shortcode'
				);
			}
			return $content;
		}
		$post = get_post( $args['id'] );
		if (
			is_wp_error( $post )
			|| empty( $post )
			|| ! is_a( $post, 'WP_Post' )
			|| $this->post_type_name !== $post->post_type
		) {
			if ( current_user_can( 'administrator' ) ) {
				$content .= $this->get_template(
					'ranking-wrong-id',
					'shortcode'
				);
			}
			return $content;
		}
		return $content . $this->get_ranking_table_by_ranking_id( $post->ID, $args );
	}

	public function get_ranking_table_by_ranking_id( $id, $args = array() ) {
		$post    = get_post( $id );
		$content = '';
		$group   = 'ranking';
		$classes = array(
			'iworks-fleet',
			'iworks-fleet-ranking',
		);
		foreach ( $this->fields[ $group ] as $key => $data ) {
			if ( ! isset( $data['add_class'] ) ) {
				continue;
			}
			$option_name = $this->options->get_option_name( $group . '_' . $key );
			switch ( $data['add_class'] ) {
				case 'key':
					switch ( $data['type'] ) {
						case 'checkbox':
							$value = get_post_meta( $id, $option_name, true );
							if ( 'yes' === $value ) {
								$classes[] = sprintf(
									'iworks-fleet-ranking-%s',
									preg_replace( '/_/', '-', $key )
								);
							}
							break;
					}
					break;
				case 'value':
					$value = get_post_meta( $id, $option_name, true );
					if ( $value ) {
						$classes[] = sprintf(
							'iworks-fleet-ranking-%s-%s',
							preg_replace( '/_/', '-', $key ),
							preg_replace( '/_/', '-', $value )
						);
					}
					break;
			}
		}
		$args['classes'] = $classes;
		$args['post']    = (array) $post;
		$content        .= $this->get_template(
			'ranking/output/header',
			'shortcode',
			$args,
		);
		/**
		 * get results
		 */
		$args         = $this->get_ranking_settings_by_ranking_id( $id );
		$args['data'] = apply_filters( 'iworks/fleet/results/get/array', array(), $args );
		$content     .= $this->get_template(
			'ranking/output/table',
			'shortcode',
			$args,
		);
		$content     .= $this->get_template(
			'ranking/output/footer',
			'shortcode',
			$args,
		);
		return $content;
	}

	private function get_ranking_settings_by_ranking_id( $id ) {
		$settings = array(
			'ranking_id' => $id,
		);
		$group    = 'ranking';
		foreach ( $this->fields[ $group ] as $key => $data ) {
			$option_name  = $this->options->get_option_name( $group . '_' . $key );
			$option_value = get_post_meta( $id, $option_name, true );
			switch ( $key ) {
				case 'year':
					$option_value = date( 'Y', intval( $option_value ) );
					break;
			}
			$settings[ $key ] = $option_value;
		}
		return $settings;
	}
}

