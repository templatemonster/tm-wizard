<?php
/**
 * Template part for displaying advanced popup
 */
?>
<div class="tm-wizard-popup popup-hidden">
	<div class="tm-wizard-popup__content">
		<h3><?php esc_html_e( 'Demo Content Settings', 'tm-wizard' ); ?></h3>
		<?php esc_html_e( 'Each theme comes with lite and full version of demo content. The number of posts and plugins may affect your site speed.', 'tm-wizard' ); ?><br><br>
		<?php esc_html_e( 'We recommend importing lite version of demo content if you are running shared inexpensive server. Full version of demo content is recommended for dedicated servers, VPS servers and premium shared hosing plans.', 'tm-wizard' ); ?>
		<div class="tm-wizard-popup__select">
			<label class="tm-wizard-popup__item">
				<input type="radio" name="install-type" value="lite" checked>
				<span class="tm-wizard-popup__item-mask"></span>
				<span class="tm-wizard-popup__item-label"><?php
					esc_html_e( 'Lite Install', 'tm-wizard' );
				?></span>
			</label>
			<label class="tm-wizard-popup__item">
				<input type="radio" name="install-type" value="full">
				<span class="tm-wizard-popup__item-mask"></span>
				<span class="tm-wizard-popup__item-label"><?php
					esc_html_e( 'Full Install', 'tm-wizard' );
				?></span>

			</label>
		</div>
		<div class="tm-wizard-popup__action">
			<button class="btn btn-primary" data-wizard="confirm-install"><span class="text"><?php
				esc_html_e( 'Start', 'tm-wizard' );
			?></span><span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span></button>
		</div>
		<button class="tm-wizard-popup__close"><span class="dashicons dashicons-no"></span></button>
	</div>
</div>