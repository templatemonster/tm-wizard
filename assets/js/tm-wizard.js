( function( $, settings ) {

	'use strict';

	var tmWizard = {
		css: {
			runInstall: '[data-wizard="start-install"]',
			confirmInstall: '[data-wizard="confirm-install"]',
			selectType: '[name="install-type"]',
			popup: '.tm-wizard-popup',
			closePopup: '.tm-wizard-popup__close',
			plugins: '.tm-wizard-plugins',
			progress: '.tm-wizard-progress__bar',
			showResults: '.tm-wizard-install-results__trigger',
		},

		vars: {
			type: 'lite',
			popup: null,
			url: null,
			skin: null,
			plugins: null,
			template: null,
			currProgress: 0,
			progress: null
		},

		init: function() {

			tmWizard.vars.popup    = $( tmWizard.css.popup );
			tmWizard.vars.progress = $( tmWizard.css.progress );
			tmWizard.vars.percent  = $( '.tm-wizard-progress__label', tmWizard.vars.progress );

			$( document )
				.on( 'click.tmWizard', tmWizard.css.runInstall, tmWizard.runInstall )
				.on( 'click.tmWizard', tmWizard.css.confirmInstall, tmWizard.confirmInstall )
				.on( 'click.tmWizard', tmWizard.css.closePopup, tmWizard.closePopup )
				.on( 'click.tmWizard', tmWizard.css.showResults, tmWizard.showResults )
				.on( 'change.tmWizard', tmWizard.css.selectType, tmWizard.selectInstallType );

			$( '.cdi-advanced-popup' ).on( 'cdi-popup-opened', tmWizard.setURL );

			if ( undefined !== settings.firstPlugin ) {
				tmWizard.vars.template = wp.template( 'wizard-item' );
				tmWizard.installPlugin( settings.firstPlugin );
			}
		},

		showResults: function() {
			var $this = $( this );
			$this.toggleClass( 'is-active' );
		},

		setURL: function() {
			var $this = $( this );
			if ( undefined !== settings.importURL ) {
				$this.data( 'url', settings.importURL );
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

		updateProgress: function() {

			var val   = 0,
				total = parseInt( settings.totalPlugins );

			tmWizard.vars.currProgress++;

			val = 100 * ( tmWizard.vars.currProgress / total );
			val = Math.round( val );

			if ( 100 < val ) {
				val = 100;
			}

			tmWizard.vars.percent.html( val + '%' );
			tmWizard.vars.progress.css( 'width', val + '%' );

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

				tmWizard.updateProgress();

				if ( true !== response.success ) {
					return;
				}

				target.append( response.data.log );

				if ( true !== response.data.isLast ) {
					tmWizard.installPlugin( response.data );
				} else {

					$( document ).trigger( 'tm-wizard-install-finished' );

					if ( true === settings.redirect ) {
						window.location = response.data.redirect;
					}

					target.after( response.data.message );

				}

				$( '.tm-wizard-loader', target ).replaceWith( '<span class="dashicons dashicons-yes"></span>' );

			});
		}
	};

	tmWizard.init();

}( jQuery, window.tmWizardSettings ) );