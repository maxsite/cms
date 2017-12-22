/**
* jQuery Showhide plugin
* (с) Cuprum, http://cuprum.name
*/

(function( $, window, document, undefined ) {


	var pluginName = 'showHide',
		defaults = {
			cookieName : ( encodeURIComponent(window.location.host) + window.location.pathname ).replace( /\//g, '-' ),
			visible : false,
			time : 400,
			clickElem : null,
			clickElemClassVisible : 'visible',
			clickElemClassHidden : 'hidden',
			foldElem : null,
			useID : false,
			cookieExpires : 30,
			cookiePath : '/'
		},
		counter = 0;

	function Plugin( element, options, lastIndx, i, counter, storage ) {
		this.element = element;
		this.options = $.extend( {}, defaults, options );
		this.i = i;
		this.init( lastIndx, counter, storage );
	}

	Plugin.prototype = {
		init : function( lastIndx, counter, storage ) {
			var opts = this.options,
				block = $( this.element ),
				clickable = opts.clickElem ? block.find( opts.clickElem ) : block.find( '> :first-child' ),
				foldable = opts.foldElem ? block.find( opts.foldElem ) : clickable.next(),
				useID = opts.useID,
				cookie = $.cookie( opts.cookieName + counter ),
				visible = opts.visible,
				indx = useID ? block.attr( "id" ) : this.i;

			if ( !cookie ) {
				if ( !visible ) {
					storage[ indx ] = 0;
					foldable.hide();
					clickable.addClass( opts.clickElemClassHidden );
				} else {
						storage[ indx ] = 1;
					clickable.addClass( opts.clickElemClassVisible );
				}

				if ( lastIndx === this.i ) {
					$.cookie( opts.cookieName + counter, storage, {expires: opts.cookieExpires, path: opts.cookiePath} );
				}
			} else {
				if ( indx in cookie ) {
					if ( cookie[ indx ] === 0 ) {
						foldable.hide();
						clickable.addClass( opts.clickElemClassHidden );
					} else {
						foldable.show();
						clickable.addClass( opts.clickElemClassVisible );
					}
				} else {
					if ( !visible ) {
						cookie[ indx ] = 0;
						foldable.hide();
						clickable.addClass( opts.clickElemClassHidden );
					} else {
						cookie[ indx ] = 1;
						clickable.addClass( opts.clickElemClassVisible );
					}

					if ( lastIndx === this.i ) {
						$.cookie( opts.cookieName + counter, cookie, {expires: opts.cookieExpires, path: opts.cookiePath} );
					}
				}
			}

			clickable.on( 'click', function ( e ) {
				e.preventDefault();

				var cookieValue = $.cookie( opts.cookieName + counter );

				if ( navigator.cookieEnabled ) {
					if ( cookieValue[ indx ] === 0 ) {
						cookieValue[ indx ] = 1;
						foldable
							.stop( false, true )
							.slideDown( opts.time );
					} else {
						cookieValue[ indx ] = 0;
						foldable
							.stop( false, true )
							.slideUp( opts.time );
					}
					$.cookie( opts.cookieName + counter, cookieValue, {expires: opts.cookieExpires, path: opts.cookiePath} );
				} else {
					foldable.slideToggle( opts.time );
				}
				clickable.toggleClass( opts.clickElemClassHidden + ' ' + opts.clickElemClassVisible );
			});
		}
	};

	$.fn[ pluginName ] = function( options ) {
		var elements = this,
			lastIndx = elements.length - 1,
			storage = {};

		if ( !options || !options.cookieName ) {
			counter++;
		} else {
			counter = '';
		}

		return this.each( function ( i ) {
			if ( !$.data( this, 'plugin_' + pluginName ) ) {
				$.data( this, 'plugin_' + pluginName, new Plugin( this, options, lastIndx, i, counter, storage ));
			}
		});
	};

})( jQuery, window, document );