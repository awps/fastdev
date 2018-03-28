/**
 * Scripts
 *
 */
;(function ( $ ) {

	"use strict";

	$( document ).ready( function () {

		// General tooltips
		$( '.fdtip' ).zTip();

		// Tooltips for tabs
		$( '.fastdev-tab' ).zTip( {
			source: function ( elem ) {
				return elem.next( 'div.fastdev-tab-tip' ).html();
			},
		} );

		function fastdev_url_param( param, value ) {
			var reg_exp = new RegExp( param + "(.+?)(&|$)", "g" );
			var new_url = window.location.href.replace( reg_exp, param + "=" + value + "$2" );
			window.history.pushState( "", "", new_url );
		}

		function fastdev_filter( elem ) {
			if ( $( elem ).length < 1 ) {
				return;
			}
			var value = $( elem ).val().toLowerCase();

			// Hide or show keywords
			$( ".fd-key-val-table .fd-kv-row" ).not( '.fd-kv-head' ).each( function () {
				$( this ).children( 'div.filter-this' ).text().toLowerCase().search( value ) > -1 ? $( this ).show() : $( this ).hide();
			} );
		}

		// Filter on document ready
		fastdev_filter( '.fd-filter-field' );

		// Filter when field value changes(real-time)
		$( '.fd-filter-field' ).on( 'keyup', function () {
			fastdev_filter( this );
		} );

		function fastdev_phpinfo_filter( elem ) {
			var value = $( elem ).val().toLowerCase();

			// Hide or show keywords
			$( "#phpinfo table" ).each( function () {
				if ( $( this ).text().toLowerCase().search( value ) > -1 ) {
					$( this ).show();
					$( this ).prev( 'h2' ).show();
				}
				else {
					$( this ).hide();
					$( this ).prev( 'h2' ).hide();
				}
			} );

			$( "#phpinfo table tr" ).not( ".h" ).each( function () {
				$( this ).text().toLowerCase().search( value ) > -1 ? $( this ).show() : $( this ).hide();
			} );
		}

		$( '.fd-filter-field' ).on( 'keyup', function () {
			fastdev_phpinfo_filter( this );
		} );

		//Events
		$( '#fd-refresh-option, #fd-delete-option' ).on( 'click', function () {
			$( this ).trigger( 'fastdev:option' );
		} );

		$( window ).on( 'focus', function () {
			if ( $( '#fd-auto-refresh' ).is( ':checked' ) ) {
				$( '#fd-refresh-option' ).trigger( 'fastdev:option' );
			}
		} );

		/* Delete and Refresh an option
		------------------------------------*/
		$( '#fd-refresh-option, #fd-delete-option' ).on( 'fastdev:option', function () {
			var _t      = $( this ),
			    _action = (_t.hasClass( 'fd-button-delete' )) ? 'fastdev_delete_option' : 'fastdev_refresh_option';

			if ( _action == 'fastdev_delete_option' ) {
				if ( !confirm( 'Warning: Deleting an option may result in a broken website! Are you sure you want to delete this option?' ) ) {
					return;
				}
			}

			_t.prepend( '<span class="fastdev-loader"></span>' ).addClass( 'active' );

			$.ajax( {
				type: "POST",
				url:  ajaxurl,
				data: {
					"action":    _action,
					"option_id": _t.data( 'option' ),
					"nonce":     _t.data( 'nonce' ),
				},

				success:  function ( response ) {
					// console.log(response);
					if ( response ) {
						$( '#fd-wpo-code-block' ).html( response );
						if ( _action == 'fastdev_refresh_option' && $( '#fd-wpo-code-block pre' ).length > 0 ) {
							var pre_block = $( '#fd-wpo-code-block pre:not(.disable-highlight)' );
							if ( pre_block.length > 0 ) {
								Prism.highlightElement( pre_block[0] );
							}
						}
					}
				},
				complete: function ( jqXHR, textStatus ) {
					_t.removeClass( 'active' ).children( '.fastdev-loader' ).remove();
				},

				timeOut: 1000 * 60 //1 minute

			} );

		} );

		$( '#wp-option-edit-key' ).on( 'change', function () {
			var _t      = $( this ),
			    _origin = _t.data( 'original-option-key' ),
			    _to     = _t.val();
			if ( !window.confirm( 'Warning: Are you sure that you want to change the key of this option? ' ) ) {
				return;
			}

			_t.attr( 'disabled', 'disabled' ).css( 'opacity', 0.4 );

			$.ajax( {
				type: "POST",
				url:  ajaxurl,
				data: {
					"action":      'fastdev_edit_option_key',
					"option_from": _origin,
					"option_to":   _to,
				},

				success:  function ( response ) {
					console.log( response );

					_t.removeAttr( 'disabled' ).css( 'opacity', '' );

					if ( response && response === 'success' ) {
						fastdev_url_param( 'fd-get-option', _to );

						$( '#fd-refresh-option' ).data( 'option', _to );
						$( '#fd-delete-option' ).data( 'option', _to );
						_t.data( 'original-option-key', _to );
					}
				},
				complete: function ( jqXHR, textStatus ) {
					// _t.removeClass('active').children( '.fastdev-loader' ).remove();
				},

				timeOut: 1000 * 60 //1 minute

			} );

		} );

		$( window ).on( 'focus', function () {
			if ( $( '#testing-autorefresh' ).is( ':checked' ) ) {
				$( '.js-fastdev-testing-form' ).trigger( 'submit' );
			}
		} );

		$( document ).on( 'submit', '.js-fastdev-testing-form', function ( event ) {
			event.preventDefault();

			var button        = $( this ).find( '.button' );
			var function_name = $( this ).find( '[name="function_name"]' ).val();
			var nonce         = $( this ).find( '[name="nonce"]' ).val();

			button.attr( 'disabled', true );

			$.ajax( {
				type: "POST",
				url:  ajaxurl,
				data: {
					"action":        'fastdev_testing',
					"function_name": function_name,
					"nonce":         nonce,
				},

				success:  function ( response ) {
					// console.log( response );
					if ( response ) {
						$( '#js-fastdev-testing-result' ).html( response );
						var pre_block = $( '#js-fastdev-testing-result pre:not(.disable-highlight)' );
						if ( pre_block.length > 0 ) {
							Prism.highlightElement( pre_block[0] );
						}
					}
				},
				complete: function ( jqXHR, textStatus ) {
					button.attr( 'disabled', false );
				},

				timeOut: 1000 * 60 //1 minute

			} );

		} );

		$( '.toggle-string span' ).on( 'click', function () {
			var _this           = $( this ),
			    _main_container = _this.parents( '.fastdev-trimmed-string' );

			if ( _this.hasClass( 'open' ) ) {
				_this.text( _this.data( 'expand' ) );
				_this.removeClass( 'open' );
				_main_container.children( '.original-string' ).slideUp( 150 );
				_main_container.children( '.trimmed-string' ).slideDown( 150 );
			}
			else {
				_this.text( _this.data( 'collapse' ) );
				_this.addClass( 'open' );
				_main_container.children( '.original-string' ).slideDown( 150 );
				_main_container.children( '.trimmed-string' ).slideUp( 150 );
			}

		} );

	} );

})( jQuery );
