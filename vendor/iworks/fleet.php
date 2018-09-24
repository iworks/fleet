<?php
/*

Copyright 2017-2018 Marcin Pietrzak (marcin@iworks.pl)

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

if ( class_exists( 'iworks_fleet' ) ) {
	return;
}

require_once( dirname( dirname( __FILE__ ) ) . '/iworks.php' );

class iworks_fleet extends iworks {

	private $capability;
	private $post_type_boat;
	private $post_type_person;
	private $post_type_result;

	public function __construct() {
		parent::__construct();
		$this->version = '1.2.3';
		$this->capability = apply_filters( 'iworks_fleet_capability', 'manage_options' );
		/**
		 * post_types
		 */
		$post_types = array( 'boat', 'person', 'result' );
		foreach ( $post_types as $post_type ) {
			include_once( $this->base.'/iworks/fleet/posttypes/'.$post_type.'.php' );
			$class = sprintf( 'iworks_fleet_posttypes_%s', $post_type );
			$value = sprintf( 'post_type_%s', $post_type );
			$this->$value = new $class();
		}
		/**
		 * admin init
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'register_boat_number' ) );
		add_action( 'init', array( $this, 'db_install' ) );
		/**
		 * shortcodes
		 */
		add_shortcode( 'fleet_stats', array( $this, 'stats' ) );
		/**
		 * Add SVG to allowed files.
		 *
		 * @since 1.2.2
		 */
		add_filter( 'upload_mimes', array( $this, 'add_mime_types' ) );
	}

	/**
	 * Add SVG to allowed files.
	 *
	 * @since 1.2.2
	 */
	public function add_mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Publish fleet Statistics.
	 *
	 * #since 1.0.0
	 */
	public function stats() {
		$content = '<div class="fleet-statistsics">';
		$content .= sprintf( '<h2>%s</h2>', esc_html__( 'Statistics', 'fleet' ) );
		$content .= '<dl>';
		$content .= sprintf( '<dt>%s</dt>', esc_html__( 'Number of sailors', 'fleet' ) );
		$content .= sprintf( '<dd><a href="%s">%d</a></dd>', get_post_type_archive_link( $this->post_type_person->get_name() ), $this->post_type_person->count() );
		$content .= sprintf( '<dt>%s</dt>', esc_html__( 'Number of boats', 'fleet' ) );
		$content .= sprintf( '<dd><a href="%s">%d</a></dd>', get_post_type_archive_link( $this->post_type_boat->get_name() ), $this->post_type_boat->count() );
		$content .= sprintf( '<dt>%s</dt>', esc_html__( 'Number of event results', 'fleet' ) );
		$content .= sprintf( '<dd><a href="%s">%d</a></dd>', get_post_type_archive_link( $this->post_type_result->get_name() ), $this->post_type_result->count() );
		$content .= '</dl>';
		$content .= '</div>';
		return $content;
	}

	public function admin_init() {
		iworks_fleet_options_init();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	public function get_post_type_name( $post_type ) {
		$value = sprintf( 'post_type_%s', $post_type );
		if ( isset( $this->$value ) ) {
			return $this->$value->get_name();
		}
		return new WP_Error( 'broke', __( 'fleet do not have such post type!', 'fleet' ) );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		/**
		 * off on not fleet pages
		 */
		$re = sprintf( '/%s_/', __CLASS__ );
		if ( ! preg_match( $re, $screen->id ) ) {
			return;
		}
		/**
		 * datepicker
		 */
		$file = 'assets/externals/datepicker/css/jquery-ui-datepicker.css';
		$file = plugins_url( $file, $this->base );
		wp_register_style( 'jquery-ui-datepicker', $file, false, '1.12.1' );
		/**
		 * select2
		 */
		$file = 'assets/externals/select2/css/select2.min.css';
		$file = plugins_url( $file, $this->base );
		wp_register_style( 'select2', $file, false, '4.0.3' );
		/**
		 * Admin styles
		 */
		$file = sprintf( '/assets/styles/fleet-admin%s.css', $this->dev );
		$version = $this->get_version( $file );
		$file = plugins_url( $file, $this->base );
		wp_register_style( 'admin-fleet', $file, array( 'jquery-ui-datepicker', 'select2' ), $version );
		wp_enqueue_style( 'admin-fleet' );
		/**
		 * select2
		 */
		wp_register_script( 'select2', plugins_url( 'assets/externals/select2/js/select2.full.min.js', $this->base ), array(), '4.0.3' );
		/**
		 * Admin scripts
		 */
		$files = array(
			'fleet-admin' => sprintf( 'assets/scripts/admin/fleet%s.js', $this->dev ),
		);
		if ( '' == $this->dev ) {
			$files = array(
				'fleet-admin-select2' => 'assets/scripts/admin/src/select2.js',
				'fleet-admin-boat' => 'assets/scripts/admin/src/boat.js',
				'fleet-admin-datepicker' => 'assets/scripts/admin/src/datepicker.js',
				'fleet-admin-person' => 'assets/scripts/admin/src/person.js',
				'fleet-admin-result' => 'assets/scripts/admin/src/result.js',
				'fleet-admin' => 'assets/scripts/admin/src/fleet.js',
			);
		}
		$deps = array(
			'jquery-ui-datepicker',
			'select2',
		);
		foreach ( $files as $handle => $file ) {
			wp_register_script(
				$handle,
				plugins_url( $file, $this->base ),
				$deps,
				$this->get_version(),
				true
			);
			wp_enqueue_script( $handle );
		}
		/**
		 * JavaScript messages
		 *
		 * @since 1.0.0
		 */
		$data = array(
			'messages' => array(),
			'nonces' => array(),
			'user_id' => get_current_user_id(),
		);
		wp_localize_script(
			'fleet-admin',
			__CLASS__,
			apply_filters( 'wp_localize_script_fleet_admin', $data )
		);
	}

	public function init() {
		if ( is_admin() ) {
		} else {
			$file = 'assets/styles/fleet'.$this->dev.'.css';
			wp_enqueue_style( 'fleet', plugins_url( $file, $this->base ), array(), $this->get_version( $file ) );
		}
	}

	/**
	 * Plugin row data
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $this->dir.'/fleet.php' == $file ) {
			if ( ! is_multisite() && current_user_can( $this->capability ) ) {
				$links[] = '<a href="themes.php?page='.$this->dir.'/admin/index.php">' . __( 'Settings' ) . '</a>';
			}

			$links[] = '<a href="http://iworks.pl/donate/fleet.php">' . __( 'Donate' ) . '</a>';

		}
		return $links;
	}

	public function register_boat_number() {
		$labels = array(
			'name'                       => _x( 'Boat Numbers', 'Taxonomy General Name', 'fleet' ),
			'singular_name'              => _x( 'Boat Number', 'Taxonomy Singular Name', 'fleet' ),
			'menu_name'                  => __( 'Boat Number', 'fleet' ),
			'all_items'                  => __( 'All Boat Numbers', 'fleet' ),
			'new_item_name'              => __( 'New Boat Number Name', 'fleet' ),
			'add_new_item'               => __( 'Add New Boat Number', 'fleet' ),
			'edit_item'                  => __( 'Edit Boat Number', 'fleet' ),
			'update_item'                => __( 'Update Boat Number', 'fleet' ),
			'view_item'                  => __( 'View Boat Number', 'fleet' ),
			'separate_items_with_commas' => __( 'Separate Boat Numbers with commas', 'fleet' ),
			'add_or_remove_items'        => __( 'Add or remove Boat Numbers', 'fleet' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'fleet' ),
			'popular_items'              => __( 'Popular Boat Numbers', 'fleet' ),
			'search_items'               => __( 'Search Boat Numbers', 'fleet' ),
			'not_found'                  => __( 'Not Found', 'fleet' ),
			'no_terms'                   => __( 'No items', 'fleet' ),
			'items_list'                 => __( 'Boat Numbers list', 'fleet' ),
			'items_list_navigation'      => __( 'Boat Numbers list navigation', 'fleet' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'show_ui'                    => true,
			'show_in_quick_edit' => true,
			'rewrite' => array(
				'slug' => _x( 'fleet-boat-number', 'slug for images', 'fleet' ),
			),
		);
		register_taxonomy( 'boat_number', array( 'attachment' ), $args );
	}

	/**
	 * Get person name
	 */
	public function get_person_name( $user_post_id ) {
		return $this->post_type_person->get_person_name_by_id( $user_post_id );
	}
	/**
	 * Get person avatar
	 */
	public function get_person_avatar( $user_post_id ) {
		return $this->post_type_person->get_person_avatar_by_id( $user_post_id );
	}

	public function get_list_by_post_type( $type ) {
		$args = array(
			'post_type' => $this->{'post_type_'.$type}->get_name(),
			'nopaging' => true,
		);
		$list = array();
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$list[ $post->post_title ] = $post->ID;
		}
		return $list;
	}

	public function db_install() {
		global $wpdb;
		$version = intval( get_option( 'fleet_db_version' ) );
		/**
		 * 20180611
		 */
		$install = 20180611;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$table_name = $wpdb->prefix . 'fleet_regatta';
			$sql = "CREATE TABLE $table_name (
                ID mediumint(9) NOT NULL AUTO_INCREMENT,
                post_regata_id mediumint(9) NOT NULL,
                year year,
                boat_id mediumint(9),
                helm_id mediumint(9),
                crew_id mediumint(9),
                helm_name text,
                crew_name text,
                place int,
                points int,
                PRIMARY KEY (ID),
                KEY ( post_regata_id ),
                KEY ( year ),
                KEY ( boat_id ),
                KEY ( helm_id ),
                KEY ( crew_id )
            ) $charset_collate;";
			dbDelta( $sql );
			$table_name = $wpdb->prefix . 'fleet_regatta_race';
			$sql = "CREATE TABLE $table_name (
                ID mediumint(9) NOT NULL AUTO_INCREMENT,
                post_regata_id mediumint(9) NOT NULL,
                regata_id mediumint(9) NOT NULL,
                number int,
                code varchar(4),
                place int,
                points int default 0,
                discard boolean default 0,
                PRIMARY KEY (ID),
                KEY ( post_regata_id ),
                KEY ( regata_id )
            ) $charset_collate;";
			dbDelta( $sql );
			update_option( 'fleet_db_version', $install );
		}
		/**
		 * 20180618
		 */
		$install = 20180618;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$table_name = $wpdb->prefix . 'fleet_regatta';
			$sql = "ALTER TABLE $table_name ADD COLUMN country TEXT AFTER boat_id;";
			$result = $wpdb->query( $sql );
			if ( $result ) {
				update_option( 'fleet_db_version', $install );
			}
		}
		/**
		 * 20180619
		 */
		$install = 20180619;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$table_name = $wpdb->prefix . 'fleet_regatta';
			$sql = "ALTER TABLE $table_name ADD COLUMN date date AFTER year;";
			$result = $wpdb->query( $sql );
			if ( $result ) {
				$sql = "ALTER TABLE $table_name ADD key ( date );";
				$result = $wpdb->query( $sql );
			}
			if ( $result ) {
				update_option( 'fleet_db_version', $install );
			}
		}
	}
}
