<?php

/**
 * Site Move Monitor Admin
 *
 * Please note that any public methods that are marked as @access 'private'
 * should be considered private. They are only public so they can be hooked
 * into via the plugin's action and filter hooks.
 *
 * This class is solely to manage admin functionality for the plugin.
 * It should not be instantiated or used by anything external to this plugin.
 * If you do the sky will fall down :)
 *
 * @package     Site Move Monitor
 * @subpackage  Admin
 */

class Site_Move_Monitor_Admin {

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
	 * Current configuration updated.
	 *
	 * @var  boolean
	 */
	private $current_configuration_updated = false;

	/**
	 * Construct
	 *
	 * @param  Site_Move_Monitor  $plugin  Plugin instance.
	 */
	public function __construct( Site_Move_Monitor $plugin, Site_Move_Monitor_Checker $checker ) {

		// Dependencies
		$this->plugin = $plugin;
		$this->checker = $checker;

		// Setup hooks
		add_action( 'admin_init', array( $this, 'update_current_configuration' ) );
		add_action( 'admin_menu', array( $this, 'create_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );

	}

	/**
	 * Create admin menu.
	 *
	 * @access  private
	 */
	public function create_menu() {

		add_management_page( __( 'Site Move Monitor', 'site-move-monitor' ), __( 'Site Move Monitor', 'site-move-monitor' ), $this->plugin->get_admin_capability(), 'site_move_monitor', array( $this, 'options_page' ) );

	}

	/**
	 * Site Move Monitor Admin Notice
	 *
	 * @access  private
	 */
	public function admin_notice() {

		if ( function_exists( 'get_current_screen' ) ) {

			$screen = get_current_screen();

			if ( 'tools_page_site_move_monitor' == $screen->id ) {

				if ( $this->current_configuration_updated ) {
					?>
					<div class="updated fade">
						<?php printf( '<p>%s</p>', esc_html__( 'Your current site configuration has been saved.', 'site-move-monitor' ) ); ?>
					</div>
					<?php
				}

			}

		}

	}

	/**
	 * Update current configuration.
	 *
	 * @access  private
	 */
	public function update_current_configuration() {

		if ( isset( $_POST['site_move_monitor_current_configuration'] ) && wp_verify_nonce( $_POST['site_move_monitor_current_configuration'], 'update' ) ) {

			$this->checker->update_stored_data();

			$this->current_configuration_updated = true;

			do_action( 'site_move_monitor_configuration_updated' );

		}

	}

	/**
	 * Admin Options Page
	 *
	 * @access  private
	 */
	public function options_page() {

		$tests = $this->checker->get_tests();
		$current = $this->checker->get_current_data();
		$stored = $this->checker->get_stored_data();

		?>
		<div class="wrap">

			<h2><?php esc_html_e( 'Site Move Monitor', 'site-move-monitor' ); ?></h2>

			<p><?php esc_html_e( 'Site Move Monitor checks your site hosting and configuration and monitors for changes in the setup that may indicate that your site has moved. This can help alert you to a change in environment. For example moving a site from a staging to a production environment.', 'site-move-monitor' ); ?></p>

			<h2><?php esc_html_e( 'Current Configuration', 'site-move-monitor' ); ?></h2>

			<form method="POST" class="site-move-monitor-current-configuration">

				<table>
					<?php foreach ( $current as $test => $value ) { ?>
						<tr>
							<th style="text-align: left; padding-right: 20px;"><?php echo $tests[ $test ]; ?></th>
							<td>
								<?php

								if ( empty( $stored[ $test ] ) || $stored[ $test ] == $value ) {
									echo $value;
								} else {
									printf( '<del style="color: #c00;">%s</del> &rarr; %s', $stored[ $test ], $value );
								}

								?>
							</td>
						</tr>
					<?php } ?>
				</table>

				<?php if ( $this->checker->test_moved() ) { ?>
					<p><input type="submit" value="<?php esc_attr_e( 'Update Current Configuration', 'site-move-monitor' ); ?>" class="button button-primary" /></p>
				<?php } else { ?>
					<p><input type="button" value="<?php esc_attr_e( 'Configuration is up-to-date', 'site-move-monitor' ); ?>" class="button" disabled="disabled" /></p>
				<?php } ?>

				<?php wp_nonce_field( 'update', 'site_move_monitor_current_configuration' ); ?>

			</form>

		</div>
		<?php

	}

}
