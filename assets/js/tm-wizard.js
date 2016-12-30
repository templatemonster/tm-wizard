( function( $, settings ) {

	'use strict';

	var tmWizard = {
		css: {
			plugins: '.tm-wizard-plugins',
			progress: '.tm-wizard-progress__bar',
			showResults: '.tm-wizard-install-results__trigger',
			showPlugins: '.tm-wizard-skin-item__plugins-title',
			loaderBtn: '[data-loader="true"]'
		},

		vars: {
			plugins: null,
			template: null,
			currProgress: 0,
			progress: null
		},

		init: function() {

			tmWizard.vars.progress = $( tmWizard.css.progress );
			tmWizard.vars.percent  = $( '.tm-wizard-progress__label', tmWizard.vars.progress );

			$( document )
				.on( 'click.tmWizard', tmWizard.css.showResults, tmWizard.showResults )
				.on( 'click.tmWizard', tmWizard.css.showPlugins, tmWizard.showPlugins )
				.on( 'click.tmWizard', tmWizard.css.loaderBtn, tmWizard.showLoader );

			if ( undefined !== settings.firstPlugin ) {
				tmWizard.vars.template = wp.template( 'wizard-item' );
				settings.firstPlugin.isFirst = true;
				tmWizard.installPlugin( settings.firstPlugin );
			}
		},

		showLoader: function() {
			$( this ).addClass( 'in-progress' );
		},

		showPlugins: function() {
			$( this ).toggleClass( 'is-active' );
		},

		showResults: function() {
			var $this = $( this );
			$this.toggleClass( 'is-active' );
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

			var icon;

			if ( undefined === data.isFirst ) {
				data.isFirst = false;
			}

			$.ajax({
				url: ajaxurl,
				type: 'get',
				dataType: 'json',
				data: {
					action: 'tm_wizard_install_plugin',
					isFirst: data.isFirst,
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

					if ( 1 == settings.redirect ) {
						window.location = response.data.redirect;
					}

					target.after( response.data.message );

				}

				if ( 'error' === response.data.resultType ) {
					icon = '<span class="dashicons dashicons-no"></span>';
				} else {
					icon = '<span class="dashicons dashicons-yes"></span>';
				}

				target.addClass( 'installed-' + response.data.resultType );
				$( '.tm-wizard-loader', target ).replaceWith( icon );

			});
		}
	};

	tmWizard.init();

}( jQuery, window.tmWizardSettings ) );