<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Admin' ) ) {

	/**
	 * Class ezTOC_Admin
	 */
	final class ezTOC_Admin {

		/**
		 * Setup plugin for admin use.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function __construct() {

			$this->hooks();
			//$this->registerMetaboxes();
		}

		/**
		 * Add the core admin hooks.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private function hooks() {
            global $pagenow;

            if($pagenow == 'options-general.php' && isset($_REQUEST['page']) && !empty($_REQUEST['page']) &&
            $_REQUEST['page'] == 'table-of-contents') {
                add_action( 'admin_head', array( $this,'_clean_other_plugins_stuff' ) );
            }
			add_action( 'admin_init', array( $this, 'registerScripts' ) );
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'init', array( $this, 'registerMetaboxes' ), 99 );
			add_filter( 'plugin_action_links_' . EZ_TOC_BASE_NAME, array( $this, 'pluginActionLinks' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action('wp_ajax_eztoc_send_query_message', array( $this, 'eztoc_send_query_message'));
		}

        /**
         * Attach to admin_head hook to hide all admin notices.
         *
         * @scope public
         * @since  2.0.33
         * @return void
         * @uses remove_all_actions()
         */
        public function _clean_other_plugins_stuff()
        {
            remove_all_actions('admin_notices');
            remove_all_actions('network_admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('user_admin_notices');
        }

        /**
		 * Callback to add the Settings link to the plugin action links.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public  function pluginActionLinks( $links, $file ) {

		    $url = add_query_arg( 'page', 'table-of-contents', self_admin_url( 'options-general.php' ) );
		    $setting_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'easy-table-of-contents' ) . '</a> |';
		 	$setting_link .= '<a href="https://tocwp.com/contact/" target="_blank">' . __( ' Support', 'easy-table-of-contents' ) . '</a> |';
		 	$setting_link .= '<a href="https://tocwp.com/pricing/" target="_blank">' . __( ' Upgrade', 'easy-table-of-contents' ) . '</a> |';
		 	$setting_link .= '<a href="https://tocwp.com/" target="_blank">' . __( ' Website', 'easy-table-of-contents' ) . '</a>';
		    array_push( $links, $setting_link );
		    return $links;
		}

		/**
		 * Register the scripts used in the admin.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function registerScripts() {

			wp_register_script( 'cn_toc_admin_script', EZ_TOC_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), ezTOC::VERSION, true );
			wp_register_style( 'cn_toc_admin_style', EZ_TOC_URL . 'assets/css/admin.css', array( 'wp-color-picker' ), ezTOC::VERSION );


//                                wp_enqueue_style( 'ez-toc' );
//                                self::inlineStickyToggleCSS();
			wp_enqueue_script( 'cn_toc_admin_script' );
            $data = array(
                'ajax_url'      		       => admin_url( 'admin-ajax.php' ),
                'eztoc_security_nonce'         => wp_create_nonce('eztoc_ajax_check_nonce'),
            );

            $data = apply_filters( 'eztoc_localize_filter', $data, 'eztoc_admin_data' );

            wp_localize_script( 'cn_toc_admin_script', 'cn_toc_admin_data', $data );
			self::inlineAdminStickyToggleJS();
                        
//                        self::inlineAdminOccasionalAdsPopUpCSS_JS();
                        
                        self::inlineAdminAMPNonJS();
						self::inlineAdminHeadingsPaddingJS();
		}
                
                /**
                 * inlineAdminAMPNonJS Method
		 * Prints out inline AMP Non JS.
		 *
		 * @access private
		 * @return void
		 * @since  2.0.46
		 * @static
		*/
                private static function inlineAdminAMPNonJS() {
                    
                    $isAmpActivated = false;
                    if ( function_exists('ez_toc_is_amp_activated') ) {
                        $isAmpActivated = ez_toc_is_amp_activated();
                    }
                    
                    if( false == $isAmpActivated ) {
                        $inlineAdminAMPNonJS = <<<inlineAdminAMPNonJS
jQuery(function($) {
    let tocAMPSupportOption = $(document).find("input[name='ez-toc-settings[toc-run-on-amp-pages]']");
//        console.log(tocAMPSupportOption.length);
    if( tocAMPSupportOption.length > 0 ) {
        $(tocAMPSupportOption).attr('disabled', true);
    }
});
inlineAdminAMPNonJS;

                        wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminAMPNonJS );
                    }
                }
                
				/**
                 * inlineAdminHeadingsPaddingJS Method
				 * Prints out inline AMP Non JS.
				 *
				 * @access private
				 * @return void
				 * @since  2.0.48
				 * @static
				*/
				private static function inlineAdminHeadingsPaddingJS() {
					
					$inlineAdminHeadingsPaddingJS = <<<inlineAdminHeadingsPaddingJS
jQuery(function($) {
	
	let headingsPaddingCheckbox = $('#eztoc-appearance').find("input[name='ez-toc-settings[headings-padding]']");
    let headingsPaddingTop = $('#eztoc-appearance').find("input[name='ez-toc-settings[headings-padding-top]']");
    let headingsPaddingBottom = $('#eztoc-appearance').find("input[name='ez-toc-settings[headings-padding-bottom]']");
    let headingsPaddingLeft = $('#eztoc-appearance').find("input[name='ez-toc-settings[headings-padding-left]']");
    let headingsPaddingRight = $('#eztoc-appearance').find("input[name='ez-toc-settings[headings-padding-right]']");

	let headingsPaddingTopHTML = $(headingsPaddingTop).parent();
	$(headingsPaddingTopHTML).find("input[name='ez-toc-settings[headings-padding-top]']").attr("type", "number");
	$(headingsPaddingTop).parents('tr').remove();
	$(headingsPaddingCheckbox).parent().append("<br/><br/><span id='headings-padding-top-container'><label for='ez-toc-settings[headings-padding-top]'><strong>Top</strong></label>&nbsp;&nbsp;&nbsp;" + $(headingsPaddingTopHTML).html() + "</span>");
	$('#eztoc-appearance').find("select[name='ez-toc-settings[headings-padding-top_units]']").html('<option value="px" selected="selected">px</option>');
	

	let headingsPaddingBottomHTML = $(headingsPaddingBottom).parent();
	$(headingsPaddingBottomHTML).find("input[name='ez-toc-settings[headings-padding-bottom]']").attr("type", "number");
	$(headingsPaddingBottom).parents('tr').remove();
	$(headingsPaddingCheckbox).parent().append("&nbsp;&nbsp;&nbsp;&nbsp;<span id='headings-padding-bottom-container'><label for='ez-toc-settings[headings-padding-bottom]'><strong>Bottom</strong></label>&nbsp;&nbsp;&nbsp;" + $(headingsPaddingBottomHTML).html() + "</span>");
	$('#eztoc-appearance').find("select[name='ez-toc-settings[headings-padding-bottom_units]']").html('<option value="px" selected="selected">px</option>');

	let headingsPaddingLeftHTML = $(headingsPaddingLeft).parent();
	$(headingsPaddingLeftHTML).find("input[name='ez-toc-settings[headings-padding-left]']").attr("type", "number");
	$(headingsPaddingLeft).parents('tr').remove();
	$(headingsPaddingCheckbox).parent().append("&nbsp;&nbsp;&nbsp;&nbsp;<span id='headings-padding-left-container'><label for='ez-toc-settings[headings-padding-left]'><strong>Left</strong></label>&nbsp;&nbsp;&nbsp;" + $(headingsPaddingLeftHTML).html() + "</span>");
	$('#eztoc-appearance').find("select[name='ez-toc-settings[headings-padding-left_units]']").html('<option value="px" selected="selected">px</option>');

	let headingsPaddingRightHTML = $(headingsPaddingRight).parent();
	$(headingsPaddingRightHTML).find("input[name='ez-toc-settings[headings-padding-right]']").attr("type", "number");
	$(headingsPaddingRight).parents('tr').remove();
	$(headingsPaddingCheckbox).parent().append("&nbsp;&nbsp;&nbsp;&nbsp;<span id='headings-padding-right-container'><label for='ez-toc-settings[headings-padding-right]'><strong>Right</strong></label>&nbsp;&nbsp;&nbsp;" + $(headingsPaddingRightHTML).html() + "</span>");
	$('#eztoc-appearance').find("select[name='ez-toc-settings[headings-padding-right_units]']").html('<option value="px" selected="selected">px</option>');

	let headingsPaddingContainerTop = $('#eztoc-appearance').find("span#headings-padding-top-container");
	let headingsPaddingContainerBottom = $('#eztoc-appearance').find("span#headings-padding-bottom-container");
	let headingsPaddingContainerLeft = $('#eztoc-appearance').find("span#headings-padding-left-container");
	let headingsPaddingContainerRight = $('#eztoc-appearance').find("span#headings-padding-right-container");

    if($(headingsPaddingCheckbox).prop('checked') == false) {
        $(headingsPaddingContainerTop).hide(500);
        $(headingsPaddingContainerBottom).hide(500);
        $(headingsPaddingContainerLeft).hide(500);
        $(headingsPaddingContainerRight).hide(500);
		$(headingsPaddingTop).val(0);
		$(headingsPaddingBottom).val(0);
		$(headingsPaddingLeft).val(0);
		$(headingsPaddingRight).val(0);
    }

    $(document).on("change, click", "input[name='ez-toc-settings[headings-padding]']", function() {
        if($(headingsPaddingCheckbox).prop('checked') == true) {
            $(headingsPaddingContainerTop).show(500);
			$(headingsPaddingContainerBottom).show(500);
			$(headingsPaddingContainerLeft).show(500);
			$(headingsPaddingContainerRight).show(500);
        } else {
            $(headingsPaddingContainerTop).hide(500);
			$(headingsPaddingContainerBottom).hide(500);
			$(headingsPaddingContainerLeft).hide(500);
			$(headingsPaddingContainerRight).hide(500);
			$(headingsPaddingTop).val(0);
			$(headingsPaddingBottom).val(0);
			$(headingsPaddingLeft).val(0);
			$(headingsPaddingRight).val(0);
        }
        
    });
});
inlineAdminHeadingsPaddingJS;

					wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminHeadingsPaddingJS );
					 
				}
				
                /**
                 * inlineAdminOccasionalAdsPopUpCSS_JS Method
		 * Prints out inline occasional ads PopUp JS.
		 *
		 * @access private
		 * @return void
		 * @since  2.0.38
		 * @static
		*/
                private static function inlineAdminOccasionalAdsPopUpCSS_JS() {
                    $inlineAdminOccasionalAdsPopUpCCS = <<<INLINEOCCASIONALADSPOPUSCCS
details#eztoc-ocassional-pop-up-container{position:fixed;right:1rem;bottom:1rem;margin-top:2rem;color:#6b7280;display:flex;flex-direction:column;margin-right: 15px;z-index:99999}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents{background-color:#1e1e27;box-shadow:0 5px 10px rgba(0,0,0,.15);padding:25px 25px 10px;border-radius:8px;position:absolute;max-height:calc(100vh - 100px);width:325px;max-width:calc(100vw - 2rem);bottom:calc(100% + 1rem);right:0;overflow:auto;transform-origin:100% 100%;color:#95a3b9;margin-bottom:44px}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents::-webkit-scrollbar{width:15px;background-color:#1e1e27}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents::-webkit-scrollbar-thumb{width:5px;border-radius:99em;background-color:#95a3b9;border:5px solid #1e1e27}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents>*+*{margin-top:.75em}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents p>code{font-size:1rem;font-family:monospace}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents pre{white-space:pre-line;border:1px solid #95a3b9;border-radius:6px;font-family:monospace;padding:.75em;font-size:.875rem;color:#fff}details#eztoc-ocassional-pop-up-container[open] div.eztoc-ocassional-pop-up-contents{bottom:0;-webkit-animation:.25s ez_toc_ocassional_pop_up_scale;animation:.25s ez_toc_ocassional_pop_up_scale}details#eztoc-ocassional-pop-up-container span.eztoc-promotion-close-btn{font-weight:400;font-size:20px;background:#37474f;font-family:sans-serif;border-radius:30px;color:#fff;position:absolute;right:-10px;z-index:99999;padding:0 8px;top:-331px;cursor:pointer;line-height:28px}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents img.eztoc-promotion-surprise-icon{width:40px;float:left;margin-right:10px}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents p.eztoc-ocassional-pop-up-headline{font-size:22px;margin:0;line-height:47px;font-weight:500;color:#fff}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents p.eztoc-ocassional-pop-up-headline span{color:#ea4c89;font-weight:700}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents p.eztoc-ocassional-pop-up-second-headline{font-size:16px;color:#fff}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents a.eztoc-ocassional-pop-up-offer-btn{background:#ea4c89;padding:13px 38px 14px;color:#fff;text-align:center;border-radius:60px;font-size:18px;display:inline-flex;align-items:center;margin:0 auto 15px;text-decoration:none;line-height:1.2;transform:perspective(1px) translateZ(0);box-shadow:0 0 20px 5px rgb(0 0 0 / 6%);transition:.3s ease-in-out;box-shadow:3px 5px .65em 0 rgb(0 0 0 / 15%);display:inherit}details#eztoc-ocassional-pop-up-container div.eztoc-ocassional-pop-up-contents p.eztoc-ocassional-pop-up-last-line{font-size:12px;color:#a6a6a6}details#eztoc-ocassional-pop-up-container summary{display:inline-flex;margin-left:auto;margin-right:auto;justify-content:center;align-items:center;font-weight:600;padding:.5em 1.25em;border-radius:99em;color:#fff;background-color:#185adb;box-shadow:0 5px 15px rgba(0,0,0,.1);list-style:none;text-align:center;cursor:pointer;transition:.15s;position:relative;font-size:.9rem;z-index:99999}details#eztoc-ocassional-pop-up-container summary::-webkit-details-marker{display:none}details#eztoc-ocassional-pop-up-container summary:hover,summary:focus{background-color:#1348af}details#eztoc-ocassional-pop-up-container summary svg{width:25px;margin-left:5px;vertical-align:baseline}@-webkit-keyframes ez_toc_ocassional_pop_up_scale{0%{transform:ez_toc_ocassional_pop_up_scale(0)}100%{transform:ez_toc_ocassional_pop_up_scale(1)}}@keyframes ez_toc_ocassional_pop_up_scale{0%{transform:ez_toc_ocassional_pop_up_scale(0)}100%{transform:ez_toc_ocassional_pop_up_scale(1)}}
INLINEOCCASIONALADSPOPUSCCS;
                            
                    wp_add_inline_style( 'cn_toc_admin_style', $inlineAdminOccasionalAdsPopUpCCS );
                    
                    $inlineAdminOccasionalAdsPopUpJS = <<<INLINEOCCASIONALADSPOPUSJS
function eztoc_set_admin_occasional_ads_pop_up_cookie(){var o=new Date;o.setFullYear(o.getFullYear()+1),document.cookie="eztoc_hide_admin_occasional_ads_pop_up_cookie_feedback=1; expires="+o.toUTCString()+"; path=/"}function eztoc_delete_admin_occasional_ads_pop_up_cookie(){document.cookie="eztoc_hide_admin_occasional_ads_pop_up_cookie_feedback=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;"}function eztoc_get_admin_occasional_ads_pop_up_cookie(){for(var o="eztoc_hide_admin_occasional_ads_pop_up_cookie_feedback=",a=decodeURIComponent(document.cookie).split(";"),e=0;e<a.length;e++){for(var c=a[e];" "==c.charAt(0);)c=c.substring(1);if(0==c.indexOf(o))return c.substring(o.length,c.length)}return""}jQuery(function(o){var a=eztoc_get_admin_occasional_ads_pop_up_cookie();void 0!==a&&""!==a&&o("details#eztoc-ocassional-pop-up-container").attr("open",!1),o("details#eztoc-ocassional-pop-up-container span.eztoc-promotion-close-btn").click(function(a){o("details#eztoc-ocassional-pop-up-container summary").click()}),o("details#eztoc-ocassional-pop-up-container summary").click(function(a){var e=o(this).parents("details#eztoc-ocassional-pop-up-container"),c=o(e).attr("open");void 0!==c&&!1!==c?eztoc_set_admin_occasional_ads_pop_up_cookie():eztoc_delete_admin_occasional_ads_pop_up_cookie()})});      
INLINEOCCASIONALADSPOPUSJS;
                            
			wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminOccasionalAdsPopUpJS );
                }

		/**
		 * inlineAdminStickyToggleJS Method
		 * Prints out inline Sticky Toggle JS after the core CSS file to allow overriding core styles via options.
		 *
		 * @access private
		 * @return void
		 * @since  2.0.32
		 * @static
		 */
		private static function inlineAdminStickyToggleJS() {
            $stickyToggleOpenButtonTextJS = "";
            if( empty( ezTOC_Option::get( 'sticky-toggle-open-button-text' ) ) ) {
                $stickyToggleOpenButtonTextJS = "$('input[name=\"ez-toc-settings[sticky-toggle-open-button-text]\"').val('Index')";
            }
			$inlineAdminStickyToggleJS = <<<INLINESTICKYTOGGLEJS
/**
 * Admin Sticky Sidebar JS
 */
jQuery(function($) {

    let stickyToggleCheckbox = $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle]']");
    let stickyTogglePosition = $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-position]']");
    let stickyToggleWidth = $('#eztoc-general').find("select[name='ez-toc-settings[sticky-toggle-width]']");
    let stickyToggleWidthCustom = $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-width-custom]']");
    let stickyToggleHeight = $('#eztoc-general').find("select[name='ez-toc-settings[sticky-toggle-height]']");
    let stickyToggleHeightCustom = $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-height-custom]']");
    let stickyToggleCloseOnMobile = $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-close-on-mobile]']");
    
    $stickyToggleOpenButtonTextJS
    
    if($(stickyToggleCheckbox).prop('checked') == false) {
        $(stickyTogglePosition).parents('tr').hide(500);
        $(stickyToggleWidth).parents('tr').hide(500);
        $(stickyToggleWidthCustom).parents('tr').hide(500);
        $(stickyToggleHeight).parents('tr').hide(500);
        $(stickyToggleCloseOnMobile).parents('tr').hide(500);
                                
        $(stickyToggleHeightCustom).parents('tr').hide(500);
        $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-position]'][value='left']").prop('checked', true);
        $(stickyToggleWidth).val('auto');
        $(stickyToggleHeight).val('auto');
        $(stickyToggleCloseOnMobile).prop('checked', false);

                                
        $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').parents('tr').hide(500);
        $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').val('Index');
    }
    $(document).on("change, click", "input[name='ez-toc-settings[sticky-toggle]']", function() {
    
        if($(stickyToggleCheckbox).prop('checked') == true) {
            $(stickyTogglePosition).parents('tr').show(500);
            $(stickyToggleWidth).parents('tr').show(500);
            $(stickyToggleHeight).parents('tr').show(500);
            $(stickyToggleCloseOnMobile).parents('tr').show(500);
                                
            $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').parents('tr').show(500);
            $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').val('Index');
        } else {
            $(stickyTogglePosition).parents('tr').hide(500);
            $(stickyToggleWidth).parents('tr').hide(500);
            $(stickyToggleWidthCustom).parents('tr').hide(500);
            $(stickyToggleHeight).parents('tr').hide(500);
            $(stickyToggleCloseOnMobile).parents('tr').hide(500);
                                
            $(stickyToggleHeightCustom).parents('tr').hide(500);
            $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').parents('tr').hide(500);
            $('#eztoc-general').find("input[name='ez-toc-settings[sticky-toggle-position]'][value='left']").prop('checked', true);
            $(stickyToggleWidth).val('auto');
            $(stickyToggleHeight).val('auto');
            $(stickyToggleCloseOnMobile).prop('checked', false);
                                
            $('input[name="ez-toc-settings[sticky-toggle-open-button-text]"').val('Index');
        }
        
    });
     
    
    if($(stickyToggleWidth).val() == '' || $(stickyToggleWidth).val() != 'custom')
        $(stickyToggleWidthCustom).parents('tr').hide();
        
    $(document).on("change", "select[name='ez-toc-settings[sticky-toggle-width]']", function() {
//        console.log("change-stickyToggleWidth");
        if($(stickyToggleWidth).val() == 'custom') {
            $(stickyToggleWidthCustom).val('350px');
            $(stickyToggleWidthCustom).parents('tr').show(500);
        } else {
            $(stickyToggleWidthCustom).val('');
            $(stickyToggleWidthCustom).parents('tr').hide(500);
        }
    });
     
    
    if($(stickyToggleHeight).val() == '' || $(stickyToggleHeight).val() != 'custom')
        $(stickyToggleHeightCustom).parents('tr').hide();
        
    $(document).on("change", "select[name='ez-toc-settings[sticky-toggle-height]']", function() {
//        console.log("change-stickyToggleHeight");
        if($(stickyToggleHeight).val() == 'custom') {
            $(stickyToggleHeightCustom).val('800px');
            $(stickyToggleHeightCustom).parents('tr').show(500);
        } else {
            $(stickyToggleHeightCustom).val('');
            $(stickyToggleHeightCustom).parents('tr').hide(500);
        }
    });
    
    
});
INLINESTICKYTOGGLEJS;
			wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminStickyToggleJS );
		}

		/**
		 * Callback to add plugin as a submenu page of the Options page.
		 *
		 * This also adds the action to enqueue the scripts to be loaded on plugin's admin pages only.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function menu() {

			$page = add_submenu_page(
				'options-general.php',
				esc_html__( 'Table of Contents', 'easy-table-of-contents' ),
				esc_html__( 'Table of Contents', 'easy-table-of-contents' ),
				'manage_options',
				'table-of-contents',
				array( $this, 'page' )
			);

			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueueScripts' ) );
		}

		/**
		 * Enqueue the scripts.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function enqueueScripts() {

			wp_enqueue_script( 'cn_toc_admin_script' );
			wp_enqueue_style( 'cn_toc_admin_style' );
		}

		/**
		 * Callback to add the action which will register the table of contents post  metaboxes.
		 *
		 * Metaboxes will only be registered for the post types per user preferences.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function registerMetaboxes() {

			foreach ( get_post_types() as $type ) {

				if ( in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ) ) ) {

					add_action( "add_meta_boxes_$type", array( $this, 'metabox' ) );
					add_action( "save_post_$type", array( $this, 'save' ), 10, 3 );
				}
			}
		}

		/**
		 * Callback to register the table of contents metaboxes.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function metabox() {

			add_meta_box( 'ez-toc', esc_html__( 'Table of Contents', 'ez-toc' ), array( $this, 'displayMetabox' ) );
		}

		/**
		 * Callback to render the content of the table of contents metaboxes.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param object $post The post object.
		 * @param        $atts
		 */
		public function displayMetabox( $post, $atts ) {

			// Add an nonce field so we can check for it on save.
			wp_nonce_field( 'ez_toc_save', '_ez_toc_nonce' );

			$suppress = get_post_meta( $post->ID, '_ez-toc-disabled', true ) == 1 ? true : false;
			$insert   = get_post_meta( $post->ID, '_ez-toc-insert', true ) == 1 ? true : false;
			$headings = get_post_meta( $post->ID, '_ez-toc-heading-levels', true );
			$exclude  = get_post_meta( $post->ID, '_ez-toc-exclude', true );
			$altText  = get_post_meta( $post->ID, '_ez-toc-alttext', true );
			$visibility_hide_by_default  = get_post_meta( $post->ID, '_ez-toc-visibility_hide_by_default', true );

			if ( ! is_array( $headings ) ) {

				$headings = array();
			}
			?>

			<table class="form-table">

				<tbody>

				<tr>
					<th scope="row"></th>
					<td>

						<?php if ( in_array( get_post_type( $post ), ezTOC_Option::get( 'auto_insert_post_types', array() ) ) ) :

							ezTOC_Option::checkbox(
								array(
									'id'      => 'disabled-toc',
									'desc'    => esc_html__( 'Disable the automatic insertion of the table of contents.', 'easy-table-of-contents' ),
									'default' => $suppress,
								),
								$suppress
							);

						elseif( in_array( get_post_type( $post ), ezTOC_Option::get( 'enabled_post_types', array() ) ) ):

							ezTOC_Option::checkbox(
								array(
									'id'      => 'insert-toc',
									'desc'    => esc_html__( 'Insert table of contents.', 'easy-table-of-contents' ),
									'default' => $insert,
								),
								$insert
							);

						endif; ?>

					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Advanced:', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::descriptive_text(
							array(
								'id' => 'exclude-desc',
								'name' => '',
								'desc' => '<p><strong>' . esc_html__( 'NOTE:', 'easy-table-of-contents' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . esc_html__( 'Using the advanced options below will override the global advanced settings.', 'easy-table-of-contents' ) . '</li>' .
								          '</ul>',
							)
						);
						?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Headings:', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::checkboxgroup(
							array(
								'id' => 'heading-levels',
								'desc' => esc_html__( 'Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.', 'easy-table-of-contents' ),
								'options' => array(
									'1' => __( 'Heading 1 (h1)', 'easy-table-of-contents' ),
									'2' => __( 'Heading 2 (h2)', 'easy-table-of-contents' ),
									'3' => __( 'Heading 3 (h3)', 'easy-table-of-contents' ),
									'4' => __( 'Heading 4 (h4)', 'easy-table-of-contents' ),
									'5' => __( 'Heading 5 (h5)', 'easy-table-of-contents' ),
									'6' => __( 'Heading 6 (h6)', 'easy-table-of-contents' ),
								),
								'default' => array(),
							),
							array_map( 'absint', $headings )
						);
						?>
					</td>
				</tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e( 'Initial View', 'easy-table-of-contents' ); ?></th>
                                    <td>
                                        <?php
                                            ezTOC_Option::checkbox(
                                                array(
							'id' => 'visibility_hide_by_default',
							'name' => __( 'Initial View', 'easy-table-of-contents' ),
							'desc' => __( 'Initially hide the table of contents.', 'easy-table-of-contents' ),
							'default' => false,
						),
                                                $visibility_hide_by_default
                                            );
                                        ?>
                                    </td>
                                </tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Alternate Headings', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::textarea(
							array(
								'id' => 'alttext',
								'desc' => __( 'Specify alternate table of contents header string. Add the header to be replaced and the alternate header on a single line separated with a pipe <code>|</code>. Put each additional original and alternate header on its own line.', 'easy-table-of-contents' ),
								'size' => 'large',
								'default' => '',
							),
							$altText
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td>
						<?php
						ezTOC_Option::descriptive_text(
							array(
								'id' => 'alttext-desc',
								'name' => '',
								'desc' => '<p><strong>' . esc_html__( 'Examples:', 'easy-table-of-contents' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . __( '<code>Level [1.1]|Alternate TOC Header</code> Replaces Level [1.1] in the table of contents with Alternate TOC Header.', 'easy-table-of-contents' ) . '</li>' .
								          '</ul>' .
								          '<p>' . __( '<strong>Note:</strong> This is case sensitive.', 'easy-table-of-contents' ) . '</p>',
							)
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Exclude Headings', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::text(
							array(
								'id' => 'exclude',
								'desc' => __( 'Specify headings to be excluded from appearing in the table of contents. Separate multiple headings with a pipe <code>|</code>. Use an asterisk <code>*</code> as a wildcard to match other text.', 'easy-table-of-contents' ),
								'size' => 'large',
								'default' => '',
							),
							$exclude
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td>
						<?php
						ezTOC_Option::descriptive_text(
							array(
								'id' => 'exclude-desc',
								'name' => '',
								'desc' => '<p><strong>' . esc_html__( 'Examples:', 'easy-table-of-contents' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . __( '<code>Fruit*</code> Ignore headings starting with "Fruit".', 'easy-table-of-contents' ) . '</li>' .
								          '<li>' . __( '<code>*Fruit Diet*</code> Ignore headings with "Fruit Diet" somewhere in the heading.', 'easy-table-of-contents' ) . '</li>' .
								          '<li>' . __( '<code>Apple Tree|Oranges|Yellow Bananas</code> Ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas".', 'easy-table-of-contents' ) . '</li>' .
								          '</ul>' .
								          '<p>' . __( '<strong>Note:</strong> This is not case sensitive.', 'easy-table-of-contents' ) . '</p>',
							)
						);
						?>
					</td>
				</tr>
				</tbody>
			</table>

			<?php
		}

		/**
		 * Callback which saves the user preferences from the table of contents metaboxes.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @param int    $post_id The post ID.
		 * @param object $post    The post object.
		 * @param bool   $update  Whether this is an existing post being updated or not.
		 */
		public function save( $post_id, $post, $update ) {

			if ( current_user_can( 'edit_post', $post_id ) &&
			     isset( $_REQUEST['_ez_toc_nonce'] ) &&
			     wp_verify_nonce( $_REQUEST['_ez_toc_nonce'], 'ez_toc_save' )
			) {

				// Checkboxes are present if checked, absent if not.
				if ( isset( $_REQUEST['ez-toc-settings']['disabled-toc'] ) ) {

					update_post_meta( $post_id, '_ez-toc-disabled', true );

				} else {

					update_post_meta( $post_id, '_ez-toc-disabled', false );

				}

				if ( isset( $_REQUEST['ez-toc-settings']['insert-toc'] ) ) {

					update_post_meta( $post_id, '_ez-toc-insert', true );

				} else {

					update_post_meta( $post_id, '_ez-toc-insert', false );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['heading-levels'] ) && ! empty( $_REQUEST['ez-toc-settings']['heading-levels'] ) ) {

					if ( is_array( $_REQUEST['ez-toc-settings']['heading-levels'] ) ) {

						$headings = array_map( 'absint', $_REQUEST['ez-toc-settings']['heading-levels'] );

					} else {

						$headings = array();
					}

					update_post_meta( $post_id, '_ez-toc-heading-levels', $headings );

				} else {

					update_post_meta( $post_id, '_ez-toc-heading-levels', array() );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['alttext'] ) && ! empty( $_REQUEST['ez-toc-settings']['alttext'] ) ) {

					if ( is_string( $_REQUEST['ez-toc-settings']['alttext'] ) ) {

						$alttext = trim( $_REQUEST['ez-toc-settings']['alttext'] );

					} else {

						$alttext = '';
					}

					/*
					 * This is basically `esc_html()` but does not encode quotes.
					 * This is to allow angle brackets and such which `wp_kses_post` would strip as "evil" scripts.
					 */
					$alttext = wp_check_invalid_utf8( $alttext );
					$alttext = _wp_specialchars( $alttext, ENT_NOQUOTES );

					update_post_meta( $post_id, '_ez-toc-alttext', wp_kses_post( $alttext ) );

				} else {

					update_post_meta( $post_id, '_ez-toc-alttext', '' );
				}
                                
                                if ( isset( $_REQUEST['ez-toc-settings']['visibility_hide_by_default'] ) && ! empty( $_REQUEST['ez-toc-settings']['visibility_hide_by_default'] ) ) {

					update_post_meta( $post_id, '_ez-toc-visibility_hide_by_default', true );

				} else {

					update_post_meta( $post_id, '_ez-toc-visibility_hide_by_default', false );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['exclude'] ) && ! empty( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

					if ( is_string( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

						$exclude = trim( $_REQUEST['ez-toc-settings']['exclude'] );

					} else {

						$exclude = '';
					}

					/*
					 * This is basically `esc_html()` but does not encode quotes.
					 * This is to allow angle brackets and such which `wp_kses_post` would strip as "evil" scripts.
					 */
					$exclude = wp_check_invalid_utf8( $exclude );
					$exclude = _wp_specialchars( $exclude, ENT_NOQUOTES );

					update_post_meta( $post_id, '_ez-toc-exclude', wp_kses_post( $exclude ) );

				} else {

					update_post_meta( $post_id, '_ez-toc-exclude', '' );
				}

			}

		}


	     /**
	     * Enqueue Admin js scripts
	     *
	     */
		public function load_scripts($pagenow){

			if (isset($pagenow) && $pagenow != 'settings_page_table-of-contents' && strpos($pagenow, 'table-of-contents') == false) {
                
                return false;
             }

			  wp_enqueue_script( 'eztoc-admin-js', EZ_TOC_URL . 'assets/js/eztoc-admin.js',array('jquery'), ezTOC::VERSION,true );

				 $data = array(     
					'ajax_url'      		       => admin_url( 'admin-ajax.php' ),
					'eztoc_security_nonce'         => wp_create_nonce('eztoc_ajax_check_nonce'),  
				);								

				$data = apply_filters('eztoc_localize_filter',$data,'eztoc_admin_data');

				wp_localize_script( 'eztoc-admin-js', 'eztoc_admin_data', $data );
		}

     /**
     * This is a ajax handler function for sending email from user admin panel to us. 
     * @return type json string
     */

		public function eztoc_send_query_message(){   
		    
		        if ( ! isset( $_POST['eztoc_security_nonce'] ) ){
		           return; 
		        }
		        if ( !wp_verify_nonce( $_POST['eztoc_security_nonce'], 'eztoc_ajax_check_nonce' ) ){
		           return;  
		        }   
		        $message        = $this->eztoc_sanitize_textarea_field($_POST['message']); 
		        $email          = sanitize_email($_POST['email']);
		                                
		        if(function_exists('wp_get_current_user')){

		            $user           = wp_get_current_user();

		         
		            $message = '<p>'.$message.'</p><br><br>'.'Query from Easy Table of Content plugin support tab';
		            
		            $user_data  = $user->data;        
		            $user_email = $user_data->user_email;     
		            
		            if($email){
		                $user_email = $email;
		            }            
		            //php mailer variables        
		            $sendto    = 'team@magazine3.in';
		            $subject   = "Easy Table of Content Query";
		            
		            $headers[] = 'Content-Type: text/html; charset=UTF-8';
		            $headers[] = 'From: '. esc_attr($user_email);            
		            $headers[] = 'Reply-To: ' . esc_attr($user_email);
		            // Load WP components, no themes.   

		            $sent = wp_mail($sendto, $subject, $message, $headers); 

		            if($sent){

		                 echo json_encode(array('status'=>'t'));  

		            }else{

		                echo json_encode(array('status'=>'f'));            

		            }
		            
		        }
		                        
		        wp_die();           
		}

		public function eztoc_sanitize_textarea_field( $str ) {

			if ( is_object( $str ) || is_array( $str ) ) {
				return '';
			}

			$str = (string) $str;

			$filtered = wp_check_invalid_utf8( $str );

			if ( strpos( $filtered, '<' ) !== false ) {
				$filtered = wp_pre_kses_less_than( $filtered );
				// This will strip extra whitespace for us.
				$filtered = wp_strip_all_tags( $filtered, false );

				// Use HTML entities in a special case to make sure no later
				// newline stripping stage could lead to a functional tag.
				$filtered = str_replace( "<\n", "&lt;\n", $filtered );
			}
			
			$filtered = trim( $filtered );

			$found = false;
			while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
				$filtered = str_replace( $match[0], '', $filtered );
				$found    = true;
			}

			if ( $found ) {
				// Strip out the whitespace that may now exist after removing the octets.
				$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
			}

			return $filtered;
		}

		/**
		 * Callback used to render the admin options page.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function page() {

			include EZ_TOC_PATH . '/includes/inc.admin-options-page.php';
		}
	}

	new ezTOC_Admin();

}
