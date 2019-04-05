<?php

class ezTOC_Post {

	/**
	 * @since 2.0
	 * @var int
	 */
	private $queriedObjectID;

	/**
	 * @since 2.0
	 * @var WP_Post
	 */
	private $post;

	/**
	 * @since 2.0
	 * @var false|string
	 */
	private $permalink;

	/**
	 * The post content broken into pages by user inserting `<!--nextpage-->` into the post content.
	 * @see ezTOC_Post::extractPages()
	 * @since 2.0
	 * @var array
	 */
	private $pages = array();

	/**
	 * The user defined heading levels to be included in the TOC.
	 * @see ezTOC_Post::getHeadingLevels()
	 * @since 2.0
	 * @var array
	 */
	private $headingLevels = array();

	/**
	 * The user defined strings to be used in the TOC in place of the post content headings.
	 * @see ezTOC_Post::getAlternateHeadings()
	 * @since 2.0
	 * @var array
	 */
	private $alternateHeadings = array();

	/**
	 * Keeps a track of used anchors for collision detecting.
	 * @see ezTOC_Post::generateHeadingIDFromTitle()
	 * @since 2.0
	 * @var array
	 */
	private $collision_collector = array();

	/**
	 * @var bool
	 */
	private $hasTOCItems = false;

	public function __construct( WP_Post $post ) {

		$this->post            = $post;
		$this->permalink       = get_permalink( $post );
		$this->queriedObjectID = get_queried_object_id();
	}

	/**
	 * @access public
	 * @since  2.0
	 *
	 * @param $id
	 *
	 * @return ezTOC_Post|null
	 */
	public static function get( $id ) {

		$post = get_post( $id );

		if ( ! $post instanceof WP_Post ) {

			return null;
		}

		return new static( $post );
	}

	/**
	 * Process post content for headings.
	 *
	 * This must be run after object init or after @see ezTOC_Post::applyContentFilter().
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return static
	 */
	public function process() {

		$this->processPages();

		return $this;
	}

	/**
	 * Apply `the_content` filter to the post content.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return static
	 */
	public function applyContentFilter() {

		/*
		 * Ensure the ezTOC content filter is not applied when running `the_content` filter.
		 */
		remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );

		/*
		 * Ensure the ezTOC shortcodes are not processed when applying `the_content` filter
		 * otherwise an infinite loop may occur.
		 */
		remove_shortcode( 'ez-toc' );
		remove_shortcode( 'toc' );

		$this->post->post_content = apply_filters( 'the_content', $this->post->post_content );

		add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );

		add_shortcode( 'ez-toc', array( 'ezTOC', 'shortcode' ) );
		add_shortcode( apply_filters( 'ez_toc_shortcode', 'toc' ), array( 'ezTOC', 'shortcode' ) );

		return $this;
	}

	/**
	 * This is a work around for theme's and plugins
	 * which break the WordPress global $wp_query var by unsetting it
	 * or overwriting it which breaks the method call
	 * that `get_query_var()` uses to return the query variable.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return int
	 */
	protected function getCurrentPage() {

		global $wp_query;

		// Check to see if the global `$wp_query` var is an instance of WP_Query and that the get() method is callable.
		// If it is then when can simply use the get_query_var() function.
		if ( $wp_query instanceof WP_Query && is_callable( array( $wp_query, 'get' ) ) ) {

			$page =  get_query_var( 'page', 1 );

			return 1 > $page ? 1 : $page;

			// If a theme or plugin broke the global `$wp_query` var, check to see if the $var was parsed and saved in $GLOBALS['wp_query']->query_vars.
		} elseif ( isset( $GLOBALS['wp_query']->query_vars[ 'page' ] ) ) {

			return $GLOBALS['wp_query']->query_vars[ 'page' ];

			// We should not reach this, but if we do, lets check the original parsed query vars in $GLOBALS['wp_the_query']->query_vars.
		} elseif ( isset( $GLOBALS['wp_the_query']->query_vars[ 'page' ] ) ) {

			return $GLOBALS['wp_the_query']->query_vars[ 'page' ];

			// Ok, if all else fails, check the $_REQUEST super global.
		} elseif ( isset( $_REQUEST[ 'page' ] ) ) {

			return $_REQUEST[ 'page' ];
		}

		// Finally, return the $default if it was supplied.
		return 1;
	}

	/**
	 * Get the number of page the post has.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return int
	 */
	protected function getNumberOfPages() {

		 return count( $this->pages );
	}

	/**
	 * Whether or not the post has multiple pages.
	 *
	 * @access protected
	 * @since  2.0
	 *
	 * @return bool
	 */
	protected function isMultipage() {

		return 1 < $this->getNumberOfPages();
	}

	/**
	 * Parse the post content and headings.
	 *
	 * @access private
	 * @since  2.0
	 */
	private function processPages() {

		$split = preg_split( '/<!--nextpage-->/msuU', $this->post->post_content );
		$pages = array();

		if ( is_array( $split ) ) {

			$page = 1;

			foreach ( $split as $content ) {

				$pages[ $page ] = array(
					'headings' => $this->extractHeadings( $content ),
					'content'  => $content,
				);

				$page++;
			}

		}

		$this->pages = $pages;
	}

	/**
	 * Get the post's parse content and headings.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array
	 */
	public function getPages() {

		return $this->pages;
	}

	/**
	 * Extract the posts content for headings.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param string $content
	 *
	 * @return array
	 */
	private function extractHeadings( $content ) {

		$matches = array();

		// reset the internal collision collection as the_content may have been triggered elsewhere
		// eg by themes or other plugins that need to read in content such as metadata fields in
		// the head html tag, or to provide descriptions to twitter/facebook
		/** @todo does this need to be used??? */
		//self::$collision_collector = array();

		$content = apply_filters( 'ez_toc_extract_headings_content', $content );

			// get all headings
			// the html spec allows for a maximum of 6 heading depths
		if ( preg_match_all( '/(<h([1-6]{1})[^>]*>)(.*)<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER ) ) {

			$minimum = absint( ezTOC_Option::get( 'start' ) );

			$this->removeHeadings( $matches );
			$this->excludeHeadings( $matches );
			$this->removeEmptyHeadings( $matches );

			if ( count( $matches ) >= $minimum ) {

				$this->alternateHeadings( $matches );
				$this->headingIDs( $matches );
				$this->hasTOCItems = true;

			} else {

				return array();
			}

		}

		return $matches;
	}

	/**
	 * Get the heading levels to be included in the TOC.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @return array
	 */
	private function getHeadingLevels() {

		$levels = get_post_meta( $this->post->ID, '_ez-toc-heading-levels', true );

		if ( ! is_array( $levels ) ) {

			$levels = array();
		}

		if ( empty( $levels ) ) {

			$levels = ezTOC_Option::get( 'heading_levels', array() );
		}

		$this->headingLevels = $levels;

		return $this->headingLevels;
	}

	/**
	 * Remove the heading levels as defined by user settings from the TOC heading matches.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function removeHeadings( &$matches ) {

		$levels = $this->getHeadingLevels();

		if ( count( $levels ) != 6 ) {

			$new_matches = array();
			$count       = count( $matches );

			for ( $i = 0; $i < $count; $i++ ) {

				if ( in_array( $matches[ $i ][2], $levels ) ) {

					$new_matches[] = $matches[ $i ];
				}
			}

			$matches = $new_matches;
		}

		return $matches;
	}

	/**
	 * Exclude the heading, by title, as defined by the user settings from the TOC matches.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array  $matches The headings from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function excludeHeadings( &$matches ) {

		$exclude = get_post_meta( $this->post->ID, '_ez-toc-exclude', true );

		if ( empty( $exclude ) ) {

			$exclude = ezTOC_Option::get( 'exclude' );
		}

		if ( $exclude ) {

			$excluded_headings = explode( '|', $exclude );
			$excluded_count    = count( $excluded_headings );

			if ( $excluded_count > 0 ) {

				for ( $j = 0; $j < $excluded_count; $j++ ) {

					$excluded_headings[ $j ] = preg_quote( $excluded_headings[ $j ] );

					// escape some regular expression characters
					// others: http://www.php.net/manual/en/regexp.reference.meta.php
					$excluded_headings[ $j ] = str_replace(
						array( '\*' ),
						array( '.*' ),
						trim( $excluded_headings[ $j ] )
					);
				}

				$new_matches = array();
				$count       = count( $matches );

				for ( $i = 0; $i < $count; $i++ ) {

					$found = false;

					for ( $j = 0; $j < $excluded_count; $j++ ) {

						// Since WP manipulates the post content it is required that the excluded header and
						// the actual header be manipulated similarly so a match can be made.
						$pattern = html_entity_decode(
							wptexturize( $excluded_headings[ $j ] ),
							ENT_NOQUOTES,
							get_option( 'blog_charset' )
						);

						$against = html_entity_decode(
							wptexturize( strip_tags( $matches[ $i ][0] ) ),
							ENT_NOQUOTES,
							get_option( 'blog_charset' )
						);

						if ( @preg_match( '/^' . $pattern . '$/imU', $against ) ) {

							$found = true;
							break;
						}
					}

					if ( ! $found ) {

						$new_matches[] = $matches[ $i ];
					}
				}

				if ( count( $matches ) != count( $new_matches ) ) {

					$matches = $new_matches;
				}
			}
		}

		return $matches;
	}

	/**
	 * Return the alternate headings added by the user, saved in the post meta.
	 *
	 * The result is an associative array where the `key` is the original post heading
	 * and the `value` is the alternate heading.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @return array
	 */
	private function getAlternateHeadings() {

		$value = get_post_meta( $this->post->ID, '_ez-toc-alttext', true );

		if ( $value ) {

			$alternates = array();
			$headings   = preg_split( '/\r\n|[\r\n]/', $value );
			$count      = count( $headings );

			if ( $headings ) {

				for ( $k = 0; $k < $count; $k++ ) {

					$heading = explode( '|', $headings[ $k ] );

					if ( 0 < strlen( $heading[0] ) && 0 < strlen( $heading[1] ) ) {

						$alternates[ $heading[0] ] = $heading[1];
					}
				}

			}

			$this->alternateHeadings = $alternates;
		}

		return $this->alternateHeadings;
	}

	/**
	 * Add the alternate headings to the array.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function alternateHeadings( &$matches ) {

		$alt_headings = $this->getAlternateHeadings();
		$count        = count( $matches );

		if ( 0 < count( $alt_headings ) ) {

			for ( $i = 0; $i < $count; $i++ ) {

				foreach ( $alt_headings as $original_heading => $alt_heading ) {

					$original_heading = preg_quote( $original_heading );

					// escape some regular expression characters
					// others: http://www.php.net/manual/en/regexp.reference.meta.php
					$original_heading = str_replace(
						array( '\*' ),
						array( '.*' ),
						trim( $original_heading )
					);

					if ( @preg_match( '/^' . $original_heading . '$/imU', strip_tags( $matches[ $i ][0] ) ) ) {

						$matches[ $i ]['alternate'] = $alt_heading;
					}
				}
			}
		}

		return $matches;
	}

	/**
	 * Add the heading `id` to the array.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return mixed
	 */
	private function headingIDs( &$matches ) {

		$count = count( $matches );

		for ( $i = 0; $i < $count; $i++ ) {

			$matches[ $i ]['id'] = $this->generateHeadingIDFromTitle( $matches[ $i ][0] );
		}

		return $matches;
	}

	/**
	 * Create unique heading ID from heading string.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param string $heading
	 *
	 * @return bool|string
	 */
	private function generateHeadingIDFromTitle( $heading ) {

		$return = false;

		if ( $heading ) {

			// WP entity encodes the post content.
			$return = html_entity_decode( $heading, ENT_QUOTES, get_option( 'blog_charset' ) );

			$return = trim( strip_tags( $return ) );

			// Convert accented characters to ASCII.
			$return = remove_accents( $return );

			// replace newlines with spaces (eg when headings are split over multiple lines)
			$return = str_replace( array( "\r", "\n", "\n\r", "\r\n" ), ' ', $return );

			// Remove `&amp;` and `&nbsp;` NOTE: in order to strip "hidden" `&nbsp;`,
			// title needs to be converted to HTML entities.
			// @link https://stackoverflow.com/a/21801444/5351316
			$return = htmlentities2( $return );
			$return = str_replace( array( '&amp;', '&nbsp;' ), ' ', $return );
			$return = html_entity_decode( $return, ENT_QUOTES, get_option( 'blog_charset' ) );

			// remove non alphanumeric chars
			$return = preg_replace( '/[^a-zA-Z0-9 \-_]*/', '', $return );

			// convert spaces to _
			$return = preg_replace( '/\s+/', '_', $return );

			// remove trailing - and _
			$return = rtrim( $return, '-_' );

			// lowercase everything?
			if ( ezTOC_Option::get( 'lowercase' ) ) {

				$return = strtolower( $return );
			}

			// if blank, then prepend with the fragment prefix
			// blank anchors normally appear on sites that don't use the latin charset
			if ( ! $return ) {

				$return = ( ezTOC_Option::get( 'fragment_prefix' ) ) ? ezTOC_Option::get( 'fragment_prefix' ) : '_';
			}

			// hyphenate?
			if ( ezTOC_Option::get( 'hyphenate' ) ) {

				$return = str_replace( '_', '-', $return );
				$return = str_replace( '--', '-', $return );
			}
		}

		if ( array_key_exists( $return, $this->collision_collector ) ) {

			$this->collision_collector[ $return ]++;
			$return .= '-' . $this->collision_collector[ $return ];

		} else {

			$this->collision_collector[ $return ] = 1;
		}

		return apply_filters( 'ez_toc_url_anchor_target', $return, $heading );
	}

	/**
	 * Remove any empty headings from the TOC.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return array
	 */
	private function removeEmptyHeadings( &$matches ) {

		$new_matches = array();
		$count       = count( $matches );

		for ( $i = 0; $i < $count; $i ++ ) {

			if ( trim( strip_tags( $matches[ $i ][0] ) ) != false ) {

				$new_matches[] = $matches[ $i ];
			}
		}

		if ( count( $matches ) != count( $new_matches ) ) {

			$matches = $new_matches;
		}

		return $matches;
	}

	/**
	 * Whether or not the post has TOC items.
	 *
	 * @see ezTOC_Post::extractHeadings()
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool
	 */
	public function hasTOCItems() {

		return $this->hasTOCItems;
	}

	/**
	 * Get the headings of the current page of the post.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|null $page
	 *
	 * @return array
	 */
	public function getHeadings( $page = null ) {

		$headings = array();

		if ( is_null( $page ) ) {

			$page = $this->getCurrentPage();
		}

		if ( isset( $this->pages[ $page ] ) ) {

			$headings = wp_list_pluck( $this->pages[ $page ]['headings'], 0 );
		}

		return $headings;
	}

	/**
	 * Get the heading with in page anchors of the current page of the post.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param int|null $page
	 *
	 * @return array
	 */
	public function getHeadingsWithAnchors( $page = null ) {

		$headings = array();

		if ( is_null( $page ) ) {

			$page = $this->getCurrentPage();
		}

		if ( isset( $this->pages[ $page ] ) ) {

			$matches = $this->pages[ $page ]['headings'];
			$count   = count( $matches );

			for ( $i = 0; $i < $count; $i++ ) {

				$anchor     = $matches[ $i ]['id'];
				$headings[] = str_replace(
					array(
						$matches[ $i ][1],                // start of heading
						'</h' . $matches[ $i ][2] . '>'   // end of heading
					),
					array(
						$matches[ $i ][1] . '<span class="ez-toc-section" id="' . $anchor . '">',
						'</span></h' . $matches[ $i ][2] . '>'
					),
					$matches[ $i ][0]
				);
			}
		}

		return $headings;
	}

	/**
	 * Get the post TOC list.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string
	 */
	public function getTOCList() {

		$html = '';

		if ( $this->hasTOCItems ) {

			foreach ( $this->pages as $page => $attribute ) {

				$html .= $this->createTOC( $page, $attribute['headings'] );
			}

			$html  = '<ul class="ez-toc-list">' . $html . '</ul>';
		}

		return $html;
	}

	/**
	 * Get the post TOC content block.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return string
	 */
	public function getTOC() {

		$css_classes = '';
		$html        = '';

		if ( $this->hasTOCItems() ) {

			// wrapping css classes
			switch ( ezTOC_Option::get( 'wrapping' ) ) {

				case 'left':
					$css_classes .= ' ez-toc-wrap-left';
					break;

				case 'right':
					$css_classes .= ' ez-toc-wrap-right';
					break;

				case 'none':
				default:
					// do nothing
			}

			if ( ezTOC_Option::get( 'show_hierarchy' ) ) {

				$css_classes .= ' counter-hierarchy';

			} else {

				$css_classes .= ' counter-flat';
			}

			switch ( ezTOC_Option::get( 'counter' ) ) {

				case 'numeric':
					$css_classes .= ' counter-numeric';
					break;

				case 'roman':
					$css_classes .= ' counter-roman';
					break;

				case 'decimal':
					$css_classes .= ' counter-decimal';
					break;
			}

			// colour themes
			switch ( ezTOC_Option::get( 'theme' ) ) {

				case 'light-blue':
					$css_classes .= ' ez-toc-light-blue';
					break;

				case 'white':
					$css_classes .= ' ez-toc-white';
					break;

				case 'black':
					$css_classes .= ' ez-toc-black';
					break;

				case 'transparent':
					$css_classes .= ' ez-toc-transparent';
					break;

				case 'grey':
					$css_classes .= ' ez-toc-grey';
					break;

				default:
					// do nothing
			}

			if ( ezTOC_Option::get( 'css_container_class' ) ) {

				$css_classes .= ' ' . ezTOC_Option::get( 'css_container_class' );
			}

			$css_classes = trim( $css_classes );

			// an empty class="" is invalid markup!
			if ( ! $css_classes ) {

				$css_classes = ' ';
			}

			// add container, toc title and list items
			$html .= '<div id="ez-toc-container" class="' . $css_classes . '">' . PHP_EOL;

			if ( ezTOC_Option::get( 'show_heading_text' ) ) {

				$toc_title = ezTOC_Option::get( 'heading_text' );

				if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== false ) {

					$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
				}

				if ( strpos( $toc_title, '%PAGE_NAME%' ) !== false ) {

					$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
				}

				$html .= '<div class="ez-toc-title-container">' . PHP_EOL;

				$html .= '<p class="ez-toc-title">' . esc_html__( htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ), 'easy-table-of-contents' ). '</p>' . PHP_EOL;

				$html .= '<span class="ez-toc-title-toggle">';

				if ( ezTOC_Option::get( 'visibility' ) ) {

					$html .= '<a class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle"><i class="ez-toc-glyphicon ez-toc-icon-toggle"></i></a>';
				}

				$html .= '</span>';

				$html .= '</div>' . PHP_EOL;
			}

			ob_start();
			do_action( 'ez_toc_before' );
			$html .= ob_get_clean();

			$html .= '<nav>' . $this->getTOCList() . '</nav>';

			ob_start();
			do_action( 'ez_toc_after' );
			$html .= ob_get_clean();

			$html .= '</div>' . PHP_EOL;

			// Enqueue the script.
			wp_enqueue_script( 'ez-toc-js' );
		}

		return $html;
	}

	/**
	 * Displays the post's TOC.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function toc() {

		echo $this->getTOC();
	}

	/**
	 * Generate the TOC list items for a given page within a post.
	 *
	 * @access private
	 * @since  2.0
	 *
	 * @param int   $page    The page of the post to create the TOC items for.
	 * @param array $matches The heading from the post content extracted with preg_match_all().
	 *
	 * @return string The HTML list of TOC items.
	 */
	private function createTOC( $page, $matches ) {

		// Whether or not the TOC should be built flat or hierarchical.
		$hierarchical = ezTOC_Option::get( 'show_hierarchy' );
		$html         = '';

		if ( $hierarchical ) {

			$current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
			$numbered_items     = array();
			$numbered_items_min = null;

			// reset the internal collision collection
			/** @todo does this need to be used??? */
			//self::$collision_collector = array();

			// find the minimum heading to establish our baseline
			for ( $i = 0; $i < count( $matches ); $i ++ ) {
				if ( $current_depth > $matches[ $i ][2] ) {
					$current_depth = (int) $matches[ $i ][2];
				}
			}

			$numbered_items[ $current_depth ] = 0;
			$numbered_items_min               = $current_depth;

			for ( $i = 0; $i < count( $matches ); $i ++ ) {

				if ( $current_depth == (int) $matches[ $i ][2] ) {

					$html .= '<li>';
				}

				// start lists
				if ( $current_depth != (int) $matches[ $i ][2] ) {

					for ( $current_depth; $current_depth < (int) $matches[ $i ][2]; $current_depth++ ) {

						$numbered_items[ $current_depth + 1 ] = 0;
						$html .= '<ul><li>';
					}
				}

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				$title = strip_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

				$html .= $this->createTOCItemAnchor( $page, $matches[ $i ]['id'], $title );

				// end lists
				if ( $i != count( $matches ) - 1 ) {

					if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {

						for ( $current_depth; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth-- ) {

							$html .= '</li></ul>';
							$numbered_items[ $current_depth ] = 0;
						}
					}

					if ( $current_depth == (int) @$matches[ $i + 1 ][2] ) {

						$html .= '</li>';
					}

				} else {

					// this is the last item, make sure we close off all tags
					for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {

						$html .= '</li>';

						if ( $current_depth != $numbered_items_min ) {
							$html .= '</ul>';
						}
					}
				}
			}

		} else {

			for ( $i = 0; $i < count( $matches ); $i++ ) {

				$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
				$title = strip_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

				$html .= '<li>';

				$html .= $this->createTOCItemAnchor( $page, $matches[ $i ]['id'], $title );

				$html .= '</li>';
			}
		}

		return $html;
	}

	/**
	 * @access private
	 * @since  2.0
	 *
	 * @param int    $page
	 * @param string $id
	 * @param string $title
	 *
	 * @return string
	 */
	private function createTOCItemAnchor( $page, $id, $title ) {

		return sprintf(
			'<a href="%1$s" title="%2$s">' . $title . '</a>',
			esc_url( $this->createTOCItemURL( $id, $page ) ),
			esc_attr( strip_tags( $title ) )
		);
	}

	/**
	 * @access private
	 * @since  2.0
	 *
	 * @param string $id
	 * @param int    $page
	 *
	 * @return string
	 */
	private function createTOCItemURL( $id, $page ) {

		$current_post = $this->post->ID === $this->queriedObjectID;
		$current_page = $this->getCurrentPage();

		if ( $page === $current_page && $current_post ) {

			return '#' . $id;

		} elseif ( 1 === $page ) {

			return $this->permalink . '#' . $id;

		}

		return $this->permalink . $page . '/#' . $id;
	}
}
