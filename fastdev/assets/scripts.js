/**
 * Scripts
 *
 */
;(function( $ ) {

	"use strict";

$(document).ready(function(){

	function fastdev_filter(elem) {
		if( $(elem).length < 1 ) return;
		var value    = $(elem).val().toLowerCase();

		// Hide or show keywords
		$(".fd-key-val-table .fd-kv-row").not('.fd-kv-head').each(function() {
			$(this).children('div.filter-this').text().toLowerCase().search(value) > -1 ? $(this).show() : $(this).hide();
		});
	}

	// Filter on document ready
	fastdev_filter('.fd-filter-field');

	// Filter when field value changes(real-time)
	$('.fd-filter-field').on( 'keyup', function(){
		fastdev_filter(this);
	});

	function fastdev_phpinfo_filter(elem) {
		var value    = $(elem).val().toLowerCase();

		// Hide or show keywords
		$("#phpinfo table").each(function() {
			if( $(this).text().toLowerCase().search(value) > -1 ){
				$(this).show();
				$(this).prev('h2').show();
			}
			else{
				$(this).hide();
				$(this).prev('h2').hide();
			}
		});

		$("#phpinfo table tr").not(".h").each(function() {
			$(this).text().toLowerCase().search(value) > -1 ? $(this).show() : $(this).hide();
		});
	}
	$('.fd-filter-field').on( 'keyup', function(){
		fastdev_phpinfo_filter(this);
	});

	//Events
	$( '#fd-refresh-option, #fd-delete-option' ).on('click', function(){
		$(this).trigger('fastdev:option');
	});

	$( window ).on('focus', function(){
		if( $('#fd-auto-refresh').is(':checked') ){
			$('#fd-refresh-option').trigger('fastdev:option');
		}
	});

	//Actions
	$( '#fd-refresh-option, #fd-delete-option' ).on('fastdev:option', function(){
		var _t  = $(this),
		_action = ( _t.hasClass('fd-button-delete') ) ? 'fastdev_delete_option' : 'fastdev_refresh_option';

		if( _action == 'fastdev_delete_option' ){
			if( ! confirm( 'Warning: Deleting an option may result in a broken website! Are you sure you want to delete this option?' ) )
				return;
		}

		_t.prepend( '<span class="fastdev-loader"></span>' ).addClass('active');

		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				"action": _action,
				"option_id": _t.data('option'),
				"nonce": _t.data('nonce')
			},

			success: function(response){
				console.log(response);
				if( response ){
					$('#fd-wpo-code-block').html(response);
					if( _action == 'fastdev_refresh_option' && $('#fd-wpo-code-block pre').length > 0 ){
						Prism.highlightElement($('#fd-wpo-code-block pre')[0]);
					}
				}
			},
			complete: function( jqXHR, textStatus ){
				_t.removeClass('active').children( '.fastdev-loader' ).remove();
			},

			timeOut: 1000*60 //1 minute

		});

	});

});

})(jQuery);