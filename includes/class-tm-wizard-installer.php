<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Wizard_Installer' ) ) {

	/**
	 * Define TM_Wizard_Installer class
	 */
	class TM_Wizard_Installer {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Installer storage
		 *
		 * @var object
		 */
		public $installer = null;

		/**
		 * Is wizard page trigger
		 *
		 * @var boolean
		 */
		private $is_wizard = false;

		/**
		 * Installation log.
		 *
		 * @var null
		 */
		private $log = null;

		/**
		 * Constructor for the class
		 */
		function __construct() {
			add_action( 'wp_ajax_tm_wizard_install_plugin', array( $this, 'install_plugin' ) );
		}

		/**
		 * Check if currently processing wizard request.
		 *
		 * @return bool
		 */
		public function is_wizard_request() {
			return $this->is_wizard;
		}

		/**
		 * AJAX-callback for plugin install
		 *
		 * @return void
		 */
		public function install_plugin() {

			$this->is_wizard = true;

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error(
					array( 'message' => esc_html__( 'You don\'t have permissions to do this', 'tm-wizard' ) )
				);
			}

			$plugin = ! empty( $_GET['plugin'] ) ? esc_attr( $_GET['plugin'] ) : false;
			$skin   = ! empty( $_GET['skin'] ) ? esc_attr( $_GET['skin'] ) : false;
			$type   = ! empty( $_GET['type'] ) ? esc_attr( $_GET['type'] ) : false;

			if ( ! $plugin || ! $skin || ! $type ) {
				wp_send_json_error(
					array( 'message' => esc_html__( 'No plugin to install', 'tm-wizard' ) )
				);
			}

			$this->do_plugin_install( tm_wizard_data()->get_plugin_data( $plugin ) );

			$next = tm_wizard_data()->get_next_skin_plugin( $plugin, $skin, $type );

			if ( ! $next ) {

				$message = esc_html__( 'All plugins are installed. Redirecting to the next step...', 'tm-wizard' );

				wp_send_json_success( array(
					'isLast'   => true,
					'message'  => sprintf( '<div class="tm-wizard-installed">%s</div>', $message ),
					'redirect' => apply_filters( 'tm_wizards_install_finish_redirect', null ),
					'log'      => $this->log,
				) );
			}

			$registered = tm_wizard_settings()->get( array( 'plugins' ) );

			if ( ! isset( $registered[ $next ] ) ) {
				wp_send_json_error(
					array( 'message' => esc_html__( 'This plugin is not registered', 'tm-wizard' ) )
				);
			}

			$data = array_merge(
				$registered[ $next ],
				array(
					'isLast' => false,
					'skin'   => $skin,
					'type'   => $type,
					'slug'   => $next,
					'log'    => $this->log,
				)
			);

			wp_send_json_success( $data );

		}

		/**
		 * Process plugin installation.
		 *
		 * @param  array $plugin Plugin data.
		 * @return bool
		 */
		public function do_plugin_install( $plugin = array() ) {

			$this->log = null;
			ob_start();

			$this->dependencies();

			$source          = $this->locate_source( $plugin );
			$this->installer = new TM_Wizard_Plugin_Upgrader(
				new TM_Wizard_Plugin_Upgrader_Skin(
					array(
						'url'    => false,
						'plugin' => $plugin['slug'],
						'source' => $plugin['source'],
						'title'  => $plugin['name'],
					)
				)
			);

			$installed       = $this->installer->install( $source );
			$this->log       = ob_get_clean();
			$plugin_activate = $this->installer->plugin_info();
			$activate        = activate_plugin( $plugin_activate );

			return $installed;
		}

		public function test() {
			$this->do_plugin_install( tm_wizard_data()->get_plugin_data( 'cherry-services-list' ) );
			die();
		}

		/**
		 * Returns plugin installation source URL.
		 *
		 * @param  array  $plugin Plugin data.
		 * @return string
		 */
		public function locate_source( $plugin = array() ) {

			$source = isset( $plugin['source'] ) ? $plugin['source'] : 'wordpress';
			$result = false;

			switch ( $source ) {
				case 'wordpress':

					require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api

					$api = plugins_api(
						'plugin_information',
						array( 'slug' => $plugin['slug'], 'fields' => array( 'sections' => false ) )
					);

					if ( is_wp_error( $api ) ) {
						wp_die( $this->installer->strings['oops'] . var_dump( $api ) );
					}

					if ( isset( $api->download_link ) ) {
						$result = $api->download_link;
					}

					break;

				case 'local':
					$result = ! empty( $plugin['path'] ) ? $plugin['path'] : false;
					break;

				case 'remote':
					$result = ! empty( $plugin['path'] ) ? esc_url( $plugin['path'] ) : false;
					break;
			}

			return $result;
		}

		/**
		 * Include dependencies.
		 *
		 * @return void
		 */
		public function dependencies() {

			if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			require_once tm_wizard()->path( 'includes/class-tm-wizard-plugin-upgrader-skin.php' );
			require_once tm_wizard()->path( 'includes/class-tm-wizard-plugin-upgrader.php' );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of TM_Wizard_Installer
 *
 * @return object
 */
function tm_wizard_installer() {
	return TM_Wizard_Installer::get_instance();
}

tm_wizard_installer();
