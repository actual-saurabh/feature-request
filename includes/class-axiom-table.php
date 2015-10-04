<?php
/**
 * Custom Tables Class.
 *
 * @package   Custom Tables
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://averta.net
 * @copyright Copyright © 2015 averta
 */

// no direct access allowed
if ( ! defined('ABSPATH') ) {
    die();
}

if( class_exists( 'Axiom_Table' ) ){
	return;
}

/**
 * Create and manipulate custom tables in WordPress database.
 */
class Axiom_Table {

	/**
	 * Current database version
	 */
	const DB_VERSION = 1.2;


	/**
	* Count of rows returned by previous query
	*
	* @since 1.0
	* @access private
	* @var int
	*/
	var $num_rows = 0;


	/**
	* Last query made
	*
	* @since 1.0
	* @access private
	* @var array
	*/
	var $last_query;


	/**
	* Results of the last query made
	*
	* @since 1.0
	* @access private
	* @var array|null
	*/
	var $last_result;


	/**
	* Table prefix
	*
	* @since 1.0
	* @access private
	* @var string
	*/
	var $table_prefix = 'item_name_';


	/**
	* Transient prefix
	*
	* @since 1.0
	* @access private
	* @var string
	*/
	var $transient_prefix = 'item_name_';


	/**
	* Cache group
	*
	* @since 1.0
	* @access private
	* @var string
	*/
	var $cache_group = 'item_name';


	/**
	* Master table tabes
	*
	* @since 1.0
	* @access private
	* @var string
	*/
	var $tabel_names = array();


	/**
	 * The database character collate.
	 *
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	var $charset_collate = '';



	/**
	 *
	 */
	public function __construct() {

		if( is_admin() ) {

			$this->update_tables();
			add_filter( 'wpmu_drop_tables', array( $this, 'wpmu_drop_tables' ), 11, 2 );
		}

	}

	/**
	 * Get known properties
	 *
	 * @param  string   property name
	 * @return string   property value
	 */
	public function __get( $name ){

		if( in_array( $name, $this->tabel_names ) ){
			return $this->get_global_table_name( $name );

		// Get list of Masterslider table names
		} elseif( 'tables' == $name ){
			global $wpdb;
			$tables = array();

			foreach ( $this->tabel_names as $table_name )
				$tables[ $table_name ] = $wpdb->prefix . $this->table_prefix . $table_name;

			return $tables;

		} else {
			return NULL;
		}
	}



	/**
	 * Create a table by name
	 *
	 * @since 1.0
	 * @return null
	 */
	protected function create_table( $table_name, $global_table_name ) {}



	/**
	 * Create master slider tables
	 *
	 * Should be invoked on plugin activation
	 *
	 * @since 1.0
	 * @return null
	 */
	public function create_tables() {
		global $wpdb, $charset_collate;

		// set database character collate
		if ( ! empty( $wpdb->charset ) ){
	        $this->charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
	    if ( ! empty ( $wpdb->collate ) ){
	        $this->charset_collate .= " COLLATE {$wpdb->collate}";
	    }

	    foreach ( $this->tables as $table_name => $global_table_name ) {

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$global_table_name'" ) != $global_table_name ){

				$method_name = 'create_table_' . $table_name;

				if( method_exists( $this, $method_name ) ){
					$this->{$method_name}();
				} elseif( method_exists( $this, 'create_table' ) ){
					$this->create_table( $table_name, $global_table_name );
				}

			}

		}

		// update tables version to current version
		update_option( $this->transient_prefix . 'db_version', self::DB_VERSION );

		do_action( $this->table_prefix . 'tables_created', $this->tables );
	}



	/**
	 * Updates masterslider tables if update is required
	 *
	 * @since 1.0
	 * @return bool  is any update required for tabels?
	 */
	public function update_tables(){
		// check if the tables need update
		if( get_option( $this->transient_prefix . 'db_version', '0' ) == self::DB_VERSION )
			return false;

		$this->create_tables();

		do_action( $this->table_prefix . 'tables_updated', $this->tables );
		return true;
	}


	/**
	 * Drop all master slider tables
	 *
	 * @since 1.0
	 * @return null
	 */
	public function delete_tables() {
		global $wpdb;

		foreach ( $this->tables as $table_id => $table_name) {
			$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		}
	}


	/**
	 * Filter the tables to drop when the blog is deleted
	 *
	 * @since 1.8
	 * @return null
	 */
	public function wpmu_drop_tables( $tables, $blog_id ){
		global $wpdb;

		foreach ( $this->tabel_names as $table_name ) {
			$tables[] = $wpdb->base_prefix . $blog_id . $this->table_prefix . $table_name;
		}

		return $tables;
	}


	/**
	 * Modifies the database based on specified SQL statements
	 *
	 * @param  $queries string|array (Optional) The query to run. Can be multiple queries in an array, or a string of queries separated by semicolons.
	 * @param  $execute bool 		 (Optional) Whether or not to execute the query right away.
	 * @return array 				 Strings containing the results of the various update queries.
	 */
	public function dbDelta( $queries = '', $execute = true ){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $queries, $execute );
	}



	/**
	 * Get real (global) name of table
     *
	 * @param  string $local_name The local name of table
	 * @return string             The global name of table
	 */
	public function get_global_table_name( $local_name ){
		global $wpdb;
		return $wpdb->prefix . $this->table_prefix . $local_name;
	}


	/**
	 * Get an array containing row results from a table
	 *
	 * @param  int $args        The query args
	 * @return array|null 	    Records in an array or null if no result found
	 */
	public function row_results( $table_name = '', $args = array() ) {

		if( empty( $table_name ) ){
			return array();

		// make sure table name is global rather than local
		} elseif ( in_array( $table_name, $this->tabel_names ) ){
			$table_name = $this->get_global_table_name( $table_name );
		}

		global $wpdb;

		$default_args = array(
			'perpage' => 0,
			'offset'  => 0,
			'orderby' => 'ID',
			'order'   => 'DESC',
			'where'   => "status='published'",
			'like' 	  => ''
		);

		$args = wp_parse_args( $args, $default_args );


		// convert perpage type to number
		$limit_num = (int) $args['perpage'];

		// convert offset type to number
		$offset_num = (int) $args['offset'];

		// remove limit if limit number is set to 0
		$limit  = ( 1 > $limit_num ) ? '' : 'LIMIT '. $limit_num;

		// remove offect if offset number is set to 0
		$offset = ( 0 == $offset_num )? '' : 'OFFSET '. $offset_num;

		// add LIKE if defined
		$like  = empty( $args['like'] ) ? '' : 'LIKE '. $args['like'];

		$where = empty( $args['where'] ) ? '' : 'WHERE '. $args['where'];

		// sanitize sort type
		$order   = strtolower( $args['order'] ) === 'desc' ? 'DESC' : 'ASC';
		$orderby = $args['orderby'];

        $order_clause = sanitize_sql_orderby( $orderby . ' ' . $order );

		$sql = "
			SELECT *
			FROM $table_name
			$where
			ORDER BY $order_clause
			$limit
			$offset
			";

		return $wpdb->get_results( $sql, ARRAY_A );
	}


	/**
	 * Get total number of sliders from sliders table
	 *
	 * @return int|null 	 total number of sliders or null on failure
	 */
	public function get_total_table_count( $table_name = '', $where = "status='published'" ) {
		global $wpdb;

		if( empty( $table_name ) ){
			return null;
		}

		$result = $wpdb->get_results( "SELECT count(ID) AS total FROM {$this->sliders} WHERE {$where} ", ARRAY_A );
		return $result ? (int)$result[0]['total'] : null;
	}



	/**
	 * Insert a row into a table.
	 *
	 * @param array $fields    array of fields and values to insert
	 * @param array $defaults  array of default fields value to insert if field value is not set
	 *
	 * @return int|false ID number for new inserted row or false if the row could not be inserted.
	 */
	public function insert( $table_name, $fields = array(), $defaults = array(), $format = NULL ) {
		global $wpdb;

		// merge input $fields with defaults
		$data = wp_parse_args( $fields, $defaults );

		// map through some fields and serialize values if data type is array or object
		$data = $this->maybe_serialize_fields( $data );

		// Insert a row into the table. returns false if the row could not be inserted.
		$result = $wpdb->insert( $table_name, $data, $format );

		return false === $result ? $result : $wpdb->insert_id;
	}


	// map through some fields and unserialize values if data field is serialized
	protected function maybe_unserialize_fields($row_fields){
		if ( empty( $row_fields ) ) {
			return $row_fields;
		}

		foreach ( $row_fields as $key => $value) {
			$row_fields[ $key ] = maybe_unserialize( $value );
		}
		return $row_fields;
	}



	// map through some fields and serialize values if data type is array
	protected function maybe_serialize_fields( $row_fields ){
		if ( empty( $row_fields ) ) {
			return $row_fields;
		}

		foreach ( $row_fields as $key => $value ) {
			$row_fields[ $key ] = maybe_serialize( $value );
		}

		return $row_fields;
	}


}
