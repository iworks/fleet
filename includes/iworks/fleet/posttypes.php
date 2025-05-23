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

if ( class_exists( 'iworks_fleet_posttypes' ) ) {
	return;
}

class iworks_fleet_posttypes {
	protected $base;
	protected $fields;
	protected $labels = array();
	protected $options;
	protected $post_type_name;
	protected $show_single_boat_flag   = false;
	protected $show_single_person_flag = false;
	protected $taxonomy_name_location  = 'iworks_fleet_location';
	protected $taxonomy_name_serie     = 'iworks_fleet_serie';
	/**
	 * Root for the plugin files
	 *
	 * @since 2.2.0
	 */
	private $root;

	/**
	 * Trophies Names
	 */
	protected $trophies_names = array();

	/**
	 * Debug
	 */
	protected $debug = false;

	/**
	 * MNA country codes array
	 *
	 * @since 2.0.1
	 */
	private $mna_codes = array();

	/**
	 * Cache
	 *
	 * @since 2.3.0
	 */
	protected $cache = array(
		'person' => array(),
	);

	public function __construct() {
		$this->options = iworks_fleet_get_options_object();
		$this->base    = preg_replace( '/\/iworks.+/', '/', __FILE__ );
		/**
		 * debug
		 */
		$this->debug = apply_filters(
			'iworks:fleet:debug',
			defined( 'WP_DEBUG' ) && WP_DEBUG
		);
		/**
		 * set plugin root
		 *
		 * @since 2.3.4
		 */
		$this->root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
		/**
		 * show sigle person flag
		 */
		$this->show_single_person_flag = $this->options->get_option( 'person_show_flag_on_single' );
		$this->show_single_boat_flag   = $this->options->get_option( 'boat_show_flag' );
		/**
		 * Trophies names
		 */
		$this->trophies_names = array(
			'world'       => __( 'World Champ', 'fleet' ),
			'continental' => __( 'Continental Championship', 'fleet' ),
			'national'    => __( 'National Championship', 'fleet' ),
		);
		/**
		 * OpenGraph
		 */
		add_filter( 'og_array', array( $this, 'og_array' ) );
		/**
		 * register
		 */
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'init', array( $this, 'register_taxonomy_location' ), 9 );
		add_filter( 'body_class', array( $this, 'add_wide_body_class' ) );
		/**
		 * save post
		 */
		add_action( 'save_post', array( $this, 'save_post_meta' ), 10, 3 );
		/**
		 * save map data
		 */
		add_action( 'created_' . $this->taxonomy_name_location, array( $this, 'save_google_map_data' ), 10, 2 );
		add_action( 'edited_' . $this->taxonomy_name_location, array( $this, 'save_google_map_data' ), 10, 2 );
		add_filter( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
		/**
		 * TwentyTwenty integration
		 */
		add_filter( 'twentytwenty_disallowed_post_types_for_meta_output', array( $this, 'twentytwenty_disallowed_post_types_for_meta_output' ) );
		/**
		 * stats
		 */
		add_filter( 'int505_archive_stats_table_row', array( $this, 'filter_int505_archive_stats_table_row' ) );
	}

	public function dashboard_glance_items( $elements ) {
		$num_posts = wp_count_posts( $this->post_type_name );
		if ( $num_posts && $num_posts->publish ) {
			$post_type_object = get_post_type_object( $this->post_type_name );
			$text             = sprintf( '%s %s', number_format_i18n( $num_posts->publish ), $post_type_object->labels->singular_name );
			if ( 1 < $num_posts->publish ) {
				$text = sprintf( '%s %s', number_format_i18n( $num_posts->publish ), $post_type_object->labels->name );
			}
			if ( $post_type_object && current_user_can( $post_type_object->cap->edit_posts ) ) {
				$elements[] = sprintf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $this->post_type_name, $text );
			} else {
				$elements[] = sprintf( '<li class="%1$s-count"><span>%2$s</span></li>', $this->post_type_name, $text );
			}
		}
		return $elements;
	}

	public function get_name() {
		return $this->post_type_name;
	}

	protected function get_meta_box_content( $post, $fields, $group ) {
		$content  = '';
		$basename = $this->options->get_option_name( $group );
		foreach ( $fields[ $group ] as $key => $data ) {
			$args = isset( $data['args'] ) ? $data['args'] : array();
			/**
			 * ID
			 */
			$args['id'] = $this->options->get_option_name( $group . '_' . $key );
			/**
			 * name
			 */
			$name = sprintf( '%s[%s]', $basename, $key );
			/**
			 * sanitize type
			 */
			$type = isset( $data['type'] ) ? $data['type'] : 'text';
			/**
			 * get value
			 */
			$value = get_post_meta( $post->ID, $args['id'], true );
			/**
			 * Handle select2
			 */
			if ( ! empty( $value ) && 'select2' === $type ) {
				$value = array(
					'value' => $value,
					'label' => get_the_title( $value ),
				);
			}
			/**
			 * Handle date
			 */
			if ( ! empty( $value ) && 'date' === $type ) {
				$value = date_i18n( 'Y-m-d', $value );
			}
			/**
			 * Handle checkbox
			 */
			if ( 'checkbox' === $type ) {
				$args['default'] = 'yes';
				if ( 'yes' === $value ) {
					$args['checked'] = 'checked';
				}
			}
			/**
			 * classes
			 */
			$classes = array(
				'iworks-fleet-row',
				sprintf( 'iworks-fleet-row-%s">', esc_attr( $key ) ),
			);
			if ( isset( $data['description'] ) ) {
				$classes[] = 'iworks-fleet-row-has-description';
			}
			/**
			 * build
			 */
			$content .= sprintf( '<div class="%s">', esc_attr( implode( ' ', $classes ) ) );
			// $content .= '<div class="iworks-fleet-row-container">';
			if ( isset( $data['label'] ) && ! empty( $data['label'] ) ) {
				$content .= sprintf( '<label for=%s">%s</label>', esc_attr( $args['id'] ), esc_html( $data['label'] ) );
			}
			$content .= $this->options->get_field_by_type( $type, $name, $value, $args );
			// $content .= '</div>';
			if ( isset( $data['description'] ) ) {
				$content .= sprintf( '<p class="description">%s</p>', $data['description'] );
			}
			$content .= '</div>';
		}
		echo $content;
	}

	/**
	 * Save post metadata when a post is saved.
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function save_post_meta_fields( $post_id, $post, $update, $fields ) {
		/*
		 * In production code, $slug should be set only once in the plugin,
		 * preferably as a class property, rather than in each function that needs it.
		 */
		$post_type = get_post_type( $post_id );
		// If this isn't a Copyricorrect post, don't update it.
		if ( $this->post_type_name != $post_type ) {
			return false;
		}
		foreach ( $fields as $group => $group_data ) {
			$post_key = $this->options->get_option_name( $group );
			if ( isset( $_POST[ $post_key ] ) ) {
				foreach ( $group_data as $key => $data ) {
					$value = isset( $_POST[ $post_key ][ $key ] ) ? $_POST[ $post_key ][ $key ] : null;
					if ( is_string( $value ) ) {
						$value = trim( $value );
					} elseif ( is_array( $value ) ) {
						if (
							isset( $value['integer'] ) && 0 == $value['integer']
							&& isset( $value['fractional'] ) && 0 == $value['fractional']
						) {
							$value = null;
						}
					}
					$option_name = $this->options->get_option_name( $group . '_' . $key );
					if ( empty( $value ) ) {
						if (
							isset( $data['save_zero'] )
							&& 'yes' === $data['save_zero']
						) {
							$value = 0;
						} else {
							delete_post_meta( $post->ID, $option_name );
						}
					} else {
						if ( isset( $data['type'] ) ) {
							switch ( $data['type'] ) {
								case 'date':
									$value = strtotime( $value );
									break;
								case 'checkbox':
									$value = 'yes';
									break;
							}
						}
						/**
						 * filter
						 */
						$value  = apply_filters( 'iworks_fleet_meta_value', $value, $post->ID, $option_name );
						$result = add_post_meta( $post->ID, $option_name, $value, true );
						if ( ! $result ) {
							update_post_meta( $post->ID, $option_name, $value );
						}
						do_action( 'iworks_fleet_posttype_update_post_meta', $post->ID, $option_name, $value, $key, $data );
					}
				}
			}
		}
		return true;
	}

	/**
	 * Check post type
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_ID Post ID to check.
	 * @returns boolean is correct post type or not
	 */
	public function check_post_type_by_id( $post_ID ) {
		$post = get_post( $post_ID );
		if ( empty( $post ) ) {
			return false;
		}
		if ( $this->post_type_name == $post->post_type ) {
			return true;
		}
		return false;
	}

	/**
	 * Return counter of published posts by post type.
	 *
	 * @since 1.0.0
	 */
	public function count() {
		if ( empty( $this->post_type_name ) ) {
			return 0;
		}
		$counter = wp_count_posts( $this->post_type_name );
		if ( ! is_object( $counter ) ) {
			return 0;
		}
		if ( isset( $counter->publish ) ) {
			return $counter->publish;
		}
		return 0;
	}

	/**
	 * add where order to prev/next post links
	 *
	 * @since 1.0.0
	 */
	public function adjacent_post_where( $sql, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		if ( $post->post_type === $this->post_type_name ) {
			global $wpdb;
			$post_title = $wpdb->_real_escape( $post->post_title );
			$sql        = preg_replace( '/p.post_date ([<> ]+) \'[^\']+\'/', "p.post_title $1 '{$post_title}'", $sql );
		}
		return $sql;
	}

	/**
	 * add sort order to prev/next post links
	 *
	 * @since 1.0.0
	 */
	public function adjacent_post_sort( $sql, $post, $order ) {
		if ( $post->post_type === $this->post_type_name ) {
			$sql = sprintf( 'ORDER BY p.post_title %s LIMIT 1', $order );
		}
		return $sql;
	}

	public function register_taxonomy_location() {
		/**
		 * check to add
		 */
		$filter = sprintf( 'iworks/fleet/register_taxonomy/%s/add', $this->post_type_name );
		if ( ! apply_filters( $filter, true ) ) {
			return;
		}
		/**
		 * Locations  Taxonomy.
		 */
		$labels = array(
			'name'                       => _x( 'Locations', 'Taxonomy General Name', 'fleet' ),
			'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'fleet' ),
			'menu_name'                  => __( 'Locations', 'fleet' ),
			'all_items'                  => __( 'All Locations', 'fleet' ),
			'new_item_name'              => __( 'New Location Name', 'fleet' ),
			'add_new_item'               => __( 'Add New Location', 'fleet' ),
			'edit_item'                  => __( 'Edit Location', 'fleet' ),
			'update_item'                => __( 'Update Location', 'fleet' ),
			'view_item'                  => __( 'View Location', 'fleet' ),
			'separate_items_with_commas' => __( 'Separate Locations with commas', 'fleet' ),
			'add_or_remove_items'        => __( 'Add or remove Locations', 'fleet' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
			'popular_items'              => __( 'Popular Locations', 'fleet' ),
			'search_items'               => __( 'Search Locations', 'fleet' ),
			'not_found'                  => __( 'Not Found', 'fleet' ),
			'no_terms'                   => __( 'No Locations', 'fleet' ),
			'items_list'                 => __( 'Locations list', 'fleet' ),
			'items_list_navigation'      => __( 'Locations list navigation', 'fleet' ),
		);
		$args   = array(
			'labels'             => $labels,
			'hierarchical'       => true,
			'public'             => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'rewrite'            => array(
				'slug' => 'fleet-locations',
			),
		);
		register_taxonomy( $this->taxonomy_name_location, array( $this->post_type_name ), $args );
	}

	protected function get_cache_key( $data, $prefix = '' ) {
		$key = sprintf(
			'dingy-%s-%s',
			$prefix,
			md5( serialize( $data ) )
		);
		$key = substr( $key, 0, 172 );
		return $key;
	}

	protected function get_cache( $key ) {
		if ( $this->debug ) {
			return false;
		}
		$cache = get_transient( $key );
		return $cache;
	}

	protected function set_cache( $data, $key, $expiration = false ) {
		if ( empty( $expiration ) ) {
			$expiration = DAY_IN_SECONDS;
		}
		set_transient( $key, $data, $expiration );
	}

	public function apply_countries_selector( $query ) {
		if ( is_admin() ) {
			return;
		}
		if ( ! $query->is_main_query() ) {
			return;
		}
		if ( ! $query->is_post_type_archive() ) {
			return;
		}
		if ( $query->get( 'post_type' ) !== $this->post_type_name ) {
			return;
		}
		$filter = $this->options->get_option( 'country' );
		if ( empty( $filter ) ) {
			return;
		}
		$meta_key = null;
		switch ( $query->get( 'post_type' ) ) {
			case $this->options->get_option_name( 'boat' ):
				$meta_key = $this->options->get_option_name( 'boat_nation' );
				break;
			case $this->options->get_option_name( 'person' ):
				$meta_key = $this->options->get_option_name( 'personal_nation' );
				break;
		}
		if ( empty( $meta_key ) ) {
			return;
		}
		$meta_query = array(
			'relation' => 'OR',
		);
		foreach ( $filter as $value ) {
			$meta_query[] = array(
				'key'   => $meta_key,
				'value' => $value,
			);
		}
		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * cache MNA codes
	 *
	 * @since 2.0.1
	 */
	private function get_mna_codes() {
		if ( empty( $this->mna_codes ) ) {
			$this->mna_codes = $this->options->get_group( 'mna_codes' );
		}
		return $this->mna_codes;
	}

	/**
	 * Get nations list from config
	 *
	 * @since 1.3.0
	 */
	protected function get_nations() {
		$data      = array();
		$mna_codes = $this->get_mna_codes();
		if ( ! empty( $mna_codes ) ) {
			foreach ( $mna_codes as $code ) {
				if ( empty( $code['code'] ) ) {
					continue;
				}
				$data[ $code['code'] ] = sprintf( '%s (%s)', $code['nation'], $code['code'] );
			}
			asort( $data );
		}
		return array_merge(
			array(
				'-' => __( 'select nation', 'fleet' ),
			),
			$data
		);
	}

	/**
	 * get country code by name
	 *
	 * @since 2.0.1
	 */
	protected function get_country_code_by_country_name( $name ) {
		switch ( $name ) {
			case 'UK':
				$name = 'Great Britain';
				break;
			case 'EU':
				return 'eu';
		}
		$mna_codes = $this->get_mna_codes();
		foreach ( $mna_codes as $one ) {
			if ( isset( $one['nation'] ) && $one['nation'] === $name ) {
				return $one['code'];
			}
			if ( isset( $one['en'] ) && $one['en'] === $name ) {
				return $one['code'];
			}
		}
		return new WP_Error( 'missing-mna-code', __( 'Missing MNA county code.', 'fleet' ) );
	}

	/**
	 * Get colors list from config
	 *
	 * @since 1.3.0
	 */
	protected function get_colors() {
		$data = $this->options->get_group( 'colors' );
		asort( $data );
		return array_merge(
			array(
				'' => __( 'select color', 'fleet' ),
			),
			$data
		);
	}
	/**
	 * Add wide body class
	 *
	 * @since 1.3.0
	 */
	public function add_wide_body_class( $classes ) {
		if ( $this->options->get_option( 'wide_class' ) ) {
			if ( is_singular( $this->post_type_name ) ) {
				$classes[] = 'template-full-width';
			}
		}
		return $classes;
	}

	/**
	 * twentytwenty disallowed post types for meta output
	 *
	 */
	public function twentytwenty_disallowed_post_types_for_meta_output( $post_types ) {
		$post_types[] = $this->post_type_name;
		return $post_types;
	}

	/**
	 * TRIM
	 */
	protected function data_trim( $data ) {
		$data = preg_replace( '/ /', ' ', $data );
		$data = preg_replace( '/[ \t\r\n]+/', ' ', $data );
		return trim( $data );
	}

	/**
	 * OpenGraph integration with OG plugin.
	 *
	 * @since 1.3.0
	 */
	protected function og_array_add( $og, $type ) {
		$counter = 1;
		$format  = get_option( 'date_format' );
		foreach ( $this->fields[ $type ] as $key => $data ) {
			if ( ! isset( $data['twitter'] ) ) {
				continue;
			}
			if ( 'yes' !== $data['twitter'] ) {
				continue;
			}
			$name  = $this->options->get_option_name( $type . '_' . $key );
			$value = get_post_meta( get_the_ID(), $name, true );
			if ( empty( $value ) ) {
				continue;
			}
			switch ( $key ) {
				case 'date_start':
				case 'date_end':
					$value = date_i18n( $format, $value );
			}
			if ( ! isset( $og['twitter'] ) ) {
				$og['twitter'] = array();
			}
			$og['twitter'][ 'label' . $counter ] = $data['label'];
			$og['twitter'][ 'data' . $counter ]  = $value;
			$counter++;
		}
		return $og;
	}

	public function save_google_map_data( $term_id, $tt_id ) {
		$location   = $this->get_location_array( array(), $term_id );
		$meta_value = $this->google_get_one( implode( ', ', $location ) );
		delete_term_meta( $term_id, 'google' );
		add_term_meta( $term_id, 'google', $meta_value, true );
	}

	private function get_location_array( $location, $term_id ) {
		$term       = get_term( $term_id, $this->taxonomy_name_location );
		$location[] = $term->name;
		if ( 0 != $term->parent ) {
			return $this->get_location_array( $location, $term->parent );
		}
		return $location;
	}

	private function google_get_one( $url, $encoded = false ) {
		$data = array();
		if ( ! $encoded ) {
			$url = urlencode( $url );
		}
		$args                 = array(
			'address' => $url,
			'sensor'  => 'false',
		);
		$google_maps_data_url = add_query_arg( $args, 'http://maps.google.com/maps/api/geocode/json' );
		$response             = wp_remote_get( $google_maps_data_url );
		if ( is_array( $response ) ) {
			$data = json_decode( $response['body'] );
			if ( 'OK' == $data->status && count( $data->results ) ) {
				$data = $data->results[0];
				$data = json_decode( json_encode( $data ), true );
			}
		}
		return $data;
	}

	public function filter_int505_archive_stats_table_row( $content ) {
		$post_type_object = get_post_type_object( $this->post_type_name );
		if ( empty( $post_type_object ) || is_wp_error( $post_type_object ) ) {
			return $content;
		}
		if ( false === $post_type_object->has_archive ) {
			return $content;
		}
		$content .= sprintf(
			'<li class="fleet-stats-row fleet-stats-row-%s">',
			esc_attr( $this->post_type_name )
		);
		$content .= sprintf(
			'<span class="fleet-stats-row-title"><a href="%s">%s</a></span>',
			get_post_type_archive_link( $this->post_type_name ),
			esc_html( $this->labels['name'] )
		);
		$content .= ' &#8212; ';
		$content .= sprintf(
			'<span class="fleet-stats-row-value">%d</span>',
			wp_count_posts( $this->post_type_name )->publish
		);
		$content .= '</li>';
		return $content;
	}

	protected function get_series_array() {
		$terms = get_terms(
			$this->taxonomy_name_serie,
			array(
				'taxonomy'   => $this->taxonomy_name_serie,
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $terms ) ) {
			return array();
		}
		$series[] = esc_html__( '&mdash; Select &mdash;', 'fleet' );
		foreach ( $terms as $one ) {
			$series[ $one->term_id ] = $one->name;
		}
		return $series;
	}

	/**
	 * get template
	 *
	 * @since 2.2.0
	 */
	private function get_file( $file, $group = '' ) {
		return sprintf(
			'%s/assets/templates/%s%s.php',
			$this->root,
			'' === $group ? '' : sanitize_title( $group ) . '/',
			$file
		);
	}

	protected function get_template( $file_name, $group, $args = array() ) {
		$file = $this->get_file( $file_name, $group );
		ob_start();
		load_template( $file, false, $args );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	protected function get_option_name_helper( $group, $key ) {
		return $this->options->get_option_name( $group . '_' . $key );
	}
}

