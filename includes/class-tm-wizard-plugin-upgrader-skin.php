<?php
/**
 * Plugin installer skin class.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class TM_Wizard_Plugin_Upgrader_Skin extends Plugin_Installer_Skin {

	/**
	 * Holder for installation source type.
	 *
	 * @var string
	 */
	public $source = 'wordpress';

	/**
	 * Construtor for the class.
	 *
	 * @param array $args Options array.
	 */
	public function __construct( $args = array() ) {
		$this->source = isset( $args['source'] ) ? $args['source'] : $this->source;
		parent::__construct( $args );
	}

	/**
	 * Output markup after plugin installation processed.
	 */
	public function after() {}

	/**
	 *  Output header markup.
	 */
	public function header() {
		if ( $this->done_header ) {
			return;
		}
		$this->done_header = true;
		echo '<div class="tm-wizard-install-results">';
			echo '<div class="tm-wizard-install-results__trigger">';
				esc_html_e( 'Details', 'tm-wizard' );
				echo '<span class="dashicons dashicons-arrow-down"></span>';
			echo '</div>';
			echo '<ul>';
	}

	/**
	 *  Output footer markup.
	 */
	public function footer() {
		if ( $this->done_footer ) {
			return;
		}
		$this->done_footer = true;
		echo '</ul>';
		echo '</div>';
	}

	/**
	 *
	 * @param string $string
	 */
	public function feedback( $string ) {

		if ( isset( $this->upgrader->strings[ $string ] ) )
			$string = $this->upgrader->strings[ $string ];

		if ( false !== strpos( $string, '%' ) ) {
			$args = func_get_args();
			$args = array_splice( $args, 1 );

			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf( $string, $args );
			}
		}
		if ( empty( $string ) ) {
			return;
		}

		if ( is_wp_error( $string ) ) {

			if ( $string->get_error_data() && is_string( $string->get_error_data() ) ) {
				$string = $string->get_error_message() . ': ' . $string->get_error_data();
			} else {
				$string = $string->get_error_message();
			}

		}

		printf( '<li>%s</li>', $string );
	}

}