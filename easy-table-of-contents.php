<?php
/**
 * Plugin Name: Easy Table of Contents
 * Plugin URI: http://connections-pro.com/
 * Description: Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.
 * Version: 1.7
 * Author: Steven A. Zahm
 * Author URI: http://connections-pro.com/
 * Text Domain: easy-table-of-contents
 * Domain Path: /languages
 *
 * Copyright 2018  Steven A. Zahm  ( email : helpdesk@connections-pro.com )
 *
 * Easy Table of Contents is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Table of Contents; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package  Easy Table of Contents
 * @category Plugin
 * @author   Steven A. Zahm
 * @version  1.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC' ) ) {

	/**
	 * Class ezTOC
	 */
	final class ezTOC {

		/**
		 * Current version.
		 *
		 * @since 1.0
		 * @var string
		 */
		const VERSION = '1.7';

		/**
		 * Stores the instance of this class.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @var ezTOC
		 */
		private static $instance;

		/**
		 * Keeps a track of used anchors for collision detecting.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @var array
		 */
		private static $collision_collector = array();

		/**
		 * A dummy constructor to prevent the class from being loaded more than once.
		 *
		 * @access public
		 * @since  1.0
		 */
		public function __construct() { /* Do nothing here */ }

		/**
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return ezTOC
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ezTOC ) ) {

				self::$instance = new ezTOC();

				self::defineConstants();
				self::includes();
				self::hooks();

				self::loadTextdomain();
			}

			return self::$instance;
		}

		/**
		 * Define the plugin constants.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function defineConstants() {

			define( 'EZ_TOC_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'EZ_TOC_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'EZ_TOC_PATH', plugin_dir_path( __FILE__ ) );
			define( 'EZ_TOC_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Includes the plugin dependency files.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function includes() {

			require_once( EZ_TOC_PATH . 'includes/class.options.php' );

			if ( is_admin() ) {

				// This must be included after `class.options.php` because it depends on it methods.
				require_once( EZ_TOC_PATH . 'includes/class.admin.php' );
			}

			require_once( EZ_TOC_PATH . 'includes/class.widget-toc.php' );
		}

		/**
		 * Add the core action filter hook.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function hooks() {

			//add_action( 'plugins_loaded', array( __CLASS__, 'loadTextdomain' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );

			// Run after shortcodes are interpreted (priority 10).
			add_filter( 'the_content', array( __CLASS__, 'the_content' ), 100 );
			add_shortcode( 'ez-toc', array( __CLASS__, 'shortcode' ) );
			add_shortcode( apply_filters( 'ez_toc_shortcode', 'toc' ), array( __CLASS__, 'shortcode' ) );
		}

		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 *
		 * @return void
		 */
		public static function loadTextdomain() {

			// Plugin textdomain. This should match the one set in the plugin header.
			$domain = 'easy-table-of-contents';

			// Set filter for plugin's languages directory
			$languagesDirectory = apply_filters( "ez_{$domain}_languages_directory", EZ_TOC_DIR_NAME . '/languages/' );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale', get_locale(), $domain );
			$fileName = sprintf( '%1$s-%2$s.mo', $domain, $locale );

			// Setup paths to current locale file
			$local  = $languagesDirectory . $fileName;
			$global = WP_LANG_DIR . "/{$domain}/" . $fileName;

			if ( file_exists( $global ) ) {

				// Look in global `../wp-content/languages/{$domain}/` folder.
				load_textdomain( $domain, $global );

			} elseif ( file_exists( $local ) ) {

				// Look in local `../wp-content/plugins/{plugin-directory}/languages/` folder.
				load_textdomain( $domain, $local );

			} else {

				// Load the default language files
				load_plugin_textdomain( $domain, FALSE, $languagesDirectory );
			}
		}

		/**
		 * Register and enqueue CSS and javascript files for frontend.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function enqueueScripts() {

			// If SCRIPT_DEBUG is set and TRUE load the non-minified JS files, otherwise, load the minified files.
			$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			$js_vars = array();

			wp_register_style( 'ez-icomoon', EZ_TOC_URL . "vendor/icomoon/style$min.css", array(), ezTOC::VERSION );
			wp_register_style( 'ez-toc', EZ_TOC_URL . "assets/css/screen$min.css", array( 'ez-icomoon' ), ezTOC::VERSION );

			wp_register_script( 'js-cookie', EZ_TOC_URL . "vendor/js-cookie/js.cookie$min.js", array(), '2.0.3', TRUE );
			wp_register_script( 'jquery-smooth-scroll', EZ_TOC_URL . "vendor/smooth-scroll/jquery.smooth-scroll$min.js", array( 'jquery' ), '1.5.5', TRUE );
			wp_register_script( 'jquery-sticky-kit', EZ_TOC_URL . "vendor/sticky-kit/jquery.sticky-kit$min.js", array( 'jquery' ), '1.9.2', TRUE );
			wp_register_script( 'jquery-waypoints', EZ_TOC_URL . "vendor/waypoints/jquery.waypoints$min.js", array( 'jquery' ), '1.9.2', TRUE );
			wp_register_script( 'ez-toc-js', EZ_TOC_URL . "assets/js/front$min.js", array( 'jquery-smooth-scroll', 'js-cookie', 'jquery-sticky-kit', 'jquery-waypoints' ), ezTOC::VERSION, TRUE );

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				wp_enqueue_style( 'ez-toc' );
				self::inlineCSS();
			}

			if ( ezTOC_Option::get( 'smooth_scroll' ) ) {

				$js_vars['smooth_scroll'] = TRUE;
			}

			//wp_enqueue_script( 'ez-toc-js' );

			if ( ezTOC_Option::get( 'show_heading_text' ) && ezTOC_Option::get( 'visibility' ) ) {

				$width = ezTOC_Option::get( 'width' ) != 'custom' ? ezTOC_Option::get( 'width' ) : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );

				$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? TRUE : FALSE;

				$js_vars['width'] = esc_js( $width );
			}

			$offset = wp_is_mobile() ? ezTOC_Option::get( 'mobile_smooth_scroll_offset', 0 ) : ezTOC_Option::get( 'smooth_scroll_offset', 30 );

			$js_vars['scroll_offset'] = esc_js( $offset );

			if ( ezTOC_Option::get( 'widget_affix_selector' ) ) {

				$js_vars['affixSelector'] = ezTOC_Option::get( 'widget_affix_selector' );
			}

			if ( 0 < count( $js_vars ) ) {

				wp_localize_script( 'ez-toc-js', 'ezTOC', $js_vars );
			}
		}

		/**
		 * Prints out inline CSS after the core CSS file to allow overriding core styles via options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function inlineCSS() {

			$css = '';

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				$css .= 'div#ez-toc-container p.ez-toc-title {font-size: ' . ezTOC_Option::get( 'title_font_size', 120 ) . ezTOC_Option::get( 'title_font_size_units', '%' ) . ';}';
				$css .= 'div#ez-toc-container p.ez-toc-title {font-weight: ' . ezTOC_Option::get( 'title_font_weight', 500 ) . ';}';
				$css .= 'div#ez-toc-container ul li {font-size: ' . ezTOC_Option::get( 'font_size' ) . ezTOC_Option::get( 'font_size_units' ) . ';}';

				if ( ezTOC_Option::get( 'theme' ) == 'custom' || ezTOC_Option::get( 'width' ) != 'auto' ) {

					$css .= 'div#ez-toc-container {';

					if ( ezTOC_Option::get( 'theme' ) == 'custom' ) {

						$css .= 'background: ' . ezTOC_Option::get( 'custom_background_colour' ) . ';border: 1px solid ' . ezTOC_Option::get( 'custom_border_colour' ) . ';';
					}

					if ( 'auto' != ezTOC_Option::get( 'width' ) ) {

						$css .= 'width: ';

						if ( 'custom' != ezTOC_Option::get( 'width' ) ) {

							$css .= ezTOC_Option::get( 'width' );

						} else {

							$css .= ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );
						}

						$css .= ';';
					}

					$css .= '}';
				}

				if ( 'custom' ==  ezTOC_Option::get( 'theme' ) ) {

					$css .= 'div#ez-toc-container p.ez-toc-title {color: ' . ezTOC_Option::get( 'custom_title_colour' ) . ';}';
					//$css .= 'div#ez-toc-container p.ez-toc-title a,div#ez-toc-container ul.ez-toc-list a {color: ' . ezTOC_Option::get( 'custom_link_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a {color: ' . ezTOC_Option::get( 'custom_link_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:hover {color: ' . ezTOC_Option::get( 'custom_link_hover_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:visited {color: ' . ezTOC_Option::get( 'custom_link_visited_colour' ) . ';}';
				}
			}

			if ( $css ) {

				wp_add_inline_style( 'ez-toc', $css );
			}
		}

		/**
		 * Returns a URL to be used as the destination anchor target.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param string $title
		 *
		 * @return bool|string
		 */
		private static function url_anchor_target( $title ) {

			$return = FALSE;

			if ( $title ) {

				// WP entity encodes the post content.
				$return = html_entity_decode( $title, ENT_QUOTES, get_option( 'blog_charset' ) );

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

			if ( array_key_exists( $return, self::$collision_collector ) ) {

				self::$collision_collector[ $return ]++;
				$return .= '-' . self::$collision_collector[ $return ];

			} else {

				self::$collision_collector[ $return ] = 1;
			}

			return apply_filters( 'ez_toc_url_anchor_target', $return, $title );
		}

		/**
		 * Generates a nested unordered list for the table of contents.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param array $matches
		 * @param array $headings Array of headers to be considered for a TOC item.
		 *
		 * @return string
		 */
		private static function build_hierarchy( &$matches, $headings ) {

			$current_depth      = 100;    // headings can't be larger than h6 but 100 as a default to be sure
			$html               = '';
			$numbered_items     = array();
			$numbered_items_min = NULL;

			// reset the internal collision collection
			self::$collision_collector = array();

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

				// list item
				if ( in_array( $matches[ $i ][2], $headings ) ) {

					//$title = apply_filters( 'ez_toc_title', strip_tags( wp_kses_post( $matches[ $i ][0] ) ) );
					$title = strip_tags( apply_filters( 'ez_toc_title', $matches[ $i ][0] ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

					//$html .= '<a href="#' . self::url_anchor_target( $title ) . '">';
					$html .= sprintf(
						'<a href="%1$s" title="%2$s">',
						esc_url( '#' . self::url_anchor_target( $matches[ $i ][0] ) ),
						esc_attr( strip_tags( $title ) )
					);

					//if ( 'decimal' == ezTOC_Option::get( 'counter' ) ) {
					//
					//	// attach leading numbers when lower in hierarchy
					//	$html .= '<span class="ez-toc-number ez-toc-depth_' . ( $current_depth - $numbered_items_min + 1 ) . '">';
					//
					//	for ( $j = $numbered_items_min; $j < $current_depth; $j ++ ) {
					//
					//		$number = ( $numbered_items[ $j ] ) ? $numbered_items[ $j ] : 0;
					//		$html .= $number . '.';
					//	}
					//
					//	$html .= ( $numbered_items[ $current_depth ] + 1 ) . '</span> ';
					//	$numbered_items[ $current_depth ] ++;
					//}

					$html .= $title . '</a>';
				}

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
					for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth -- ) {

						$html .= '</li>';

						if ( $current_depth != $numbered_items_min ) {
							$html .= '</ul>';
						}
					}
				}
			}

			return $html;
		}

		/**
		 * Returns a string with all items from the $find array replaced with their matching
		 * items in the $replace array.  This does a one to one replacement (rather than globally).
		 *
		 * This function is multibyte safe.
		 *
		 * $find and $replace are arrays, $string is the haystack.  All variables are passed by reference.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param bool   $find
		 * @param bool   $replace
		 * @param string $string
		 *
		 * @return mixed|string
		 */
		private static function mb_find_replace( &$find = FALSE, &$replace = FALSE, &$string = '' ) {

			if ( is_array( $find ) && is_array( $replace ) && $string ) {

				// check if multibyte strings are supported
				if ( function_exists( 'mb_strpos' ) ) {

					for ( $i = 0; $i < count( $find ); $i ++ ) {

						$string = mb_substr(
							          $string,
							          0,
							          mb_strpos( $string, $find[ $i ] )
						          ) .    // everything before $find
						          $replace[ $i ] . // its replacement
						          mb_substr(
							          $string,
							          mb_strpos( $string, $find[ $i ] ) + mb_strlen( $find[ $i ] )
						          )    // everything after $find
						;
					}

				} else {

					for ( $i = 0; $i < count( $find ); $i ++ ) {

						$string = substr_replace(
							$string,
							$replace[ $i ],
							strpos( $string, $find[ $i ] ),
							strlen( $find[ $i ] )
						);
					}
				}
			}

			return $string;
		}

		/**
		 * This function extracts headings from the html formatted $content.  It will pull out
		 * only the required headings as specified in the options.  For all qualifying headings,
		 * this function populates the $find and $replace arrays (both passed by reference)
		 * with what to search and replace with.
		 *
		 * Returns a HTML formatted string of list items for each qualifying heading.  This
		 * is everything between and NOT including <ul> and </ul>
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param array   $find
		 * @param array   $replace
		 * @param WP_Post $post
		 *
		 * @return bool|string
		 */
		public static function extract_headings( &$find, &$replace, $post ) {

			$matches = array();
			$anchor  = '';
			$items   = '';

			$headings = get_post_meta( $post->ID, '_ez-toc-heading-levels', TRUE );
			$exclude  = get_post_meta( $post->ID, '_ez-toc-exclude', TRUE );
			$altText  = get_post_meta( $post->ID, '_ez-toc-alttext', TRUE );

			if ( ! is_array( $headings ) ) {

				$headings = array();
			}

			if ( empty( $headings ) ) {

				$headings = ezTOC_Option::get( 'heading_levels', array() );
			}

			if ( empty( $exclude ) ) {

				$exclude = ezTOC_Option::get( 'exclude' );
			}

			// reset the internal collision collection as the_content may have been triggered elsewhere
			// eg by themes or other plugins that need to read in content such as metadata fields in
			// the head html tag, or to provide descriptions to twitter/facebook
			self::$collision_collector = array();

			$content = apply_filters( 'ez_toc_extract_headings_content', $post->post_content );

			if ( is_array( $find ) && is_array( $replace ) && $content ) {

				// get all headings
				// the html spec allows for a maximum of 6 heading depths
				if ( preg_match_all( '/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER ) ) {

					// remove undesired headings (if any) as defined by heading_levels
					if ( count( $headings ) != 6 ) {

						$new_matches = array();

						for ( $i = 0; $i < count( $matches ); $i ++ ) {

							if ( in_array( $matches[ $i ][2], $headings ) ) {

								$new_matches[] = $matches[ $i ];
							}
						}
						$matches = $new_matches;
					}

					// remove specific headings if provided via the 'exclude' property
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

							for ( $i = 0; $i < count( $matches ); $i++ ) {

								$found = FALSE;

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

										$found = TRUE;
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

					// remove empty headings
					$new_matches = array();

					for ( $i = 0; $i < count( $matches ); $i ++ ) {

						if ( trim( strip_tags( $matches[ $i ][0] ) ) != FALSE ) {

							$new_matches[] = $matches[ $i ];
						}
					}

					if ( count( $matches ) != count( $new_matches ) ) {

						$matches = $new_matches;
					}

					$toc = $matches;

					// Replace headers with toc alt text.
					if ( $altText ) {

						$alt_headings         = array();
						$split_headings       = preg_split( '/\r\n|[\r\n]/', $altText );
						$split_headings_count = count( $split_headings );

						if ( $split_headings ) {

							for ( $k = 0; $k < $split_headings_count; $k++ ) {

								$explode_headings = explode( '|', $split_headings[ $k ] );

								if ( 0 < strlen( $explode_headings[0] ) && 0 < strlen( $explode_headings[1] ) ) {

									$alt_headings[ $explode_headings[0] ] = $explode_headings[1];
								}
							}

						}

						if ( 0 <  count( $alt_headings ) ) {

							for ( $i = 0; $i < count( $toc ); $i++ ) {

								foreach ( $alt_headings as $original_heading => $alt_heading ) {

									$original_heading = preg_quote( $original_heading );

									// escape some regular expression characters
									// others: http://www.php.net/manual/en/regexp.reference.meta.php
									$original_heading = str_replace(
										array( '\*' ),
										array( '.*' ),
										trim( $original_heading )
									);

									if ( @preg_match( '/^' . $original_heading . '$/imU', strip_tags( $toc[ $i ][0] ) ) ) {

										//$matches[ $i ][0] = str_replace( $original_heading, $alt_heading, $matches[ $i ][0] );
										$toc[ $i ][0] = $alt_heading;
									}
								}
							}
						}
					}

					// check minimum number of headings
					if ( count( $matches ) >= ezTOC_Option::get( 'start' ) ) {

						for ( $i = 0; $i < count( $matches ); $i++ ) {

							// get anchor and add to find and replace arrays
							$anchor    = isset( $toc[ $i ][0] ) ? self::url_anchor_target( $toc[ $i ][0] ) : self::url_anchor_target( $matches[ $i ][0] );
							$find[]    = $matches[ $i ][0];
							$replace[] = str_replace(
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

							// assemble flat list
							if ( ! ezTOC_Option::get( 'show_hierarchy' ) ) {

								$items .= '<li><a href="' . esc_url( '#' . $anchor ) . '">';
								//$title  = apply_filters( 'ez_toc_title', strip_tags( wp_kses_post( $toc[ $i ][0] ) ) );
								$title = strip_tags( apply_filters( 'ez_toc_title', $matches[ $i ][0] ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );

								//if ( 'decimal' == ezTOC_Option::get( 'counter' ) ) {
								//
								//	$items .= count( $replace ) . ' ';
								//}

								$items .= $title . '</a></li>';
							}
						}

						// build a hierarchical toc?
						// we could have tested for $items but that var can be quite large in some cases
						if ( ezTOC_Option::get( 'show_hierarchy' ) ) {

							$items = self::build_hierarchy( $toc, $headings );
						}

					}
				}
			}

			return $items;
		}

		/**
		 * Array search deep.
		 *
		 * Search an array recursively for a value.
		 *
		 * @link https://stackoverflow.com/a/5427665/5351316
		 *
		 * @param        $search
		 * @param array  $array
		 * @param string $mode
		 *
		 * @return bool
		 */
		public static function array_search_deep( $search, array $array, $mode = 'value' ) {

			foreach ( new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) ) as $key => $value ) {

				if ( $search === ${${"mode"}} ) {
					return TRUE;
				}
			}

			return FALSE;
		}

		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return bool
		 */
		public static function is_eligible() {

			global $wp_query;

			$post = $wp_query->post;

			if ( empty( $post ) ) {
				return FALSE;
			}

			if ( has_shortcode( $post->post_content, 'toc' ) || has_shortcode( $post->post_content, 'ez-toc' ) ) {
				return TRUE;
			}

			if ( is_front_page() && ! ezTOC_Option::get( 'include_homepage' ) ) {
				return FALSE;
			}

			$type = get_post_type( $post->ID );

			$enabled = in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ) );
			$insert  = in_array( $type, ezTOC_Option::get( 'auto_insert_post_types', array() ) );

			if ( $insert || $enabled ) {

				if ( ezTOC_Option::get( 'restrict_path' ) ) {

					//if ( strpos( $_SERVER['REQUEST_URI'], ezTOC_Option::get( 'restrict_path' ) ) === 0 ) {
					//
					//	return TRUE;
					//
					//} else {
					//
					//	return FALSE;
					//}

					/**
					 * @link https://wordpress.org/support/topic/restrict-path-logic-does-not-work-correctly?
					 */
					if ( FALSE !== strpos( ezTOC_Option::get( 'restrict_path' ), $_SERVER['REQUEST_URI'] ) ) {

						return FALSE;

					} else {

						return TRUE;
					}

				} else {

					if ( $insert && 1 == get_post_meta( $post->ID, '_ez-toc-disabled', TRUE ) ) {

						return FALSE;

					} elseif ( $insert && 0 == get_post_meta( $post->ID, '_ez-toc-disabled', TRUE ) ) {

						return TRUE;

					} elseif ( $enabled && 1 == get_post_meta( $post->ID, '_ez-toc-insert', TRUE ) ) {

						return TRUE;
					}

					return FALSE;
					//return TRUE;
				}

			} else {

				return FALSE;
			}
		}

		/**
		 * Build the table of contents.
		 *
		 * @access private
		 * @since  1.3
		 * @static
		 *
		 * @param WP_Post $post The page/post content.
		 *
		 * @return array
		 */
		public static function build( $post ) {

			$css_classes = '';

			$html    = '';
			$find    = array();
			$replace = array();
			$items   = self::extract_headings( $find, $replace, $post );

			if ( $items ) {

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

					if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== FALSE ) {

						$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
					}

					if ( strpos( $toc_title, '%PAGE_NAME%' ) !== FALSE ) {

						$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
					}

					$html .= '<div class="ez-toc-title-container">' . PHP_EOL;

					$html .= '<p class="ez-toc-title">' . esc_html( htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ) ). '</p>' . PHP_EOL;

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

				$html .= '<nav><ul class="ez-toc-list">' . $items . '</ul></nav>';

				ob_start();
				do_action( 'ez_toc_after' );
				$html .= ob_get_clean();

				$html .= '</div>' . PHP_EOL;

				// Enqueue the script.
				wp_enqueue_script( 'ez-toc-js' );
			}

			return array( 'find' => $find, 'replace' => $replace, 'content' => $html );
		}

		/**
		 * Callback for the registered shortcode `[ez-toc]`
		 *
		 * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
		 *
		 * @access private
		 * @since  1.3
		 * @static
		 *
		 * @param array|string $atts    Shortcode attributes array or empty string.
		 * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
		 * @param string       $tag     Shortcode name.
		 *
		 * @return mixed
		 */
		public static function shortcode( $atts, $content, $tag ) {

			static $run = TRUE;
			$out = '';

			if ( $run ) {

				$args = self::build( get_post( get_the_ID() ) );
				$out  = $args['content'];
				$run  = FALSE;
			}

			return $out;
		}

		/**
		 * Callback for the `the_content` filter.
		 *
		 * This will add the inline table of contents page anchors to the post content. It will also insert the
		 * table of contents inline with the post content as defined by the user defined preference.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public static function the_content( $content ) {

			// bail if feed, search or archive
			if ( is_feed() || is_search() || is_archive() ) {
				return $content;
			}

			// bail if post not eligible and widget is not active
			$is_eligible = self::is_eligible();

			if ( ! $is_eligible && ! is_active_widget( false, false, 'ezw_tco' ) ) {

				return $content;
			}

			/*
			 * get_post() does not return post_content filtered via `the_content` filter, which is good otherwise this
			 * might cause an infinite loop.
			 *
			 * Since the ezTOC `the_content` filter is added at priority 100, it should run last in most situations
			 * and already be filtered by other plugins/themes which ezTOC should take into account when building the
			 * TOC. So, take the post content past via `the_content` filter callback and replace the post_content with
			 * it before building the TOC.
			 */
			$post = get_post( get_the_ID() );
			$post->post_content = $content;

			// build toc
			$args    = self::build( $post );
			$find    = $args['find'];
			$replace = $args['replace'];
			$html    = $args['content'];

			// bail if no headings found
			if ( empty( $find ) ) {

				return $content;
			}

			// if shortcode used or post not eligible, return content with anchored headings
			if ( strpos( $content, 'ez-toc-container' ) || ! $is_eligible ) {

				return self::mb_find_replace( $find, $replace, $content );
			}

			// else also add toc to content
			switch ( ezTOC_Option::get( 'position' ) ) {

				case 'top':
					$content = $html . self::mb_find_replace( $find, $replace, $content );
					break;

				case 'bottom':
					$content = self::mb_find_replace( $find, $replace, $content ) . $html;
					break;

				case 'after':
					$replace[0] = $replace[0] . $html;
					$content    = self::mb_find_replace( $find, $replace, $content );
					break;

				case 'before':
				default:
					$replace[0] = $html . $replace[0];
					$content    = self::mb_find_replace( $find, $replace, $content );
			}

			return $content;
		}

	} // end class

	/**
	 * The main function responsible for returning the Easy Table of Contents instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: <?php $instance = ezTOC(); ?>
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return ezTOC
	 */
	function ezTOC() {

		return ezTOC::instance();
	}

	// Start Easy Table of Contents.
	add_action( 'plugins_loaded', 'ezTOC' );
}


/**
 * Returns a HTML formatted string of the table of contents without the surrounding UL or OL
 * tags to enable the theme editor to supply their own ID and/or classes to the outer list.
 *
 * There are three optional parameters you can feed this function with:
 *
 *        - $content is the entire content with headings.  If blank, will default to the current $post
 *
 *        - $link is the URL to prefix the anchor with.  If provided a string, will use it as the prefix.
 *        If set to true then will try to obtain the permalink from the $post object.
 *
 *        - $apply_eligibility bool, defaults to false.  When set to true, will apply the check to
 *        see if bit of content has the prerequisites needed for a TOC, eg minimum number of headings
 *        enabled post type, etc.
 */
//function toc_get_index( $content = '', $prefix_url = '', $apply_eligibility = FALSE ) {
//
//	global $wp_query, $tic;
//
//	$return  = '';
//	$find    = $replace = array();
//	$proceed = TRUE;
//
//	if ( ! $content ) {
//		$post    = get_post( $wp_query->post->ID );
//		$content = wptexturize( $post->post_content );
//	}
//
//	if ( $apply_eligibility ) {
//		if ( ! $tic->is_eligible() ) {
//			$proceed = FALSE;
//		}
//	} else {
//		$tic->set_option( array( 'start' => 0 ) );
//	}
//
//	if ( $proceed ) {
//		$return = $tic->extract_headings( $find, $replace, $content );
//		if ( $prefix_url ) {
//			$return = str_replace( 'href="#', 'href="' . $prefix_url . '#', $return );
//		}
//	}
//
//	return $return;
//}
