<?php
/**
 * Plugin installer skin class.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class TM_Wizard_Plugin_Upgrader_Skin extends Plugin_Installer_Skin {

	public $source = 'wordpress';

	public function __construct( $args = null ) {
		var_dump( $args );
		$this->source = isset( $args['source'] ) ? $args['source'] : $this->source;
		parent::__construct( $args = array() );
	}

}