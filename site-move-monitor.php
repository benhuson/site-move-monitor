<?php

/*

Plugin Name: Site Move Monitor
Plugin URI: https://github.com/benhuson/site-move-monitor
Description: Monitors whether a site has been moved and shows an alert in the admin bar.
Author: Ben Huson
Author URI: https://github.com/benhuson/
Version: 0.1
Tested up to: 4.3.1
Minimum WordPress Version Required: 3.9

Released under the GPL:
http://www.opensource.org/licenses/gpl-license.php

*/

/**
 * Site Monitor Plugin
 *
 * Manages base plugin functionality such as install,
 * uninstall and plugin variables.
 *
 * This class is managed as a singleton - there can only be one instance
 * of this class which can be accessed using Site_Move_Monitor::get_instance().
 *
 * Please note that any public methods that are marked as @access 'private'
 * should be considered private. They are only public so they can be hooked
 * into via the plugin's action and filter hooks.
 * 
 * @package  Site Move Monitor
 */

class Site_Move_Monitor {

	/**
	 * Version
	 *
	 * @var  string
	 */
	private static $version = '0.1';

	/**
	 * Database Version
	 *
	 * @var  string
	 */
	private static $db_version = '0.1';

	/**
	 * Instance
	 *
	 * @var  Site_Move_Monitor|boolean
	 */
	private static $class = false;

	/**
	 * Admin Instance
	 *
	 * @var  Site_Move_Monitor_Admin|boolean
	 */
	private $admin = false;

	/**
	 * Admin Bar Instance
	 *
	 * @var  Site_Move_Monitor_Admin_Bar|boolean
	 */
	private $admin_bar = false;

	/**
	 * Private Constructor
	 *
	 * This class operates as a singleton.
	 * To get an instance call Site_Move_Monitor::get_instance()
	 */
	private function __construct() {

		add_action( 'plugins_loaded', array( $this, 'loaded' ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );

		// Global includes
		require_once( $this->dir_path() . 'includes/checker.php' );

	}

	/**
	 * Get Instance.
	 *
	 * @return  Site_Move_Monitor  Instance.
	 */
	public static function get_instance() {

		if ( self::$class == false ) {
			self::$class = new Site_Move_Monitor;
		} else {
			return self::$class;
		}

	}

	/**
	 * Plugin Loaded
	 *
	 * @access  private
	 */
	public function loaded() {

		load_plugin_textdomain( 'site-move-monitor', false, $this->dirname() . '/languages/' );

		$checker = new Site_Move_Monitor_Checker();

		// Admin Only
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			require_once( $this->dir_path() . 'includes/admin.php' );
			$this->admin = new Site_Move_Monitor_Admin( $this, $checker );
		}

		// Admin bar for logged in users
		if ( is_user_logged_in() ) {
			require_once( $this->dir_path() . 'includes/admin-bar.php' );
			$this->admin_bar = new Site_Move_Monitor_Admin_Bar( $this, $checker );
		}

	}

	/**
	 * Version
	 *
	 * @return  string  Plugin version.
	 */
	public function version() {

		return $this->version;

	}

	/**
	 * Database Version
	 *
	 * @return  string  Plugin database version.
	 */
	public function db_version() {

		return $this->db_version;

	}

	/**
	 * Basename
	 *
	 * @return  string  Plugin basename.
	 */
	public function basename() {

		return plugin_basename( __FILE__ );

	}

	/**
	 * Dirname
	 *
	 * @return  string  Plugin dir name.
	 */
	public function dirname() {

		return dirname( $this->basename() );

	}

	/**
	 * Dir Path
	 *
	 * @return  string  Plugin dir path.
	 */
	public function dir_path() {

		return plugin_dir_path( __FILE__ );

	}

	/**
	 * Dir URL
	 *
	 * @return  string  Plugin URL path.
	 */
	public function dir_url() {

		return plugins_url( '/', __FILE__ );

	}

	/**
	 * Install
	 *
	 * @access  private
	 */
	public function install() {

		$checker = new Site_Move_Monitor_Checker();

		if ( ! $checker->is_stored() ) {
			$checker->update_stored_data();
		}

	}

	/**
	 * Uninstall
	 *
	 * @access  private
	 */
	public function uninstall() {

		// Exit if uninstall not called from WordPress.
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			exit();
		}

		$checker = new Site_Move_Monitor_Checker();
		$checker->reset_stored_data();

	}

	/**
	 * Get Admin Capability
	 *
	 * Returns the capability required to view and manage Site Move Monitor
	 * in the admin. By default only administrators with the 'manage_options'
	 * capability are allowed.
	 *
	 * @return  string  Filter capability.
	 */
	public function get_admin_capability() {

		return apply_filters( 'site_move_monitor_admin_capability', 'manage_options' );

	}

}

Site_Move_Monitor::get_instance();
