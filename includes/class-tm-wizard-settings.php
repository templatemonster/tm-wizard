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

if ( ! class_exists( 'TM_Wizard_Settings' ) ) {

	/**
	 * Define TM_Wizard_Settings class
	 */
	class TM_Wizard_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Manifest file content
		 *
		 * @var array
		 */
		private $manifest = null;

		/**
		 * Get settings from array.
		 *
		 * @param  array  $settings Settings trail to get.
		 * @return mixed
		 */
		public function get( $settings = array() ) {

			$manifest = $this->get_manifest();

			if ( ! $manifest ) {
				return false;
			}

			if ( ! is_array( $settings ) ) {
				$settings = array( $settings );
			}

			$count  = count( $settings );
			$result = $manifest;

			for ( $i = 0; $i < $count; $i++ ) {

				if ( empty( $result[ $settings[ $i ] ] ) ) {
					return false;
				}

				$result = $result[ $settings[ $i ] ];

				if ( $count - 1 === $i ) {
					return $result;
				}

			}

		}

		/**
		 * Get mainfest
		 *
		 * @return mixed
		 */
		public function get_manifest() {

			if ( null !== $this->manifest ) {
				return $this->manifest;
			}

			$file = locate_template( array( 'tm-wizard-manifest.php' ) );

			if ( ! $file ) {
				return false;
			}

			include $file;

			$this->manifest = array(
				'plugins' => isset( $plugins ) ? $plugins : false,
				'skins'   => isset( $skins )   ? $skins   : false,
			);

			return $this->manifest;
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
 * Returns instance of TM_Wizard_Settings
 *
 * @return object
 */
function tm_wizard_settings() {
	return TM_Wizard_Settings::get_instance();
}
