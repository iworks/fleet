<?php
/**
 * Database class for Fleet
 *
 * @package Fleet
 * @since 2.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class iworks_opi_polls_client_db
 *
 * Handles all database operations for the OPI Polls Client plugin.
 */
class iworks_fleet_db {

	/**
	 * DB version name
	 *
	 * @since 2.6.0
	 * @var string
	 */
	private $db_version_name = 'fleet_db_version';

	/**
	 * Array of database table names without prefix
	 *
	 * Contains the names of all custom database tables used by the plugin.
	 * These names will be automatically prefixed with the WordPress table prefix.
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	private array $table_names = array(
		'fleet_regatta',
		'fleet_regatta_race',
	);
	/**
	 * Class constructor
	 *
	 * @since 2.6.0
	 */
	public function __construct() {
		$this->register_tables();
		/**
		 * WordPress Hooks
		 */
		add_action( 'shutdown', array( $this, 'db_install' ) );
	}

	/**
	 * Register plugin tables with $wpdb
	 *
	 * This allows the tables to be referenced as $wpdb->{table_name} throughout the plugin.
	 * Tables are registered with the WordPress database prefix.
	 *
	 * @since 2.6.0
	 */
	private function register_tables() {
		global $wpdb;

		// Register each table
		foreach ( $this->table_names as $key => $table_name ) {
			$wpdb->$table_name = $wpdb->prefix . $table_name;
		}
	}

	/**
	 * Create the database table if it doesn't exist
	 *
	 * @since 1.0.0
	 */
	public function db_install() {
		global $wpdb;
		$version = intval( get_option( $this->db_version_name ) );
		/**
		 * 20180611
		 */
		$install = 20180611;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $wpdb->fleet_regatta (
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
			$sql = "CREATE TABLE $wpdb->fleet_regatta_race (
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
			update_option( $this->db_version_name, $install );
		}
		/**
		 * 20180618
		 */
		$install = 20180618;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$result = $wpdb->query( "ALTER TABLE $wpdb->fleet_regatta ADD COLUMN country TEXT AFTER boat_id;" );
			if ( $result ) {
				update_option( $this->db_version_name, $install );
			}
		}
		/**
		 * 20180619
		 */
		$install = 20180619;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$result = $wpdb->query( "ALTER TABLE $wpdb->fleet_regatta ADD COLUMN date date AFTER year;" );
			if ( $result ) {
				$result = $wpdb->query( "ALTER TABLE $wpdb->fleet_regatta ADD key ( date );" );
			}
			if ( $result ) {
				update_option( $this->db_version_name, $install );
			}
		}
		/**
		 * 20241006
		 */
		$install = 20241006;
		if ( $install > $version ) {
			$charset_collate = $wpdb->get_charset_collate();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$result = $wpdb->query( "ALTER TABLE $wpdb->fleet_regatta ADD COLUMN ranking INT DEFAULT 0 AFTER points" );
			if ( $result ) {
				update_option( $this->db_version_name, $install );
			}
		}
	}
}
