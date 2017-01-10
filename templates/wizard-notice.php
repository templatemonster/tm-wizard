<?php
/**
 * Wizard notice template.
 */
?>
<div class="tm-wizard-notice notice">
	<div class="tm-wizard-notice__content"><?php
		printf( esc_html__( 'This wizard will help you to select skin, install plugins and import demo data for your %s theme. To start the install click the button below!', 'tm-wizard' ), '<b>Monstroid&sup2;</b>' );
	?></div>
	<div class="tm-wizard-notice__actions">
		<a class="tm-wizard-btn" href="<?php echo tm_wizard()->get_page_link(); ?>"><?php
			esc_html_e( 'Start Install', 'tm-wizard' );
		?></a>
		<a class="notice-dismiss" href="<?php echo add_query_arg( array( 'tm_wizard_dismiss' => true, '_nonce' => tm_wizard()->nonce() ) ); ?>"><?php
			esc_html_e( 'Dismiss', 'tm-wizard' );
		?></a>
	</div>
</div>
