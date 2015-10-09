<?php

/**
 * Admin Bar
 *
 * Manages the display of a Site Move Monitor menu in the WordPress
 * admin bar for logged in users. This class must be instantiated
 * with instances of the Site_Move_Monitor and Site_Move_Monitor_Checker.
 *
 * Please note that any public methods that are marked as @access 'private'
 * should be considered private. There are only public so they can be hooked
 * into via the plugin's action and filter hooks.
 *
 * This class is solely to manage the admin bar functionality for the plugin.
 * It should not be instantiated or used by anything external to this plugin.
 * If you do the sky will fall down :)
 *
 * @package     Site Move Monitor
 * @subpackage  Admin Bar
 */

class Site_Move_Monitor_Admin_Bar {

	/**
	 * Plugin Instance
	 *
	 * @var  Site_Move_Monitor|boolean
	 */
	private $plugin = false;

	/**
	 * Checker Instance
	 *
	 * @var  Site_Move_Monitor_Checker|boolean
	 */
	private $checker = false;

	/**
	 * Construct
	 *
	 * @param  Site_Move_Monitor          $plugin   Plugin instance.
	 * @param  Site_Move_Monitor_Checker  $checker  Checker instance.
	 */
	public function __construct( Site_Move_Monitor $plugin, Site_Move_Monitor_Checker $checker ) {

		// Dependencies
		$this->plugin = $plugin;
		$this->checker = $checker;

		// Setup hooks
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 999 );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_alerts_submenu' ), 0 );
		add_action( 'admin_menu', array( $this, 'print_admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

	}

	/**
	 * Add Admin Bar Menu
	 *
	 * Called with priority 0 to show at the top of
	 * the Site Move Monitor admin bar menu.
	 *
	 * If the site has moved a class is added so that the menu item
	 * and the alerts group submenu items can be highlighted.
	 *
	 * @access  private
	 */
	public function add_admin_bar_menu( $wp_admin_bar ) {

		// Only add menu if user has capability
		if ( ! current_user_can( $this->plugin->get_admin_capability() ) ) {
			return;
		}

		// Menu item classes
		$classes = array( 'site-move-monitor' );
		if ( $this->checker->test_moved() ) {
			$classes[] = 'site-move-monitor-moved';
		}
		if ( apply_filters( 'site_move_monitor_admin_bar_menu_icon_only', false ) ) {
			$classes[] = 'site-move-monitor-icon-only';
		}
		

		// Add main menu item
		$wp_admin_bar->add_menu( array(
			'id'    => 'site_move_monitor',
			'title' => sprintf( '<span class="ab-icon"></span><span class="ab-icon-text">%s</span>', __( 'Site Move Monitor', 'site-move-monitor' ) ),
			'href'  => admin_url( 'tools.php?page=site_move_monitor' ),
			'meta'  => array(
				'class' => implode( ' ', $classes )
			)
		) );

	}

	/**
	 * Add Admin Bar Alerts Submenu
	 *
	 * Adds an 'site_move_monitor_alerts' group to the Site Move Monitor
	 * admin bar menu. Only alerts should be displayed in this group.
	 * This group will only show if site move is detected.
	 *
	 * Called with priority 0 to show at the top of
	 * the Site Move Monitor admin bar menu.
	 *
	 * @access  private
	 *
	 * @param  WP_Admin_Bar  $wp_admin_bar  Reference to the admin bar instance.
	 */
	public function add_admin_bar_alerts_submenu( $wp_admin_bar ) {

		// If site moved...
		if ( $this->checker->test_moved() ) {

			// Add alerts group
			$wp_admin_bar->add_group( array(
				'id'     => 'site_move_monitor_alerts',
				'parent' => 'site_move_monitor',
				'meta'   => array( 'class' => 'site-move-monitor-alerts' )
			) );

			$this->add_admin_bar_alerts( $wp_admin_bar );

		}

	}

	/**
	 * Add Admin Bar Alerts
	 *
	 * @param  WP_Admin_Bar  $wp_admin_bar  Reference to the admin bar instance.
	 */
	private function add_admin_bar_alerts( $wp_admin_bar ) {

		$changed = $this->checker->get_changed_data();

		foreach ( $changed as $test => $value ) {

			$args = array(
				'id'     => 'site_move_monitor_alert_' . sanitize_key( $test ),
				'title'  => sprintf( __( '%s Changed', 'site-move-monitor' ), $value ),
				'parent' => 'site_move_monitor_alerts',
				'href'   => admin_url( 'tools.php?page=site_move_monitor' ),
			);
			$wp_admin_bar->add_node( $args );

		}

	}

	/**
	 * Print Admin Styles
	 *
	 * @access  private
	 */
	public function print_admin_styles() {

		if ( current_user_can( $this->plugin->get_admin_capability() ) ) {
			add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
		}

	}

	/**
	 * Enqueue Styles
	 *
	 * @access  private
	 */
	public function enqueue_styles() {

		if ( ( is_admin() || is_user_logged_in() ) && current_user_can( $this->plugin->get_admin_capability() ) ) {
			wp_enqueue_style( 'site-move-monitor-admin-bar', $this->plugin->dir_url() . 'css/admin-bar.css', array(), $this->plugin->version() );
		}

	}

}
