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
		 * Returns information about plugin.
		 *
		 * @param  string $plugin Plugin slug.
		 * @return array
		 */
		public function get_plugin_data( $plugin = '' ) {
			$plugins = tm_wizard_settings()->get( array( 'plugins' ) );

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

			$skins = tm_wizard_settings()->get( array( 'skins' ) );
			$base  = ! empty( $skins['base'] ) ? $skins['base'] : array();
			$lite  = ! empty( $skins['advanced'][ $skin ]['lite'] ) ? $skins['advanced'][ $skin ]['lite'] : array();
			$full  = ! empty( $skins['advanced'][ $skin ]['full'] ) ? $skins['advanced'][ $skin ]['full'] : array();

			return array(
				'lite' => array_merge( $base, $lite ),
				'full' => array_merge( $base, $full ),
			);

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
		public function get_first_plugin_data( $skin, $type ) {

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
