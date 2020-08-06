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

if ( class_exists( 'iworks_fleet_posttypes_person' ) ) {
	return;
}

require_once( dirname( dirname( __FILE__ ) ) . '/posttypes.php' );

class iworks_fleet_posttypes_person extends iworks_fleet_posttypes {

	protected $post_type_name     = 'iworks_fleet_person';
	protected $taxonomy_name_club = 'iworks_fleet_club';
	private $nonce_list           = 'iworks_fleet_person_persons_list_nonce';
	private $users_list           = array();
	private $boats_list           = array();

	public function __construct() {
		parent::__construct();
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_filter( 'international_fleet_posted_on', array( $this, 'get_club' ), 10, 2 );
		add_filter( 'the_title', array( $this, 'add_flag_to_single_title' ), 10, 2 );
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
		 * AJAX list
		 */
		if ( is_a( $this->options, 'iworks_options' ) ) {
			$this->nonce_list = $this->options->get_option_name( 'persons_list_nonce' );
		}
		add_action( 'wp_ajax_iworks_fleet_persons_list', array( $this, 'get_select2_list' ) );
		/**
		 * add nonce
		 */
		add_filter( 'wp_localize_script_fleet_admin', array( $this, 'add_nonce' ) );
		/**
		 * maybe update country
		 */
		add_action( 'maybe_add_person_nation', array( $this, 'maybe_add_person_nation' ), 10, 2 );
		/**
		 * fields
		 */
		$this->fields = array(
			'personal' => array(
				'nation'     => array(
					'label' => __( 'Nation', 'fleet' ),
					'type'  => 'select2',
					'args'  => array(
						'options' => $this->get_nations(),
					),
				),
				'birth_year' => array( 'label' => __( 'Birth Year', 'fleet' ) ),
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
		$parent = true;
		$labels = array(
			'name'                  => _x( 'Persons', 'Person General Name', 'fleet' ),
			'singular_name'         => _x( 'Person', 'Person Singular Name', 'fleet' ),
			'menu_name'             => __( 'Fleet', 'fleet' ),
			'name_admin_bar'        => __( 'Person', 'fleet' ),
			'archives'              => __( 'Persons', 'fleet' ),
			'attributes'            => __( 'Item Attributes', 'fleet' ),
			'all_items'             => __( 'Persons', 'fleet' ),
			'add_new_item'          => __( 'Add New Person', 'fleet' ),
			'add_new'               => __( 'Add New Person', 'fleet' ),
			'new_item'              => __( 'New Person', 'fleet' ),
			'edit_item'             => __( 'Edit Person', 'fleet' ),
			'update_item'           => __( 'Update Person', 'fleet' ),
			'view_item'             => __( 'View Person', 'fleet' ),
			'view_items'            => __( 'View Persons', 'fleet' ),
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
		$args   = array(
			'label'                => __( 'person', 'fleet' ),
			'labels'               => $labels,
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
		$args   = apply_filters( 'fleet_register_person_post_type_args', $args );
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
		add_meta_box( 'personal', __( 'Personal data', 'fleet' ), array( $this, 'personal' ), $this->post_type_name );
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
		 * only main query
		 */
		if ( ! $query->is_main_query() ) {
			return $query;
		}
		/**
		 * do not change outsite th admin area
		 */
		$post_type = get_query_var( 'post_type' );
		if ( is_admin() ) {
			/**
			 * check get_current_screen()
			 */
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( isset( $screen->post_type ) && $this->get_name() == $screen->post_type ) {
					$query->set( 'order', 'ASC' );
					$query->set( 'orderby', 'post_title' );
				}
			}
		} else {
			if ( ! empty( $post_type ) && $post_type === $this->post_type_name ) {
				$query->set( 'order', 'ASC' );
				$query->set( 'orderby', 'post_title' );
				return $query;
			}
		}
		return $query;
	}

	public function get_club( $content, $post_id ) {
		$valid_post_type = $this->check_post_type_by_id( $post_id );
		if ( ! $valid_post_type ) {
			return $content;
		}
		$terms = wp_get_post_terms( $post_id, $this->taxonomy_name_club );
		$t     = array();
		foreach ( $terms as $term ) {
			$t[] = $term->name;
		}
		return implode( ', ', $t );
	}

	public function get_select2_list() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['user_id'] ) ) {
			wp_send_json_error();
		}
		$nonce = $_POST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, $this->nonce_list . $_POST['user_id'] ) ) {
			wp_send_json_error();
		}
		$data      = array();
		$args      = array(
			'nopaging'  => true,
			'post_type' => $this->get_name(),
			'orderby'   => 'post_title',
			'order'     => 'ASC',
		);
		$the_query = new WP_Query( $args );
		// The Loop
		if ( $the_query->have_posts() ) {
			foreach ( $the_query->posts as $post ) {
				$data[] = array(
					'id'   => $post->ID,
					'text' => $post->post_title,
				);
			}
			wp_send_json_success( $data );
		}
		wp_send_json_error();
	}

	public function add_nonce( $data ) {
		$data['nonces'][ $this->nonce_list ] = wp_create_nonce( $this->nonce_list . get_current_user_id() );
		return $data;
	}

	private function get_user( $user_post_id ) {
		$avatar_size = 100;
		if ( ! isset( $this->users_list[ $user_post_id ] ) ) {
			$thumbnail = '';
			/**
			 * try to get gravatar
			 */
			$email       = $this->options->get_option_name( 'contact_email' );
			$email       = get_post_meta( $user_post_id, $email, true );
			$avatar      = get_avatar( $email, $avatar_size, null );
			$avatar_meta = get_avatar_data( $email, $avatar_size );
			if ( $avatar_meta['found_avatar'] ) {
				$thumbnail = $avatar;
			}
			/**
			 * fallback go post thumbnail
			 */
			if ( empty( $thumbnail ) ) {
				$thumbnail = get_the_post_thumbnail( $user_post_id, array( $avatar_size, $avatar_size ) );
			}
			/**
			 * fallback to default gravatar
			 */
			if ( empty( $thumbnail ) ) {
				$thumbnail = $avatar;
			}
			$post                              = get_post( $user_post_id );
			$this->users_list[ $user_post_id ] = array(
				'user_post_id' => $user_post_id,
				'permalink'    => get_permalink( $post ),
				'post_title'   => get_the_title( $post ),
				'avatar'       => $thumbnail,
			);
		}
		return $this->users_list[ $user_post_id ];
	}

	/**
	 * Get person name
	 */
	public function get_person_name_by_id( $user_post_id ) {
		if ( empty( $user_post_id ) ) {
			return _x( 'not set', 'Person name on crews list if it is not set', 'fleet' );
		}
		$correct_post_type = $this->check_post_type_by_id( $user_post_id );
		if ( ! $correct_post_type ) {
			return _x( 'not set', 'Person name on crews list if it is not set', 'fleet' );
		}
		$user = $this->get_user( $user_post_id );
		return sprintf(
			'<a href="%s">%s</a>',
			esc_url( $user['permalink'] ),
			$user['post_title']
		);
	}

	/**
	 * Get person avatar
	 */
	public function get_person_avatar_by_id( $user_post_id ) {
		if ( empty( $user_post_id ) ) {
			return '';
		}
		$correct_post_type = $this->check_post_type_by_id( $user_post_id );
		if ( ! $correct_post_type ) {
			return '';
		}
		$user = $this->get_user( $user_post_id );
		return sprintf(
			'<a href="%s" title="%s">%s</a>',
			esc_url( $user['permalink'] ),
			esc_attr( $user['post_title'] ),
			$user['avatar']
		);
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
		$post_id = get_the_ID();
		/**
		 * trophies!
		 */
		if ( is_main_query() ) {
			$show     = $this->options->get_option( 'person_show_trophy' );
			$trophies = apply_filters( 'iworks_fleet_result_sailor_trophies', '', $post_id );
			$content  = $trophies . $content;
		}
		/**
		 * add social media
		 */
		$content .= $this->social_media( $post_id );
		/**
		 * regatta
		 */
		$content .= apply_filters( 'iworks_fleet_result_sailor_regata_list', '', $post_id );
		/**
		 * add boats
		 */
		$content .= $this->boats( $post_id );
		$content .= $this->own_boats( $post_id );
		/**
		 * posts list
		 *
		 * @since 1.2.5
		 */
		$show = $this->options->get_option( 'person_show_articles_with_person_tag' );
		if ( '1' === $show ) {
			$term = get_term_by( 'name', get_the_title(), 'post_tag' );
			if ( ! empty( $term ) ) {
				$args      = array(
					'tag_id'         => $term->term_id,
					'posts_per_page' => get_option( 'posts_per_page' ),
				);
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) {
					$content .= '<div class="fleet-person-post-list">';
					$content .= sprintf(
						'<h3>%s</h3>',
						esc_html__( 'Read more about sailor', 'fleet' )
					);
					$content .= '<ul>';
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$content .= sprintf(
							'<li><a href="%s">%s</a></li>',
							get_permalink(),
							get_the_title()
						);
					}
					$content .= '</ul>';
					$content .= '</div>';
					/* Restore original Post Data */
					wp_reset_postdata();
				}
				wp_reset_query();
			}
		}
		/**
		 * Endomondo
		 */
		$name  = $this->options->get_option_name( 'social_endomondo' );
		$value = get_post_meta( $post_id, $name, true );
		if ( ! empty( $value ) ) {
			$content .= sprintf( '<iframe src="https://www.endomondo.com/embed/user/summary?id=%d&sport=12&measure=0&zone=Gp0100_SAR&width=400&height=217" width="400" height="217" frameborder="0" scrolling="no" ></iframe>', $value );
		}
		return $content;
	}

	/**
	 * get boat name with link
	 *
	 * @since 1.1.1
	 */
	private function get_boat( $boat_id ) {
		if ( isset( $this->boats_list[ $boat_id ] ) ) {
			return $this->boats_list[ $boat_id ];
		}
		$content = get_the_title( $boat_id );
		$url     = get_permalink( $boat_id );
		if ( ! empty( $url ) ) {
			$content = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html( $content )
			);
		}
		$this->boats_list[ $boat_id ] = $content;
		return $this->boats_list[ $boat_id ];
	}

	private function social_media( $post_id ) {
		$content = '';
		$show    = $this->options->get_option( 'person_show_social_media' );
		if ( empty( $show ) ) {
			return $content;
		}
		foreach ( $this->fields['social'] as $key => $data ) {
			$name  = $this->options->get_option_name( 'social_' . $key );
			$value = get_post_meta( $post_id, $name, true );
			if ( empty( $value ) ) {
				continue;
			}
			$content .= sprintf(
				'<li><a href="%s" class="icon icon-%s" title="%s"></a></li>',
				esc_url( $value ),
				esc_attr( $key ),
				esc_attr( $data['label'] )
			);
		}
		if ( empty( $content ) ) {
			return $content;
		}
		$content = sprintf( '<ul class="fleet-person-social-media">%s</ul>', $content );
		return $content;
	}

	private function own_boats( $post_id ) {
		$content = '';
		$show    = $this->options->get_option( 'person_show_boats_owned_table' );
		if ( empty( $show ) ) {
			return $content;
		}
		/**
		 * boats
		 */
		return $content = apply_filters( 'iworks_fleet_boat_get_by_owner_id', $content, $post_id );
	}

	private function boats( $post_id ) {
		$content = '';
		$show    = $this->options->get_option( 'person_show_boats_table' );
		if ( empty( $show ) ) {
			return $content;
		}
		/**
		 * boats
		 */
		$boats = get_post_meta( $post_id, '_iworks_fleet_boat' );
		if ( empty( $boats ) ) {
			return $content;
		}
		$meta_name          = $this->options->get_option_name( 'crew' );
		$boats              = array_unique( $boats );
		$currently_sails_on = $sails_on = array();
		$done               = array();
		/**
		 * past
		 */
		foreach ( $boats as $boat_id ) {
			$crew = get_post_meta( $boat_id, $meta_name, true );
			if ( ! isset( $crew['crew'] ) ) {
				continue;
			}
			/**
			 * current
			 */
			if ( isset( $crew['current'] ) && isset( $crew['crew'][ $crew['current'] ] ) ) {
				$value = $crew['crew'][ $crew['current'] ];
				unset( $crew['crew'][ $crew['current'] ] );
				if ( isset( $value['helmsman'] ) && $post_id == $value['helmsman'] ) {
					$currently_sails_on[] = sprintf(
						__( 'Sail on %s as helmsman.', 'fleet' ),
						$this->get_boat( $boat_id )
					);
					$done[]               = $this->get_done_key( 'helmsman', $boat_id, $post_id );
				}
				if ( isset( $value['crew'] ) && $post_id == $value['crew'] ) {
					$currently_sails_on[] = sprintf(
						__( 'Sail on %s as crew.', 'fleet' ),
						$this->get_boat( $boat_id )
					);
				}
			}
			/**
			 * past
			 */
			foreach ( $crew['crew'] as $key => $value ) {
				if ( isset( $value['helmsman'] ) && $post_id == $value['helmsman'] ) {
					$done_key = $this->get_done_key( 'helmsman', $boat_id, $post_id );
					if ( in_array( $done_key, $done ) ) {
						continue;
					}
					$done[]     = $done_key;
					$sails_on[] = sprintf(
						__( 'Sailed on %s as helmsman.', 'fleet' ),
						$this->get_boat( $boat_id )
					);
				}
				if ( isset( $value['crew'] ) && $post_id == $value['crew'] ) {
					$done_key = $this->get_done_key( 'crew', $boat_id, $post_id );
					if ( in_array( $done_key, $done ) ) {
						continue;
					}
					$done[]     = $done_key;
					$sails_on[] = sprintf(
						__( 'Sailed on %s as crew.', 'fleet' ),
						$this->get_boat( $boat_id )
					);
				}
			}
		}
		if ( ! empty( $sails_on ) || ! empty( $currently_sails_on ) ) {
			$content .= sprintf( '<h2>%s</h2>', __( 'Sail or sailed', 'fleet' ) );
			$content .= '<ul>';
			if ( ! empty( $currently_sails_on ) ) {
				rsort( $currently_sails_on );
				foreach ( $currently_sails_on as $one ) {
					$content .= sprintf( '<li class="intfleet-current">%s</li>', $one );
				}
			}
			if ( ! empty( $sails_on ) ) {
				rsort( $sails_on );
				foreach ( $sails_on as $one ) {
					$content .= sprintf( '<li>%s</li>', $one );
				}
			}
			$content .= '</ul>';
		}
		return $content;
	}

	/**
	 * generate key
	 */
	private function get_done_key( $prefix, $boat_id, $post_id ) {
		$done_key = 'helmsman-' . $boat_id . '-' . $post_id;
		return $done_key;
	}

	/**
	 * Change tag link to person
	 *
	 * @since 1.2.5
	 *
	 * @param string $termlink Term link URL.
	 * @param objec $term Term object.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function change_tag_link_to_person_link( $termlink, $term, $taxonomy ) {
		if ( 'post_tag' !== $taxonomy ) {
			return $termlink;
		}
		$show = $this->options->get_option( 'person_tag_to_person' );
		if ( '1' !== $show ) {
			return $termlink;
		}
		$post_id = get_term_meta( $term->term_id, $this->post_type_name, true );
		if ( empty( $post_id ) ) {
			$post = get_page_by_title( $term->name, OBJECT, $this->post_type_name );
			if ( $post ) {
				$post_id = $post->ID;
				add_term_meta( $term->term_id, $this->post_type_name, $post->ID, true );
			}
		}
		if ( ! empty( $post_id ) ) {
			$link = get_permalink( $post_id );
			if ( false === $link ) {
				delete_term_meta( $term->term_id, $this->post_type_name );
			} else {
				return $link;
			}
		}
		return $termlink;
	}

	public function maybe_add_person_nation( $post_ID, $nation ) {
		if ( empty( $nation ) ) {
			return;
		}
		$meta_key = $this->options->get_option_name( 'personal_nation' );
		$value    = get_post_meta( $post_ID, $meta_key, true );
		if ( empty( $value ) ) {
			add_post_meta( $post_ID, $meta_key, $nation, true );
		}
	}

	private function get_code( $post_ID ) {
		$meta_key = $this->options->get_option_name( 'personal_nation' );
		return get_post_meta( $post_ID, $meta_key, true );
	}

	private function get_flag( $post_ID ) {
		$code = $this->get_code( $post_ID );
		if ( empty( $code ) ) {
			return '';
		}
		$file    = sprintf(
			'%s/assets/images/flags/%s.svg',
			dirname( $this->base ),
			strtolower( $code )
		);
		$content = $code;
		if ( is_file( $file ) ) {
			$content = file_get_contents( $file );
		}
		return $content . ' ';
	}

	public function add_flag_to_single_title( $post_title, $post_ID ) {
		if ( is_admin() ) {
			return $post_title;
		}
		if ( $this->show_single_person_flag ) {
			$post_type = get_post_type( $post_ID );
			if ( $post_type === $this->post_type_name ) {
				$code = $this->get_code( $post_ID );
				if ( ! empty( $code ) ) {
					return sprintf( '<span class="flag flag-%s">%s</span>', esc_attr( strtolower( $code ) ), $post_title );
				}
			}
		}
		return $post_title;
	}
}

