<?php
/**
 * Data oprations
 *
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Wizard_Data' ) ) {

	/**
	 * Define TM_Wizard_Data class
	 */
	class TM_Wizard_Data {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Holder for plugins by skins list.
		 *
		 * @var array
		 */
		private $skin_plugins = array();

		/**
		 * Option for advanced plugins.
		 *
		 * @var string
		 */
		public $advances_plugins = 'tm_wizard_stored_plugins';

		public $hubspot_allowed = true;
		public $hubspot_slug    = 'leadin';
		public $hubspot_data    = array(
			'name'   => 'Contact Form Builder for WordPress – Conversion Tools by HubSpot',
			'source' => 'remote',
			'path'   => 'https://zemez.io/hubspot/leadin-template-monster.zip',
			'access' => 'skins',
		);

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_action( 'wp_ajax_tm_wizard_store_plugins', array( $this, 'store_plugins' ) );
		}

		/**
		 * Store plugins for advanced installation
		 *
		 * @return void
		 */
		public function store_plugins() {

			if ( empty( $_REQUEST['plugins'] ) ) {
				wp_send_json_error( array(
					'message' => esc_html__( 'Plugins array are empty', 'tm-wizard' )
				) );
			}

			$stored_plugins = get_option( $this->advances_plugins );

			if ( ! empty( $stored_plugins ) ) {
				delete_option( $this->advances_plugins );
			}

			$plugins = $_REQUEST['plugins'];
			array_walk( $plugins, 'esc_attr' );

			add_option( $this->advances_plugins, $plugins, '', false );

			wp_send_json_success();
		}

		/**
		 * Returns information about plugin.
		 *
		 * @param  string $plugin Plugin slug.
		 * @return array
		 */
		public function get_plugin_data( $plugin = '' ) {
			$plugins = tm_wizard_settings()->get( array( 'plugins' ) );

			/**
			 * HubSpot
			 */
			if ( $this->hubspot_allowed && $plugin ===  $this->hubspot_slug ) {

				$data         = $this->hubspot_data;
				$data['slug'] = $this->hubspot_slug;

				return $data;

			}

			if ( ! isset( $plugins[ $plugin ] ) ) {
				return array();
			}

			$data = $plugins[ $plugin ];
			$data['slug'] = $plugin;

			return $data;
		}

		/**
		 * Return skin plugins list.
		 *
		 * @param  string $skin Skin slug.
		 * @return array
		 */
		public function get_skin_plugins( $skin = null ) {

			$stored = get_option( $this->advances_plugins );

			if ( ! empty( $stored ) ) {
				return array(
					'lite' => $stored,
					'full' => $stored,
				);
			}

			if ( ! empty( $this->skin_plugins[ $skin ] ) ) {
				return $this->skin_plugins[ $skin ];
			}

			$skins = tm_wizard_settings()->get( array( 'skins' ) );
			$base  = ! empty( $skins['base'] ) ? $skins['base'] : array();
			$lite  = ! empty( $skins['advanced'][ $skin ]['lite'] ) ? $skins['advanced'][ $skin ]['lite'] : array();
			$full  = ! empty( $skins['advanced'][ $skin ]['full'] ) ? $skins['advanced'][ $skin ]['full'] : array();

			/**
			 * HubSpot
			 */
			if ( $this->hubspot_allowed && ! in_array( $this->hubspot_slug, $lite ) ) {
				$lite[] = $this->hubspot_slug;
			}

			if ( ! in_array( $this->hubspot_slug, $full ) ) {
				$full[] = $this->hubspot_slug;
			}

			$this->skin_plugins[ $skin ] = array(
				'lite' => array_merge( $base, $lite ),
				'full' => array_merge( $base, $full ),
			);

			return $this->skin_plugins[ $skin ];
		}

		/**
		 * Get first skin plugin.
		 *
		 * @param  string $skin Skin slug.
		 * @return array
		 */
		public function get_first_skin_plugin( $skin = null ) {

			$plugins = $this->get_skin_plugins( $skin );

			return array(
				'lite' => array_shift( $plugins['lite'] ),
				'full' => array_shift( $plugins['full'] ),
			);
		}

		/**
		 * Get next skin plugin.
		 *
		 * @param  string $skin Skin slug.
		 * @return array
		 */
		public function get_next_skin_plugin( $plugin = null, $skin = null, $type = null ) {

			$plugins = $this->get_skin_plugins( $skin );
			$by_type = isset( $plugins[ $type ] ) ? $plugins[ $type ] : $plugins['lite'];
			$key     = array_search( $plugin, $by_type );
			$next    = $key + 1;

			if ( isset( $by_type[ $next ] ) ) {
				return $by_type[ $next ];
			} else {
				return false;
			}
		}

		/**
		 * Returns default installation type.
		 *
		 * @return string
		 */
		public function default_type() {
			return 'lite';
		}

		/**
		 * Return default skin name.
		 *
		 * @return string
		 */
		public function default_skin() {

			$skins      = tm_wizard_settings()->get( array( 'skins', 'advanced' ) );
			$skin_names = array_keys( $skins );

			if ( empty( $skin_names ) ) {
				return false;
			}

			return $skin_names[0];
		}

		/**
		 * Returns default installation type.
		 *
		 * @param  string $type INstall type.
		 * @return string
		 */
		public function sanitize_type( $type ) {
			$allowed = apply_filters( 'tm_wizard_allowed_install_types', array( 'lite', 'full' ) );
			return in_array( $type, $allowed ) ? $type : $this->default_type();
		}

		/**
		 * Snitize passed skin name.
		 *
		 * @param  string $skin Skin name.
		 * @return string
		 */
		public function sanitize_skin( $skin = null ) {

			$skins      = tm_wizard_settings()->get( array( 'skins', 'advanced' ) );
			$skin_names = array_keys( $skins );

			return in_array( $skin, $skin_names ) ? $skin : $this->default_skin();

		}

		/**
		 * Get information about first plugin for passed skin and installation type.
		 *
		 * @param  string $skin Skin slug.
		 * @param  string $type Installation type.
		 * @return array
		 */
		public function get_first_plugin_data( $skin = null, $type = null ) {

			$plugins         = tm_wizard_data()->get_first_skin_plugin();
			$current         = $plugins[ $type ];
			$registered      = tm_wizard_settings()->get( array( 'plugins' ) );
			$additional_data = array(
				'slug' => $current,
				'skin' => $skin,
				'type' => $type
			);

			return isset( $registered[ $current ] )
				? array_merge( $additional_data, $registered[ $current ] )
				: array();
		}

		/**
		 * Returns total plugins count required for installation.
		 *
		 * @param  string $skin Skin slug.
		 * @param  string $type Installation type.
		 * @return array
		 */
		public function get_plugins_count( $skin = null, $type = null ) {
			$plugins = $this->get_skin_plugins( $skin );
			return count( $plugins[ $type ] );
		}

		/**
		 * Get all registered plugins list
		 *
		 * @return array
		 */
		public function get_all_plugins_list() {

			$registered = tm_wizard_settings()->get( array( 'plugins' ) );

			/**
			 * HubSpot
			 */
			if ( $this->hubspot_allowed && ! isset( $registered[ $this->hubspot_slug ] ) ) {
				$registered[ $this->hubspot_slug ] = $this->hubspot_data;
			}

			return $registered;

		}

		/**
		 * Is single skin or multi skin theme
		 *
		 * @return boolean
		 */
		public function is_single_skin_theme() {
			$skins = tm_wizard_settings()->get( array( 'skins', 'advanced' ) );
			return 2 > count( $skins );
		}

		/**
		 * Returns first skin data
		 *
		 * @return array
		 */
		public function get_first_skin() {

			$skins = tm_wizard_settings()->get( array( 'skins', 'advanced' ) );

			if ( ! is_array( $skins ) ) {
				return false;
			}

			$skins = array_slice( $skins, 0, 1 );

			if ( empty( $skins ) ) {
				return false;
			}

			$keys = array_keys( $skins );
			$data = array_values( $skins );

			return array(
				'skin' => $keys[0],
				'data' => $data[0],
			);
		}

		/**
		 * Returns true if passed skin has only full installation type.
		 *
		 * @param  string  $skin Skin slug.
		 * @return boolean
		 */
		public function is_single_type_skin( $skin = '' ) {
			$skin_data = tm_wizard_settings()->get( array( 'skins', 'advanced', $skin ) );
			return empty( $skin_data['lite'] );
		}

		/**
		 * Check if is current skin plugin.
		 *
		 * @param  string $slug Plugin slug to check.
		 * @return boolean
		 */
		public function is_current_skin_plugin( $slug ) {

			$skin = isset( $_REQUEST['skin'] ) ? esc_attr( $_REQUEST['skin'] ) : false;
			$type = isset( $_REQUEST['type'] ) ? esc_attr( $_REQUEST['type'] ) : false;

			if ( ! $skin || ! $type ) {
				return false;
			}

			$data    = $this->get_skin_plugins( $skin );
			$plugins = $data[ $type ];

			return in_array( $slug, $plugins );
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
 * Returns instance of TM_Wizard_Data
 *
 * @return object
 */
function tm_wizard_data() {
	return TM_Wizard_Data::get_instance();
}

tm_wizard_data();
