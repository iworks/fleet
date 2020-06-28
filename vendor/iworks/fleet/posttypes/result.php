<?php
/*
Copyright 2018 Marcin Pietrzak (marcin@iworks.pl)

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
		add_action( 'template_redirect', array( $this, 'download' ) );
		/**
		 * fields
		 */
		$this->fields = array(
			'result' => array(
				'location'              => array( 'label' => __( 'Area', 'fleet' ) ),
				'organizer'             => array( 'label' => __( 'Organizer', 'fleet' ) ),
				'secretary'             => array( 'label' => __( 'Secretary', 'fleet' ) ),
				'arbiter'               => array( 'label' => __( 'Arbiter', 'fleet' ) ),
				'date_start'            => array(
					'type'  => 'date',
					'label' => __( 'Event start', 'fleet' ),
				),
				'date_end'              => array(
					'type'  => 'date',
					'label' => __( 'Event end', 'fleet' ),
				),
				'number_of_races'       => array(
					'type'  => 'number',
					'label' => __( 'Number of races', 'fleet' ),
				),
				'number_of_competitors' => array(
					'type'  => 'number',
					'label' => __( 'Number of competitors', 'fleet' ),
				),
				'wind_direction'        => array( 'label' => __( 'Wind direction', 'fleet' ) ),
				'wind_power'            => array( 'label' => __( 'Wind power', 'fleet' ) ),
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
		add_filter( 'iworks_fleet_result_boat_regatta_list', array( $this, 'regatta_list_by_boat_id' ), 10, 2 );
		add_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		/**
		 * save custom slug
		 */
		add_action( 'save_post', array( $this, 'set_slug' ), 10, 3 );
		/**
		 * change default sort order
		 */
		add_action( 'pre_get_posts', array( $this, 'change_order' ) );
		/**
		 * shortcodes
		 */
		add_shortcode( 'fleet_regattas_list', array( $this, 'shortcode_list' ) );
		add_shortcode( 'fleet_ranking', array( $this, 'shortcode_ranking' ) );
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
	}

	/**
	 * allow to download results
	 */
	public function download() {
		global $wpdb;
		if ( ! is_singular( $this->post_type_name ) ) {
			return;
		}
		$action = filter_input( INPUT_GET, 'fleet', FILTER_SANITIZE_STRING );
		switch ( $action ) {
			case 'download':
				$file = sanitize_title( get_the_title() ) . '.csv';
				header( 'Content-Type: text/csv' );
				header( 'Content-Disposition: attachment; filename=' . $file );
				$out     = fopen( 'php://output', 'w' );
				$post_id = get_the_ID();
				$row     = array();
				$row[]   = __( 'Place', 'fleet' );
				$row[]   = __( 'Boat', 'fleet' );
				$row[]   = __( 'Helm', 'fleet' );
				$row[]   = __( 'Crew', 'fleet' );
				$number  = intval( get_post_meta( $post_id, 'iworks_fleet_result_number_of_races', true ) );
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
				fclose( $out );
				exit;
		}
	}

	public function shortcode_list_helper_table_start( $serie_show_image ) {
		$content  = '<table class="fleet-results fleet-results-list">';
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= sprintf( '<th class="dates">%s</th>', esc_attr__( 'Dates', 'fleet' ) );
		if ( $serie_show_image ) {
			$content .= sprintf( '<th class="Serie">%s</th>', '&nbsp' );
		}
		$content .= sprintf( '<th class="title">%s</th>', esc_attr__( 'Title', 'fleet' ) );
		$content .= sprintf( '<th class="area">%s</th>', esc_attr__( 'Area', 'fleet' ) );
		$content .= sprintf( '<th class="races">%s</th>', esc_attr__( 'Races', 'fleet' ) );
		$content .= sprintf( '<th class="teams">%s</th>', esc_attr__( 'Teams', 'fleet' ) );
		$content .= '</tr>';
		$content .= '<thead>';
		$content .= '<tbody>';
		return $content;
	}

	public function shortcode_list( $atts ) {
		$atts    = shortcode_atts(
			array(
				'year'       => date( 'Y' ),
				'serie'      => null,
				'title'      => __( 'Results', 'fleet' ),
				'title_show' => 'on',
				'order'      => 'DESC',
			),
			$atts,
			'fleet_results_list'
		);
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
				$content .= $this->shortcode_list_helper_table_start( $serie_show_image );
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
							$content .= $this->shortcode_list_helper_table_start( $serie_show_image );
						}
						$current = $value;
					}
				}
				$tbody .= '<tr>';
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
							$image_id = get_term_meta( $term->term_id, 'image', true );

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
				$tbody .= sprintf(
					'<td class="title"><a href="%s">%s</a></td>',
					get_permalink(),
					get_the_title()
				);
				$terms  = get_the_term_list( get_the_ID(), $this->taxonomy_name_location );
				$tbody .= sprintf( '<td class="area">%s</td>', $terms );
				$check  = $this->has_races( get_the_ID() );
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
		return $content;
	}

	public function change_order( $query ) {
		if ( is_admin() ) {
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
					echo esc_html( $year );
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
		return $this->add_results_metadata( $wpdb->get_results( $sql ) );
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
			'post_type' => $this->post_type_name,
			'post__in'  => $ids,
			'fields'    => 'ids',
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
		if ( empty( $content ) ) {
			$content = __( 'There is no register regatta for this sailor.', 'fleet' );
		}
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		$regattas = $this->get_list_by_sailor_id( $sailor_id );
		$post_id  = get_the_ID();
		if ( ! empty( $regattas ) ) {
			$content  = '<table class="fleet-results"><thead><tr>';
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

			$format = get_option( 'date_format' );
			foreach ( $regattas as $regatta ) {
				$dates    = sprintf(
					'%s - %s',
					date_i18n( $format, $regatta->date_start ),
					date_i18n( $format, $regatta->date_end )
				);
				$content .= sprintf( '<tr class="fleet-place-%d">', $regatta->place );
				$content .= sprintf( '<td class="year" title="%s">%d</td>', esc_attr( $dates ), $regatta->year );
				$content .= sprintf( '<td class="name"><a href="%s">%s</a></td>', get_permalink( $regatta->post_regata_id ), get_the_title( $regatta->post_regata_id ) );
				/**
				 * Boat
				 */
				$content .= '<td class="boat">';
				if ( isset( $regatta->boat ) ) {
					$content .= sprintf(
						'<a href="%s">%s</a>',
						esc_url( $regatta->boat['url'] ),
						esc_html( $regatta->boat['post_title'] )
					);
				} elseif ( empty( $regatta->country ) && empty( $regatta->boat_id ) ) {
					$content .= '&ndash;';
				} else {
					$content .= sprintf( '%s %s', $regatta->country, $regatta->boat_id );
				}
				$content .= '</td>';
				/**
				 * Helmsman
				 */
				if ( $regatta->helm_id && $regatta->helm_id != $post_id ) {
					$content .= sprintf( '<td class="helmsman"><a href="%s">%s</a></td>', get_permalink( $regatta->helm_id ), get_the_title( $regatta->helm_id ) );
				} elseif ( $regatta->helm_id == $post_id ) {
					$content .= sprintf( '<td class="helmsman current">%s</td>', $regatta->helm_name );
				} else {
					$content .= sprintf( '<td class="helmsman">%s</td>', $regatta->helm_name );
				}
				/**
				 * crew
				 */
				if ( $regatta->crew_id && $regatta->crew_id != $post_id ) {
					$content .= sprintf( '<td class="crew"><a href="%s">%s</a></td>', get_permalink( $regatta->crew_id ), get_the_title( $regatta->crew_id ) );
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

	public function regatta_list_by_boat_id( $content, $boat_id ) {
		remove_filter( 'the_title', array( $this, 'add_year_to_title' ), 10, 2 );
		$regattas = $this->get_list_by_boat_id( $boat_id );
		if ( empty( $regattas ) ) {
			return '';
		}
		$post_id = get_the_ID();
		if ( ! empty( $regattas ) ) {
			$content  = '<table class="fleet-results"><thead><tr>';
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
				$content .= sprintf( '<tr class="fleet-place-%d">', $regatta->place );
				$content .= sprintf( '<td class="year">%d</td>', $regatta->year );
				$content .= sprintf( '<td class="name"><a href="%s">%s</a></td>', get_permalink( $regatta->post_regata_id ), get_the_title( $regatta->post_regata_id ) );
				/**
				 * Helmsman
				 */
				if ( $regatta->helm_id && $regatta->helm_id != $post_id ) {
					$content .= sprintf( '<td class="helmsman"><a href="%s">%s</a></td>', get_permalink( $regatta->helm_id ), get_the_title( $regatta->helm_id ) );
				} elseif ( $regatta->helm_id == $post_id ) {
					$content .= sprintf( '<td class="helmsman current">%s</td>', $regatta->helm_name );
				} else {
					$content .= sprintf( '<td class="helmsman">%s</td>', $regatta->helm_name );
				}
				/**
				 * crew
				 */
				if ( $regatta->crew_id && $regatta->crew_id != $post_id ) {
					$content .= sprintf( '<td class="crew"><a href="%s">%s</a></td>', get_permalink( $regatta->crew_id ), get_the_title( $regatta->crew_id ) );
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
		echo '<input type="file" name="file" id="file_fleet_races"/>';
		wp_nonce_field( 'upload-races', __CLASS__ );
		echo '<button>import</button>';
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
			$boat = array_shift( $row );
			if ( preg_match( '/^([a-zA-Z\/]+)[ \-\t]*(\d+)$/', $boat, $matches ) ) {
				$country = $matches[1];
				$boat_id = $matches[2];
			} else {
				$country = $boat;
				$boat_id = intval( array_shift( $row ) );
			}
			$helm    = trim( array_shift( $row ) );
			$crew    = trim( array_shift( $row ) );
			$club    = trim( array_shift( $row ) );
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
					'post_regata_id' => $_POST['id'],
					'regata_id'      => $regatta_id,
					'number'         => $number++,
				);
				$one  = trim( $one );
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
					case 's';
					case 'S';
						$race['code'] = 'DNS';
					break;
					case 'f';
					case 'F';
						$race['code'] = 'DNF';
					break;
					case 'q';
					case 'Q';
						$race['code'] = 'DSQ';
					break;
				}
				$race['points'] = preg_replace( '/[^\d^\,^\.]+/', '', $one );
				$wpdb->insert( $table_name_regatta_race, $race );
			}
		}
		wp_send_json_success();
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
			if ( preg_match( '/^wind/', $key ) ) {
				continue;
			}
			$classes = array(
				sprintf( 'fleet-results-%s', $key ),
			);
			$name    = $this->options->get_option_name( 'result_' . $key );
			$value   = trim( get_post_meta( $post_id, $name, true ) );
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
		$args     = array(
			'fleet'  => 'download',
			'format' => 'csv',
		);
		$url      = add_query_arg( $args );
		$content .= sprintf(
			'<div class="fleet-results-get"><a href="%s" class="fleet-results-csv">%s</a></div>',
			$url,
			__( 'Download', 'fleet' )
		);
		$content .= '<table class="fleet-results fleet-results-person">';
		$content .= '<thead>';
		$content .= '<tr>';
		$content .= sprintf( '<td class="place">%s</td>', esc_html__( 'Place', 'fleet' ) );
		$content .= sprintf( '<td class="boat">%s</td>', esc_html__( 'Boat', 'fleet' ) );
		$content .= sprintf( '<td class="helm">%s</td>', esc_html__( 'Helm', 'fleet' ) );
		$content .= sprintf( '<td class="crew">%s</td>', esc_html__( 'Crew', 'fleet' ) );
		$number   = intval( get_post_meta( $post_id, 'iworks_fleet_result_number_of_races', true ) );
		for ( $i = 1; $i <= $number; $i++ ) {
			$content .= sprintf( '<td class="race race-%d">%d</td>', $i, $i );
		}
		$content .= sprintf( '<td class="sum">%s</td>', esc_html__( 'Sum', 'fleet' ) );
		$content .= '</tr>';
		$content .= '</thead>';
		$content .= '<tbody>';
		$show     = current_user_can( 'manage_options' );
		foreach ( $regatta as $one ) {
			$content .= sprintf( '<tr class="fleet-place-%d">', $one->place );
			$content .= sprintf( '<td class="place">%d</td>', $one->place );

			/**
			 * boat
			 */
			$content  .= sprintf(
				'<td class="boat_id country-%s">',
				esc_attr( strtolower( $one->country ) )
			);
			$boat      = $this->get_boat_data_by_number( $one->boat_id );
			$boat_name = sprintf( '%s %d', $one->country, $one->boat_id );
			if ( false === $boat ) {
				$content .= esc_html( $boat_name );
			} else {
				$content .= sprintf(
					'<a href="%s">%s</a>',
					esc_url( $boat['url'] ),
					esc_html( $boat_name )
				);
			}
			$content .= '</td>';
			/**
			 * helmsman
			 */
			if ( ! empty( $one->helm_id ) ) {
				$extra = '';
				if ( $show ) {
					$extra = $this->get_extra_data( $one->helm_id );
				}
				$content .= sprintf( '<td class="helm_name"><a href="%s">%s</a>%s</td>', get_permalink( $one->helm_id ), $one->helm_name, $extra );
			} else {
				$content .= sprintf( '<td class="helm_name">%s</td>', $one->helm_name );
			}
			/**
			 * crew
			 */
			if ( ! empty( $one->crew_id ) ) {
				$extra = '';
				if ( $show ) {
					$extra = $this->get_extra_data( $one->crew_id );
				}
				$content .= sprintf( '<td class="crew_name"><a href="%s">%s</a>%s</td>', get_permalink( $one->crew_id ), $one->crew_name, $extra );
			} else {
				$content .= sprintf( '<td class="crew_name">%s</td>', $one->crew_name );
			}
			if ( isset( $races[ $one->ID ] ) && ! empty( $races[ $one->ID ] ) ) {
				foreach ( $races[ $one->ID ] as $race_number => $race_points ) {
					$class    = preg_match( '/\*/', $race_points ) ? 'race-discard' : '';
					$content .= sprintf(
						'<td class="race race-%d %s">%s</td>',
						esc_attr( $race_number ),
						esc_attr( $class ),
						$race_points
					);
				}
			}
			$content .= sprintf( '<td class="points">%d</td>', $one->points );
			$content .= '</tr>';
		}
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

	private function get_td( $name, $post_id ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return '-';
		}
		$meta_key = $this->options->get_option_name( 'result_' . $name );
		$value    = get_post_meta( get_the_ID(), $meta_key, true );
		if ( empty( $value ) ) {
			$value = '-';
		}
		return sprintf(
			'<td class="%s">%s</td>',
			esc_attr( preg_replace( '/_/', '-', $name ) ),
			esc_html( $value )
		);
	}

	private function get_extra_data( $user_id ) {
		$extra = '';
		$name  = $this->options->get_option_name( 'personal_birth_year' );
		$year  = get_post_meta( $user_id, $name, true );
		if ( empty( $year ) ) {
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

}
