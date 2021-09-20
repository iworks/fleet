<?php
/*
Copyright 2018-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_fleet_posttypes_result' ) ) {
	return;
}

require_once dirname( dirname( __FILE__ ) ) . '/posttypes.php';

class iworks_fleet_posttypes_result extends iworks_fleet_posttypes {

	/**
	 * Post type name
	 */
	protected $post_type_name = 'iworks_fleet_result';
	/**
	 * Sinle crew meta field name
	 */
	private $single_crew_field_name = 'iworks_fleet_result_crew';
	/**
	 * Sinle result meta field name
	 */
	private $single_result_field_name = 'iworks_fleet_result_result';
	protected $taxonomy_name_serie    = 'iworks_fleet_serie';
	/**
	 * sailors to id
	 */
	private $sailors = array();
	/**
	 * show points
	 */
	private $show_points = false;

	/**
	 * Dinghy post type of Boat
	 */
	private $boat_post_type;

	/**
	 * Last result exception
	 */
	private $get_last_results_html_ids = array();

	/**
	 * suppress filter pre get posts limit to year
	 *
	 * @since 2.0.4
	 */
	private $suppress_filter_pre_get_posts_limit_to_year = false;

	public function __construct() {
		parent::__construct();
		/**
		 * set show points
		 */
		if ( is_object( $this->options ) ) {
			$this->show_points = ! empty( $this->options->get_option( 'results_show_points' ) );
		}
		/**
		 * filter content
		 */
		add_filter( 'the_content', array( $this, 'the_content' ) );
		/**
		 * change default columns
		 */
		add_filter( "manage_{$this->get_name()}_posts_columns", array( $this, 'add_columns' ) );
		add_filter( "manage_{$this->get_name()}_posts_custom_column", array( $this, 'column' ), 10, 2 );
		add_filter( "manage_edit-{$this->post_type_name}_sortable_columns", array( $this, 'add_sortable_columns' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
		/**
		 * download results
		 */
		add_action( 'plugins_loaded', array( $this, 'download' ), PHP_INT_MAX );
		/**
		 * fields
		 */
		$this->fields = array(
			'result' => array(
				'english'               => array(
					'label'   => __( 'English name', 'fleet' ),
					'twitter' => 'yes',
				),
				'location'              => array(
					'label'   => __( 'Area', 'fleet' ),
					'twitter' => 'yes',
				),
				'organizer'             => array( 'label' => __( 'Organizer', 'fleet' ) ),
				'secretary'             => array( 'label' => __( 'Secretary', 'fleet' ) ),
				'arbiter'               => array( 'label' => __( 'Arbiter', 'fleet' ) ),
				'date_start'            => array(
					'type'    => 'date',
					'label'   => __( 'Event start', 'fleet' ),
					'twitter' => 'yes',
				),
				'date_end'              => array(
					'type'    => 'date',
					'label'   => __( 'Event end', 'fleet' ),
					'twitter' => 'yes',
				),
				'number_of_races'       => array(
					'type'    => 'number',
					'label'   => __( 'Number of races', 'fleet' ),
					'twitter' => 'yes',
				),
				'number_of_competitors' => array(
					'type'    => 'number',
					'label'   => __( 'Number of competitors', 'fleet' ),
					'twitter' => 'yes',
				),
				'wind_direction'        => array( 'label' => __( 'Wind direction', 'fleet' ) ),
				'wind_power'            => array( 'label' => __( 'Wind power', 'fleet' ) ),
				'columns'               => array(
					'label'       => __( 'Custom columns name', 'fleet' ),
					'type'        => 'textarea',
					'description' => __( 'Add one column per line if you want to have a custom race column header.', 'fleet' ),
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
		 * handle results
		 */
		add_action( 'wp_ajax_iworks_fleet_upload_races', array( $this, 'upload' ) );
		/**
		 * content filters
		 */
		add_filter( 'iworks_fleet_result_sailor_regata_list', array( $this, 'regatta_list_by_sailor_id' ), 10, 2 );
		add_filter( 'iworks_fleet_result_sailor_last_regatta', array( $this, 'filter_get_last_regatta_by_sailor_id' ), 10, 2 );
		add_filter( 'iworks_fleet_result_boat_regatta_list', array( $this, 'regatta_list_by_boat_id' ), 10, 2 );
		add_filter( 'iworks_fleet_result_serie_regatta_list', array( $this, 'filter_regatta_list_by_serie_slug' ), 10, 3 );
		add_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		add_filter( 'iworks_fleet_result_sailor_trophies', array( $this, 'get_trophies_by_sailor_id' ), 10, 2 );
		/**
		 * save custom slug
		 */
		add_action( 'save_post', array( $this, 'set_slug' ), 10, 3 );
		/**
		 * change default sort order
		 */
		add_action( 'pre_get_posts', array( $this, 'change_order' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts_limit_to_year' ) );
		/**
		 * shortcodes
		 */
		add_shortcode( 'fleet_regattas_list', array( $this, 'shortcode_list' ) );
		add_shortcode( 'fleet_ranking', array( $this, 'shortcode_ranking' ) );
		add_shortcode( 'fleet_regattas_list_years', array( $this, 'shortcode_years_links' ) );
		add_shortcode( 'fleet_regattas_list_countries', array( $this, 'shortcode_countries_links' ) );
		/**
		 * sort next/previous links by title
		 */
		add_filter( 'get_previous_post_sort', array( $this, 'adjacent_post_sort' ), 10, 3 );
		add_filter( 'get_next_post_sort', array( $this, 'adjacent_post_sort' ), 10, 3 );
		add_filter( 'get_previous_post_where', array( $this, 'adjacent_post_where' ), 10, 5 );
		add_filter( 'get_next_post_where', array( $this, 'adjacent_post_where' ), 10, 5 );
		add_filter( 'get_previous_post_join', array( $this, 'adjacent_post_join' ), 10, 5 );
		add_filter( 'get_next_post_join', array( $this, 'adjacent_post_join' ), 10, 5 );
		/**
		 * adjust dates
		 */
		add_filter( 'iworks_fleet_result_adjust_dates', array( $this, 'adjacent_dates' ), 10, 3 );
		/**
		 * import
		 */
		add_action( 'iworks_fleet_result_import_data', array( $this, 'import_data' ), 10, 2 );
		add_filter( 'wp_localize_script_fleet_admin', array( $this, 'add_import_js_messages' ) );
		/**
		 * add month & year archive
		 */
		add_action( 'init', array( $this, 'add_rewrite_rules' ), 11 );
		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ), 10, 3 );
	}

	public function get_trophies_by_sailor_id( $content, $sailor_id ) {
		global $wpdb;
		$cache_key = $this->options->get_option_name( 'trophies_' . $sailor_id );
		$cache     = $this->get_cache( $cache_key );
		if ( ! empty( $cache ) ) {
			$content .= $cache;
			return $content;
		}
		$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
		$query              = $wpdb->prepare(
			"SELECT post_regata_id, year, place FROM {$table_name_regatta} where ( helm_id = %d or crew_id = %d ) and place < 4 order by year",
			$sailor_id,
			$sailor_id
		);
		$regatta            = $wpdb->get_results( $query );
		if ( empty( $regatta ) ) {
			return $content;
		}
		/**
		 * ids
		 */
		$year = array();
		$ids  = array();
		foreach ( $regatta as $one ) {
			$ids[]                        = $one->post_regata_id;
			$year[ $one->post_regata_id ] = array(
				'place' => $one->place,
				'year'  => $one->year,
			);
		}
		if ( empty( $ids ) ) {
			return $content;
		}
		/**
		 * series
		 */
		$series = array();
		foreach ( $this->trophies_names as $one => $label ) {
			$serie = $this->options->get_option( 'results_serie_trophy_' . $one );
			if ( ! empty( $serie ) ) {
				$series[ $one ] = $serie;
			}
		}
		if ( empty( $series ) ) {
			return $content;
		}
		$trophies = array();
		$args     = array(
			'post_type'      => $this->post_type_name,
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => $this->taxonomy_name_serie,
					'terms'    => array_values( $series ),
				),
			),
			'post__in'       => $ids,
			'fields'         => 'ids',
		);
		/**
		 * WP_Query
		 */
		$the_query = new WP_Query( $args );
		if ( empty( $the_query->posts ) ) {
			return $content;
		}
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		foreach ( $the_query->posts as $post_id ) {
			$type = '';
			foreach ( $series as $serie => $term_id ) {
				if ( has_term( $term_id, $this->taxonomy_name_serie, $post_id ) ) {
					$type = $serie;
				}
			}
			if ( empty( $type ) ) {
				continue;
			}
			$trophies[] = array(
				'type'    => $type,
				'place'   => $year[ $post_id ]['place'],
				'year'    => $year[ $post_id ]['year'],
				// 'url' => get_permalink( $post_id ),
				'post_id' => $post_id,
				'title'   => get_the_title( $post_id ),
			);
		}
		add_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		if ( empty( $trophies ) ) {
			return $content;
		}
		$cache  = '';
		$cache .= '<div class="iworks-fleet-trophies">';
		$cache .= sprintf( '<h2>%s</h2>', esc_html__( 'Trophies', 'fleet' ) );
		$cache .= '<table>';
		foreach ( $this->trophies_names as $trophy_key => $trophy_label ) {
			$trophy_content = '';
			foreach ( $trophies as $one ) {
				if ( $trophy_key !== $one['type'] ) {
					continue;
				}
				$trophy_content .= sprintf( '<li class="fleet-type-%s fleet-place-%d">', esc_attr( $one['type'] ), $one['place'] );
				$trophy_content .= sprintf(
					'<a href="#fleet-regatta-%d" title="%s">',
					esc_attr( $one['post_id'] ),
					esc_attr( $one['title'] )
				);
				$trophy_content .= '<span class="trophy"></span>';
				$trophy_content .= sprintf( '<span class="year">%d</span>', $one['year'] );
				$trophy_content .= '</a></li>';
			}
			if ( $trophy_content ) {
				$cache .= sprintf(
					'<tr class="iworks-fleet-trophy iworks-fleet-trophy-%s">',
					esc_attr( $trophy_key )
				);
				$cache .= '<tr>';
				$cache .= sprintf( '<td>%s</td>', $trophy_label );
				$cache .= sprintf( '<td><ul>%s</ul></td>', $trophy_content );
				$cache .= '</tr>';
			}
		}
		$cache .= '</table>';
		$cache .= '</div>';
		set_transient( $cache_key, $cache, DAY_IN_SECONDS );
		return $content . $cache;
	}


	public function filter_get_series_array( $series ) {
		$terms = get_terms(
			$this->taxonomy_name_serie,
			array(
				'taxonomy'   => $this->taxonomy_name_serie,
				'hide_empty' => false,
			)
		);
		foreach ( $terms as $one ) {
			$series[ $one->term_id ] = $one->name;
		}
		return $series;
	}

	/**
	 * Add messages
	 *
	 */
	public function add_import_js_messages( $array ) {
		if ( ! isset( $array['messages'] ) ) {
			$array['messages'] = array();
		}
		if ( ! isset( $array['messages']['result'] ) ) {
			$array['messages']['result'] = array();
		}
		$array['messages']['result']['choose_file_first'] = esc_html__( 'Please select some file first!', 'fleet' );
		return $array;
	}

	/**
	 * allow to download results
	 */
	public function download() {
		global $wpdb, $iworks_fleet;
		$action  = filter_input( INPUT_GET, 'fleet', FILTER_SANITIZE_STRING );
		$nonce   = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
		$type    = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
		$post_id = intval( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ) );
		/**
		 * check input data
		 */
		if ( empty( $action ) || 'download' !== $action ) {
			return;
		}
		if ( empty( $type ) ) {
			return;
		}
		if ( empty( $post_id ) || 1 > $post_id ) {
			return;
		}
		if ( ! wp_verify_nonce( $nonce, $post_id ) ) {
			return;
		}
		/**
		 * check post type & settings
		 */
		switch ( get_post_type( $post_id ) ) {
			case $this->post_type_name:
				$open_file = $this->options->get_option( 'result_show_download_link' );
				$action    = 'regatta';
				break;
			case $iworks_fleet->get_post_type_name( 'person' ):
				$open_file = $this->options->get_option( 'person_show_download_link' );
				$action    = 'person';
				break;
			case $iworks_fleet->get_post_type_name( 'boat' ):
				$open_file = $this->options->get_option( 'boat_show_download_link' );
				$action    = 'boat';
				break;
			default:
				return;
		}
		/**
		 * Check there is anything to show
		 */
		$regattas = array();
		switch ( get_post_type( $post_id ) ) {
			case $iworks_fleet->get_post_type_name( 'person' ):
				$regattas = $this->get_list_by_sailor_id( $post_id );
				l( $regattas );
				if ( empty( $regattas ) ) {
					return;
				}
				break;
			case $iworks_fleet->get_post_type_name( 'boat' ):
				$regattas = $this->get_list_by_boat_id( $post_id );
				if ( empty( $regattas ) ) {
					return;
				}
				break;
		}

		/**
		 * return if no file
		 */
		if ( false === $open_file ) {
			return;
		}
		/**
		 * file
		 */
		$file = sprintf(
			'%s.csv',
			sanitize_title(
				sprintf(
					'%s-%s-%s',
					date( 'Y-m-d-h-i' ),
					$action,
					get_the_title( $post_id )
				)
			)
		);
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename=' . $file );
		$out = fopen( 'php://output', 'w' );
		/**
		 * do not add year to title
		 */
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		/**
		 * init of variables
		 */
		$row    = array();
		$format = 'Y-m-d';
		/**
		 * get data
		 */
		switch ( get_post_type( $post_id ) ) {
			/**
			 * Person or boat
			 */
			case $iworks_fleet->get_post_type_name( 'person' ):
			case $iworks_fleet->get_post_type_name( 'boat' ):
				$row[] = __( 'Date start', 'fleet' );
				$row[] = __( 'Date end', 'fleet' );
				$row[] = __( 'Name', 'fleet' );
				$row[] = __( 'Boat', 'fleet' );
				$row[] = __( 'Helmsman', 'fleet' );
				$row[] = __( 'Crew', 'fleet' );
				$row[] = __( 'Place', 'fleet' );
				$row[] = __( 'Number of competitors', 'fleet' );
				$row[] = __( 'Points', 'fleet' );
				fputcsv( $out, $row );
				foreach ( $regattas as $regatta ) {
					$row   = array();
					$row[] = date_i18n( $format, $regatta->date_start );
					$row[] = date_i18n( $format, $regatta->date_end );
					$row[] = get_the_title( $regatta->post_regata_id );
					$row[] = $regatta->boat_id;
					$row[] = $regatta->helm_name;
					$row[] = $regatta->crew_name;
					$row[] = $regatta->place;
					$row[] = get_post_meta( $regatta->post_regata_id, $this->options->get_option_name( 'result_number_of_competitors' ), true );
					$row[] = $regatta->points;
					fputcsv( $out, $row );
				}
				break;
			/**
			 * CSV of a Event
			 */
			case $this->post_type_name:
				$row[]  = __( 'Place', 'fleet' );
				$row[]  = __( 'Boat', 'fleet' );
				$row[]  = __( 'Helm', 'fleet' );
				$row[]  = __( 'Crew', 'fleet' );
				$number = intval( get_post_meta( $post_id, 'iworks_fleet_result_number_of_races', true ) );
				for ( $i = 1; $i <= $number; $i++ ) {
					$row[] = 'R' . $i;
				}
				$row[] = __( 'Sum', 'fleet' );
				fputcsv( $out, $row );
				$races              = $this->get_races_data( $post_id, 'csv' );
				$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
				$query              = $wpdb->prepare( "SELECT * FROM {$table_name_regatta} where post_regata_id = %d order by place", $post_id );
				$regatta            = $wpdb->get_results( $query );
				foreach ( $regatta as $one ) {
					$row   = array();
					$row[] = $one->place;
					$boat  = $this->get_boat_data_by_number( $one->boat_id );
					$row[] = sprintf( '%s %d', $one->country, $one->boat_id );
					$row[] = $one->helm_name;
					$row[] = $one->crew_name;
					if ( isset( $races[ $one->ID ] ) && ! empty( $races[ $one->ID ] ) ) {
						foreach ( $races[ $one->ID ] as $race_number => $race_points ) {
							$row[] = $race_points;
						}
					}
					$row[] = $one->points;
					fputcsv( $out, $row );
				}
				break;
			default:
				return;
		}
		fclose( $out );
		exit;
	}

	public function shortcode_list_helper_table_start( $serie_show_image, $serie_show_location_country ) {
		$content  = '<table class="fleet-results fleet-results-list">';
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= sprintf( '<th class="dates">%s</th>', esc_attr__( 'Dates', 'fleet' ) );
		if ( $serie_show_image ) {
			$content .= sprintf( '<th class="serie">%s</th>', '&nbsp' );
		}
		$content .= sprintf( '<th class="title">%s</th>', esc_attr__( 'Title', 'fleet' ) );
		if ( $serie_show_location_country ) {
			$content .= sprintf( '<th class="area">%s</th>', esc_attr__( 'Area', 'fleet' ) );
		}
		$content .= sprintf( '<th class="races">%s</th>', esc_attr__( 'Races', 'fleet' ) );
		$content .= sprintf( '<th class="teams">%s</th>', esc_attr__( 'Teams', 'fleet' ) );
		$content .= '</tr>';
		$content .= '<thead>';
		$content .= '<tbody>';
		return $content;
	}

	public function shortcode_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'year'               => date( 'Y' ),
				'serie'              => null,
				'title'              => __( 'Results', 'fleet' ),
				'title_show'         => 'on',
				'area_show'          => 'on',
				'year_show'          => 'on',
				'serie_show'         => 'off',
				'country_show'       => 'off',
				'country'            => $this->options->get_option( 'results_show_country' ) ? 'on' : 'off',
				'order'              => 'DESC',
				'show_english_title' => $this->options->get_option( 'result_show_english_title' ) ? 'on' : 'off',
			),
			$atts,
			'fleet_results_list'
		);
		/**
		 * cache
		 */
		$cache_key = $this->options->get_option_name( __FUNCTION__ . '_' . md5( serialize( $atts ) ) );
		$cache     = $this->get_cache( $cache_key );
		if ( false !== $cache ) {
			return $cache;
		}
		/**
		 * boolean params
		 */
		$show_area             = preg_match( '/^(on|yes|1)$/i', $atts['area_show'] );
		$show_country          = preg_match( '/^(on|yes|1)$/i', $atts['country_show'] );
		$show_serie            = preg_match( '/^(on|yes|1)$/i', $atts['serie_show'] );
		$show_title            = preg_match( '/^(on|yes|1)$/i', $atts['title_show'] );
		$show_year             = preg_match( '/^(on|yes|1)$/i', $atts['year_show'] );
		$show_location_country = preg_match( '/^(on|yes|1)$/i', $atts['country'] );
		$show_english_title    = preg_match( '/^(on|yes|1)$/i', $atts['show_english_title'] );
		/**
		 * content
		 */
		$content = '';
		/**
		 * params: year
		 */
		$year = $atts['year'];
		if ( 'all' !== $year ) {
			$year = intval( $atts['year'] );
			if ( empty( $year ) ) {
				return '';
			}
		}
		/**
		 * params: order
		 */
		$order = 'asc' === strtolower( $atts['order'] ) ? 'ASC' : 'DESC';
		/**
		 * WP Query base args
		 */
		$args = array(
			'post_type' => $this->post_type_name,
			'nopaging'  => true,
			'orderby'   => 'meta_value_num',
			'order'     => $order,
		);
		/**
		 * year
		 */
		$by_year = false;
		if ( 'all' === $year ) {
			$by_year          = true;
			$args['meta_key'] = $this->options->get_option_name( 'result_date_start' );
		} else {
			$args['meta_query'] = array(
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( ( $year - 1 ) . '-12-31 23:59:59' ),
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( ( $year + 1 ) . '-01-01 00:00:00' ),
					'compare' => '<',
					'type'    => 'NUMERIC',
				),
			);
		}
		/**
		 * serie
		 */
		$serie_show_image = true;
		if ( ! empty( $atts['serie'] ) ) {
			$serie_show_image = false;
			if ( preg_match( '/^\d+$/', $atts['serie'] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy_name_serie,
						'terms'    => $atts['serie'],
					),
				);
			} else {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy_name_serie,
						'field'    => 'name',
						'terms'    => $atts['serie'],
					),
				);
			}
		}
		/**
		 * title
		 */
		if ( 'on' === $atts['title_show'] && ! empty( $atts['title'] ) ) {
			$content .= sprintf( '<h2>%s</h2>', $atts['title'] );
		}
		/**
		 * start
		 */
		$format = get_option( 'date_format' );
		/**
		 * WP_Query
		 */
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
			if ( ! $by_year ) {
				$content .= $this->shortcode_list_helper_table_start( $serie_show_image, $show_location_country );
			}
			$current     = 0;
			$rows        = '';
			$serie_image = array();
			while ( $the_query->have_posts() ) {
				$tbody = '';
				$the_query->the_post();
				if ( $by_year ) {
					$value = $this->get_date( 'start', get_the_ID(), 'Y' );
					if ( $current != $value ) {
						if ( $by_year ) {
							if ( 0 < $current ) {
								$content .= $rows;
								$content .= '</tbody></table>';
								$rows     = '';
							}
							$content .= sprintf( '<h2>%s</h2>', $value );
							$content .= $this->shortcode_list_helper_table_start( $serie_show_image, $show_location_country );
						}
						$current = $value;
					}
				}
				$tbody .= sprintf( '<tr class="%s">', esc_attr( implode( ' ', get_post_class() ) ) );
				/**
				 * start date
				 */
				$start  = $this->get_date( 'start', get_the_ID(), 'U' );
				$end    = $this->get_date( 'end', get_the_ID(), 'U' );
				$date   = $this->get_dates( $start, $end );
				$tbody .= sprintf( '<td class="dates">%s</td>', $date );
				/**
				 * serie images
				 */
				if ( $serie_show_image ) {
					$t     = array();
					$terms = wp_get_post_terms( get_the_ID(), $this->taxonomy_name_serie );
					foreach ( $terms as $term ) {
						if ( ! isset( $serie_image[ $term->term_id ] ) ) {
							$image_id                      = get_term_meta( $term->term_id, 'image', true );
							$serie_image[ $term->term_id ] = array( 'id' => $image_id );
							if ( ! empty( $image_id ) ) {
								$serie_image[ $term->term_id ]['url']   = get_term_link( $term->term_id );
								$serie_image[ $term->term_id ]['image'] = wp_get_attachment_image_src( $image_id, array( 48, 48 ) );
							}
						}
						if ( empty( $serie_image[ $term->term_id ]['id'] ) ) {
							continue;
						}
						$t[] = sprintf(
							'<a href="%s"><img src="%s" alt="%s" title="%s" width="24" height="24" /></a>',
							$serie_image[ $term->term_id ]['url'],
							$serie_image[ $term->term_id ]['image'][0],
							esc_attr( $term->name ),
							esc_attr( $term->name )
						);
					}
					if ( empty( $t ) ) {
						$tbody .= '<td class="series series-empty">&ndash;</td>';
					} else {
						$tbody .= sprintf( '<td class="series"><span>%s</span></td>', implode( ' ', $t ) );
					}
				}
				/**
				 * title
				 */
				$title = get_the_title();
				$en    = '';
				if ( $show_english_title ) {
					$en = $this->get_en_name( get_the_ID() );
				}
				$tbody .= sprintf(
					'<td class="title"><a href="%s">%s</a>%s</td>',
					get_permalink(),
					$title,
					$en
				);
				/**
				 * country
				 */
				if ( $show_location_country ) {
					$terms  = get_the_term_list( get_the_ID(), $this->taxonomy_name_location );
					$tbody .= sprintf( '<td class="area">%s</td>', $terms );
				}
				$check = $this->has_races( get_the_ID() );
				if ( $check ) {
					$tbody .= $this->get_td( 'number_of_races', get_the_ID() );
					$tbody .= $this->get_td( 'number_of_competitors', get_the_ID() );
				} else {
					$tbody .= sprintf(
						'<td class="fleet-no-results" colspan="2"><span>%s</span></td>',
						esc_html__( 'No race results.', 'fleet' )
					);
				}
				$tbody .= '</tr>';
				if ( $by_year ) {
					$rows = $tbody . $rows;
				} else {
					$content .= $tbody;
					$tbody    = '';
				}
			}
			if ( $by_year ) {
				$content .= $rows;
			}
			$content .= $tbody;
			$content .= '</tbody>';
			$content .= '</table>';
			/* Restore original Post Data */
			wp_reset_postdata();
		} else {
			$content .= sprintf( '<p class="no-results">%s</p>', __( 'There is no results.', 'fleet' ) );
		}
		/**
		 * wrap content
		 */
		if ( $content ) {
			$content = sprintf( '<div class="fleet-results">%s</content>', $content );
		}
		/**
		 * Cache
		 */
		$this->set_cache( $content, $cache_key );
		return $content;
	}

	/**
	 * Filter results by year
	 *
	 * @since 1.3.0
	 */
	public function pre_get_posts_limit_to_year( $query ) {
		if ( true === apply_filters( 'suppress_filter_pre_get_posts_limit_to_year', $this->suppress_filter_pre_get_posts_limit_to_year ) ) {
			return;
		}
		if ( ! isset( $query->query['post_type'] ) ) {
			return;
		}
		if ( $this->post_type_name !== $query->query['post_type'] ) {
			return;
		}
		$year = filter_input( INPUT_GET, 'iworks_fleet_result_year', FILTER_VALIDATE_INT );
		if ( empty( $year ) ) {
			$year = get_query_var( 'year' );
			if ( ! empty( $year ) ) {
				$query->set( 'year', null );
			}
		}
		if ( empty( $year ) ) {
			return;
		}
		$query->set( 'nopaging', true );
		$query->set( 'iworks_fleet_result_year', $year );
		$query->set(
			'meta_query',
			array(
				'relation' => 'AND',
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( sprintf( '%d-12-31 23:59:59', $year - 1 ) ),
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( sprintf( '%d-01-01 00:00:00', $year + 1 ) ),
					'compare' => '<',
					'type'    => 'NUMERIC',
				),
			)
		);
	}

	public function change_order( $query ) {
		if ( is_admin() ) {
			if ( __( 'Year' === $query->get( 'orderby' ) ) ) {
				$query->set( 'meta_key', $this->options->get_option_name( 'result_date_start' ) );
				$query->set( 'meta_value_num', 0 );
				$query->set( 'meta_compare', '>' );
				$query->set( 'orderby', 'meta_value_num' );
			}
			return;
		}
		if (
			$query->is_main_query()
			&& isset( $query->query )
			&& (
				(
					isset( $query->query['post_type'] )
					&& $this->post_type_name === $query->query['post_type']
				)
				|| (
					isset( $query->query[ $this->taxonomy_name_serie ] )
					&& ! empty( $query->query[ $this->taxonomy_name_serie ] )
				)
				|| (
					isset( $query->query[ $this->taxonomy_name_location ] )
					&& ! empty( $query->query[ $this->taxonomy_name_location ] )
				)
			)
		) {
			$query->set( 'meta_key', $this->options->get_option_name( 'result_date_start' ) );
			$query->set( 'meta_value_num', 0 );
			$query->set( 'meta_compare', '>' );
			$query->set( 'order', 'DESC' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}

	public function set_slug( $post_id, $post, $update ) {
		if ( $this->post_type_name != $post->post_type ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		remove_action( 'save_post', array( $this, 'set_slug' ), 10, 3 );
		$slug = $this->add_year_to_title( $post->post_title, $post_id );
		$data = array(
			'ID'        => $post_id,
			'post_name' => wp_unique_post_slug( $slug, $post_id, $post->post_status, $post->post_status, null ),
		);
		wp_update_post( $data );
	}

	public function column( $column, $post_id ) {
		switch ( $column ) {
			case 'year':
				$year = $this->get_year( $post_id );
				if ( empty( $year ) ) {
					esc_html_e( 'Start date is not set.', 'fleet' );
				} else {
					printf(
						'<a href="%s">%d</a>',
						add_query_arg( 'iworks_fleet_result_year', intval( $year ) ),
						esc_html( $year )
					);
				}
				break;
		}
	}

	private function get_year( $post_id ) {
		$start = $this->options->get_option_name( 'result_date_start' );
		$start = get_post_meta( $post_id, $start, true );
		if ( empty( $start ) ) {
			return '';
		}
		return date( 'Y', $start );
	}

	public function add_year_to_title( $title, $post_id = null ) {
		$post_type = get_post_type( $post_id );
		if ( $post_type != $this->post_type_name ) {
			return $title;
		}
		if ( apply_filters( 'iworks_fleet_result_skip_year_in_title', false ) ) {
			return $title;
		}
		$year = $this->get_year( $post_id );
		if ( ! empty( $year ) ) {
			return sprintf( '%d - %s', $year, $title );
		}
		return $title;
	}

	private function get_list_by_year( $year ) {
		global $wpdb;
		$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
		$sql                = $wpdb->prepare(
			"select * from {$table_name_regatta} where year = %d order by date, year desc",
			$year
		);
		return $this->add_results_metadata( $wpdb->get_results( $sql ) );
	}

	private function get_list_by_sailor_id( $sailor_id ) {
		global $wpdb;
		$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
		$sql                = $wpdb->prepare(
			"select * from {$table_name_regatta} where helm_id = %d or crew_id = %d order by date, year desc",
			$sailor_id,
			$sailor_id
		);
		$this->suppress_filter_pre_get_posts_limit_to_year = true;
		return $this->add_results_metadata( $wpdb->get_results( $sql ) );
	}

	/**
	 * get last recorded regatta filter
	 *
	 * @since 2.0.0
	 *
	 * @param string $content
	 * @param integer $sailor_id sailor id
	 */
	public function filter_get_last_regatta_by_sailor_id( $content, $sailor_id ) {
		$sailor_id = intval( $sailor_id );
		if ( 1 > $sailor_id ) {
			return $content;
		}
		global $wpdb;
		$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
		$sql                = $wpdb->prepare(
			"select * from {$table_name_regatta} where helm_id = %d or crew_id = %d order by date, year desc limit 1",
			$sailor_id,
			$sailor_id
		);
		$data               = $this->add_results_metadata( $wpdb->get_results( $sql ) );
		if ( empty( $data ) ) {
			return $content;
		}
		$data = $data[0];
		add_filter( 'iworks_fleet_result_skip_year_in_title', '__return_true' );
		if ( intval( $data->helm_id ) === $sailor_id ) {
			$content .= sprintf(
				__( 'As a helmsman in %1$s in the %2$s on %3$s', 'fleet' ),
				$this->get_year_link( $data->year ),
				sprintf( '<a href="%s">%s</a>', get_permalink( $data->post_regata_id ), get_the_title( $data->post_regata_id ) ),
				$data->boat_id
			);
		} elseif ( intval( $data->crew_id ) === $sailor_id ) {
			$content .= sprintf(
				__( 'As a crew in %1$s in the %2$s on %3$s', 'fleet' ),
				$this->get_year_link( $data->year ),
				sprintf( '<a href="%s">%s</a>', get_permalink( $data->post_regata_id ), get_the_title( $data->post_regata_id ) ),
				$data->boat_id
			);
		} else {
			$content .= sprintf(
				__( 'In %1$s in the %2$s on %3$s', 'fleet' ),
				$this->get_year_link( $data->year ),
				sprintf( '<a href="%s">%s</a>', get_permalink( $data->post_regata_id ), get_the_title( $data->post_regata_id ) ),
				$data->boat_id
			);
		}
		remove_filter( 'iworks_fleet_result_skip_year_in_title', '__return_true' );
		return $content;
	}

	private function add_results_metadata( $regattas ) {
		if ( empty( $regattas ) ) {
			return $regattas;
		}
		$cache_key = $this->get_cache_key( $regattas, __FUNCTION__ );
		$cache     = $this->get_cache( $cache_key );
		if ( false !== $cache ) {
			return $cache;
		}
		$ref = array();
		$ids = array();
		$i   = 0;
		foreach ( $regattas as $regatta ) {
			$ids[]                           = $regatta->post_regata_id;
			$ref[ $regatta->post_regata_id ] = $i++;
		}
		$args  = array(
			'post_type'        => $this->post_type_name,
			'post__in'         => $ids,
			'fields'           => 'ids',
			'suppress_filters' => true,
		);
		$query = new WP_Query( $args );
		/**
		 * Get boat post type
		 */
		if ( ! empty( $query->posts ) ) {
			$start = $this->options->get_option_name( 'result_date_start' );
			$end   = $this->options->get_option_name( 'result_date_end' );
			foreach ( $ref as $post_id => $index ) {
				$regattas[ $index ]->date_start = get_post_meta( $post_id, $start, true );
				$regattas[ $index ]->date_end   = get_post_meta( $post_id, $end, true );
				/**
				 * add boat data
				 */
				$boat = $this->get_boat_data_by_number( $regattas[ $index ]->boat_id );
				if ( false !== $boat ) {
					$regattas[ $index ]->boat = $boat;
				}
			}
		}
		uasort( $regattas, array( $this, 'sort_by_date_start' ) );
		$this->set_cache( $regattas, $cache_key );
		return $regattas;
	}

	private function sort_by_date_start( $a, $b ) {
		if (
			! isset( $a->date_start )
			|| ! isset( $b->date_start )
		) {
			return 0;
		}
		return ( $a->date_start < $b->date_start ) ? 1 : -1;
	}

	private function get_list_by_boat_id( $boat_id ) {
		global $wpdb;
		$table_name_regatta = $wpdb->prefix . 'fleet_regatta';
		$boat_title         = get_the_title( $boat_id );
		if ( empty( $boat_title ) ) {
			return array();
		}
		$boat_title = intval( preg_replace( '/[^\d]/', '', $boat_title ) );
		if ( empty( $boat_title ) ) {
			return array();
		}
		$sql = $wpdb->prepare(
			"select * from {$table_name_regatta} where boat_id = %d order by date, year desc",
			$boat_title
		);
		return $wpdb->get_results( $sql );
	}

	public function regatta_list_by_sailor_id( $content, $sailor_id ) {
		$cache_key = $this->options->get_option_name( 'regatta_' . $sailor_id );
		$cache     = $this->get_cache( $cache_key );
		if ( ! empty( $cache ) ) {
			return $cache;
		}
		if ( empty( $content ) ) {
			$content = __( 'There is no register regatta for this sailor.', 'fleet' );
		}
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		$regattas = $this->get_list_by_sailor_id( $sailor_id );
		$post_id  = get_the_ID();
		if ( ! empty( $regattas ) ) {
			$content = '';
			/**
			 * CSV link
			 */
			if ( $this->options->get_option( 'person_show_download_link' ) ) {
				$args     = array(
					'id'       => get_the_ID(),
					'type'     => 'person',
					'fleet'    => 'download',
					'format'   => 'csv',
					'_wpnonce' => wp_create_nonce( $post_id ),
				);
				$url      = add_query_arg( $args );
				$content .= sprintf(
					'<div class="fleet-results-get"><a href="%s" rel="alternate nofollow" class="fleet-results-csv">%s</a></div>',
					$url,
					__( 'Download', 'fleet' )
				);
			}
			$content .= '<table class="fleet-results"><thead><tr>';
			$content .= sprintf( '<th class="year">%s</th>', esc_html__( 'Year', 'fleet' ) );
			$content .= sprintf( '<th class="name">%s</th>', esc_html__( 'Name', 'fleet' ) );
			$content .= sprintf( '<th class="boat">%s</th>', esc_html__( 'Boat', 'fleet' ) );
			$content .= sprintf( '<th class="helmsman">%s</th>', esc_html__( 'Helmsman', 'fleet' ) );
			$content .= sprintf( '<th class="crew">%s</th>', esc_html__( 'Crew', 'fleet' ) );
			$content .= sprintf( '<th class="place">%s</th>', esc_html__( 'Place (of)', 'fleet' ) );
			if ( $this->show_points ) {
				$content .= sprintf( '<th class="points">%s</th>', esc_html__( 'Points', 'fleet' ) );
			}
			$content .= '</tr></thead><tbody>';
			$format   = get_option( 'date_format' );
			foreach ( $regattas as $regatta ) {
				$dates   = sprintf(
					'%s - %s',
					date_i18n( $format, $regatta->date_start ),
					date_i18n( $format, $regatta->date_end )
				);
				$classes = array(
					sprintf( 'fleet-place-%d', $regatta->place ),
				);
				if ( 4 > $regatta->place ) {
					$classes[] = 'fleet-place-medal';
				}
				$content .= sprintf( '<tr class="%s" id="fleet-regatta-%d">', esc_attr( implode( ' ', $classes ) ), esc_attr( $regatta->post_regata_id ) );
				$content .= sprintf( '<td class="year" title="%s">%d</td>', esc_attr( $dates ), $regatta->year );
				$content .= sprintf( '<td class="name"><a href="%s">%s</a></td>', get_permalink( $regatta->post_regata_id ), get_the_title( $regatta->post_regata_id ) );
				/**
				 * Boat
				 */
				$content .= '<td class="boat">';
				if ( isset( $regatta->boat ) ) {
					$content .= sprintf(
						'<a href="%s">%s%s</a>',
						esc_url( $regatta->boat['url'] ),
						$this->show_single_boat_flag ? ( empty( $regatta->country ) ? '' : $regatta->country . ' ' ) : '',
						esc_html( $regatta->boat['post_title'] )
					);
				} elseif ( empty( $regatta->country ) && empty( $regatta->boat_id ) ) {
					$content .= '&ndash;';
				} else {
					if ( $this->show_single_boat_flag ) {
						$content .= sprintf( '%s %s', $regatta->country, abs( $regatta->boat_id ) );
					} else {
						$content .= abs( $regatta->boat_id );
					}
				}
				$content .= '</td>';
				/**
				 * Helmsman
				 */
				if ( $regatta->helm_id && $regatta->helm_id != $post_id ) {
					$content .= sprintf( '<td class="helmsman"><a href="%s">%s</a></td>', get_permalink( $regatta->helm_id ), get_the_title( $regatta->helm_id ) );
				} elseif ( $regatta->helm_id == $post_id ) {
					$content .= sprintf( '<td class="helmsman current">%s</td>', apply_filters( 'the_title', $regatta->helm_name, $regatta->helm_id ) );
				} else {
					$content .= sprintf( '<td class="helmsman">%s</td>', $regatta->helm_name );
				}
				/**
				 * crew
				 */
				if ( $regatta->crew_id && $regatta->crew_id != $post_id ) {
					$content .= sprintf( '<td class="crew"><a href="%s">%s</a></td>', get_permalink( $regatta->crew_id ), get_the_title( $regatta->crew_id ) );
				} elseif ( $regatta->crew_id == $post_id ) {
					$content .= sprintf( '<td class="crew current">%s</td>', apply_filters( 'the_title', $regatta->crew_name, $regatta->crew_id ) );
				} else {
					$content .= sprintf( '<td class="crew">%s</td>', $regatta->crew_name );
				}
				$x        = get_post_meta( $regatta->post_regata_id, $this->options->get_option_name( 'result_number_of_competitors' ), true );
				$content .= sprintf( '<td class="place">%d <small>(%d)</small></td>', $regatta->place, $x );
				if ( $this->show_points ) {
					$content .= sprintf( '<td class="points">%d</td>', $regatta->points );
				}
				$content .= '</tr>';
			}
			$content .= '</tbody></table>';
		}
		$content = sprintf(
			'<div class="iworks-fleet-regatta-list"><h2>%s</h2>%s</div>',
			esc_html__( 'Regatta list', 'fleet' ),
			$content
		);
		set_transient( $cache_key, $content, DAY_IN_SECONDS );
		return $content;
	}

	public function regatta_list_by_boat_id( $content, $boat_id ) {
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		$regattas = $this->get_list_by_boat_id( $boat_id );
		if ( empty( $regattas ) ) {
			return '';
		}
		$post_id = get_the_ID();
		if ( ! empty( $regattas ) ) {
			$content = '';
			/**
			 * CSV link
			 */
			if ( $this->options->get_option( 'boat_show_download_link' ) ) {
				$args     = array(
					'id'       => get_the_ID(),
					'type'     => 'boat',
					'fleet'    => 'download',
					'format'   => 'csv',
					'_wpnonce' => wp_create_nonce( $post_id ),
				);
				$url      = add_query_arg( $args );
				$content .= sprintf(
					'<div class="fleet-results-get"><a href="%s" rel="alternate nofollow" class="fleet-results-csv">%s</a></div>',
					$url,
					__( 'Download', 'fleet' )
				);
			}
			$content .= '<table class="fleet-results"><thead><tr>';
			$content .= sprintf( '<th class="year">%s</th>', esc_html__( 'Year', 'fleet' ) );
			$content .= sprintf( '<th class="name">%s</th>', esc_html__( 'Name', 'fleet' ) );
			$content .= sprintf( '<th class="helmsman">%s</th>', esc_html__( 'Helmsman', 'fleet' ) );
			$content .= sprintf( '<th class="crew">%s</th>', esc_html__( 'Crew', 'fleet' ) );
			$content .= sprintf( '<th class="place">%s</th>', esc_html__( 'Place (of)', 'fleet' ) );
			if ( $this->show_points ) {
				$content .= sprintf( '<th class="points">%s</th>', esc_html__( 'Points', 'fleet' ) );
			}
			$content .= '</tr></thead><tbody>';
			foreach ( $regattas as $regatta ) {
				$classes = array(
					sprintf( 'fleet-place-%d', $regatta->place ),
				);
				if ( 4 > $regatta->place ) {
					$classes[] = 'fleet-place-medal';
				}
				$content .= sprintf( '<tr class="%s">', esc_attr( implode( ' ', $classes ) ) );
				$content .= sprintf( '<td class="year">%d</td>', $regatta->year );
				$content .= sprintf( '<td class="name"><a href="%s">%s</a></td>', get_permalink( $regatta->post_regata_id ), get_the_title( $regatta->post_regata_id ) );
				/**
				 * Helmsman
				 */
				if ( $regatta->helm_id && $regatta->helm_id != $post_id ) {
					$content .= sprintf(
						'<td class="helmsman"><a href="%s">%s</a></td>',
						get_permalink( $regatta->helm_id ),
						get_the_title( $regatta->helm_id )
					);
				} elseif ( $regatta->helm_id == $post_id ) {
					$content .= sprintf( '<td class="helmsman current">%s</td>', $regatta->helm_name );
				} else {
					$content .= sprintf( '<td class="helmsman">%s</td>', $regatta->helm_name );
				}
				/**
				 * crew
				 */
				if ( $regatta->crew_id && $regatta->crew_id != $post_id ) {
					$content .= sprintf(
						'<td class="crew"><a href="%s">%s</a></td>',
						get_permalink( $regatta->crew_id ),
						get_the_title( $regatta->crew_id )
					);
				} elseif ( $regatta->crew_id == $post_id ) {
					$content .= sprintf( '<td class="crew current">%s</td>', $regatta->crew_name );
				} else {
					$content .= sprintf( '<td class="crew">%s</td>', $regatta->crew_name );
				}
				$x        = get_post_meta( $regatta->post_regata_id, $this->options->get_option_name( 'result_number_of_competitors' ), true );
				$content .= sprintf( '<td class="place">%d (%d)</td>', $regatta->place, $x );
				if ( $this->show_points ) {
					$content .= sprintf( '<td class="points">%d</td>', $regatta->points );
				}
				$content .= '</tr>';
			}
			$content .= '</tbody></table>';
		}
		$content = sprintf(
			'<div class="iworks-fleet-regatta-list"><h2>%s</h2>%s</div>',
			esc_html__( 'Regatta list', 'fleet' ),
			$content
		);
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
		$show_in_menu = add_query_arg( 'post_type', $iworks_fleet->get_post_type_name( 'person' ), 'edit.php' );
		$labels       = array(
			'name'                  => _x( 'Results', 'Result General Name', 'fleet' ),
			'singular_name'         => _x( 'Result', 'Result Singular Name', 'fleet' ),
			'menu_name'             => __( '5O5', 'fleet' ),
			'name_admin_bar'        => __( 'Result', 'fleet' ),
			'archives'              => __( 'Results', 'fleet' ),
			'attributes'            => __( 'Item Attributes', 'fleet' ),
			'all_items'             => __( 'Results', 'fleet' ),
			'add_new_item'          => __( 'Add New Result', 'fleet' ),
			'add_new'               => __( 'Add New', 'fleet' ),
			'new_item'              => __( 'New Result', 'fleet' ),
			'edit_item'             => __( 'Edit Result', 'fleet' ),
			'update_item'           => __( 'Update Result', 'fleet' ),
			'view_item'             => __( 'View Result', 'fleet' ),
			'view_items'            => __( 'View Results', 'fleet' ),
			'search_items'          => __( 'Search Result', 'fleet' ),
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
			'label'                => __( 'Result', 'fleet' ),
			'labels'               => $labels,
			'supports'             => array( 'title', 'editor', 'thumbnail', 'revision' ),
			'taxonomies'           => array(
				$this->taxonomy_name_serie,
				$this->taxonomy_name_location,
			),
			'hierarchical'         => false,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => $show_in_menu,
			'show_in_admin_bar'    => true,
			'show_in_nav_menus'    => true,
			'can_export'           => true,
			'has_archive'          => _x( 'results', 'slug for archive', 'fleet' ),
			'exclude_from_search'  => false,
			'publicly_queryable'   => true,
			'capability_type'      => 'page',
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
			'rewrite'              => array(
				'slug' => _x( 'result', 'slug for single result', 'fleet' ),
			),
		);
		register_post_type( $this->post_type_name, $args );
		/**
		 * Serie Taxonomy.
		 */
		$labels = array(
			'name'                       => _x( 'Series', 'Taxonomy General Name', 'fleet' ),
			'singular_name'              => _x( 'Serie', 'Taxonomy Singular Name', 'fleet' ),
			'menu_name'                  => __( 'Serie', 'fleet' ),
			'all_items'                  => __( 'All Series', 'fleet' ),
			'new_item_name'              => __( 'New Serie Name', 'fleet' ),
			'add_new_item'               => __( 'Add New Serie', 'fleet' ),
			'edit_item'                  => __( 'Edit Serie', 'fleet' ),
			'update_item'                => __( 'Update Serie', 'fleet' ),
			'view_item'                  => __( 'View Serie', 'fleet' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'fleet' ),
			'add_or_remove_items'        => __( 'Add or remove series', 'fleet' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
			'popular_items'              => __( 'Popular Series', 'fleet' ),
			'search_items'               => __( 'Search Series', 'fleet' ),
			'not_found'                  => __( 'Not Found', 'fleet' ),
			'no_terms'                   => __( 'No series', 'fleet' ),
			'items_list'                 => __( 'Series list', 'fleet' ),
			'items_list_navigation'      => __( 'Series list navigation', 'fleet' ),
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
				'slug' => 'fleet-result-serie',
			),
		);
		register_taxonomy( $this->taxonomy_name_serie, array( $this->post_type_name ), $args );
		/**
		 * Series
		 */
		add_filter( 'iworks_fleet_get_series', array( $this, 'filter_get_series_array' ) );
	}

	public function save_post_meta( $post_id, $post, $update ) {
		$result = $this->save_post_meta_fields( $post_id, $post, $update, $this->fields );
		if ( ! $result ) {
			return;
		}
	}

	public function register_meta_boxes( $post ) {
		add_meta_box( 'result', __( 'Result data', 'fleet' ), array( $this, 'result' ), $this->post_type_name );
		add_meta_box( 'race', __( 'Races data', 'fleet' ), array( $this, 'races' ), $this->post_type_name );
	}

	public function print_js_templates() {
		?>
<script type="text/html" id="tmpl-iworks-result-crew">
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


	public function result( $post ) {
		$this->get_meta_box_content( $post, $this->fields, __FUNCTION__ );
	}

	public function races( $post ) {
		echo '<span class="spinner" style="display:none"></span>';
		echo '<input type="file" name="file" id="file_fleet_races"/>';
		wp_nonce_field( 'upload-races', __CLASS__ );
		printf( '<button>%s</button>', esc_html__( 'Import regatta results!', 'fleet' ) );
		printf( '<p class="description">%s</p>', esc_html__( 'CSV file with fields: Nation, Boat, Helm. Crew(s), Club, R1, R..n, any-fileds, netto points, place.', 'fleet' ) );
	}

	/**
	 * Get custom column values.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $column Column name,
	 * @param integer $post_id Current post id (Result),
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
								'post_type' => 'iworks_fleet_result',
							),
							admin_url( 'edit.php' )
						),
						get_post_meta( $id, 'iworks_fleet_manufacturer_data_full_name', true )
					);
				}
				break;
			case 'build_year':
				$name = $this->options->get_option_name( 'result_build_year' );
				echo get_post_meta( $post_id, $name, true );
				break;
			case 'location':
				$name = $this->options->get_option_name( 'result_location' );
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
		$columns['location'] = __( 'Location', 'fleet' );
		$columns['year']     = __( 'Year', 'fleet' );
		return $columns;
	}

	/**
	 * change default columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns list of columns.
	 * @return array $columns list of columns.
	 */
	public function add_sortable_columns( $columns ) {
		unset( $columns['date'] );
		$columns['location'] = __( 'Location', 'fleet' );
		$columns['year']     = __( 'Year', 'fleet' );
		return $columns;
	}

	private function has_races( $regatta_id ) {
		global $wpdb;
		$table_name_regatta_race = $wpdb->prefix . 'fleet_regatta_race';
		$query                   = $wpdb->prepare(
			sprintf( 'SELECT COUNT(*) from %s WHERE `post_regata_id` = %%d', $table_name_regatta_race ),
			$regatta_id
		);
		$val                     = $wpdb->get_var( $query );
		return 0 < $val;
	}

	/**
	 * handle upload results file
	 */
	public function upload() {
		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}
		$post_id = intval( $_POST['id'] );
		if ( empty( $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			wp_send_json_error();
		}
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'upload-races' ) ) {
			wp_send_json_error();
		}
		if ( empty( $_FILES ) || ! isset( $_FILES['file'] ) ) {
			wp_send_json_error();
		}
		$file = $_FILES['file'];
		if ( 'text/csv' != $file['type'] ) {
			wp_send_json_error();
		}
		$row  = 1;
		$data = array();
		if ( ( $handle = fopen( $file['tmp_name'], 'r' ) ) !== false ) {
			while ( ( $d = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
				$data[] = $d;
			}
			fclose( $handle );
		}
		if ( empty( $data ) ) {
			wp_send_json_error();
		}
		$result = $this->import_data( $post_id, $data );
		wp_send_json_success();
	}

	public function import_data( $post_id, $data ) {
		global $wpdb, $iworks_fleet;
		$table_name_regatta      = $wpdb->prefix . 'fleet_regatta';
		$table_name_regatta_race = $wpdb->prefix . 'fleet_regatta_race';
		array_shift( $data );
		$sailors = $iworks_fleet->get_list_by_post_type( 'person' );
		$wpdb->delete( $table_name_regatta, array( 'post_regata_id' => $post_id ), array( '%d' ) );
		$wpdb->delete( $table_name_regatta_race, array( 'post_regata_id' => $post_id ), array( '%d' ) );
		/**
		 * Result date end
		 */
		$name = $this->options->get_option_name( 'result_date_end' );
		$year = get_post_meta( $post_id, $name, true );
		$year = $year ? date( 'Y', $year ) : '';
		/**
		 * Result date end
		 */
		$name            = $this->options->get_option_name( 'result_number_of_races' );
		$number_of_races = intval( get_post_meta( $post_id, $name, true ) );
		foreach ( $data as $row ) {
			$country = '';
			$boat_id = null;
			$boat    = array_shift( $row );
			if ( preg_match( '/^[A-Z]+$/', $boat ) ) {
				$country = $boat;
				$boat    = array_shift( $row );
				$boat_id = intval( $boat );
				if ( ! preg_match( '/^\d+$/', $boat ) ) {
					$boat_id *= -1;
				}
			} elseif ( preg_match( '/^\?(\d+)$/', $boat, $matches ) ) {
				$boat_id = intval( $matches[1] );
			} elseif ( preg_match( '/^([a-zA-Z\/]+)[ \-\t]*(\d+)$/', $boat, $matches ) ) {
				$country = $matches[1];
				$boat_id = $matches[2];
			} elseif ( preg_match( '/^\d+$/', $row[0] ) ) {
				$country = $boat;
				$boat_id = intval( array_shift( $row ) );
			} else {
				$boat_id = intval( $boat );
				$country = $this->data_trim( preg_replace( '/\d+/', '', $boat ) );
			}
			/**
			 * helm & crew
			 */
			$h    = $helm    = $this->data_trim( array_shift( $row ) );
			$crew = $this->data_trim( array_shift( $row ) );
			$helm = $this->data_trim( apply_filters( 'iworks_fleet_result_upload_helm', $helm, $crew ) );
			$crew = $this->data_trim( apply_filters( 'iworks_fleet_result_upload_crew', $crew, $h ) );
			/**
			 * ( helm or crew ) && $boat_id
			 */
			if ( is_integer( $boat_id ) && 0 < $boat_id ) {
				$helm = $this->data_trim( apply_filters( 'iworks_fleet_result_upload_person_with_boat', $helm, $boat_id ) );
				$crew = $this->data_trim( apply_filters( 'iworks_fleet_result_upload_person_with_boat', $crew, $boat_id ) );
			}
			/**
			 * club
			 */
			$club    = $this->data_trim( array_shift( $row ) );
			$place   = intval( array_pop( $row ) );
			$points  = intval( array_pop( $row ) );
			$regatta = array(
				'year'           => $year,
				'post_regata_id' => $post_id,
				'boat_id'        => $boat_id,
				'country'        => $country,
				'helm_id'        => isset( $sailors[ $helm ] ) ? intval( $sailors[ $helm ] ) : 0,
				'helm_name'      => $helm,
				'crew_id'        => isset( $sailors[ $crew ] ) ? intval( $sailors[ $crew ] ) : 0,
				'crew_name'      => $crew,
				'place'          => $place,
				'points'         => $points,
			);
			/**
			 * maybe update sailors nation
			 */
			if ( 0 < $regatta['helm_id'] ) {
				do_action( 'maybe_add_person_nation', $regatta['helm_id'], $country );
			}
			if ( 0 < $regatta['crew_id'] ) {
				do_action( 'maybe_add_person_nation', $regatta['crew_id'], $country );
			}
			/**
			 * insert
			 */
			$wpdb->insert( $table_name_regatta, $regatta );
			$regatta_id = $wpdb->insert_id;
			if ( empty( $row ) ) {
				continue;
			}
			$races = array();
			foreach ( $row as $one ) {
				$races[] = $one;
			}
			$number = 1;
			foreach ( $races as $one ) {
				if ( 0 < $number_of_races ) {
					if ( $number > $number_of_races ) {
						continue;
					}
				}
				$race = array(
					'post_regata_id' => $post_id,
					'regata_id'      => $regatta_id,
					'number'         => $number++,
				);
				$one  = $this->data_trim( $one );
				if (
					preg_match( '/\*/', $one )
					|| preg_match( '/^\‑/', $one )
					|| preg_match( '/^\-/', $one )
					|| preg_match( '/^\([^\)]+\)$/', $one )
					|| preg_match( '/^\[[^\]]+\]$/', $one )
					|| 0 > $one
				) {
					$race['discard'] = true;
				}
				$one          = preg_replace( '/\*/', '', $one );
				$race['code'] = preg_replace( '/[^a-z]+/i', '', $one );
				switch ( $race['code'] ) {
					case 's':
					case 'S':
					case 'n':
					case 'N':
						$race['code'] = 'DNS';
						break;
					case 'f':
					case 'F':
					case 'NP':
						$race['code'] = 'DNF';
						break;
					case 'q':
					case 'Q':
					case 'd':
					case 'D':
					case 'DQ':
						$race['code'] = 'DSQ';
						break;
					case 'r':
					case 'R':
					case 'AB':
					case 'Abandoned':
						$race['code'] = 'RET';
						break;
				}
				$race['points'] = preg_replace( '/[^\d^\,^\.]+/', '', $one );
				$wpdb->insert( $table_name_regatta_race, $race );
			}
		}
		return true;
	}

	public function the_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		$post_type = get_post_type();
		if ( $post_type != $this->post_type_name ) {
			return $content;
		}
		$post_id = get_the_ID();
		global $wpdb, $iworks_fleet;
		$table_name_regatta      = $wpdb->prefix . 'fleet_regatta';
		$table_name_regatta_race = $wpdb->prefix . 'fleet_regatta_race';
		/**
		 * regata meta
		 */
		$meta  = '';
		$terms = get_the_term_list( $post_id, $this->taxonomy_name_location );
		if ( ! empty( $terms ) ) {
			$meta = sprintf(
				'<tr><td>%s</td><td>%s</td></tr>',
				esc_html__( 'Location', 'fleet' ),
				$terms
			);
		}
		$format = get_option( 'date_format' );
		foreach ( $this->fields['result'] as $key => $data ) {
			if ( preg_match( '/^(wind|columns)/', $key ) ) {
				continue;
			}
			$classes = array(
				sprintf( 'fleet-results-%s', $key ),
			);
			$name    = $this->options->get_option_name( 'result_' . $key );
			$value   = $this->data_trim( get_post_meta( $post_id, $name, true ) );
			if ( empty( $value ) ) {
				continue;
			}
			switch ( $key ) {
				case 'date_start':
				case 'date_end':
					$value     = date_i18n( $format, $value );
					$classes[] = 'fleet-results-date';
					break;
				case 'location':
					$url   = add_query_arg( 'q', $value, 'https://maps.google.com/' );
					$value = sprintf( '<a href="%s">%s</a>', $url, $value );
					break;
				case 'number_of_races':
				case 'number_of_competitors':
					$classes[] = 'fleet-results-number';
					break;
			}
			$meta .= sprintf(
				'<tr class="%s"><td>%s</td><td>%s</td></tr>',
				esc_attr( implode( ' ', $classes ) ),
				$data['label'],
				$value
			);
		}
		if ( ! empty( $meta ) ) {
			$content .= sprintf(
				'<table class="fleet-results-meta hidden" data-show="%s">%s</table>',
				esc_attr__( 'Show meta', 'fleet' ),
				$meta
			);
		}
		/**
		 * get regata data
		 */
		$query   = $wpdb->prepare( "SELECT * FROM {$table_name_regatta} where post_regata_id = %d order by place", $post_id );
		$regatta = $wpdb->get_results( $query );
		/**
		 * get regata races data
		 */
		$races = $this->get_races_data( $post_id );
		if ( empty( $races ) ) {
			$content .= __( 'There is no race data.', 'fleet' );
			return $content;
		}
		/**
		 * CSV link
		 */
		if ( $this->options->get_option( 'result_show_download_link' ) ) {
			$args     = array(
				'id'       => get_the_ID(),
				'type'     => 'result',
				'fleet'    => 'download',
				'format'   => 'csv',
				'_wpnonce' => wp_create_nonce( $post_id ),
			);
			$url      = add_query_arg( $args );
			$content .= sprintf(
				'<div class="fleet-results-get"><a href="%s" rel="alternate nofollow" class="fleet-results-csv">%s</a></div>',
				$url,
				__( 'Download', 'fleet' )
			);
		}
		$content .= '<table class="fleet-results fleet-results-person">';
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= sprintf( '<td class="place">%s</td>', esc_html__( 'Place', 'fleet' ) );
		$content .= sprintf( '<td class="boat">%s</td>', esc_html__( 'Boat', 'fleet' ) );
		$content .= sprintf( '<td class="helm">%s</td>', esc_html__( 'Helmsman', 'fleet' ) );
		$content .= sprintf( '<td class="crew">%s</td>', esc_html__( 'Crew', 'fleet' ) );
		$number   = intval( get_post_meta( $post_id, 'iworks_fleet_result_number_of_races', true ) );
		/**
		 * custom race columens
		 */
		$columns  = array();
		$meta_key = $this->options->get_option_name( 'result_columns' );
		$value    = get_post_meta( $post_id, $meta_key, true );
		if ( ! empty( $value ) ) {
			$columns = preg_split( '/[\n\r;]+/', $value );
		}
		for ( $i = 0; $i < $number; $i++ ) {
			$race_number = $i + 1;
			if ( isset( $columns[ $i ] ) && ! empty( $columns[ $i ] ) ) {
				$content .= sprintf( '<td class="race race-%d">%s</td>', $race_number, esc_html( $columns[ $i ] ) );
			} else {
				$content .= sprintf( '<td class="race race-%d">%d</td>', $race_number, $race_number );
			}
		}
		$content   .= sprintf( '<td class="sum">%s</td>', esc_html__( 'Sum', 'fleet' ) );
		$content   .= '</tr>';
		$content   .= '</thead>';
		$content   .= '<tbody>';
		$show       = current_user_can( 'manage_options' );
		$at_the_end = '';
		foreach ( $regatta as $one ) {
			$one_content = '';
			$classes     = array(
				sprintf( 'fleet-place-%d', $one->place ),
			);
			if ( 4 > $one->place ) {
				$classes[] = 'fleet-place-medal';
			}
			$one_content .= sprintf( '<tr class="%s">', esc_attr( implode( ' ', $classes ) ) );
			if ( 0 < $one->place ) {
				$one_content .= sprintf( '<td class="place">%d</td>', $one->place );
			} else {
				$one_content .= sprintf(
					'<td class="place place-tda"><small>%s</small></td>',
					empty( $one->place ) ? '?' : 'TDA'
				);
			}
			/**
			 * boat
			 */
			$one_content .= sprintf(
				'<td class="boat_id country-%s">',
				esc_attr( strtolower( $one->country ) )
			);
			$boat         = $this->get_boat_data_by_number( $one->boat_id );
			/**
			 * Boat number
			 */
			$one->boat_id = intval( $one->boat_id );
			if ( 0 === $one->boat_id ) {
				$one->boat_id = '&ndash;';
			}
			$boat_name = $one->boat_id;
			if ( $this->show_single_boat_flag ) {
				$boat_name = sprintf( '%s %s', $one->country, abs( $one->boat_id ) );
			}
			if ( false === $boat ) {
				$one_content .= esc_html( $boat_name );
			} else {
				$one_content .= sprintf(
					'<a href="%s">%s</a>',
					esc_url( $boat['url'] ),
					esc_html( $boat_name )
				);
			}
			$one_content .= '</td>';
			/**
			 * helmsman
			 */
			if ( ! empty( $one->helm_id ) ) {
				$extra = '';
				if ( $show ) {
					$extra = $this->get_extra_data( $one->helm_id );
				}
				$one_content .= sprintf(
					'<td class="helm_name"><a href="%s">%s</a>%s</td>',
					get_permalink( $one->helm_id ),
					apply_filters( 'the_title', $one->helm_name, $one->helm_id ),
					$extra
				);
			} else {
				$one_content .= sprintf( '<td class="helm_name">%s</td>', $one->helm_name );
			}
			/**
			 * crew
			 */
			if ( ! empty( $one->crew_id ) ) {
				$extra = '';
				if ( $show ) {
					$extra = $this->get_extra_data( $one->crew_id );
				}
				$one_content .= sprintf(
					'<td class="crew_name"><a href="%s">%s</a>%s</td>',
					get_permalink( $one->crew_id ),
					apply_filters( 'the_title', $one->crew_name, $one->crew_id ),
					$extra
				);
			} else {
				$one_content .= sprintf( '<td class="crew_name">%s</td>', $one->crew_name );
			}
			if ( isset( $races[ $one->ID ] ) && ! empty( $races[ $one->ID ] ) ) {
				foreach ( $races[ $one->ID ] as $race_number => $race_points ) {
					if ( '0' === $race_points ) {
						$race_points = '&ndash;';
					}
					$class        = preg_match( '/\*/', $race_points ) ? 'race-discard' : '';
					$one_content .= sprintf(
						'<td class="race race-%d %s">%s</td>',
						esc_attr( $race_number ),
						esc_attr( $class ),
						$race_points
					);
				}
			}
			$one_content .= sprintf( '<td class="points">%s</td>', '0' === $one->points ? '&ndash;' : $one->points );
			$one_content .= '</tr>';
			if ( 0 < $one->place ) {
				$content .= $one_content;
			} else {
				$at_the_end .= $one_content;
			}
		}
		$content .= $at_the_end;
		$content .= '<tbody>';
		$content .= '</table>';
		return $content;
	}

	/**
	 * Get start/end date
	 */
	private function get_date( $type, $post_id = 0, $format = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return '-';
		}
		$meta_key = $this->options->get_option_name( 'result_date_' . $type );
		$value    = get_post_meta( get_the_ID(), $meta_key, true );
		$value    = intval( $value );
		if ( empty( $value ) ) {
			return '-';
		}
		if ( empty( $format ) ) {
			$format = get_option( 'date_format' );
		}
		return date_i18n( $format, $value );
	}

	/**
	 * separate from get_td() to allow multple usage.
	 */
	private function get_meta_value( $name, $post_id ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return '&ndash;';
		}
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return '&ndash;';
		}
		$meta_key = $this->options->get_option_name( 'result_' . $name );
		$value    = get_post_meta( get_the_ID(), $meta_key, true );
		if ( empty( $value ) ) {
			return '&ndash;';
		}
		return $value;
	}

	private function get_td( $name, $post_id ) {
		return sprintf(
			'<td class="%s">%s</td>',
			esc_attr( preg_replace( '/_/', '-', $name ) ),
			esc_html( $this->get_meta_value( $name, $post_id ) )
		);
	}

	private function get_extra_data( $user_id ) {
		$extra = '';
		$name  = $this->options->get_option_name( 'personal_birth_year' );
		$year  = get_post_meta( $user_id, $name, true );
		if ( apply_filters( 'iworks_fleet_show_sailor_edit_year_link', false ) && empty( $year ) ) {
			$extra .= sprintf(
				' <a href="%s" class="fleet-missing-data fleet-missing-data-year" title="%s">EY</a>',
				get_edit_post_link( $user_id ),
				esc_attr__( 'Edit Sailor - Missing Birth Year', 'fleet' )
			);
		}
		if ( empty( $extra ) ) {
			return $extra;
		}
		return sprintf( '<small>%s</small>', $extra );
	}

	private function get_boat_data_by_number( $number ) {
		if ( empty( $this->boat_post_type ) ) {
			global $iworks_fleet;
			$this->boat_post_type = $iworks_fleet->get_post_type_name( 'boat' );
		}
		$boat = get_page_by_title( $number, OBJECT, $this->boat_post_type );
		if ( is_a( $boat, 'WP_Post' ) ) {
			return array(
				'ID'         => $boat->ID,
				'post_title' => $boat->post_title,
				'url'        => get_permalink( $boat ),
			);
		}
		return false;
	}

	/**
	 * add where order to prev/next post links
	 *
	 * @since 1.0.0
	 */
	public function adjacent_post_where( $sql, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		if ( $post->post_type === $this->post_type_name ) {
			global $wpdb;
			$key   = $this->options->get_option_name( 'result_date_start' );
			$value = get_post_meta( $post->ID, $key, true );
			$sql   = preg_replace(
				'/p.post_date ([<> ]+) \'[^\']+\'/',
				"{$wpdb->postmeta}.meta_value $1 {$value} and {$wpdb->postmeta}.meta_key = '{$key}'",
				$sql
			);
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
			global $wpdb;
			$sql = sprintf( 'ORDER BY %s.meta_value %s LIMIT 1', $wpdb->postmeta, $order );
		}
		return $sql;
	}

	/**
	 * add sort order to prev/next post links
	 *
	 * @since 1.0.0
	 */
	public function adjacent_post_join( $join, $in_same_term, $excluded_terms, $taxonomy, $post ) {
		if ( $post->post_type === $this->post_type_name ) {
			global $wpdb;
			$key   = $this->options->get_option_name( 'result_date_start' );
			$join .= "LEFT JOIN {$wpdb->postmeta} ON p.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = '{$key}'";
		}
		return $join;
	}

	/**
	 * Get race data
	 */
	private function get_races_data( $post_id, $output = 'html' ) {
		global $wpdb;
		$races                   = array();
		$table_name_regatta_race = $wpdb->prefix . 'fleet_regatta_race';
		$query                   = $wpdb->prepare( "SELECT * FROM {$table_name_regatta_race} where post_regata_id = %d order by regata_id, number", $post_id );
		$r                       = $wpdb->get_results( $query );
		$pattern                 = '<small style="fleet-results-code fleet-results-code-%3$s" title="%2$d">%1$s</small>';
		if ( 'csv' === $output ) {
			$pattern = '%2$d %1$s';
		}
		foreach ( $r as $one ) {
			if ( ! isset( $races[ $one->regata_id ] ) ) {
				$races[ $one->regata_id ] = array();
			}
			$races[ $one->regata_id ][ $one->number ] = '';
			if ( empty( $one->code ) ) {
				$races[ $one->regata_id ][ $one->number ] .= $one->points;
			} else {
				$races[ $one->regata_id ][ $one->number ] .= sprintf(
					$pattern,
					esc_html( strtoupper( $one->code ) ),
					$one->points,
					esc_attr( strtolower( $one->code ) )
				);
			}
			if ( $one->discard ) {
				if ( 'csv' === $output ) {
					$races[ $one->regata_id ][ $one->number ] .= '*';
				} else {
					$races[ $one->regata_id ][ $one->number ] .= '<span class="discard">*</span>';
				}
			}
		}
		return $races;
	}

	private function get_dates( $start, $end ) {
		$start_year  = date( 'Y', $start );
		$end_year    = date( 'Y', $end );
		$start_month = date( 'F', $start );
		$end_month   = date( 'F', $end );
		$start_day   = date( 'j', $start );
		$end_day     = date( 'j', $end );
		if (
			$start_day === $end_day
			&& $start_month === $end_month
			&& $start_year === $end_year
		) {
			return date_i18n( 'j F', $start );
		}
		if (
			$start_year === $end_year
			&& $start_month === $end_month
		) {
			return sprintf( '%d - %s', date_i18n( 'j', $start ), date_i18n( 'j F', $end ) );
		}
		if ( $start_year === $end_year ) {
			return sprintf( '%s - %s', date_i18n( 'j M', $start ), date_i18n( 'j M', $end ) );
		}
		return sprintf( '%s - %s', date_i18n( 'j M Y', $start ), date_i18n( 'j M y', $end ) );
	}

	public function adjacent_dates( $content, $start, $end ) {
		return $this->get_dates( $start, $end );
	}

	/**
	 * get ranking
	 *
	 * @since 1.2.8
	 */
	public function shortcode_ranking( $atts ) {
		$atts    = shortcode_atts(
			array(
				'year'       => date( 'Y' ),
				'serie'      => null,
				'title'      => __( 'Ranking', 'fleet' ),
				'protected'  => 0,
				'remove_one' => 'yes',
			),
			$atts,
			'fleet_results_ranking'
		);
		$content = '';
		/**
		 * params: year
		 */
		$year = intval( $atts['year'] );
		if ( 0 === $year ) {
			return __( 'Please setup year attribute first!', 'fleet' );
		}
		/**
		 * params: serie
		 */
		$serie = $atts['serie'];
		if ( empty( $serie ) ) {
			return __( 'Please setup serie attribute first!', 'fleet' );
		}
		/**
		 * WP Query base args
		 */
		$args = array(
			'post_type'  => $this->post_type_name,
			'nopaging'   => true,
			'orderby'    => 'meta_value_num',
			'order'      => 'asc',
			'meta_query' => array(
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( ( $year - 1 ) . '-12-31 23:59:59' ),
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
				array(
					'key'     => $this->options->get_option_name( 'result_date_start' ),
					'value'   => strtotime( ( $year + 1 ) . '-01-01 00:00:00' ),
					'compare' => '<',
					'type'    => 'NUMERIC',
				),
			),
		);
		/**
		 * serie
		 */
		if ( preg_match( '/^\d+$/', $atts['serie'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy_name_serie,
					'terms'    => $atts['serie'],
				),
			);
		} else {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->taxonomy_name_serie,
					'field'    => 'name',
					'terms'    => $atts['serie'],
				),
			);
		}
		/**
		 * WP_Query
		 */
		$the_query = new WP_Query( $args );
		if ( ! $the_query->have_posts() ) {
			return __( 'Currently we do not have anny results!', 'fleet' );
		}
		/**
		 * prepare
		 */
		global $wpdb, $iworks_fleet;
		$table_name_regatta      = $wpdb->prefix . 'fleet_regatta';
		$table_name_regatta_race = $wpdb->prefix . 'fleet_regatta_race';
		$all                     = array();
		$max                     = 0;
		$regattas                = array();
		/**
		 * get regatta data
		 */
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_id = get_the_ID();
			/**
			 * $regattas_ids
			 */
			if ( ! array_key_exists( $post_id, $regattas ) ) {
				$regattas[ $post_id ] = 0;
			}
		}
		/**
		 * results
		 */
		foreach ( $regattas as $post_id => $points ) {
			$query   = $wpdb->prepare( "SELECT * FROM {$table_name_regatta} where post_regata_id = %d order by place", $post_id );
			$results = $wpdb->get_results( $query );
			foreach ( $results as $one ) {
				$x = array( $one->boat_id, $one->helm_id, $one->crew_id );
				sort( $x );
				$keys = array(
					implode( $x, '-' ),
					sprintf( 'b%dp%d', $one->boat_id, $one->helm_id ),
					sprintf( 'b%dp%d', $one->boat_id, $one->crew_id ),
					sprintf( 'p%dp%d', $one->helm_id, $one->crew_id ),
				);
				foreach ( $keys as $key ) {
					if (
						empty( $one->boat_id )
						|| empty( $one->helm_id )
						|| empty( $one->crew_id )
					) {
						continue;
					}
					if ( ! isset( $all[ $key ] ) ) {
						$all[ $key ] = array(
							'results' => array(),
							'boats'   => array(),
							'persons' => array(
								'helms' => array(),
								'crews' => array(),
							),
						);
						foreach ( $regattas as $id => $v ) {
							$all[ $key ]['results'][ $id ] = null;
						}
					}
					$all[ $key ]['results'][ $one->post_regata_id ] = $one->place;
					if ( ! in_array( $one->boat_id, $all[ $key ]['boats'] ) ) {
						$all[ $key ]['boats'][] = $one->boat_id;
					}
					if ( ! in_array( $one->helm_id, $all[ $key ]['persons']['helms'] ) ) {
						$all[ $key ]['persons']['helms'][] = $one->helm_id;
					}
					if ( ! in_array( $one->crew_id, $all[ $key ]['persons']['crews'] ) ) {
						$all[ $key ]['persons']['crews'][] = $one->crew_id;
					}
					$all[ $key ]['md5'] = md5( serialize( $all[ $key ]['results'] ) );
					/**
					 * improve max
					 */
					if ( $max < $one->place ) {
						$max = $one->place;
					}
					/**
					 * improve max per reggata
					 */
					if ( $regattas[ $post_id ] < $one->place ) {
						$regattas[ $post_id ] = $one->place;
					}
				}
			}
		}
		/**
		 * remove duplicates, identical results
		 */
		$results = array();
		foreach ( $all as $key => $one ) {
			if ( array_key_exists( $one['md5'], $results ) ) {
				continue;
			}
			/**
			 * remove more changes
			 */
			if (
				1 < count( $one['persons']['helms'] )
				&& 1 < count( $one['persons']['crews'] )
			) {
				continue;
			}
			$sum   = 0;
			$worse = 0;
			foreach ( $regattas as $id => $points ) {
				if ( empty( $one['results'][ $id ] ) && 0 < $points ) {
					$one['results'][ $id ] = $max + 1;
				}
				$sum += $one['results'][ $id ];
				if (
					$one['results'][ $id ] > $worse
					&& intval( $atts['protected'] ) !== $id
				) {
					$one['worse'] = $id;
					$worse        = $one['results'][ $id ];
				}
			}
			$one['brutto']          = $sum;
			$one['netto']           = $sum - $one['results'][ $one['worse'] ];
			$results[ $one['md5'] ] = $one;
		}
		/**
		 * remove the worse result
		 */

		/**
		 * sort
		 */
		$this->sort_on_tie = intval( $atts['protected'] );
		uasort( $results, array( $this, 'sort_by_result' ) );
		/**
		 * remove changing crew
		 */
		$all     = $results;
		$results = array();
		foreach ( $all as $key => $one ) {
			if (
				1 === count( $one['persons']['helms'] )
				&& 1 === count( $one['boats'] )
			) {
				$key = sprintf( '%d-%d', $one['boats'][0], $one['persons']['helms'][0] );
			}
			if ( array_key_exists( $key, $results ) ) {
				continue;
			}
			$results[ $key ] = $one;
		}
		/**
		 * remove changing helm
		 */
		$all     = $results;
		$results = array();
		foreach ( $all as $key => $one ) {
			if (
				1 === count( $one['persons']['crews'] )
				&& 1 === count( $one['boats'] )
			) {
				$key = sprintf( '%d-%d', $one['boats'][0], $one['persons']['crews'][0] );
			}
			if ( array_key_exists( $key, $results ) ) {
				continue;
			}
			$results[ $key ] = $one;
		}
		/**
		 * print
		 */
		$content  = '<table class="fleet-results fleet-results-list">';
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= sprintf( '<th class="dates" rowspan="2">%s</th>', esc_attr__( '#', 'fleet' ) );
		$content .= sprintf( '<th class="title" rowspan="2">%s</th>', esc_attr__( 'Boat', 'fleet' ) );
		$content .= sprintf( '<th class="helm" rowspan="2">%s</th>', esc_attr__( 'Helm', 'fleet' ) );
		$content .= sprintf( '<th class="crew" rowspan="2">%s</th>', esc_attr__( 'Crew', 'fleet' ) );
		$content .= sprintf( '<th colspan="%d" class="races">%s</th>', count( $regattas ), esc_attr__( 'Results', 'fleet' ) );
		$content .= sprintf( '<th colspan="2" class="points">%s</th>', esc_attr__( 'Points', 'fleet' ) );
		$content .= '</tr>';
		$content .= '<tr>';
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		foreach ( $regattas as $id => $points ) {
			$content .= sprintf(
				'<th class="r-%d"><a href="%s">%s</a></td>',
				$id,
				get_permalink( $id ),
				get_the_title( $id )
			);
		}
		$content      .= sprintf( '<th class="brutto">%s</th>', esc_attr__( 'brutto', 'fleet' ) );
		$content      .= sprintf( '<th class="netto">%s</th>', esc_attr__( 'netto', 'fleet' ) );
		$content      .= '</tr>';
		$content      .= '<thead>';
		$content      .= '<tbody>';
		$i             = 0;
		$last_score    = 0;
		$last_protectd = 0;
		foreach ( $results as $result ) {
			if ( $result['netto'] > $last_score ) {
				$i++;
			} elseif ( isset( $result['results'][ $atts['protected'] ] ) ) {
				if ( $result['results'][ $atts['protected'] ] > $last_protectd ) {
					$i++;
				}
			}
			$content .= '<tr>';
			$content .= sprintf( '<td class="place">%d</td>', $i );
			$content .= sprintf( '<td class="boat">%s</td>', implode( ', ', $result['boats'] ) );
			$content .= sprintf( '<td class="helm">%s</td>', $this->implode_persons( $result['persons']['helms'] ) );
			$content .= sprintf( '<td class="crew">%s</td>', $this->implode_persons( $result['persons']['crews'] ) );
			foreach ( $regattas as $id => $points ) {
				if ( empty( $result['results'][ $id ] ) ) {
					$content .= sprintf( '<td class="r-%d no-result">&ndash;</td>', $id );
				} else {
					$content .= sprintf(
						'<td class="r-%d %s result">%d%s</td>',
						$id,
						$id === intval( $atts['protected'] ) ? 'protected-result' : '',
						$result['results'][ $id ],
						$id === $result['worse'] ? '*' : ''
					);
				}
			}
			$content .= sprintf( '<td class="brutto">%d</td>', $result['brutto'] );
			$content .= sprintf( '<td class="netto">%d</td>', $result['netto'] );
			$content .= '</tr>';
			/**
			 * set last
			 */
			$last_score = $result['netto'];
			if ( isset( $result['results'][ $atts['protected'] ] ) ) {
				$last_protectd = $result['results'][ $atts['protected'] ];
			}
		}
		$content .= '</tbody>';
		$content .= '</table>';

		return $content;
	}

	private function sort_by_result( $a, $b ) {
		if ( $a['netto'] === $b['netto'] ) {
			if (
				isset( $a['results'][ $this->sort_on_tie ] )
				&& isset( $b['results'][ $this->sort_on_tie ] )
			) {
				return $a['results'][ $this->sort_on_tie ] > $b['results'][ $this->sort_on_tie ] ? 1 : -1;
			}
			if ( isset( $a['results'][ $this->sort_on_tie ] ) ) {
				return 1;
			}
			if ( isset( $b['results'][ $this->sort_on_tie ] ) ) {
				return -1;
			}
			return 0;
		}
		return $a['netto'] > $b['netto'] ? 1 : -1;
	}

	private function implode_persons( $persons ) {
		if ( empty( $this->sailors ) ) {
			global $iworks_fleet;
			$this->sailors = array_flip( $iworks_fleet->get_list_by_post_type( 'person' ) );
		}
		$content = '';
		foreach ( $persons as $id ) {
			if ( ! isset( $this->sailors[ $id ] ) ) {
				continue;
			}
			if ( ! empty( $content ) ) {
				$content .= '<br />';
			}
			$content .= sprintf(
				'<a href="%s">%s</a>',
				get_permalink( $id ),
				$this->sailors[ $id ]
			);
		}
		return $content;
	}

	/**
	 * add year & month archive
	 *
	 * @since 1.3.0
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule(
			sprintf( '%s/([0-9]{4})/([0-9]{1,2})/?$', _x( 'results', 'rewrite rule handler for results', 'fleet' ) ),
			sprintf( 'index.php?post_type=%s&year=$matches[1]&monthnum=$matches[2]', $this->post_type_name ),
			'top'
		);
		add_rewrite_rule(
			sprintf( '%s/([0-9]{4})/?$', _x( 'results', 'rewrite rule handler for results', 'fleet' ) ),
			sprintf( 'index.php?post_type=%s&year=$matches[1]', $this->post_type_name ),
			'top'
		);
	}

	/**
	 * handle year title archive
	 *
	 * @since 1.3.0
	 */
	public function get_the_archive_title( $title, $original_title, $prefix ) {
		if ( ! is_archive( $this->post_type_name ) ) {
			return $title;
		}
		if ( ! is_year() ) {
			return $title;
		}
		$year = get_query_var( 'iworks_fleet_result_year' );
		if ( empty( $year ) ) {
			return $title;
		}
		return sprintf( __( 'Year: %s', 'fleet' ), sprintf( '<span>%d</span>', $year ) );
	}

	/**
	 * Add OpenGraph data.
	 *
	 * @since 1.3.0
	 */
	public function og_array( $og ) {
		if ( is_singular( $this->post_type_name ) ) {
			return $this->og_array_add( $og, 'result' );
		}
		return $og;
	}

	public function filter_regatta_list_by_serie_slug( $content, $serie_slug, $options ) {
		$options = wp_parse_args(
			$options,
			array(
				'posts_per_page' => 5,
				'show_flags'     => false,
				'show_english'   => false,
				'show_more'      => false,
				'group_by_year'  => true,
				'output'         => 'html',
				'order'          => 'DESC',
			)
		);
		if ( $options['show_english'] ) {
			$options['show_english'] = $this->options->get_option( 'result_show_english_title' );
		}
		$args = array(
			'post_type'      => $this->post_type_name,
			'posts_per_page' => $options['posts_per_page'],
			'tax_query'      => array(
				array(
					'taxonomy' => $this->taxonomy_name_serie,
					'field'    => 'slug',
					'terms'    => $serie_slug,
				),
			),
			'meta_query'     => array(
				array(
					'key'       => $this->options->get_option_name( 'result_date_start' ),
					'value_num' => 0,
					'compare'   => '>',
				),
			),
			'orderby'        => 'meta_value_num',
			'post__not_in'   => $this->get_last_results_html_ids,
			'order'          => $options['order'],
		);
		if ( '::last' === $serie_slug ) {
			unset( $args['meta_query'], $args['orderby'], $args['tax_query'] );
		}
		$the_query = new WP_Query( $args );
		if ( ! $the_query->have_posts() ) {
			return $content;
		}
		$rows     = array();
		$content .= sprintf(
			'<div class="results results-serie-%s">',
			esc_attr( $serie_slug )
		);
		if ( '::last' === $serie_slug ) {
			unset( $args['meta_query'], $args['orderby'], $args['tax_query'] );
			$content .= sprintf(
				'<h2>%s</h2>',
				esc_html__( 'Last added', 'fleet' )
			);
		} else {
			$term     = get_term_by( 'slug', $serie_slug, $this->taxonomy_name_serie );
			$content .= sprintf(
				'<h2>%s</h2>',
				esc_html( $term->name )
			);
		}
		$year_last = 0;
		if ( $options['group_by_year'] ) {
			remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		}
		$open = false;
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			if ( 'raw' === $options['output'] ) {
				$rows[] = array(
					'ID'                    => get_the_ID(),
					'year'                  => $this->get_year( get_the_ID() ),
					'title'                 => get_the_title(),
					'title_en'              => $this->get_en_name( get_the_ID() ),
					'permalink'             => get_permalink(),
					'location'              => get_the_terms( get_the_ID(), $this->taxonomy_name_location ),
					'organizer'             => $this->get_meta_value( 'organizer', get_the_ID() ),
					'number_of_races'       => intval( $this->get_meta_value( 'number_of_races', get_the_ID() ) ),
					'number_of_competitors' => intval( $this->get_meta_value( 'number_of_competitors', get_the_ID() ) ),
				);
			} else {
				if ( $options['group_by_year'] ) {
					$year = $this->get_year( get_the_ID() );
					if ( $year_last !== $year ) {
						$year_last = $year;
						if ( $open ) {
							$content .= '</ul>';
							$open     = false;
						}
						$content .= sprintf( '<h3>%d</h3>', $year );
					}
				}
				if ( ! $open ) {
					$open     = true;
					$content .= '<ul>';
				}
				$this->get_last_results_html_ids[] = get_the_ID();
				$title                             = get_the_title();
				if ( $options['show_english'] ) {
					$title .= $this->get_en_name( get_the_ID() );
				}
				$content .= sprintf(
					'<li class="%s"><a href="%s"><span>%s</span></a></li>',
					esc_attr( implode( ' ', get_post_class() ) ),
					get_permalink(),
					$title
				);
			}
		}
		if ( $open ) {
			$content .= '</ul>';
		}
		/**
		 * more link
		 */
		if ( $options['show_more'] ) {
			$term = get_term_by( 'slug', $serie_slug, $this->taxonomy_name_serie );
			if ( is_a( $term, 'WP_Term' ) ) {
				$content .= sprintf(
					'<p class="more more-%s"><a href="%s">%s</a></p>',
					esc_attr( $serie_slug ),
					get_term_link( $serie_slug, $this->taxonomy_name_serie ),
					sprintf(
						_x( 'Show more: %s', 'all of taxonomy link title', 'fleet' ),
						$term->name
					)
				);
			};
		}
		$content .= '</div>';
		wp_reset_postdata();
		/**
		 * year
		 */
		if ( $options['group_by_year'] ) {
			add_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		}
		/**
		 * Cache
		 */
		// $this->set_cache( $content, $cache_key );
		if ( 'raw' === $options['output'] ) {
			return $rows;
		}
		return $content;
	}

	public function shortcode_years_links( $content, $atts ) {
		global $wpdb;
		$sql   = sprintf(
			'select substring( date_add(from_unixtime(0), interval meta_value second), 1, 4 ) as year, count(*) as counter from %s where meta_key = %%s and meta_value is not null group by 1 order by 1 desc',
			$wpdb->postmeta
		);
		$query = $wpdb->prepare(
			$sql,
			$this->options->get_option_name( 'result_date_start' )
		);
		$years = $wpdb->get_results( $query );
		if ( empty( $years ) ) {
			return $content;
		}
		$content .= '<div class="results results-years-list">';
		$content .= sprintf(
			'<h2>%s</h2>',
			esc_html__( 'Regatta results by year', 'fleet' )
		);
		$content .= '<ul>';
		foreach ( $years as $one ) {
			$content .= sprintf(
				'<li class="result-year-%d" title="%s">%s</li>',
				esc_attr( $one->year ),
				sprintf(
					_n(
						'Number of result: %d',
						'Number of results: %d',
						$one->counter,
						'fleet'
					),
					$one->counter
				),
				$this->get_year_link( $one->year )
			);
		}
		$content .= '</ul>';
		$content .= '</div>';
		return $content;
	}

	private function get_en_name( $post_ID ) {
		$en = get_post_meta( $post_ID, $this->options->get_option_name( 'result_english' ), true );
		if ( empty( $en ) ) {
			return '';
		}
		return sprintf(
			'<br /><small class="fleet-en-name">%s</small>',
			$en
		);
	}

	public function shortcode_countries_links( $atts, $content = '' ) {
		$attr  = wp_parse_args(
			$atts,
			array(
				'title' => 1,
				'flags' => 0,
			)
		);
		$terms = get_terms(
			array(
				'taxonomy'   => $this->taxonomy_name_location,
				'hide_empty' => false,
			)
		);
		if ( empty( $terms ) ) {
			return $content;
		}
		$content .= '<div class="results results-countries-list">';
		if ( 0 < intval( $attr['title'] ) ) {
			$content .= sprintf(
				'<h2>%s</h2>',
				esc_html__( 'Regatta results by country', 'fleet' )
			);
		}
		$mna_codes = $this->options->get_group( 'mna_codes' );
		$content  .= '<ul>';
		foreach ( $terms as $term ) {
			$classes = array();
			if ( 0 < $attr['flags'] ) {
				$classes[] = 'flag';
				$mna_code  = $this->get_country_code_by_country_name( $term->name );
				if ( ! is_wp_error( $mna_code ) && is_string( $mna_code ) && ! empty( $mna_code ) ) {
					$classes[] = sprintf( 'flag-%s', strtolower( $mna_code ) );
				}
			}
			$content .= sprintf(
				'<li class="result-country-%1$s"><a href="%2$s" class="%4$s">%3$s</a></li>',
				esc_attr( $term->slug ),
				get_term_link( $term ),
				esc_html( $term->name ),
				esc_attr( implode( ' ', $classes ) )
			);
		}
		$content .= '</ul>';
		$content .= '</div>';
		return $content;
	}

	/**
	 * get year link
	 *
	 * @since 2.0.0
	 */
	public function get_year_link( $year, $counter = 0 ) {
		$y = intval( $year );
		if ( 1 > $y ) {
			return $year;
		}
		$year = $y;
		if ( 0 < intval( $counter ) ) {
			return sprintf(
				'<a href="/%2$s/%1$d"><span>%3$d <small>(%4$d)</small></span></a>',
				esc_attr( $year ),
				esc_attr( _x( 'results', 'rewrite rule handler for results', 'fleet' ) ),
				esc_html( $year ),
				$counter
			);
		}
		return sprintf(
			'<a href="/%2$s/%1$d">%3$d</a>',
			esc_attr( $year ),
			esc_attr( _x( 'results', 'rewrite rule handler for results', 'fleet' ) ),
			esc_html( $year ),
		);
	}
}
