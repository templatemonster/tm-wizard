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
		 * Constructor for the class
		 */
		function __construct() {

			add_action( 'wp_ajax_tm_wizard_install_plugin', array( $this, 'install_plugin' ) );
			//add_action( 'admin_init', array( $this, 'test' ) );

		}

		/**
		 * AJAX-callback for plugin install
		 *
		 * @return void
		 */
		public function install_plugin() {

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
				wp_send_json_success( array(
					'isLast'   => true,
					'redirect' => apply_filters( 'tm_wizards_install_finish_redirect' ),
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
				)
			);

			wp_send_json_success( $data );

		}

		/**
		 * Process plugin installation.
		 *
		 * @param  array $plugin Plugin slug.
		 * @return bool
		 */
		public function do_plugin_install( $plugin = null ) {

			$this->dependencies();

			$installer = new TM_Wizard_Plugin_Upgrader(
				new Plugin_Installer_Skin(
					array(
						'url'    => false,
						'plugin' => $plugin['slug'],
						'source' => $plugin['source'],
					)
				)
			);

			var_dump( $installer->skin );

		}

		public function test() {
			$this->do_plugin_install( tm_wizard_data()->get_plugin_data( 'cherry-services-list' ) );
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
