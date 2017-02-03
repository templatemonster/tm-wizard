<?php
/**
 * Extensions
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Wizard_Extensions' ) ) {

	/**
	 * Define TM_Wizard_Extensions class
	 */
	class TM_Wizard_Extensions {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class.
		 */
		public function __construct() {

			add_action( 'tm_wizard_after_plugin_activation', array( $this, 'prevent_bp_redirect' ) );
			add_action( 'tm_wizard_after_plugin_activation', array( $this, 'prevent_bbp_redirect' ) );
			add_action( 'tm_wizard_after_plugin_activation', array( $this, 'prevent_booked_redirect' ) );
			add_action( 'tm_wizard_after_plugin_activation', array( $this, 'prevent_tribe_redirect' ) );
			add_action( 'tm_wizard_after_plugin_activation', array( $this, 'prevent_woo_redirect' ) );

			add_filter( 'tm_wizard_send_install_data', array( $this, 'add_multi_arg' ), 10, 2 );
		}

		/**
		 * Prevent redirect after WooCommerce activation.
		 *
		 * @param  string $plugin Plugin slug.
		 * @return bool
		 */
		public function prevent_woo_redirect( $plugin ) {

			if ( 'woocommerce' !== $plugin['slug'] ) {
				return false;
			}

			delete_transient( '_wc_activation_redirect' );

			return true;
		}

		/**
		 * Prevent BuddyPress redirect.
		 *
		 * @return bool
		 */
		public function prevent_bp_redirect( $plugin ) {

			if ( 'buddypress' !== $plugin['slug'] ) {
				return false;
			}

			delete_transient( '_bp_activation_redirect' );
			delete_transient( '_bp_is_new_install' );

			return true;
		}

		/**
		 * Prevent BBPress redirect.
		 *
		 * @return bool
		 */
		public function prevent_bbp_redirect( $plugin ) {

			if ( 'bbpress' !== $plugin['slug'] ) {
				return false;
			}

			delete_transient( '_bbp_activation_redirect' );

			return true;
		}

		/**
		 * Prevent booked redirect.
		 *
		 * @return bool
		 */
		public function prevent_booked_redirect( $plugin ) {

			if ( 'booked' !== $plugin['slug'] ) {
				return false;
			}

			delete_transient( '_booked_welcome_screen_activation_redirect' );

			return true;
		}

		/**
		 * Prevent tribe events calendar redirect.
		 *
		 * @return bool
		 */
		public function prevent_tribe_redirect( $plugin ) {

			if ( 'the-events-calendar' !== $plugin['slug'] ) {
				return false;
			}

			delete_transient( '_tribe_tickets_activation_redirect' );
			delete_transient( '_tribe_events_activation_redirect' );

			return true;
		}

		/**
		 * Add multi-install argument.
		 *
		 * @param  array  $data   Send data.
		 * @param  string $plugin Plugin slug.
		 * @return array
		 */
		public function add_multi_arg( $data = array(), $plugin = '' ) {

			if ( in_array( $plugin, array( 'woocommerce', 'booked' ) ) ) {
				$data['activate-multi'] = true;
			}

			return $data;
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
 * Returns instance of TM_Wizard_Extensions
 *
 * @return object
 */
function tm_wizard_ext() {
	return TM_Wizard_Extensions::get_instance();
}

tm_wizard_ext();
