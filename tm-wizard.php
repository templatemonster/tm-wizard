<?php
/*
Plugin Name: TM Wizard
Plugin URI: http://www.templatemonster.com/wordpress-themes.php
Description: Plugins installation wizard.
Author: TemplateMonster
Author URI: http://www.templatemonster.com/wordpress-themes.php
Version: 1.0.0
Text Domain: tm-wizard
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * Main importer plugin class
 *
 * @package   TM_Wizard
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'TM_Wizard' ) ) {

	/**
	 * Define TM_Wizard class
	 */
	class TM_Wizard {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Plugin base url
		 *
		 * @var string
		 */
		private $url = null;

		/**
		 * Plugin base path
		 *
		 * @var string
		 */
		private $path = null;

		/**
		 * Menu page slug.
		 * @var string
		 */
		private $slug = 'tm-wizard';

		/**
		 * Dispalying tab data
		 * @var array
		 */
		public $current_tab = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			if ( ! is_admin() ) {
				return;
			}

			add_action( 'plugins_loaded',        array( $this, 'lang' ), 2 );
			add_action( 'init',                  array( $this, 'init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 10 );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		public function lang() {
			load_plugin_textdomain( 'tm-wizard', false, $this->path( 'languages' ) );
		}

		/**
		 * Init plugin
		 *
		 * @return void
		 */
		public function init() {

			require_once $this->path( 'includes/class-tm-wizard-settings.php' );

			if ( ! tm_wizard_settings()->get_manifest() ) {
				add_action( 'admin_notices', array( $this, 'manifest_notice' ) );
				return;
			}

			$this->load();
		}

		/**
		 * Get plugin page URL.
		 *
		 * @param  array $args Arguments array.
		 * @return string
		 */
		public function get_page_link( $args = array() ) {
			$args['page'] = $this->slug();
			return add_query_arg( $args, esc_url( admin_url( 'admin.php' ) ) );
		}

		/**
		 * Show warnong notice if manifest file not exists in theme
		 *
		 * @return void
		 */
		public function manifest_notice() {
			echo '<div class="notice notice-warning is-dismissible"><p>';
			esc_html_e( 'TM Wizard: Manifest file not found in current theme', 'tm-wizard' );
			echo '</p></div>';
		}

		/**
		 * Get plugin template
		 *
		 * @param  string $template Template name.
		 * @return void
		 */
		public function get_template( $template ) {

			$file = locate_template( 'tm-wizard/' . $template );

			if ( ! $file ) {
				$file = $this->path( 'templates/' . $template );
			}

			if ( file_exists( $file ) ) {
				include $file;
			}

		}

		/**
		 * Load globally required files
		 */
		public function load() {
			require_once $this->path( 'includes/class-tm-wizard-interface.php' );
			require_once $this->path( 'includes/class-tm-wizard-installer.php' );
			require_once $this->path( 'includes/class-tm-wizard-data.php' );
		}

		/**
		 * Returns plugin slug
		 *
		 * @return string
		 */
		public function slug() {
			return $this->slug;
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function path( $path = null ) {

			if ( ! $this->path ) {
				$this->path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->path . $path;

		}

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function url( $path = null ) {

			if ( ! $this->url ) {
				$this->url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->url . $path;

		}

		/**
		 * Enqueue required assets
		 *
		 * @return void
		 */
		public function enqueue_assets( $hook ) {

			if ( ! $this->is_wizard() ) {
				return;
			}

			$handle   = 'tm-wizard';
			$settings = array(
				'redirect' => true,
			);

			if ( $this->is_wizard( 3 ) ) {

				$skin = ! empty( $_GET['skin'] )
					? tm_wizard_data()->sanitize_skin( $_GET['skin'] )
					: tm_wizard_data()->default_skin();

				$type = ! empty( $_GET['type'] )
					? tm_wizard_data()->sanitize_type( $_GET['type'] )
					: tm_wizard_data()->default_type();

				$settings['firstPlugin']  = tm_wizard_data()->get_first_plugin_data( $skin, $type );
				$settings['totalPlugins'] = tm_wizard_data()->get_plugins_count( $skin, $type );
				$settings['importURL']    = tm_wizard_data()->get_content_import_url( $skin, $type );
			}

			wp_enqueue_script( $handle, $this->url( 'assets/js/tm-wizard.js' ), array( 'wp-util' ), '20161214', true );
			wp_enqueue_style( $handle, $this->url( 'assets/css/tm-wizard.css' ), false, '20161214' );

			do_action( 'tm_wizard_enqueue_assets' );

			wp_localize_script( $handle, 'tmWizardSettings', apply_filters( 'tm_wizard_js_settings', $settings ) );
		}

		/**
		 * Check if is wizard-related page.
		 *
		 * @param  bool|int $step Current step.
		 * @return bool
		 */
		public function is_wizard( $step = false ) {

			if ( ! isset( $_GET['page'] ) || $this->slug() !== $_GET['page'] ) {
				return false;
			}

			if ( false === $step ) {
				return true;
			}

			if ( empty( $_GET['step'] ) ) {
				$current = 1;
			} else {
				$current = intval( $_GET['step'] );
			}

			if ( $step === $current ) {
				return true;
			} else {
				return false;
			}
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
 * Returns instance of TM_Wizard
 *
 * @return object
 */
function tm_wizard() {
	return TM_Wizard::get_instance();
}

tm_wizard();
