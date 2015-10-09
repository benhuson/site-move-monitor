<?php

/**
 * Site Move Monitor Checker
 *
 * This class manages tests to see if the WordPress configuration may have changed.
 *
 * By default it checks:
 *
 *  - IP Address
 *  - File Path
 *  - URL
 *  - Database
 *  - Database Host
 *  - Database Table Prefix
 * 
 * @package     Site Move Monitor
 * @subpackage  Checker
 */

class Site_Move_Monitor_Checker {

	/**
	 * Option Key
	 *
	 * @var  string
	 */
	private $stored_option_key = 'site_move_monitor_stored';

	/**
	 * Constructor
	 */
	public function __construct() {

	}

	/**
	 * Check if the site has been moved or configuration changed.
	 *
	 * @return  boolean
	 */
	public function test_moved() {

		$tests = array_keys( $this->get_default_data() );

		foreach ( $tests as $test ) {
			if ( $this->test_changed( $test ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Check if a configuration has changed.
	 *
	 * @param   string   $key  Test key.
	 * @return  boolean
	 */
	private function test_changed( $key ) {

		return $this->get_stored( $key ) != $this->get_current( $key );

	}

	/**
	 * Get changed data.
	 *
	 * @return  array  Chnaged test data.
	 */
	public function get_changed_data() {

		$changed = array();
		$data = $this->get_tests();

		foreach ( $data as $key => $value ) {
			if ( $this->test_changed( $key ) ) {
				$changed[ $key ] = $value;
			}
		}

		return $changed;

	}

	/**
	 * Get stored test data.
	 *
	 * @param   string  $key  Test key.
	 * @return  string        Stored test value.
	 */
	private function get_stored( $key ) {

		$stored = $this->get_stored_data();

		if ( isset( $stored[ $key ] ) ) {
			return $stored[ $key ];
		}

		return '';

	}

	/**
	 * Is any data currently stored?
	 *
	 * @return  boolean
	 */
	public function is_stored() {

		$data =  $this->get_stored_data_raw();

		return ! empty( $data );

	}

	/**
	 * Get current test value.
	 *
	 * @param   string  $key  Test key.
	 * @return  string        Current test value.
	 */
	private function get_current( $key ) {

		$current = $this->get_current_data();

		if ( isset( $current[ $key ] ) ) {
			return $current[ $key ];
		}

		return '';

	}

	/**
	 * Get current test data.
	 *
	 * @return  array  Current test data.
	 */
	public function get_current_data() {

		global $table_prefix;

		return array(
			'ip'        => $_SERVER['SERVER_ADDR'],
			'path'      => ABSPATH,
			'url'       => home_url( '/' ),
			'db'        => DB_NAME,
			'db_host'   => DB_HOST,
			'db_prefix' => $table_prefix
		);

	}

	/**
	 * Get default test data.
	 *
	 * @return  array  Default test data.
	 */
	private function get_default_data() {

		$tests = array_keys( $this->get_tests() );

		return array_fill_keys( $tests, '' );

	}

	/**
	 * Get tests.
	 *
	 * @return  array  Tests.
	 */
	public function get_tests() {

		return array(
			'ip'        => __( 'IP Address', 'site-move-monitor' ),
			'path'      => __( 'File Path', 'site-move-monitor' ),
			'url'       => __( 'URL', 'site-move-monitor' ),
			'db'        => __( 'Database', 'site-move-monitor' ),
			'db_host'   => __( 'Database Host', 'site-move-monitor' ),
			'db_prefix' => __( 'Database Table Prefix', 'site-move-monitor' )
		);

	}

	/**
	 * Get stored data.
	 *
	 * Any tests that have not been stored will be filled
	 * with blank values.
	 *
	 * @return  array  Stored test data.
	 */
	public function get_stored_data() {

		return wp_parse_args( $this->get_stored_data_raw(), $this->get_default_data() );

	}

	/**
	 * Get raw stored data.
	 *
	 * @return  array  Stored test data.
	 */
	private function get_stored_data_raw() {

		return get_option( $this->stored_option_key );

	}

	/**
	 * Update stored test data with current data.
	 */
	public function update_stored_data() {

		update_option( $this->stored_option_key, $this->get_current_data() );

	}

	/**
	 * Reset (delete) stored data.
	 */
	public function reset_stored_data() {

		delete_option( $this->stored_option_key );

	}

}
