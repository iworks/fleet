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

if ( class_exists( 'iworks_fleet' ) ) {
	return;
}

require_once dirname( __DIR__, 1 ) . '/iworks.php';

class iworks_fleet extends iworks {

	private $capability;
	private $blocks;
	protected $options;

	/**
	 * integrations objects
	 *
	 * @since 2.1.6
	 */
	public $objects = array();

	public function __construct() {
		parent::__construct();
		$this->base       = dirname( __DIR__, 1 );
		$this->dir        = basename( dirname( $this->base ) );
		$this->version    = 'PLUGIN_VERSION';
		$this->capability = apply_filters( 'iworks_fleet_capability', 'manage_options' );
		/**
		 * Include files
		 *
		 * separate db function to file
		 *
		 * @since 2.6.0
		 */
		$classes = array(
			'db',
			'github',
		);
		foreach ( $classes as $class ) {
			$file = $this->base . '/iworks/fleet/class-iworks-fleet-' . $class . '.php';
			if ( is_file( $file ) ) {
				include_once $file;
				$this->objects[ $class ] = new iworks_fleet_db();
			}
		}
		/**
		 * post_types
		 */
		$post_types = array( 'boat', 'person', 'ranking', 'result' );
		foreach ( $post_types as $post_type ) {
			include_once $this->base . '/iworks/fleet/posttypes/class-iworks-fleet-posttypes-' . $post_type . '.php';
			$class                   = sprintf( 'iworks_fleet_posttypes_%s', $post_type );
			$value                   = sprintf( 'post_type_%s', $post_type );
			$this->objects[ $value ] = new $class();
		}
		/**
		 * blocks
		 */
		// include_once $this->base . '/iworks/fleet/class-iworks-fleet-blocks.php';
		// $this->blocks = new iworks_fleet_blocks( $this );
		/**
		 * admin init
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'action_init_load_post_types' ) );
		add_action( 'init', array( $this, 'register_boat_number' ) );
		add_action( 'init', array( $this, 'db_install' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'plugins_loaded', array( $this, 'action_plugins_loaded' ) );
		/**
		 * custom theme
		 */
		// add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 0 );
		// add_action( 'init', array( $this, 'register_theme_directory' ) );
		// add_filter( 'theme_root_uri', array( $this, 'theme_root_uri'), 10, 3 );
		/**
		 * shortcodes
		 */
		add_shortcode( 'fleet_stats', array( $this, 'stats' ) );
		/**
		 * iWorks Rate integration - change logo for rate
		 *
		 * @since 2.0.6
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
	}

	/**
	 * Load post types
	 */
	public function action_init_load_post_types() {
		$this->check_option_object();
	}

	/**
	 * Publish fleet Statistics.
	 *
	 * #since 1.0.0
	 */
	public function stats() {
		$elements = array(
			'person' => __( 'Number of sailors', 'fleet' ),
			'boat'   => __( 'Number of boats', 'fleet' ),
			'result' => __( 'Number of event results', 'fleet' ),
		);
		$content  = '<div class="fleet-statistsics">';
		$content .= sprintf( '<h2>%s</h2>', esc_html__( 'Statistics', 'fleet' ) );
		$content .= '<dl>';
		foreach ( $elements as $type => $label ) {
			$post_type = $this->objects[ 'post_type_' . $type ];
			$content  .= sprintf(
				'<dt class="fleet-statistsics-%s">%s</dt>',
				esc_attr( $type ),
				esc_html( $label )
			);
			$content  .= sprintf(
				'<dd class="fleet-statistsics-%s"><a href="%s">%d</a></dd>',
				esc_attr( $type ),
				get_post_type_archive_link( $post_type->get_name() ),
				$post_type->count()
			);
		}
		$content .= '</dl>';
		$content .= '</div>';
		return $content;
	}

	public function admin_init() {
		$this->check_option_object();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	public function get_post_type_name( $post_type ) {
		$value = sprintf( 'post_type_%s', $post_type );
		if ( isset( $this->objects[ $value ] ) ) {
			return $this->objects[ $value ]->get_name();
		}
		return new WP_Error( 'broke', __( 'The Fleet plugin do not have such post type!', 'fleet' ) );
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
		$file    = sprintf( '/assets/styles/fleet-admin%s.css', $this->dev );
		$version = $this->get_version( $file );
		$file    = plugins_url( $file, $this->base );
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
				'fleet-admin-select2'    => 'assets/scripts/admin/src/select2.js',
				'fleet-admin-boat'       => 'assets/scripts/admin/src/boat.js',
				'fleet-admin-datepicker' => 'assets/scripts/admin/src/datepicker.js',
				'fleet-admin-person'     => 'assets/scripts/admin/src/person.js',
				'fleet-admin-result'     => 'assets/scripts/admin/src/result.js',
				'fleet-admin'            => 'assets/scripts/admin/src/fleet.js',
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
			'nonces'   => array(),
			'user_id'  => get_current_user_id(),
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
			$file = 'assets/styles/fleet' . $this->dev . '.css';
			wp_enqueue_style( 'fleet', plugins_url( $file, $this->base ), array(), $this->get_version( $file ) );
		}
	}

	/**
	 * Plugin row data
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $this->dir . '/fleet.php' == $file ) {
			if ( ! is_multisite() && current_user_can( $this->capability ) ) {
				$links[] = '<a href="themes.php?page=' . $this->dir . '/admin/index.php">' . __( 'Settings', 'fleet' ) . '</a>';
			}

			$links[] = '<a href="http://iworks.pl/donate/fleet.php">' . __( 'Donate', 'fleet' ) . '</a>';

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
				'slug' => _x( 'fleet-boat-number', 'slug for images', 'fleet' ),
			),
		);
		register_taxonomy( 'boat_number', array( 'attachment' ), $args );
	}

	/**
	 * Get person name
	 */
	public function get_person_name( $user_post_id ) {
		return $this->objects['post_type_person']->get_person_name_by_id( $user_post_id );
	}
	/**
	 * Get person avatar
	 */
	public function get_person_avatar( $user_post_id ) {
		return $this->objects['post_type_person']->get_person_avatar_by_id( $user_post_id );
	}

	public function get_list_by_post_type( $type ) {
		$args  = array(
			'post_type' => $this->objects[ 'post_type_' . $type ]->get_name(),
			'nopaging'  => true,
		);
		$list  = array();
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$list[ $post->post_title ] = $post->ID;
		}
		return $list;
	}

	public function db_install() {
	}

	/**
	 * register styles
	 *
	 * @since 1.3.0
	 */
	public function register_styles() {
		$this->check_option_object();
		wp_register_style(
			$this->options->get_option_name( 'frontend' ),
			sprintf( plugins_url( '/assets/styles/fleet%s.css', $this->base ), $this->dev ? '' : '.min' ),
			array(),
			$this->version
		);
	}

	/**
	 * Enquque styles
	 *
	 * @since 1.3.0
	 */
	public function enqueue_styles() {
		$this->check_option_object();
		if ( $this->options->get_option( 'load_frontend_css' ) ) {
			wp_enqueue_style( $this->options->get_option_name( 'frontend' ) );
		}
	}

	/**
	 * register_theme_directory
	 *
	 * @since 1.3.0
	 *
	 */
	public function register_theme_directory() {
		register_theme_directory( dirname( $this->base ) . '/integration/themes' );
	}

	public function after_setup_theme() {
		$theme = wp_get_theme();
		if ( 'twentytwenty-5o5-child' !== $theme->get( 'Name' ) ) {
			return;
		}
	}

	public function theme_root_uri( $theme_root_uri, $siteurl, $stylesheet_or_template ) {
		return $theme_root_uri;
	}

	/**
	 * Plugin logo for rate messages
	 *
	 * @since 2.0.6
	 *
	 * @param string $logo Logo, can be empty.
	 * @param object $plugin Plugin basic data.
	 */
	public function filter_plugin_logo( $logo, $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}
		if ( 'fleet' === $plugin['slug'] ) {
			return plugin_dir_url( dirname( __DIR__, 1 ) ) . '/assets/images/logo.svg';
		}
		return $logo;
	}

	/**
	 * load integrations
	 *
	 * @since 2.1.6
	 */
	public function action_plugins_loaded() {
		$dir = __DIR__ . '/fleet';
		/**
		 * og
		 *
		 * @since 2.1.6
		 */
		if ( class_exists( 'iWorks_OpenGraph' ) ) {
			include_once $dir . '/integration/class-iworks-fleet-integration-og.php';
			$this->objects['og'] = new iworks_fleet_integration_og();
		}
		/**
		 * fleet loaded action
		 *
		 * @since 2.1.6
		 */
		do_action( 'fleet/loaded' );
	}

	/**
	 * @since 2.3.6
	 */
	public function get_regatta_select_by_year_and_serie_id( $year, $serie ) {
		return $this->objects['post_type_result']->get_regatta_select_by_year_and_serie_id( $year, $serie );
	}

	/**
	 * check option object
	 *
	 * @since 2.3.6
	 */
	private function check_option_object() {
		if ( is_a( $this->options, 'iworks_options' ) ) {
			return;
		}
		$this->options = iworks_fleet_get_options_object();
	}
}
