<?php
/**
 * Template for configure plugins step.
 */
?>
<h2><?php esc_html_e( 'Configure plugins', 'tm-wizard' ); ?></h2>



<a href="<?php echo tm_wizard()->get_page_link( array( 'step' => 3 ) ); ?>" data-loader="true" class="btn btn-primary start-install">
	<span class="text"><?php esc_html_e( 'Next', 'tm-wizard' ); ?></span>
	<span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span>
</a>