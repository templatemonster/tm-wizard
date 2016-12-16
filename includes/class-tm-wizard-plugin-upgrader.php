<?php
/**
 * Plugin installer class.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class TM_Wizard_Plugin_Upgrader extends Plugin_Upgrader {

	public function __construct( $skin = null ) {
		parent::__construct( $skin );
	}

}
