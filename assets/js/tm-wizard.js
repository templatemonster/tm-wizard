( function( $, settings ) {

	'use strict';

	var tmWizard = {
		css: {
			runInstall: '[data-wizard="start-install"]',
			confirmInstall: '[data-wizard="confirm-install"]',
			selectType: '[name="install-type"]',
			popup: '.tm-wizard-popup',
			closePopup: '.tm-wizard-popup__close',
			plugins: '.tm-wizard-plugins'
		},

		vars: {
			type: 'lite',
			popup: null,
			url: null,
			skin: null,
			plugins: null,
			template: null,
			progress: 0,
		},

		init: function() {

			tmWizard.vars.popup = $( tmWizard.css.popup );

			$( document )
				.on( 'click.tmWizard', tmWizard.css.runInstall, tmWizard.runInstall )
				.on( 'click.tmWizard', tmWizard.css.confirmInstall, tmWizard.confirmInstall )
				.on( 'click.tmWizard', tmWizard.css.closePopup, tmWizard.closePopup )
				.on( 'change.tmWizard', tmWizard.css.selectType, tmWizard.selectInstallType );

			if ( undefined !== settings.firstPlugin ) {
				tmWizard.vars.template = wp.template( 'wizard-item' );
				tmWizard.installPlugin( settings.firstPlugin );
			}
		},

		closePopup: function() {
			tmWizard.vars.popup.addClass( 'popup-hidden' );
			$( '.btn.in-progress', tmWizard.vars.popup ).removeClass( 'in-progress' );
		},

		selectInstallType: function() {
			var $this = $( this ),
				type  = $this.val();

			tmWizard.vars.type = type;
		},

		confirmInstall: function() {
			$( this ).addClass( 'in-progress' );
			window.location = tmWizard.vars.url.replace( '%type%', tmWizard.vars.type );
		},

		runInstall: function() {

			var $this = $( this );

			tmWizard.vars.skin = $this.data( 'skin' );
			tmWizard.vars.url  = $this.data( 'href' );
			tmWizard.vars.popup.removeClass( 'popup-hidden' );
		},

		installPlugin: function( data ) {

			var $target = $( tmWizard.vars.template( data ) );

			if ( null === tmWizard.vars.plugins ) {
				tmWizard.vars.plugins = $( tmWizard.css.plugins );
			}

			$target.appendTo( tmWizard.vars.plugins );
			tmWizard.installRequest( $target, data );

		},

		installRequest: function( target, data ) {

			$.ajax({
				url: ajaxurl,
				type: 'get',
				dataType: 'json',
				data: {
					action: 'tm_wizard_install_plugin',
					plugin: data.slug,
					skin: data.skin,
					type: data.type
				}
			}).done( function( response ) {

				tmWizard.vars.progress = tmWizard.vars.progress + 1;

				if ( true === response.success && true !== response.data.isLast ) {
					tmWizard.installPlugin( response.data );
				}

				if ( true === response.success ) {
					$( '.tm-wizard-loader', target ).replaceWith( '<span class="dashicons dashicons-yes"></span>' );
				}
			});
		}
	};

	tmWizard.init();

}( jQuery, window.tmWizardSettings ) );