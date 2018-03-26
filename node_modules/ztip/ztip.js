/**
 * Plugin Name : zTip
 * Version     : 1.0.0
 * Author      : Andrei Surdu
 * Author URL  : http://zerowp.com/
 * Plugin URL  : http://ztip.zerowp.com/
 * License     : MIT
 */
;(function( $ ) {

	"use strict";

	$.fn.zTip = function( options ) {

		if (this.length > 1){
			this.each(function() {
				$(this).zTip(options);
			});
			return this;
		}

		/* Setup options
		---------------------*/
		var settings = $.extend({
			// Theme
			theme: 'default',

			// attr:title | > .child-elem | .dom-elem | function callback
			source: 'attr:title',

			// Tooltip positon relative to element top | bottom
			position: 'top',

		}, options );

		// Is this instance
		var plugin = this;

		// Is the tooltip hidden at the end of body
		var holder;

		// Get data-* attributes
		$.each(settings, function( option ) {
			var data_attr = option.replace(/([A-Z])/g, '-$1').toLowerCase().toString(), //`optionsName` becomes `option-name`
			new_val       =  plugin.data( data_attr );

			if( new_val || false === new_val ){
				settings[ option ] = new_val;
			}
		});

		// Constructor
		var init = function() {
			plugin.firstInit();
			plugin.build();
		};

		this.firstInit = function(){
			// Create the holder that will be used later to display the tooltip
			if( $('body').find('.ztip-holder').length < 1 ){
				var spans = '<span class="zt-text"></span><span class="zt-arrow"></span>';
				$('body').append('<div class="ztip-holder ztip-position-top">'+ spans +'</div>');
			}

			holder = $('body').find('.ztip-holder');
		};

		this.build = function(){
			plugin.on( 'mouseover', function() {
				var elem = $(this),
				tip      = plugin.getElemTip( elem );

				if( ! tip ) {
					return;
				}

				// Refresh holder theme if needed...
				plugin.refreshHolderTheme();

				// Add the new tooltip text to holder
				holder.children('.zt-text').html( tip );

				var coords = plugin.getElementCoordinates( elem );

				// If the target elem is larger than the holder, allow the max-width to be equal to it
				// Window is larger than this element... Ya, that's it...
				if( holder.outerWidth() < coords.width ) {
					holder.css({
						'max-width': coords.width + 'px',
					});
				}

				// The magic is here
				plugin.autoPosition( coords );

				// Finally show it to user.
				holder.addClass('ztip-show');
			} );

			// When the mouse leaves the container
			plugin.on( 'mouseout', function() {
				if( holder ) {
					holder.removeClass('ztip-show').css({
						'top': 0,
						'right': '',
						'bottom': '',
						'left': '-110%',
						'max-width': '',
					});
				}
			} );

			// When the window is modified
			$(window).on( 'scroll resize', function() {
				if( holder ) {
					holder.removeClass('ztip-show');
				}
			} );
		};

		this.getElemTip = function( elem ){
			// No misleading title buble. The tooltip acts like a replacer for title tag,
			// and even if the source is not the title tag, we must disabe it so only one
			// tip is displayed and that should be ours.
			var _old_title = elem.attr('title');

			if( _old_title ) {
				elem.attr('data-ztip-title', _old_title ).removeAttr('title');
			}

			// It's a callback
			if( typeof settings.source === "function" ) {
				return settings.source.call(this, elem);
			}

			// The source is the title attribute
			else if( plugin.stringStartsWith( settings.source, 'attr:title' ) ){
				return elem.attr('data-ztip-title');
			}

			// The source is another attribute
			else if(plugin.stringStartsWith( settings.source, 'attr:' )) {
				return elem.attr( settings.source.replace( 'attr:', '' ) );
			}

			// The is a child of this element
			else if(plugin.stringStartsWith( settings.source, '>' )) {
				return elem.children( settings.source.replace( '>', '' ) ).html();
			}

			// Its a DOM element? Probably...
			else {
				return $( settings.source ).html();
			}

		};

		this.autoPosition = function( coords ){
			var _top = '',
			_left = '',
			viewport = plugin.getViewport();

			// It's not wider than current window?
			if( holder.outerWidth() > viewport.width ){
				holder.css({
					'max-width': viewport.width,
				});
			}

			// Display on top or bottom?
			// TODO: DRY. The following if else needs improvements
			if( 'bottom' === settings.position ){
				if( holder.outerHeight() + 10 > coords.fromBottom ){
					_top = coords.top - holder.outerHeight() - 10;
					plugin.changeHolderPosition( 'top' );
				}
				else{
					_top = coords.bottom + 10;
					plugin.changeHolderPosition( 'bottom' );
				}
			}
			else{
				if( holder.outerHeight() + 10 < coords.top ){
					_top = coords.top - holder.outerHeight() - 10;
					plugin.changeHolderPosition( 'top' );
				}
				else{
					_top = coords.bottom + 10;
					plugin.changeHolderPosition( 'bottom' );
				}
			}

			// Center tooltip on X axis. If it gets out of viewport realign it.
			var half_holder = holder.outerWidth() / 2,
			is_small        = holder.outerWidth() < viewport.width,
			maybe_left      = ( viewport.width - holder.outerWidth() ) / 2;

			// Attempt to align the tooltip based element coordinates
			// We need only the distance from left.
			if( half_holder > coords.centerX ){
				_left = 0;

				if( is_small && maybe_left < coords.left ){
					_left = maybe_left;
				}
				else if( coords.fromRight + coords.width > holder.outerWidth() ){
					_left = coords.left;
				}
			}
			else if( half_holder < coords.centerX && viewport.width - coords.centerX < half_holder ){
				_left = viewport.width - holder.outerWidth();

				if( is_small && maybe_left < coords.fromRight ){
					_left = maybe_left;
				}
				else if( coords.right > holder.outerWidth() ){
					_left = coords.right - holder.outerWidth();
				}
			}
			else{
				_left = coords.centerX - holder.outerWidth() / 2;
			}

			// Align the tooltip in space
			plugin.holderCss({
				'top': ( _top !== '' ? _top + 'px' : '' ),
				'left': ( _left !== '' ? _left + 'px' : '' ),
			});

			var rec  = holder[0].getBoundingClientRect(),
			arr_left = coords.centerX - rec.left,
			arr      = holder.children('.zt-arrow');

			// Align the arrow
			arr.css({
				'left'        : arr_left,
				'margin-left' : - arr.outerWidth() / 2,
			});
		};

		this.holderCss = function( holder_css ){
			var new_holder_css = {
				'top': holder_css.top || '',
				'right': holder_css.right || '',
				'bottom': holder_css.bottom || '',
				'left': holder_css.left || '',
			};

			holder.css( new_holder_css );
		};

		/**
		 * Get element coordinates.
		 *
		 * @param {object} elem The jQuery element object to get coordinates for.
		 * @return {object} Element coordinates.
		 */
		this.getElementCoordinates = function( elem ){
			// pure JS selected element
			var element = elem;

			// jQuery selected element
			if( elem instanceof jQuery ) {
				element = elem[0];
			}

			// Get top/left rectangular positions.
			var rec = element.getBoundingClientRect();

			// Get element dimensions
			var
				width  = rec.right - rec.left,
				height = rec.bottom - rec.top;

			// Return the coordinates of this element in current viewport
			return {
				width   : width,
				height  : height,
				top     : rec.top,
				left    : rec.left,
				bottom  : rec.bottom,
				right   : rec.right,

				fromTop     : rec.top,
				fromLeft    : rec.left,
				fromBottom  : $(window).innerHeight() - rec.bottom,
				fromRight   : $(window).innerWidth() - rec.right,

				centerX : rec.left + width / 2,
				centerY : rec.top + height / 2,
			};
		};

		/**
		 * Get viewport dimensions
		 *
		 * @return {object}
		 */
		this.getViewport = function(){
			return {
				width  : $(window).innerWidth(),
				height : $(window).innerHeight(),
			};
		};

		this.changeHolderPosition = function( position ){
			plugin.replaceClass( /\bztip-position-\S+/g, 'ztip-position-' + position );
		};

		this.refreshHolderTheme = function(){
			plugin.replaceClass( /\bztip-theme-\S+/g, 'ztip-theme-' + settings.theme );
		};

		this.replaceClass = function( _to_replace, _with ){
			if( ! holder.hasClass( _with ) ) {
				holder.removeClass (
					function ( index, css ) {
						return ( css.match ( _to_replace ) || [] ).join(' ');
					}
				).addClass( _with );
			}
		};

		this.stringStartsWith = function(the_string, search_string, position){
			return the_string.substr(position || 0, search_string.length) === search_string;
		};

		init();
		return this;
	};

})(jQuery);
