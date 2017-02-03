<?php
/**
 * Template for configure plugins step.
 */
?>
<h2><?php esc_html_e( 'Configure plugins', 'tm-wizard' ); ?></h2>

<div class="tm-config-list">
<?php
	foreach ( tm_wizard_data()->get_all_plugins_list() as $slug => $plugin_data) {
		tm_wizard()->get_template( 'configure-plugins/item.php', array_merge(
			array( 'slug' => $slug ),
			$plugin_data
		) );
	}
?>
</div>

<a href="<?php echo tm_wizard()->get_page_link( array( 'step' => 3 ) ); ?>" data-loader="true" class="btn btn-primary store-plugins">
	<span class="text"><?php esc_html_e( 'Next', 'tm-wizard' ); ?></span>
	<span class="tm-wizard-loader"><span class="tm-wizard-loader__spinner"></span></span>
</a>