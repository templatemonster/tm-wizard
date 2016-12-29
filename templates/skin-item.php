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
			<button data-skin="<?php echo $skin; ?>" data-wizard="start-install" data-href="<?php echo tm_wizard()->get_page_link( array( 'step' => 2, 'skin' => $skin, 'type' => '%type%' ) ) ?>" class="btn btn-primary"><?php
				esc_html_e( 'Start Install', 'tm-wizard' );
			?></button>
			<a href="<?php echo tm_wizard_interface()->get_skin_data( 'demo' ) ?>" class="btn btn-default"><?php
				esc_html_e( 'Live Demo', 'tm-wizard' );
			?></a>
		</div>
	</div>
</div>