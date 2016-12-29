<?php
/**
 * Template part for displaying advanced popup
 */

$skin = tm_wizard_interface()->get_skin_data( 'slug' );
?>
<h2><?php esc_html_e( 'Demo Content Settings', 'tm-wizard' ); ?></h2>

<?php esc_html_e( 'Each theme comes with lite and full version of demo content. The number of posts and plugins may affect your site speed. We recommend importing lite version of demo content if you are running shared inexpensive server.', 'tm-wizard' ); ?><br><br>
<?php esc_html_e( 'Full version of demo content is recommended for dedicated servers, VPS servers and premium shared hosing plans.', 'tm-wizard' ); ?>
<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
	<div class="tm-wizard-type__select">
		<label class="tm-wizard-type__item">
			<input type="radio" name="type" value="lite" checked>
			<span class="tm-wizard-type__item-mask"></span>
			<span class="tm-wizard-type__item-label"><?php
				esc_html_e( 'Lite Install', 'tm-wizard' );
			?></span>
		</label>
		<label class="tm-wizard-type__item">
			<input type="radio" name="type" value="full">
			<span class="tm-wizard-type__item-mask"></span>
			<span class="tm-wizard-type__item-label"><?php
				esc_html_e( 'Full Install', 'tm-wizard' );
			?></span>

		</label>
	</div>
	<input type="hidden" name="step" value="3">
	<input type="hidden" name="skin" value="<?php echo $skin; ?>">
	<input type="hidden" name="page" value="<?php echo tm_wizard()->slug(); ?>">
	<button class="btn btn-primary" data-wizard="confirm-install" data-loader="true" data-href=""><span class="text"><?php
		esc_html_e( 'Start', 'tm-wizard' );
	?></span><span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span></button>
</form>