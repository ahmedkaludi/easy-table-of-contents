<?php
/**
 * Plugin Name: Easy Table of Contents
 * Plugin URI: https://tocwp.com/
 * Description: Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.
 * Version: 2.0.48
 * Author: Magazine3
 * Author URI: https://tocwp.com/
 * Text Domain: easy-table-of-contents
 * Domain Path: /languages
 *
 * Copyright 2022  Magazine3  ( email : team@magazine3.in )
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
 * @author   Magazine3
 * @version  2.0.48
 */

use Easy_Plugins\Table_Of_Contents\Debug;
use function Easy_Plugins\Table_Of_Contents\String\insertElementByPTag;
use function Easy_Plugins\Table_Of_Contents\String\mb_find_replace;

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
		const VERSION = '2.0.48';

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
		 * @since 2.0
		 * @var array
		 */
		private static $store = array();

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

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

				self::$instance = new self;

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
			define( 'EZ_TOC_PATH', dirname( __FILE__ ) );
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

			require_once( EZ_TOC_PATH . '/includes/class.options.php' );

			if ( is_admin() ) {

				// This must be included after `class.options.php` because it depends on it methods.
				require_once( EZ_TOC_PATH . '/includes/class.admin.php' );
				require_once(EZ_TOC_PATH. "/includes/helper-function.php" );
				require_once( EZ_TOC_PATH . '/includes/newsletter.php' );
			}

			require_once( EZ_TOC_PATH . '/includes/class.post.php' );
                        require_once( EZ_TOC_PATH . '/includes/class.widget-toc.php' );
			require_once( EZ_TOC_PATH . '/includes/class.widget-toc-sticky.php' );
			require_once( EZ_TOC_PATH . '/includes/Debug.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.functions.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.string-functions.php' );

			require_once( EZ_TOC_PATH . '/includes/inc.plugin-compatibility.php' );
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
			add_option('ez-toc-shortcode-exist-and-render', false);
                        if ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Pale Moon' == ez_toc_get_browser_name() || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
				add_option( 'ez-toc-post-content-core-level', false );
			}
                        
                        add_option( 'ez-toc-list', '' );
                        add_action('admin_head', array( __CLASS__, 'addEditorButton' ));
//                        if( false === strpos( $_SERVER['REQUEST_URI'], "/edit.php" ) ) {
                            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );
                            if ( ezTOC_Option::get( 'exclude_css' ) && 'css' == ezTOC_Option::get( 'toc_loading' ) ) {
                                add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScriptsforExcludeCSS' ) );
                            }

                            if( !self::checkBeaverBuilderPluginActive() ) {
                                    add_filter( 'the_content', array( __CLASS__, 'the_content' ), 100 );
                                    add_filter( 'category_description',  array( __CLASS__, 'toc_category_content_filter' ), 99,2);
                                    add_shortcode( 'ez-toc', array( __CLASS__, 'shortcode' ) );
                                    add_shortcode( 'lwptoc', array( __CLASS__, 'shortcode' ) );
                                    add_shortcode( apply_filters( 'ez_toc_shortcode', 'toc' ), array( __CLASS__, 'shortcode' ) );

                                    add_shortcode( 'ez-toc-widget-sticky', array( __CLASS__, 'ez_toc_widget_sticky_shortcode' ) );

                            }
//                        }
		}
	
                
        /**
	 * enqueueScriptsforExcludeCSS Method
	 * for adding toggle css on loading as CSS
	 * @access public
	 * @since  2.0.40
         * @static
	 */
        public static function enqueueScriptsforExcludeCSS()
        {
                                
            $cssChecked = '#ez-toc-container input[type="checkbox"]:checked + nav, #ez-toc-widget-container input[type="checkbox"]:checked + nav {opacity: 0;max-height: 0;border: none;display: none;}';
            wp_register_style( 'ez-toc-exclude-toggle-css', '', array(), ezTOC::VERSION );
            wp_enqueue_style( 'ez-toc-exclude-toggle-css', '', array(), ezTOC::VERSION );
            wp_add_inline_style( 'ez-toc-exclude-toggle-css', $cssChecked );
        }
        
		/**
         * checkBeaverBuilderPluginActive Method
         * @since 2.0.34
		 * @return bool
		 */
		private static function checkBeaverBuilderPluginActive() {
			if( has_action( 'the_content' ) && isset($_REQUEST['fl_builder'])) {
				return true;
			}
			return false;
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
		 * Call back for the `wp_enqueue_scripts` action.
		 *
		 * Register and enqueue CSS and javascript files for frontend.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function enqueueScripts() {
			$eztoc_post_id = get_the_ID();
			// If SCRIPT_DEBUG is set and TRUE load the non-minified JS files, otherwise, load the minified files.
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$js_vars = array();

			if ( in_array( 'js_composer_salient/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				
				$postMetaContent = get_post_meta( $eztoc_post_id, '_nectar_portfolio_extra_content',true );
				if( !empty( $postMetaContent ) ){
					update_option( 'ez-toc-post-meta-content', array( $eztoc_post_id => do_shortcode( $postMetaContent ) ) );
				}
			}

			$isEligible = self::is_eligible( get_post() );

			if ( ! $isEligible && ! is_active_widget( false, false, 'ezw_tco' ) && ! get_option( 'ez-toc-shortcode-exist-and-render' ) && ! is_active_widget( false, false, 'ez_toc_widget_sticky' ) && !get_post_meta( $eztoc_post_id, '_nectar_portfolio_extra_content',true )) {
                return false;
			}

			if (!ezTOC_Option::get( 'inline_css' )) {
				wp_register_style( 'ez-toc', EZ_TOC_URL . "assets/css/screen$min.css",
				 array( ),
				 ezTOC::VERSION );
			}
                        if ( 'css' != ezTOC_Option::get( 'toc_loading' ) ) {
                            wp_register_script( 'ez-toc-js-cookie', EZ_TOC_URL . "vendor/js-cookie/js.cookie$min.js", array(), '2.2.1', TRUE );
                        }
			wp_register_script( 'ez-toc-jquery-sticky-kit', EZ_TOC_URL . "vendor/sticky-kit/jquery.sticky-kit$min.js", array( 'jquery' ), '1.9.2', TRUE );

			if (ezTOC_Option::get( 'toc_loading' ) != 'css') {
				wp_register_script(
				'ez-toc-js',
				EZ_TOC_URL . "assets/js/front{$min}.js",
				array( 'ez-toc-js-cookie', 'ez-toc-jquery-sticky-kit' ),
				ezTOC::VERSION . '-' . filemtime( EZ_TOC_PATH . "/assets/js/front{$min}.js" ),
				true
				);
			}

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				wp_enqueue_style( 'ez-toc' );
				self::inlineCSS();
                                if ( ezTOC_Option::get( 'smooth_scroll' ) ) {
                                    self::inlineScrollEnqueueScripts();
                                }
                                
			}
                       

			if ( ezTOC_Option::get( 'sticky-toggle' ) ) {
				wp_register_style(
					'ez-toc-sticky',
					EZ_TOC_URL . "assets/css/ez-toc-sticky{$min}.css",
					array( ),
					self::VERSION
				);
				wp_enqueue_style( 'ez-toc-sticky' );
				self::inlineStickyToggleCSS();
				wp_register_script( 'ez-toc-sticky', '', array(), '', true );
                                wp_enqueue_script( 'ez-toc-sticky', '', '', '', true );
				self::inlineStickyToggleJS();
			}

			if ( ezTOC_Option::get( 'smooth_scroll' ) ) {

				$js_vars['smooth_scroll'] = true;
			}

			//wp_enqueue_script( 'ez-toc-js' );

			if ( ezTOC_Option::get( 'show_heading_text' ) && ezTOC_Option::get( 'visibility' ) ) {

				$width = ezTOC_Option::get( 'width' ) !== 'custom' ? ezTOC_Option::get( 'width' ) : (wp_is_mobile() ? 'auto' : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' ));

				$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;
                                
                                if( true == get_post_meta( $eztoc_post_id, '_ez-toc-visibility_hide_by_default', true ) )
                                    $js_vars['visibility_hide_by_default'] = true;

				$js_vars['width'] = esc_js( $width );
			}else{
				if(ezTOC_Option::get( 'visibility' )){
					$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;
                                        if( true == get_post_meta( $eztoc_post_id, '_ez-toc-visibility_hide_by_default', true ) )
                                            $js_vars['visibility_hide_by_default'] = true;
				}
			}

			$offset = wp_is_mobile() ? ezTOC_Option::get( 'mobile_smooth_scroll_offset', 0 ) : ezTOC_Option::get( 'smooth_scroll_offset', 30 );

			$js_vars['scroll_offset'] = esc_js( $offset );

			if ( ezTOC_Option::get( 'widget_affix_selector' ) ) {

				$js_vars['affixSelector'] = ezTOC_Option::get( 'widget_affix_selector' );
			}

			if (ezTOC_Option::get( 'toc_loading' ) != 'css') {
				$icon = ezTOC::getTOCToggleIcon();
				if( function_exists( 'ez_toc_pro_activation_link' ) ) {
						$icon = apply_filters('ez_toc_modify_icon',$icon);
				}
				$js_vars['fallbackIcon'] = $icon;
			}

			if ( 0 < count( $js_vars ) ) {

				wp_localize_script( 'ez-toc-js', 'ezTOC', $js_vars );
			}

			if ( in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                self::inlineWPBakeryJS();
			}
                        
                         self::inlineMainCountingCSS();
		}
        
        /**
         * inlineScrollEnqueueScripts Method
         * Set scroll offset & smoothness
         *
         * @since  2.0.40
         * @static
         * @uses wp_add_inline_style()
         * @return void
         *
         */
        private static function inlineScrollEnqueueScripts()
        {

            $offset = wp_is_mobile() ? ezTOC_Option::get( 'mobile_smooth_scroll_offset', 0 ) : ezTOC_Option::get( 'smooth_scroll_offset', 30 );
            
             $inlineScrollJS = <<<INLINESCROLLJS
jQuery(document).ready(function(){document.querySelectorAll(".ez-toc-section").forEach(t=>{t.setAttribute("ez-toc-data-id","#"+decodeURI(t.getAttribute("id")))}),jQuery("a.ez-toc-link").click(function(){let t=jQuery(this).attr("href"),e=jQuery("#wpadminbar"),i=0;$offset>30&&(i=$offset),e.length&&(i+=e.height()),jQuery('[ez-toc-data-id="'+decodeURI(t)+'"]').length>0&&(i=jQuery('[ez-toc-data-id="'+decodeURI(t)+'"]').offset().top-i),jQuery("html, body").animate({scrollTop:i},500)})});
INLINESCROLLJS;
            wp_register_script( 'ez-toc-scroll-scriptjs', '', array( 'jquery' ), ezTOC::VERSION );
            wp_enqueue_script( 'ez-toc-scroll-scriptjs', '', array( 'jquery' ), ezTOC::VERSION );
            wp_add_inline_script( 'ez-toc-scroll-scriptjs', $inlineScrollJS );
        }
        
        /**
         * inlineWPBakeryJS Method
         * Javascript code for WP Bakery Plugin issue for mobile screen
         *
         * @since  2.0.35
         * @static
         * @uses \wp_add_inline_script()
         * @return void
         *
         * ez-toc-list ez-toc-link
         * ez-toc-section
         */
        private static function inlineWPBakeryJS()
        {
            $stickyJS = '';

            if( wp_is_mobile() )
			{
                $stickyJS = <<<INLINESTICKJSFORMOBILE
let ezTocStickyContainer = document.querySelector('#ez-toc-sticky-container');
if(document.querySelectorAll('#ez-toc-sticky-container').length > 0) {
    let ezTocStickyContainerUL = ezTocStickyContainer.querySelectorAll('.ez-toc-link');
    for(let i = 0; i < ezTocStickyContainerUL.length; i++) {
        let anchorHREF = ezTocStickyContainerUL[i].getAttribute('href');
        ezTocStickyContainerUL[i].setAttribute('href', anchorHREF + '-' + uniqID);
    }
}       
INLINESTICKJSFORMOBILE;
            }
            $inlineWPBakeryJS = <<<INLINEWPBAKERYJS
let mobileContainer = document.querySelector("#mobile.vc_row-fluid");
if(document.querySelectorAll("#mobile.vc_row-fluid").length > 0) {
    let ezTocContainerUL = mobileContainer.querySelectorAll('.ez-toc-link');
    let uniqID = 'xs-sm-' + Math.random().toString(16).slice(2);
    for(let i = 0; i < ezTocContainerUL.length; i++) {
        let anchorHREF = ezTocContainerUL[i].getAttribute('href');
        mobileContainer.querySelector("span.ez-toc-section"+ anchorHREF).setAttribute('id', anchorHREF.replace
            ('#','') + '-' +
            uniqID);
        ezTocContainerUL[i].setAttribute('href', anchorHREF + '-' + uniqID);
    }
    $stickyJS
    
}    
INLINEWPBAKERYJS;
            wp_add_inline_script( 'ez-toc-js', $inlineWPBakeryJS );
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
				$css .= 'div#ez-toc-container nav ul ul li ul li {font-size: ' . ezTOC_Option::get( 'child_font_size' ) . ezTOC_Option::get( 'font_size_units' ) . '!important;}';

				if ( ezTOC_Option::get( 'theme' ) === 'custom' || ezTOC_Option::get( 'width' ) != 'auto' ) {

					$css .= 'div#ez-toc-container {';

					if ( ezTOC_Option::get( 'theme' ) === 'custom' ) {

						$css .= 'background: ' . ezTOC_Option::get( 'custom_background_colour' ) . ';border: 1px solid ' . ezTOC_Option::get( 'custom_border_colour' ) . ';';
					}

					if ( 'auto' !== ezTOC_Option::get( 'width' ) ) {

						$css .= 'width: ';

						if ( 'custom' !== ezTOC_Option::get( 'width' ) ) {

							$css .= ezTOC_Option::get( 'width' );

						} else {

							$css .= wp_is_mobile() ? 'auto' : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );
						}

						$css .= ';';
					}

					$css .= '}';
				}

				if ( 'custom' === ezTOC_Option::get( 'theme' ) ) {

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
         * inlineMainCountingCSS Method
         * for adding inlineCounting CSS
         * in wp_head in last
         * @since 2.0.37
         * @return void
        */
        public static function inlineMainCountingCSS() {
            $css = '';
            /**
             * RTL Direction
             * @since 2.0.33
            */
            $css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) );
            $css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ),'ez-toc-widget-direction','ez-toc-widget-container', 'counter', 'ez-toc-widget-container' );

            if( ezTOC_Option::get( 'sticky-toggle' ) ) {
                $cssSticky = self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ), 'ez-toc-sticky-toggle-direction', 'ez-toc-sticky-toggle-counter', 'counter', 'ez-toc-sticky-container' );
                wp_add_inline_style( 'ez-toc-sticky', $cssSticky );
            }
            /* End rtl direction */

            if ( ! ezTOC_Option::get( 'exclude_css' ) ) {
                  wp_add_inline_style( 'ez-toc', $css );
            }
        }

        /**
         * InlineCountingCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param string $direction
         * @param string $directionClass
         * @param string $class
         * @param string $counter
         * @param string $containerId
         * @return string
        */
        public static function InlineCountingCSS( $direction = 'ltr', $directionClass = 'ez-toc-container-direction', $class = 'ez-toc-counter',  $counter = 'counter', $containerId = 'ez-toc-container' )
        {
            $list_type = ezTOC_Option::get( $counter, 'decimal' );
			if( $list_type != 'none' ) {
	            $inlineCSS = '';
	            $counterListAll = array_merge( ezTOC_Option::getCounterListDecimal(), ezTOC_Option::getCounterList_i18n() );
	            $listTypesForCounting = array_keys( $counterListAll );
	            $inlineCSS .= <<<INLINECSS
.$directionClass {direction: $direction;}
INLINECSS;
				$listAnchorPosition = 'before';
	            $marginCSS = 'margin-right: .2em;';
	            $floatPosition = 'float: left;';
	            if( $direction == 'rtl' )
	            {
	                $class .= '-rtl';

	                $marginCSS = 'margin-left: .2em;';
					$floatPosition = 'float: right;';
	            }

				if( $list_type == '- ' )
				{
	                $inlineCSS .= <<<INLINECSS
#$containerId.$class nav ul li { list-style-type: '- ' !important; list-style-position: inside !important;}
INLINECSS;
				} else if( in_array( $list_type, $listTypesForCounting ) ) {
	                if( $direction == 'rtl' )
					{
	                    $length = 6;
	                    $counterRTLCSS = self::rtlCounterResetCSS( $length, $class );
	                    $counterRTLCSS .= self::rtlCounterIncrementCSS( $length, $class );
	                    $counterRTLCSS .= self::rtlCounterContentCSS( $length, $list_type, $class );
	                    $inlineCSS .= <<<INLINECSS
	$counterRTLCSS
INLINECSS;
	                }
	                if( $direction == 'ltr' )
					{
	                     $inlineCSS .= <<<INLINECSS
.$class ul{counter-reset: item;}.$class nav ul li a::$listAnchorPosition {content: counters(item, ".", $list_type) ". ";display: inline-block;counter-increment: item;flex-grow: 0;flex-shrink: 0;$marginCSS $floatPosition}
INLINECSS;
	                }
	            } else {

					$content = "  ";
					if( $list_type == 'numeric' || $list_type == 'cjk-earthly-branch' )
						$content = ". ";

	                $inlineCSS .= <<<INLINECSS
.$class ul {direction: $direction;counter-reset: item;}.$class nav ul li a::$listAnchorPosition {content: counter(item, $list_type) "$content";$marginCSS counter-increment: item;flex-grow: 0;flex-shrink: 0;$floatPosition	}
INLINECSS;

	            }
                  return $inlineCSS;
            }
        }

        /**
         * rtlCounterResetCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $class
         * @return string
        */
        private static function rtlCounterResetCSS( $length = 6, $class = 'ez-toc-counter-rtl' )
        {
            if ($length < 6) {
                $length = 6;
            }
            $counterResetCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $items = [];
                for ($j = $i; $j <= $length; $j++) {
                    $items[$j] = "item-level$j";
                }
                $items = implode(", ", $items);
                $counterResetCSS .= <<<COUNTERRESETCSS
.$class $ul {direction: rtl;counter-reset: $items;}
COUNTERRESETCSS;
            }
            return $counterResetCSS;
        }

        /**
         * rtlCounterIncrementCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $class
         * @return string
        */
        private static function rtlCounterIncrementCSS( $length = 6, $class = 'ez-toc-counter-rtl' )
        {
            if ($length < 6) {
                $length = 6;
            }
            $counterIncrementCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $item = "item-level$i";
                $counterIncrementCSS .= <<<COUNTERINCREMENTCSS
.$class $ul li {counter-increment: $item;}
COUNTERINCREMENTCSS;
            }
            return $counterIncrementCSS;
        }

        /**
         * rtlCounterContentCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $list_type
         * @param string $class
         * @return string
        */
        private static function rtlCounterContentCSS( $length = 6, $list_type = 'decimal', $class = 'ez-toc-counter-rtl' )
        {
            if ($length < 6) {
                $length = 6;
            }
            $counterContentCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $items = [];

                $cnt = $i;
                for ($j = 1; $j <= $i; $j++) {
                    $items[$cnt] = "counter(item-level$cnt, $list_type)";
                    $cnt--;
                }
                $items = implode(' "." ', $items);
                $counterContentCSS .= <<<COUNTERINCREMENTCSS
.$class nav $ul li a::before {content: $items ". ";float: right;margin-left: 0.2rem;flex-grow: 0;flex-shrink: 0;}
COUNTERINCREMENTCSS;
            }
            return $counterContentCSS;
        }

		/**
         * inlineHeadingsPaddingCSS Method
         *
         * @since  2.0.48
         * @static
         */
        private static function inlineHeadingsPaddingCSS()
        {
            $headingsPaddingTop = 0;
            if ( null !== ezTOC_Option::get( 'headings-padding-top' ) && !empty( ezTOC_Option::get( 'headings-padding-top' ) ) && 0 != ezTOC_Option::get( 'headings-padding-top' ) && '0' != ezTOC_Option::get( 'headings-padding-top' ) ) {
                $headingsPaddingTop =  ezTOC_Option::get( 'headings-padding-top' ) . '' . ezTOC_Option::get( 'headings-padding-top_units' );
            }
            $headingsPaddingBottom = 0;
            if ( null !== ezTOC_Option::get( 'headings-padding-bottom' ) && !empty( ezTOC_Option::get( 'headings-padding-bottom' ) ) && 0 != ezTOC_Option::get( 'headings-padding-bottom' ) && '0' != ezTOC_Option::get( 'headings-padding-bottom' ) ) {
                $headingsPaddingBottom =  ezTOC_Option::get( 'headings-padding-bottom' ) . '' . ezTOC_Option::get( 'headings-padding-bottom_units' );
            }
            $headingsPaddingLeft = 0;
            if ( null !== ezTOC_Option::get( 'headings-padding-left' ) && !empty( ezTOC_Option::get( 'headings-padding-left' ) ) && 0 != ezTOC_Option::get( 'headings-padding-left' ) && '0' != ezTOC_Option::get( 'headings-padding-left' ) ) {
                $headingsPaddingLeft =  ezTOC_Option::get( 'headings-padding-left' ) . '' . ezTOC_Option::get( 'headings-padding-left_units' );
            }
            $headingsPaddingRight = 0;
            if ( null !== ezTOC_Option::get( 'headings-padding-right' ) && !empty( ezTOC_Option::get( 'headings-padding-right' ) ) && 0 != ezTOC_Option::get( 'headings-padding-right' ) && '0' != ezTOC_Option::get( 'headings-padding-right' ) ) {
                $headingsPaddingRight =  ezTOC_Option::get( 'headings-padding-right' ) . '' . ezTOC_Option::get( 'headings-padding-right_units' );
            }
            
            
            $inlineHeadingsPaddingCSS = <<<inlineHeadingsPaddingCSS
ul.ez-toc-list a.ez-toc-link { padding: $headingsPaddingTop $headingsPaddingRight $headingsPaddingBottom $headingsPaddingLeft; }
inlineHeadingsPaddingCSS;

			wp_add_inline_style( 'ez-toc-headings-padding', $inlineHeadingsPaddingCSS );
		}

        /**
         * inlineStickyToggleCSS Method
         * Prints out inline Sticky Toggle CSS after the core CSS file to allow overriding core styles via options.
         *
         * @since  2.0.32
         * @static
         */
        private static function inlineStickyToggleCSS()
        {
            $custom_width = 'max-width: auto;';
            if (null !== ezTOC_Option::get('sticky-toggle-width-custom') && !empty(ezTOC_Option::get(
                    'sticky-toggle-width-custom'
                ))) {
                $custom_width = 'max-width: ' . ezTOC_Option::get('sticky-toggle-width-custom') . ';' . PHP_EOL;
                $custom_width .= 'min-width: ' . ezTOC_Option::get('sticky-toggle-width-custom') . ';' . PHP_EOL;
            }
            $custom_height = 'max-height: 100vh;';
            if (null !== ezTOC_Option::get('sticky-toggle-height-custom') && !empty(ezTOC_Option::get(
                    'sticky-toggle-height-custom'
                ))) {
                $custom_height = 'max-height: ' . ezTOC_Option::get('sticky-toggle-height-custom') . ';' . PHP_EOL;
                $custom_height .= 'min-height: ' . ezTOC_Option::get('sticky-toggle-height-custom') . ';' . PHP_EOL;
            }
            
            $topMarginStickyContainer = '65px';
            if ( ezTOC_Option::get( 'show_heading_text' ) ) {
                $toc_title = ezTOC_Option::get( 'heading_text' );
                if( strlen($toc_title) > 20 ) {
                    $topMarginStickyContainer = '70px';
                }
                if( strlen($toc_title) > 40 ) {
                    $topMarginStickyContainer = '80px';
                }
                if( strlen($toc_title) > 60 ) {
                    $topMarginStickyContainer = '90px';
                }
            }
            
            $inlineStickyToggleCSS = <<<INLINESTICKYTOGGLECSS
.ez-toc-sticky-fixed{position: fixed;top: 0;left: 0;z-index: 999999;width: auto;max-width: 100%;} .ez-toc-sticky-fixed .ez-toc-sidebar {position: relative;top: auto;width: auto !important;height: 100%;box-shadow: 1px 1px 10px 3px rgb(0 0 0 / 20%);box-sizing: border-box;padding: 20px 30px;background: white;margin-left: 0 !important;height: auto; {$custom_height} overflow-y: auto;overflow-x: hidden;} .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { {$custom_width} max-width: auto;padding: 0px;border: none;margin-bottom: 0;margin-top: $topMarginStickyContainer;} #ez-toc-sticky-container a { color: #000;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container {border-bottom-color: #EEEEEE;background-color: #FAFAFA;padding: 15px;border-bottom: 1px solid #e5e5e5;width: 100%;position: absolute;height: auto;top: 0;left: 0;z-index: 99999999;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container .ez-toc-sticky-title {font-weight: 550;font-size: 18px;color: #111;} .ez-toc-sticky-fixed .ez-toc-close-icon {-webkit-appearance: none;padding: 0;cursor: pointer;background: 0 0;border: 0;float: right;font-size: 30px;font-weight: 600;line-height: 1;position: relative;color: #000;top: -2px;text-decoration: none;} .ez-toc-open-icon {position: fixed;left: 0px;top: 8%;text-decoration: none;font-weight: bold;padding: 5px 10px 15px 10px;box-shadow: 1px -5px 10px 5px rgb(0 0 0 / 10%);background-color: #fff;display: inline-grid;line-height: 1.4;border-radius: 0px 10px 10px 0px;z-index: 999999;} .ez-toc-sticky-fixed.hide {-webkit-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);-ms-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);-o-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);left: -100%;} .ez-toc-sticky-fixed.show {-webkit-transition: left 0.3s linear, left 0.3s easy-out;-moz-transition: left 0.3s linear;-o-transition: left 0.3s linear;transition: left 0.3s linear;left: 0;} .ez-toc-open-icon span.arrow { font-size: 18px; } .ez-toc-open-icon span.text {font-size: 13px;writing-mode: vertical-rl;text-orientation: mixed;} @media screen  and (max-device-width: 640px) {.ez-toc-sticky-fixed .ez-toc-sidebar {min-width: auto;} .ez-toc-sticky-fixed .ez-toc-sidebar.show { padding-top: 35px; } .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { min-width: 100%; } }
INLINESTICKYTOGGLECSS;

                        if( 'right' == ezTOC_Option::get( 'sticky-toggle-position', 'left') ) {
                            $inlineStickyToggleCSS = <<<INLINESTICKYTOGGLECSS
.ez-toc-sticky-fixed { position: fixed;top: 0;right: 0;z-index: 999999;width: auto;max-width: 100%;} .ez-toc-sticky-fixed .ez-toc-sidebar { position: relative;top: auto;width: auto !important;height: 100%;box-shadow: 1px 1px 10px 3px rgb(0 0 0 / 20%);box-sizing: border-box;padding: 20px 30px;background: white;margin-left: 0 !important;height: auto;overflow-y: auto;overflow-x: hidden; {$custom_height} } .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { {$custom_width} max-width: auto;padding: 0px;border: none;margin-bottom: 0;margin-top: {$topMarginStickyContainer};} #ez-toc-sticky-container a { color: #000; } .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container {border-bottom-color: #EEEEEE;background-color: #FAFAFA;padding: 15px;border-bottom: 1px solid #e5e5e5;width: 100%;position: absolute;height: auto;top: 0;left: 0;z-index: 99999999;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container .ez-toc-sticky-title { font-weight: 550; font-size: 18px; color: #111; } .ez-toc-sticky-fixed .ez-toc-close-icon{-webkit-appearance:none;padding:0;cursor:pointer;background:0 0;border:0;float:right;font-size:30px;font-weight:600;line-height:1;position:relative;color:#000;top:-2px;text-decoration:none}.ez-toc-open-icon{position:fixed;right:0;top:8%;text-decoration:none;font-weight:700;padding:5px 10px 15px;box-shadow:1px -5px 10px 5px rgb(0 0 0 / 10%);background-color:#fff;display:inline-grid;line-height:1.4;border-radius:10px 0 0 10px;z-index:999999}.ez-toc-sticky-fixed.hide{-webkit-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);-ms-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);-o-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);right:-100%}.ez-toc-sticky-fixed.show{-moz-transition:right .3s linear;-o-transition:right .3s linear;transition:right .3s linear;right:0}.ez-toc-open-icon span.arrow{font-size:18px}.ez-toc-open-icon span.text{font-size:13px;writing-mode:vertical-lr;text-orientation:mixed;-webkit-transform:rotate(180deg);-moz-transform:rotate(180deg);-ms-transform:rotate(180deg);-o-transform:rotate(180deg);transform:rotate(180deg)}@media screen and (max-device-width:640px){.ez-toc-sticky-fixed .ez-toc-sidebar{min-width:auto}.ez-toc-sticky-fixed .ez-toc-sidebar.show{padding-top:35px}.ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container{min-width:100%}}
INLINESTICKYTOGGLECSS;
                        }
			wp_add_inline_style( 'ez-toc-sticky', $inlineStickyToggleCSS );
		}

		/**
		 * inlineStickyToggleJS Method
		 * Prints out inline Sticky Toggle JS after the core CSS file to allow overriding core styles via options.
		 *
		 * @since  2.0.32
		 * @static
		 */
		private static function inlineStickyToggleJS() {
                    $mobileJS = '';
                    if( ( 1 == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) || '1' == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) || true == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) ) && wp_is_mobile() ) {
                        $mobileJS = <<<INLINESTICKYTOGGLEMOBILEJS
jQuery(document).ready(function() {
    jQuery("#ez-toc-sticky-container a.ez-toc-link").click(function(e) {
        ezTOC_hideBar(e);
    });
});
INLINESTICKYTOGGLEMOBILEJS;

                    } 
                    $inlineStickyToggleJS = <<<INLINESTICKYTOGGLEJS
function ezTOC_hideBar(e) { var sidebar = document.querySelector(".ez-toc-sticky-fixed"); if ( typeof(sidebar) !== "undefined" && sidebar !== null ) { sidebar.classList.remove("show"); sidebar.classList.add("hide"); setTimeout(function() { document.querySelector(".ez-toc-open-icon").style = "z-index: 9999999"; }, 200); } } function ezTOC_showBar(e) { document.querySelector(".ez-toc-open-icon").style = "z-index: -1;";setTimeout(function() { var sidebar = document.querySelector(".ez-toc-sticky-fixed"); sidebar.classList.remove("hide"); sidebar.classList.add("show"); }, 200); } (function() { let ez_toc_sticky_fixed_container = document.querySelector('div.ez-toc-sticky-fixed');if(ez_toc_sticky_fixed_container) { document.body.addEventListener("click", function (evt) { ezTOC_hideBar(evt); }); ez_toc_sticky_fixed_container.addEventListener('click', function(event) { event.stopPropagation(); }); document.querySelector('.ez-toc-open-icon').addEventListener('click', function(event) { event.stopPropagation(); }); } })();
                
                $mobileJS
INLINESTICKYTOGGLEJS;
			wp_add_inline_script( 'ez-toc-sticky', $inlineStickyToggleJS );
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
		 * @param WP_Post $post
		 *
		 * @return bool
		 */
		public static function is_eligible( $post ) {

			//global $wp_current_filter;

			if ( empty( $post ) || ! $post instanceof WP_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', $post );
				return false;
			}

			// This can likely be removed since it is checked in maybeApplyTheContentFilter().
			// Do not execute if root filter is one of those in the array.
			//if ( in_array( $wp_current_filter[0], array( 'get_the_excerpt', 'wp_head' ), true ) ) {
			//
			//	return false;
			//}
                        
                        /**
                         * Easy TOC Run On Amp Pages Check
                         * @since 2.0.46
                         */
                        if ( ( ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) !== false && 0 == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || '0' == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || false == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) ) && !ez_toc_non_amp() ) {
				Debug::log( 'non_amp', 'Is frontpage, TOC is not enabled.', false );
				return false;
                            
                        }

			if ( has_shortcode( $post->post_content, apply_filters( 'ez_toc_shortcode', 'toc' ) ) ||
			     has_shortcode( $post->post_content, 'ez-toc' ) ) {

				Debug::log( 'has_ez_toc_shortcode', 'Has instance of shortcode.', true );
				return true;
			}
                        
			if ( is_front_page() && ! ezTOC_Option::get( 'include_homepage' ) ) {

				Debug::log( 'is_front_page', 'Is frontpage, TOC is not enabled.', false );
				return false;
			}

			$type = get_post_type( $post->ID );

			Debug::log( 'current_post_type', 'Post type is.', $type );

			$enabled = in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ), true );
			$insert  = in_array( $type, ezTOC_Option::get( 'auto_insert_post_types', array() ), true );

			Debug::log( 'is_supported_post_type', 'Is supported post type?', $enabled );
			Debug::log( 'is_auto_insert_post_type', 'Is auto insert for post types?', $insert );

			if ( $insert || $enabled ) {

				if ( ezTOC_Option::get( 'restrict_path' ) ) {

					/**
					 * @link https://wordpress.org/support/topic/restrict-path-logic-does-not-work-correctly?
					 */
					if ( false !== strpos( ezTOC_Option::get( 'restrict_path' ), $_SERVER['REQUEST_URI'] ) ) {

						Debug::log( 'is_restricted_path', 'In restricted path, post not eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return false;

					} else {

						Debug::log( 'is_not_restricted_path', 'Not in restricted path, post is eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return true;
					}

				} else {

					if ( $insert && 1 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_disable_post_meta', 'Auto insert enabled and disable TOC is enabled in post meta.', false );
						return false;

					} elseif ( $insert && 0 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_enabled_post_meta', 'Auto insert enabled and disable TOC is not enabled in post meta.', true );
						return true;

					} elseif ( $enabled && 1 === (int) get_post_meta( $post->ID, '_ez-toc-insert', true ) ) {

						Debug::log( 'is_supported_post_type_disable_insert_post_meta', 'Supported post type and insert TOC is enabled in post meta.', true );
						return true;

					} elseif ( $enabled && $insert ) {

						Debug::log( 'supported_post_type_and_auto_insert', 'Supported post type and auto insert TOC is enabled.', true );
						return true;
					}

					Debug::log( 'not_auto_insert_or_not_supported_post_type', 'Not supported post type or insert TOC is disabled.', false );
					return false;
				}

			} else {

				Debug::log( 'not_auto_insert_and_not_supported post_type', 'Not supported post type and do not auto insert TOC.', false );
				return false;
			}
		}

		/**
		 * Get TOC from store and if not in store process post and add it to the store.
		 *
		 * @since 2.0
		 *
		 * @param int $id
		 *
		 * @return ezTOC_Post|null
		 */
		public static function get( $id ) {

			$post = null;

			if ( isset( self::$store[ $id ] ) && self::$store[ $id ] instanceof ezTOC_Post && !in_array( 'js_composer_salient/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				$post = self::$store[ $id ];

			} else {

				$post = ezTOC_Post::get( get_the_ID() );

				if ( $post instanceof ezTOC_Post ) {

					self::$store[ $id ] = $post;
				}
			}

			return $post;
		}

        /**
         * Callback for the registered shortcode `[ez-toc-widget-sticky]`
         *
         * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
         *
         * @access private
         * @since  2.0.41
         *
         * @param array|string $atts    Shortcode attributes array or empty string.
         * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
         * @param string       $tag     Shortcode name.
         *
         * @return string
         */
        public static function ez_toc_widget_sticky_shortcode( $atts, $content, $tag ) {             global $wp_widget_factory;

            if ( 'ez-toc-widget-sticky' == $tag ) {
    
                extract( shortcode_atts( array(
                    'highlight_color' => '#ededed',
                    'title' => 'Table of Contents',
                    'advanced_options' => '',
                    'scroll_fixed_position' => 30,
                    'sidebar_width' => 'auto',
                    'sidebar_width_size_unit' => 'none',
                    'fixed_top_position' => 30,
                    'fixed_top_position_size_unit' => 'px',
                    'navigation_scroll_bar' => 'on',
                    'scroll_max_height' => 'auto',
                    'scroll_max_height_size_unit' => 'none',
                    'ez_toc_widget_sticky_before_widget_container' => '',
                    'ez_toc_widget_sticky_before_widget' => '',
                    'ez_toc_widget_sticky_before' => '',
                    'ez_toc_widget_sticky_after' => '',
                    'ez_toc_widget_sticky_after_widget' => '',
                    'ez_toc_widget_sticky_after_widget_container' => '',
                ), $atts ) );

                $widget_name = wp_specialchars( 'ezTOC_WidgetSticky' );
                
                $instance = array(
                    'title' => ( ! empty ( $title ) ) ? $title : '',
                    'highlight_color' => ( ! empty ( $highlight_color ) ) ? $highlight_color : '#ededed',
                    'advanced_options' => ( ! empty ( $advanced_options ) ) ? $advanced_options : '',
                    'scroll_fixed_position' => ( ! empty ( $scroll_fixed_position ) ) ? ( int ) $scroll_fixed_position : 30,
                    'sidebar_width' => ( ! empty ( $sidebar_width ) ) ? ( 'auto' == $sidebar_width ) ? $sidebar_width : ( int ) strip_tags ( $sidebar_width ) : 'auto',
                    'sidebar_width_size_unit' => ( ! empty ( $sidebar_width_size_unit ) ) ? $sidebar_width_size_unit : 'none',
                    'fixed_top_position' => ( ! empty ( $fixed_top_position ) ) ? ( 'auto' == $fixed_top_position ) ? $fixed_top_position : ( int ) strip_tags ( $fixed_top_position ) : 30,
                    'fixed_top_position_size_unit' => ( ! empty ( $fixed_top_position_size_unit ) ) ? $fixed_top_position_size_unit : 'px',
                    'navigation_scroll_bar' => ( ! empty ( $navigation_scroll_bar ) ) ? $navigation_scroll_bar : 'on',
                    'scroll_max_height' => ( ! empty ( $scroll_max_height ) ) ? ( 'auto' == $scroll_max_height ) ? $scroll_max_height : ( int ) strip_tags ( $scroll_max_height ) : 'auto',
                    'scroll_max_height_size_unit' => ( ! empty ( $scroll_max_height_size_unit ) ) ? $scroll_max_height_size_unit : 'none',
                );
                
                if ( !is_a( $wp_widget_factory->widgets[ $widget_name ], 'WP_Widget' ) ):
                    $wp_class = 'WP_Widget_' . ucwords(strtolower($class));

                    if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
                        return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
                    else:
                        $class = $wp_class;
                    endif;
                endif;

                $id = uniqid( time() );
                ob_start();
                the_widget( $widget_name, $instance, array(
                    'widget_id' => 'ez-toc-widget-sticky-' . $id,
                    'ez_toc_widget_sticky_before_widget_container' => $ez_toc_widget_sticky_before_widget_container,
                    'ez_toc_widget_sticky_before_widget' => $ez_toc_widget_sticky_before_widget,
                    'ez_toc_widget_sticky_before' => $ez_toc_widget_sticky_before,
                    'ez_toc_widget_sticky_after' => $ez_toc_widget_sticky_after,
                    'ez_toc_widget_sticky_after_widget' => $ez_toc_widget_sticky_after_widget,
                    'ez_toc_widget_sticky_after_widget_container' => $ez_toc_widget_sticky_after_widget_container,
                    ) 
                );
                $output = ob_get_contents();
                ob_end_clean();
                return $output;
            }
        }
                
		/**
		 * Callback for the registered shortcode `[ez-toc]`
		 *
		 * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
		 *
		 * @access private
		 * @since  1.3
		 *
		 * @param array|string $atts    Shortcode attributes array or empty string.
		 * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
		 * @param string       $tag     Shortcode name.
		 *
		 * @return string
		 */
		public static function shortcode( $atts, $content, $tag ) {

//			static $run = true;
			$html = '';

                        if( ( ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) !== false && 0 == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || '0' == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || false == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) ) && !ez_toc_non_amp() )
                            return $html;
                            
			if ( 'ez-toc' == $tag || 'toc' == $tag ) {
                            
                                $post = self::get( get_the_ID() );

                                if ( ! $post instanceof ezTOC_Post ) {

                                        Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );

                                        return Debug::log()->appendTo( $content );
                                }

                                $html = $post->getTOC();
//				$run  = false;
			}

			if( !empty( $html ) )
			{
				update_option('ez-toc-shortcode-exist-and-render', true);
			} else
			{
				update_option('ez-toc-shortcode-exist-and-render', false);
			}
			if (isset($atts["initial_view"]) && !empty($atts["initial_view"]) && $atts["initial_view"] == 'hide') {
                            $options = array(
                                'visibility_hide_by_default' => true,
                            );
                            $html = $post->getTOC($options);
//				$html = preg_replace('/class="ez-toc-list ez-toc-list-level-1"/', 'class="ez-toc-list ez-toc-list-level-1" style="display:none"', $html);
			}

                        if( !is_home() ) {
                            if ( ezTOC_Option::get('sticky-toggle') ) {
                                add_action('wp_footer', array(__CLASS__, 'stickyToggleContent'));
                            }
                        }
                        
			return $html;
		}

		/**
		 * Whether or not apply `the_content` filter.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		private static function maybeApplyTheContentFilter() {

			$apply = true;

			global $wp_current_filter;

			// Do not execute if root current filter is one of those in the array.
			if ( in_array( $wp_current_filter[0], array( 'get_the_excerpt', 'init', 'wp_head' ), true ) ) {

				$apply = false;
			}

			// bail if feed, search or archive
			if ( is_feed() || is_search() || is_archive() ) {

                            if( true == ezTOC_Option::get( 'include_category', false) && is_category() ) {
                                $apply = true;
                            } else {
				$apply = false;
                            }
			}

			if ( ezTOC_Option::get( 'headings-padding' ) ) {
				wp_register_style(
					'ez-toc-headings-padding',
					'',
					array( ),
					self::VERSION
				);
				wp_enqueue_style( 'ez-toc-headings-padding' );
				self::inlineHeadingsPaddingCSS();
			}
                        
			if( function_exists('get_current_screen') ) {
				$my_current_screen = get_current_screen();
				if ( isset( $my_current_screen->id )  ) {

					if( $my_current_screen->id == 'edit-post' ) {          
						$apply = false;
					}
				}
			}

			if ( ! empty( array_intersect( $wp_current_filter, array( 'get_the_excerpt', 'init', 'wp_head' ) ) ) ) {
				$apply = false;
			}
			/**
			 * Whether or not to apply `the_content` filter callback.
			 *
			 * @see ezTOC::the_content()
			 *
			 * @since 2.0
			 *
			 * @param bool $apply
			 */
			return apply_filters( 'ez_toc_maybe_apply_the_content_filter', $apply );
		}

		/**
		 * Callback for the `the_content` filter.
		 *
		 * This will add the inline table of contents page anchors to the post content. It will also insert the
		 * table of contents inline with the post content as defined by the user defined preference.
		 *
		 * @since 1.0
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public static function the_content( $content ) {
                    
                        if( function_exists( 'post_password_required' ) ) {
                            if( post_password_required() ) return Debug::log()->appendTo( $content );
                        }
                    
			$maybeApplyFilter = self::maybeApplyTheContentFilter();
                        
                        if ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Pale Moon' == ez_toc_get_browser_name() || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
                            update_option( 'ez-toc-post-content-core-level', $content );
			}
			Debug::log( 'the_content_filter', 'The `the_content` filter applied.', $maybeApplyFilter );

			if ( ! $maybeApplyFilter ) {

				return Debug::log()->appendTo( $content );
			}

			// Bail if post not eligible and widget is not active.
			$isEligible = self::is_eligible( get_post() );
			$isEligible = apply_filters('eztoc_do_shortcode',$isEligible);
			Debug::log( 'post_eligible', 'Post eligible.', $isEligible );

			if ( ! $isEligible && ! is_active_widget( false, false, 'ezw_tco' ) && ! get_option( 'ez-toc-shortcode-exist-and-render' ) && ! is_active_widget( false, false, 'ez_toc_widget_sticky' ) ) {

				return Debug::log()->appendTo( $content );
			}

			$post = self::get( get_the_ID() );

			if ( ! $post instanceof ezTOC_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );

				return Debug::log()->appendTo( $content );
			}

			// Bail if no headings found.
			if ( ! $post->hasTOCItems() ) {

				return Debug::log()->appendTo( $content );
			}
                        
                        $find    = $post->getHeadings();
                        $replace = $post->getHeadingsWithAnchors();
                        $toc     = $post->getTOC();
                            
			$headings = implode( PHP_EOL, $find );
			$anchors  = implode( PHP_EOL, $replace );

			$headingRows = count( $find ) + 1;
			$anchorRows  = count( $replace ) + 1;

			$style = "background-image: linear-gradient(#F1F1F1 50%, #F9F9F9 50%); background-size: 100% 4em; border: 1px solid #CCC; font-family: monospace; font-size: 1em; line-height: 2em; margin: 0 auto; overflow: auto; padding: 0 8px 4px; white-space: nowrap; width: 100%;";

			Debug::log(
				'found_post_headings',
				'Found headings:',
				"<textarea rows='{$headingRows}' style='{$style}' wrap='soft'>{$headings}</textarea>"
			);

			Debug::log(
				'replace_post_headings',
				'Replace found headings with:',
				"<textarea rows='{$anchorRows}' style='{$style}' wrap='soft'>{$anchors}</textarea>"
			);

			// If shortcode used or post not eligible, return content with anchored headings.
			if ( strpos( $content, 'ez-toc-container' ) || ! $isEligible ) {

				Debug::log( 'shortcode_found', 'Shortcode found, add links to content.', true );

				return mb_find_replace( $find, $replace, $content );
			}

			$position = ezTOC_Option::get( 'position' );

			Debug::log( 'toc_insert_position', 'Insert TOC at position', $position );

			// else also add toc to content
			switch ( $position ) {

				case 'top':
					$content = $toc . mb_find_replace( $find, $replace, $content );
					break;

				case 'bottom':
					$content = mb_find_replace( $find, $replace, $content ) . $toc;
					break;

				case 'after':
					$replace[0] = $replace[0] . $toc;
					$content    = mb_find_replace( $find, $replace, $content );
					break;
				case 'afterpara':
					$content = insertElementByPTag( mb_find_replace( $find, $replace, $content ), $toc );
					break;	
				case 'before':
				default:
					//$replace[0] = $html . $replace[0];
					$content    = mb_find_replace( $find, $replace, $content );

					/**
					 * @link https://wordpress.org/support/topic/php-notice-undefined-offset-8/
					 */
					if ( ! array_key_exists( 0, $replace ) ) {
						break;
					}

					$pattern = '`<h[1-6]{1}[^>]*' . preg_quote( $replace[0], '`' ) . '`msuU';
					$result  = preg_match( $pattern, $content, $matches );

					/*
					 * Try to place TOC before the first heading found in eligible heading, failing that,
					 * insert TOC at top of content.
					 */
					if ( 1 === $result ) {

						Debug::log( 'toc_insert_position_found', 'Insert TOC before first eligible heading.', $result );

						$start   = strpos( $content, $matches[0] );
						$content = substr_replace( $content, $toc, $start, 0 );

					} else {

						Debug::log( 'toc_insert_position_not_found', 'Insert TOC before first eligible heading not found.', $result );

						// Somehow, there are scenarios where the processing get this far and
						// the TOC is being added to pages where it should not. Disable for now.
						//$content = $html . $content;
					}
			}

            /**
             * @since 2.0.32
             */
            if ( ezTOC_Option::get('sticky-toggle') && !is_home() && !self::checkBeaverBuilderPluginActive() ) {
                add_action('wp_footer', array(__CLASS__, 'stickyToggleContent'));
            }

			return Debug::log()->appendTo( $content );
		}

		/**
		 * stickyToggleContent Method
		 * Call back for the `wp_footer` action.
		 *
		 * @since  2.0.32
		 * @static
		 */
		public static function stickyToggleContent() {
			$post = self::get( get_the_ID() );
			if ( null !== $post ) {
				$stickyToggleTOC = $post->getStickyToggleTOC();
				$openButtonText = "Index";
				if( !empty( ezTOC_Option::get( 'sticky-toggle-open-button-text' ) ) ) {
					$openButtonText = ezTOC_Option::get( 'sticky-toggle-open-button-text' );
				}
                                $arrowSide = "&#8594;";
                                if( 'right' == ezTOC_Option::get( 'sticky-toggle-position', 'left') )
                                    $arrowSide = "&#8592;"; 
				echo <<<STICKYTOGGLEHTML
					<div class="ez-toc-sticky">
				        <div class="ez-toc-sticky-fixed hide">
		                    <div class='ez-toc-sidebar'>{$stickyToggleTOC}</div>
				        </div>
			            <a class='ez-toc-open-icon' href='javascript:void(0)' onclick='ezTOC_showBar(event)'>
                            <span class="arrow">{$arrowSide}</span>
                            <span class="text">{$openButtonText}</span>
                        </a>
					</div>
STICKYTOGGLEHTML;
			}
		}

		/**
		 * Call back for the `wp_head` action.
		 *
		 * Add add button for shortcode in wysisyg editor .
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function addEditorButton() {
			
            if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
                       return;
               }
			   
		
           if ( 'true' == get_user_option( 'rich_editing' ) ) {
               add_filter( 'mce_external_plugins', array( __CLASS__, 'toc_add_tinymce_plugin'));
               add_filter( 'mce_buttons', array( __CLASS__, 'toc_register_mce_button' ));
               }
			
		}
		
		/**
		 * Call back for the `mce_external_plugins` action.
		 *
		 * Register new button in the editor.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */		
		
		public static function toc_register_mce_button( $buttons ) {
            
				array_push( $buttons, 'toc_mce_button' );
				return $buttons;
		}
			
		/**
		 * Call back for the `mce_buttons` action.
		 *
		 * Add  js to insert the shortcode on the click event.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function toc_add_tinymce_plugin( $plugin_array ) {
			
				$plugin_array['toc_mce_button'] = EZ_TOC_URL .'assets/js/toc-mce-button.js';
				return $plugin_array;
		}

		/**
         * getTOCToggleIcon Method
         * @access public
   		 * @since  2.0.35
   		 * @static
		 * @return string
		 */
		public static function getTOCToggleIcon()
		{
			$iconColor = '#000000';
			if( ezTOC_Option::get( 'custom_title_colour' ) )
			{
				$iconColor = ezTOC_Option::get( 'custom_title_colour' );
			}
			return '<span style="display: flex;align-items: center;width: 35px;height: 30px;justify-content: center;direction:ltr;"><svg style="fill: ' . esc_attr($iconColor) . ';color:' . esc_attr($iconColor) . '" xmlns="http://www.w3.org/2000/svg" class="list-377408" width="20px" height="20px" viewBox="0 0 24 24" fill="none"><path d="M6 6H4v2h2V6zm14 0H8v2h12V6zM4 11h2v2H4v-2zm16 0H8v2h12v-2zM4 16h2v2H4v-2zm16 0H8v2h12v-2z" fill="currentColor"></path></svg><svg style="fill: ' . esc_attr($iconColor) . ';color:' . esc_attr($iconColor) . '" class="arrow-unsorted-368013" xmlns="http://www.w3.org/2000/svg" width="10px" height="10px" viewBox="0 0 24 24" version="1.2" baseProfile="tiny"><path d="M18.2 9.3l-6.2-6.3-6.2 6.3c-.2.2-.3.4-.3.7s.1.5.3.7c.2.2.4.3.7.3h11c.3 0 .5-.1.7-.3.2-.2.3-.5.3-.7s-.1-.5-.3-.7zM5.8 14.7l6.2 6.3 6.2-6.3c.2-.2.3-.5.3-.7s-.1-.5-.3-.7c-.2-.2-.4-.3-.7-.3h-11c-.3 0-.5.1-.7.3-.2.2-.3.5-.3.7s.1.5.3.7z"/></svg></span>';
		}

		 /**
         * the_category_content_filter Method
         * @access public
   		 * @since  2.0.46
   		 * @static
		 * @return string
		 */
		public static function toc_category_content_filter( $description , $cat_id ) {
                    if( true == ezTOC_Option::get( 'include_category', false) ) {
			if(!is_admin() && !empty($description)){
				return self::the_content($description);
			}
                    }
                    return $description;
		}


	}

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
register_activation_hook(__FILE__, 'ez_toc_activate');
add_action('admin_init', 'ez_toc_redirect');

function ez_toc_activate() {
    add_option('ez_toc_do_activation_redirect', true);
}

function ez_toc_redirect() {
    if (get_option('ez_toc_do_activation_redirect', false)) {
        delete_option('ez_toc_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("options-general.php?page=table-of-contents#welcome");
        }
    }
}