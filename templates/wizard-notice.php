<?php
/**
 * Wizard notice template.
 */
?>
<div class="tm-wizard-notice notice">
	<div class="tm-wizard-notice__content"><?php
		esc_html_e( 'Wizard will help you to install your Monstroid theme.', 'tm-wizard' );
	?></div>
	<div class="tm-wizard-notice__actions">
		<a class="tm-wizard-btn" href="<?php echo tm_wizard()->get_page_link(); ?>"><?php
			esc_html_e( 'Install', 'tm-wizard' );
		?></a>
		<a class="tm-wizard-dismiss" href="<?php echo add_query_arg( array( 'tm_wizard_dismiss' => true, '_nonce' => tm_wizard()->nonce() ) ); ?>"><?php
			esc_html_e( 'Dismiss', 'tm-wizard' );
		?></a>
	</div>
</div>
