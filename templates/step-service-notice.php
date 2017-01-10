<?php
/**
 * Template for service notice step.
 */
?>
<h2><?php esc_html_e( 'Installation Wizard', 'tm-wizard' ); ?></h2>
<div class="tm-wizard-msg"><?php esc_html_e( 'Demo data import wizard will guide you through the process of demo content import and recommended plugins installation. Before gettings started make sure your server complies with', 'tm-wizard' ); ?> <b><?php esc_html_e( 'WordPress minimal requirements.', 'tm-wizard' ); ?></b></div>
<h4><?php esc_html_e( 'Your system information:', 'tm-wizard' ); ?></h4>
<?php echo tm_wizard_interface()->server_notice(); ?>
<?php
	$errors = wp_cache_get( 'errors', 'tm-wizard' );
	if ( $errors ) {
		printf(
			'<div class="tm-warning-notice">%s</div>',
			esc_html__( 'Not all of your server parameters met requirements', 'tm-wizard' )
		);
	}
?>
<a href="<?php echo tm_wizard()->get_page_link( array( 'step' => 1 ) ); ?>" data-loader="true" class="btn btn-primary">
	<span class="text"><?php esc_html_e( 'Start Install', 'tm-wizard' ); ?></span>
	<span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span>
</a>