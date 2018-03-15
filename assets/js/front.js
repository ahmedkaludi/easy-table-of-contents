jQuery( document ).ready( function( $ ) {

	if ( typeof ezTOC != 'undefined' ) {

		var affix = $( '.ez-toc-widget-container.ez-toc-affix' );

		if ( 0 !== affix.length ) {

			/**
			 * The smooth scroll offset needs to be taken into account when defining the offset_top property.
			 * @link https://github.com/shazahm1/Easy-Table-of-Contents/issues/19
			 *
			 * @type {number}
			 */
			var affixOffset = 30;

			// check offset setting
			if ( typeof ezTOC.scroll_offset != 'undefined' ) {

				affixOffset =  ezTOC.scroll_offset;
			}

			$( ezTOC.affixSelector ).stick_in_parent({
				inner_scrolling : false,
				offset_top : parseInt( affixOffset )
			});
		}

		$.fn.shrinkTOCWidth = function() {

			$( this ).css( {
				width:   'auto',
				display: 'table'
			});

			if ( /MSIE 7\./.test( navigator.userAgent ) )
				$( this ).css( 'width', '' );
		};

		if ( ezTOC.smooth_scroll == 1 ) {

			var target = hostname = pathname = qs = hash = null;

			$( 'body a' ).click( function( event ) {

				hostname = $( this ).prop( 'hostname' );
				pathname = $( this ).prop( 'pathname' );
				qs = $( this ).prop( 'search' );
				hash = $( this ).prop( 'hash' );

				// ie strips out the preceding / from pathname
				if ( pathname.length > 0 ) {
					if ( pathname.charAt( 0 ) != '/' ) {
						pathname = '/' + pathname;
					}
				}

				if ( (window.location.hostname == hostname) && (window.location.pathname == pathname) && (window.location.search == qs) && (hash !== '') ) {

					// escape jquery selector chars, but keep the #
					var hash_selector = hash.replace( /([ !"$%&'()*+,.\/:;<=>?@[\]^`{|}~])/g, '\\$1' );

					// check if element exists with id=__
					if ( $( hash_selector ).length > 0 )
						target = hash;
					else {
						// must be an anchor (a name=__)
						anchor = hash;
						anchor = anchor.replace( '#', '' );
						target = 'a[name="' + anchor + '"]';
						// verify it exists
						if ( $( target ).length == 0 )
							target = '';
					}

					// check offset setting
					if ( typeof ezTOC.scroll_offset != 'undefined' ) {

						var offset = -1 * ezTOC.scroll_offset;

					} else {

						var adminbar = $( '#wpadminbar' );

						if ( adminbar.length > 0 ) {

							if ( adminbar.is( ':visible' ) )
								offset = -30;	// admin bar exists, give it the default
							else
								offset = 0;		// there is an admin bar but it's hidden, so no offset!

						} else {

							// no admin bar, so no offset!
							offset = 0;
						}
					}

					if ( target ) {
						$.smoothScroll( {
							scrollTarget: target,
							offset:       offset
						} );
					}
				}
			} );
		}

		if ( typeof ezTOC.visibility_hide_by_default != 'undefined' ) {

			var toggle = $( 'a.ez-toc-toggle' );
			var invert = ezTOC.visibility_hide_by_default;

			if ( Cookies ) {

				Cookies.get( 'ezTOC_hidetoc' ) == 1 ? toggle.data( 'visible', false ) : toggle.data( 'visible', true );

			} else {

				toggle.data( 'visible', true );
			}

			if ( invert ) {

				toggle.data( 'visible', false )
			}

			if ( ! toggle.data( 'visible' ) ) {

				$( 'ul.ez-toc-list' ).hide();
			}

			toggle.click( function( event ) {

				event.preventDefault();

				if ( $( this ).data( 'visible' ) ) {

					$( this ).data( 'visible', false );

					if ( Cookies ) {

						if ( invert )
							Cookies.set( 'ezTOC_hidetoc', null, { path: '/' } );
						else
							Cookies.set( 'ezTOC_hidetoc', '1', { expires: 30, path: '/' } );
					}

					$( 'ul.ez-toc-list' ).hide( 'fast' );

				} else {

					$( this ).data( 'visible', true );

					if ( Cookies ) {

						if ( invert )
							Cookies.set( 'ezTOC_hidetoc', '1', { expires: 30, path: '/' } );
						else
							Cookies.set( 'ezTOC_hidetoc', null, { path: '/' } );
					}

					$( 'ul.ez-toc-list' ).show( 'fast' );

				}

			} );
		}

		// ======================================
		// Waypoints helper functions
		// ======================================

		// Get link by section or article id
		function getRelatedNavigation( element ) {
			return $( '.ez-toc-widget-container .ez-toc-list a[href="#' + $( element ).attr( 'id' ) + '"]' );
		}

		function getScrollOffset( element ) {

			var scrollOffset = ( typeof ezTOC.scroll_offset != 'undefined' ) ? parseInt( ezTOC.scroll_offset ) : 30;
			var offset       = $( element ).height() + scrollOffset;

			var adminbar = $( '#wpadminbar' );

			if ( 0 === adminbar.length ) {

				offset = offset-30;
			}

			return parseInt( offset );
		}

		// ======================================
		// Waypoints
		// ======================================

		$('span.ez-toc-section')
			.waypoint( function( direction ) {
				// Highlight element when related content is 10% percent from the bottom - remove if below.
				var item = getRelatedNavigation( this.element ).toggleClass( 'active', direction === 'down' );
				item.toggleClass( 'active', direction === 'down' ).parent().toggleClass( 'active', direction === 'down' );
			}, {
				offset: '90%' //
			});
		$('span.ez-toc-section')
			.waypoint( function( direction ) {
				// Highlight element when bottom of related content is 30px from the top - remove if less.
				var item = getRelatedNavigation( this.element ).toggleClass( 'active', direction === 'up' );
				item.toggleClass( 'active', direction === 'up' ).parent().toggleClass( 'active', direction === 'up' );
			}, {
				offset: getScrollOffset( this.element )
			});


		var div_height = $('.ez-toc-widget-container ul.ez-toc-list li').css('line-height');

		$('<style>.ez-toc-widget-container ul.ez-toc-list li::before{line-height:' + div_height + ';height:' + div_height + '}</style>').appendTo('head');
	}
} );
