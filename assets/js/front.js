jQuery( function( $ ) {

	/**
	 * @typedef ezTOC
	 * @type {Object} ezTOC
	 * @property {string} affixSelector
	 * @property {string} scroll_offset
	 * @property {string} smooth_scroll
	 * @property {string} visibility_hide_by_default
	 */

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

				affixOffset = parseInt( ezTOC.scroll_offset );
			}

			$( ezTOC.affixSelector ).stick_in_parent( {
				inner_scrolling: false,
				offset_top:      affixOffset
			} )
		}

		$.fn.shrinkTOCWidth = function() {

			$( this ).css( {
				width:   'auto',
				display: 'table'
			});

			if ( /MSIE 7\./.test( navigator.userAgent ) )
				$( this ).css( 'width', '' );
		};

		var smoothScroll = parseInt( ezTOC.smooth_scroll );

		if ( 1 === smoothScroll ) {

			$( 'a.ez-toc-link' ).on( 'click', function() {

				var self = $( this );

				var target = '';
				var hostname = self.prop( 'hostname' );
				var pathname = self.prop( 'pathname' );
				var qs = self.prop( 'search' );
				var hash = self.prop( 'hash' );

				// ie strips out the preceding / from pathname
				if ( pathname.length > 0 ) {
					if ( pathname.charAt( 0 ) !== '/' ) {
						pathname = '/' + pathname;
					}
				}

				if ( ( window.location.hostname === hostname ) &&
					( window.location.pathname === pathname ) &&
					( window.location.search === qs ) &&
					( hash !== '' )
				) {

					// var id = decodeURIComponent( hash.replace( '#', '' ) );
					target = '[id="' + hash.replace( '#', '' ) + '"]';

					// verify it exists
					if ( $( target ).length === 0 ) {
						console.log( 'ezTOC scrollTarget Not Found: ' + target );
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
							offset:       offset,
                            beforeScroll: deactivateSetActiveEzTocListElement,
                            afterScroll: function() { setActiveEzTocListElement(); activateSetActiveEzTocListElement(); }
						} );

					}
				}
			} );
		}

		if ( typeof ezTOC.visibility_hide_by_default != 'undefined' ) {

			var toc = $( 'ul.ez-toc-list' );
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

				toc.hide();
			}

			toggle.on( 'click', function( event ) {

				event.preventDefault();

				if ( $( this ).data( 'visible' ) ) {

					$( this ).data( 'visible', false );

					if ( Cookies ) {

						if ( invert )
							Cookies.set( 'ezTOC_hidetoc', null, { path: '/' } );
						else
							Cookies.set( 'ezTOC_hidetoc', '1', { expires: 30, path: '/' } );
					}

					toc.hide( 'fast' );

				} else {

					$( this ).data( 'visible', true );

					if ( Cookies ) {

						if ( invert )
							Cookies.set( 'ezTOC_hidetoc', '1', { expires: 30, path: '/' } );
						else
							Cookies.set( 'ezTOC_hidetoc', null, { path: '/' } );
					}

					toc.show( 'fast' );

				}

			} );
		}


        // ======================================
        // Set active heading in ez-toc-widget list
        // ======================================

        var headings = $( 'span.ez-toc-section' ).toArray();
        var headingToListElementLinkMap = getHeadingToListElementLinkMap( headings );
        var listElementLinks = $.map( headingToListElementLinkMap, function ( value, key ) {
            return value
        } );
        var scrollOffset = getScrollOffset();

        activateSetActiveEzTocListElement();

        function setActiveEzTocListElement() {
            var activeHeading = getActiveHeading( scrollOffset, headings );
            if ( activeHeading ) {
                var activeListElementLink = headingToListElementLinkMap[ activeHeading.id ];
                removeStyleFromNonActiveListElement( activeListElementLink, listElementLinks );
                setStyleForActiveListElementElement( activeListElementLink );
            }
        }

        function activateSetActiveEzTocListElement() {
            if ( headings.length > 0 && $('.ez-toc-widget-container').length) {
                $( window ).on( 'load resize scroll', setActiveEzTocListElement );
            }
        }

        function deactivateSetActiveEzTocListElement() {
            $( window ).off( 'load resize scroll', setActiveEzTocListElement );
        }

        function getEzTocListElementLinkByHeading( heading ) {
            return $( '.ez-toc-widget-container .ez-toc-list a[href="#' + $( heading ).attr( 'id' ) + '"]' );
        }

        function getHeadingToListElementLinkMap( headings ) {
            return headings.reduce( function ( map, heading ) {
                map[ heading.id ] = getEzTocListElementLinkByHeading( heading );
                return map;
            }, {} );
        }

        function getScrollOffset() {
            var scrollOffset = 5; // so if smooth offset is off, the correct title is set as active
            if ( typeof ezTOC.smooth_scroll != 'undefined' && parseInt( ezTOC.smooth_scroll ) === 1 ) {
                scrollOffset = ( typeof ezTOC.scroll_offset != 'undefined' ) ? parseInt( ezTOC.scroll_offset ) : 30;
            }

            var adminbar = $( '#wpadminbar' );

            if ( adminbar.length ) {
                scrollOffset += adminbar.height();
            }
            return scrollOffset;
        }

        function getActiveHeading( topOffset, headings ) {
            var scrollTop = $( window ).scrollTop();
            var relevantOffset = scrollTop + topOffset + 1;
            var activeHeading = headings[ 0 ];
            var closestHeadingAboveOffset = relevantOffset - $( activeHeading ).offset().top;
            headings.forEach( function ( section ) {
                var topOffset = relevantOffset - $( section ).offset().top;
                if ( topOffset > 0 && topOffset < closestHeadingAboveOffset ) {
                    closestHeadingAboveOffset = topOffset;
                    activeHeading = section;
                }
            } );
            return activeHeading;
        }

        function removeStyleFromNonActiveListElement( activeListElementLink, listElementLinks ) {
            listElementLinks.forEach( function ( listElementLink ) {
                if ( activeListElementLink !== listElementLink && listElementLink.parent().hasClass( 'active' ) ) {
                    listElementLink.parent().removeClass( 'active' );
                }
            } );
        }

        function correctActiveListElementBackgroundColorHeight( activeListElement ) {
            var listElementHeight = getListElementHeightWithoutUlChildren( activeListElement );
            addListElementBackgroundColorHeightStyleToHead( listElementHeight );
        }

        function getListElementHeightWithoutUlChildren( listElement ) {
            var $listElement = $( listElement );
            var content = $listElement.html();
            // Adding list item with class '.active' to get the real height.
            // When adding a class to an existing element and using jQuery(..).height() directly afterwards,
            // the height is the 'old' height. The height might change due to text-wraps when setting the text-weight bold for example
            // When adding a new item, the height is calculated correctly.
            // But only when it might be visible (so display:none; is not possible...)
            // But because it get's directly removed afterwards it never will be rendered by the browser
            // (at least in my tests in FF, Chrome, IE11 and Edge)
            $listElement.parent().append( '<li id="ez-toc-height-test" class="active">' + content + '</li>' );
            var listItem = $( '#ez-toc-height-test' );
            var height = listItem.height();
	        listItem.remove();
            return height - $listElement.children( 'ul' ).first().height();
        }

        function addListElementBackgroundColorHeightStyleToHead( listElementHeight ) {
            // Remove existing
            $( '#ez-toc-active-height' ).remove();
            // jQuery(..).css(..) doesn't work, because ::before is a pseudo element and not part of the DOM
            // Workaround is to add it to head
            $( '<style id="ez-toc-active-height">' +
                '.ez-toc-widget-container ul.ez-toc-list li.active::before {' +
                // 'line-heigh:' + listElementHeight + 'px; ' +
                'height:' + listElementHeight + 'px;' +
                '} </style>' )
                .appendTo( 'head' );
        }

        function setStyleForActiveListElementElement( activeListElementLink ) {
            var activeListElement = activeListElementLink.parent();
            if ( !activeListElement.hasClass( 'active' ) ) {
                activeListElement.addClass( 'active' );
            }
            correctActiveListElementBackgroundColorHeight( activeListElement );
        }
    }
} );
