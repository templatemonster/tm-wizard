<?php
/**
 * Skin item template
 */

$skin = tm_wizard_interface()->get_skin_data( 'slug' );

?>
<div class="tm-wizard-skin-item">
	<?php if ( tm_wizard_interface()->get_skin_data( 'thumb' ) ) : ?>
	<div class="tm-wizard-skin-item__thumb">
		<img src="<?php echo tm_wizard_interface()->get_skin_data( 'thumb' ); ?>" alt="">
	</div>
	<?php endif; ?>
	<div class="tm-wizard-skin-item__summary">
		<h4 class="tm-wizard-skin-item__title"><?php echo tm_wizard_interface()->get_skin_data( 'name' ); ?></h4>
		<h5 class="tm-wizard-skin-item__plugins-title"><?php esc_html_e( 'Recommended Plugins', 'tm-wizard' ); ?></h5>
		<div class="tm-wizard-skin-item__plugins">
			<div class="tm-wizard-skin-item__plugins-content">
				<?php echo tm_wizard_interface()->get_skin_plugins( $skin ); ?>
			</div>
		</div>
		<div class="tm-wizard-skin-item__actions">
			<a href="<?php echo tm_wizard()->get_page_link( array( 'step' => 2, 'skin' => $skin ) ) ?>" data-loader="true" class="btn btn-primary"><span class="text"><?php
				esc_html_e( 'Select Skin', 'tm-wizard' );
			?></span><span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span></a>
			<a href="<?php echo tm_wizard_interface()->get_skin_data( 'demo' ) ?>" data-loader="true" class="btn btn-default"><span class="text"><?php
				esc_html_e( 'View Demo', 'tm-wizard' );
			?></span><span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span></a>
		</div>
	</div>
</div>