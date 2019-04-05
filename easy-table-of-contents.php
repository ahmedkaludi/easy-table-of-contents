<?php
/**
 * Plugin Name: Easy Table of Contents
 * Plugin URI: http://connections-pro.com/
 * Description: Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.
 * Version: 2.0-rc2
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
 * @version  2.0-rc2
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
		const VERSION = '2.0-rc2';

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

			require_once( EZ_TOC_PATH . 'includes/class.post.php' );
			require_once( EZ_TOC_PATH . 'includes/class.widget-toc.php' );
			require_once( EZ_TOC_PATH . 'includes/inc.functions.php' );
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
				load_plugin_textdomain( $domain, false, $languagesDirectory );
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

			wp_register_script( 'js-cookie', EZ_TOC_URL . "vendor/js-cookie/js.cookie$min.js", array(), '2.0.3', true );
			wp_register_script( 'jquery-smooth-scroll', EZ_TOC_URL . "vendor/smooth-scroll/jquery.smooth-scroll$min.js", array( 'jquery' ), '1.5.5', true );
			wp_register_script( 'jquery-sticky-kit', EZ_TOC_URL . "vendor/sticky-kit/jquery.sticky-kit$min.js", array( 'jquery' ), '1.9.2', true );
			wp_register_script( 'ez-toc-js', EZ_TOC_URL . "assets/js/front$min.js", array( 'jquery-smooth-scroll', 'js-cookie', 'jquery-sticky-kit'), ezTOC::VERSION, true );

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				wp_enqueue_style( 'ez-toc' );
				self::inlineCSS();
			}

			if ( ezTOC_Option::get( 'smooth_scroll' ) ) {

				$js_vars['smooth_scroll'] = true;
			}

			//wp_enqueue_script( 'ez-toc-js' );

			if ( ezTOC_Option::get( 'show_heading_text' ) && ezTOC_Option::get( 'visibility' ) ) {

				$width = ezTOC_Option::get( 'width' ) != 'custom' ? ezTOC_Option::get( 'width' ) : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );

				$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;

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
		private static function mb_find_replace( &$find = false, &$replace = false, &$string = '' ) {

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
					return true;
				}
			}

			return false;
		}

		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 *
		 * NOTE: Must bve use only within the loop.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return bool
		 */
		public static function is_eligible() {

			$post = get_post();

			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return false;
			}

			$content = get_the_content();

			if ( has_shortcode( $content, apply_filters( 'ez_toc_shortcode', 'toc' ) ) ||
			     has_shortcode( $content, 'ez-toc' ) ) {
				return true;
			}

			if ( is_front_page() && ! ezTOC_Option::get( 'include_homepage' ) ) {
				return false;
			}

			$type = get_post_type( $post->ID );

			$enabled = in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ) );
			$insert  = in_array( $type, ezTOC_Option::get( 'auto_insert_post_types', array() ) );

			if ( $insert || $enabled ) {

				if ( ezTOC_Option::get( 'restrict_path' ) ) {

					/**
					 * @link https://wordpress.org/support/topic/restrict-path-logic-does-not-work-correctly?
					 */
					if ( false !== strpos( ezTOC_Option::get( 'restrict_path' ), $_SERVER['REQUEST_URI'] ) ) {

						return false;

					} else {

						return true;
					}

				} else {

					if ( $insert && 1 == get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						return false;

					} elseif ( $insert && 0 == get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						return true;

					} elseif ( $enabled && 1 == get_post_meta( $post->ID, '_ez-toc-insert', true ) ) {

						return true;
					}

					return false;
				}

			} else {

				return false;
			}
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

			static $run = true;
			$out = '';

			if ( $run ) {

				$post = ezTOC_Post::get( get_the_ID() )->applyContentFilter()->process();
				$out  = $post->getTOC();

				$run  = false;
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

			if ( is_null( $post = ezTOC_Post::get( get_the_ID() ) ) ) {

				return $content;
			}

			$post->applyContentFilter()->process();

			$find    = $post->getHeadings();
			$replace = $post->getHeadingsWithAnchors();
			$html    = $post->getTOC();

			// bail if no headings found
			if ( ! $post->hasTOCItems() ) {

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
