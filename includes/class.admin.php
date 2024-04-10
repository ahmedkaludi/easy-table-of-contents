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
			$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'cn_toc_admin_script', EZ_TOC_URL . "assets/js/admin$min.js", array( 'jquery', 'wp-color-picker' ), ezTOC::VERSION, true );
			wp_register_style( 'cn_toc_admin_style', EZ_TOC_URL . "assets/css/admin$min.css", array( 'wp-color-picker' ), ezTOC::VERSION );

			wp_enqueue_script( 'cn_toc_admin_script' );
            $data = array(
                'ajax_url'      		       => admin_url( 'admin-ajax.php' ),
                'eztoc_security_nonce'         => wp_create_nonce('eztoc_ajax_check_nonce'),
            );

            $data = apply_filters( 'eztoc_localize_filter', $data, 'eztoc_admin_data' );

            wp_localize_script( 'cn_toc_admin_script', 'cn_toc_admin_data', $data );
                        
                        self::inlineAdminAMPNonJS();
						self::inlineAdminHeadingsPaddingJS();
						self::inlineAdminDisplayHeaderLabel();
						self::inlineAdminInitialView();
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
		 * inlineAdminDisplayHeaderLabel Method
		 * Prints out inline AMP Non JS.
		 *
		 * @access private
		 * @return void
		 * @since  2.0.51
		 * @static
		*/
		private static function inlineAdminDisplayHeaderLabel() {
			
		$inlineAdminDisplayHeaderLabel = <<<inlineAdminDisplayHeaderLabel
jQuery(function($) {

	let showHeadingText = $('#eztoc-general').find("input[name='ez-toc-settings[show_heading_text]']");
	let visiblityOnHeaderText = $('#eztoc-general').find("input[name='ez-toc-settings[visibility_on_header_text]']");
	let headerText = $('#eztoc-general').find("input[name='ez-toc-settings[heading_text]']");

	if($(showHeadingText).prop('checked') == false) {
		$(visiblityOnHeaderText).parents('tr').hide(500);
		$(headerText).parents('tr').hide(500);
	}

	$(document).on("change, click", "input[name='ez-toc-settings[show_heading_text]']", function() {
	
		if($(this).prop('checked') == true) {
			$(visiblityOnHeaderText).parents('tr').show(500);
			$(headerText).parents('tr').show(500);
		} else {
			$(visiblityOnHeaderText).parents('tr').hide(500);
			$(headerText).parents('tr').hide(500);
		}

	});
});
inlineAdminDisplayHeaderLabel;

			wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminDisplayHeaderLabel );
				
		}

		/**
		 * inlineAdminInitialView Method
		 * Prints out inline AMP Non JS.
		 *
		 * @access private
		 * @return void
		 * @since  2.0.51
		 * @static
		*/
		private static function inlineAdminInitialView() {
			
		$inlineAdminInitialView = <<<inlineAdminInitialView
jQuery(function($) {

	let visibility = $('#eztoc-general').find("input[name='ez-toc-settings[visibility]']");
	let visiblityHideByDefault = $('#eztoc-general').find("input[name='ez-toc-settings[visibility_hide_by_default]']");
	
	if($(visibility).prop('checked') == false) {
		$(visiblityHideByDefault).parents('tr').hide(500);
	}

	$(document).on("change, click", "input[name='ez-toc-settings[visibility]']", function() {
	
		if($(this).prop('checked') == true) {
			$(visiblityHideByDefault).parents('tr').show(500);
		} else {
			$(visiblityHideByDefault).parents('tr').hide(500);
		}

	});
});
inlineAdminInitialView;

			wp_add_inline_script( 'cn_toc_admin_script', $inlineAdminInitialView );
				
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
			if(apply_filters('ez_toc_register_metaboxes_flag', true)){
			foreach ( get_post_types() as $type ) {

				if ( in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ) ) ) {

					add_action( "add_meta_boxes_$type", array( $this, 'metabox' ) );
					add_action( "save_post_$type", array( $this, 'save' ), 10, 3 );
				}
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

			add_meta_box( 'ez-toc', esc_html__( 'Table of Contents', 'easy-table-of-contents' ), array( $this, 'displayMetabox' ) );
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

			$suppress      = get_post_meta( $post->ID, '_ez-toc-disabled', true ) == 1 ? true : false;
			$insert        = get_post_meta( $post->ID, '_ez-toc-insert', true ) == 1 ? true : false;
			$header_label  = get_post_meta( $post->ID, '_ez-toc-header-label', true );
			$alignment     = get_post_meta( $post->ID, '_ez-toc-alignment', true );
			$headings      = get_post_meta( $post->ID, '_ez-toc-heading-levels', true );
			$exclude       = get_post_meta( $post->ID, '_ez-toc-exclude', true );
			$altText       = get_post_meta( $post->ID, '_ez-toc-alttext', true );
			$initial_view  = get_post_meta( $post->ID, '_ez-toc-visibility_hide_by_default', true );
			$hide_counter  = get_post_meta( $post->ID, '_ez-toc-hide_counter', true );

			$position  = get_post_meta( $post->ID, '_ez-toc-position-specific', true );
			if (empty($position)) {
				$position = ezTOC_Option::get( 'position' );
			}

			$custom_para_number  = get_post_meta( $post->ID, '_ez-toc-s_custom_para_number', true );
			if (empty($custom_para_number)) {
				$custom_para_number = ezTOC_Option::get( 'custom_para_number' );
			}

			$blockqoute_checkbox  = get_post_meta( $post->ID, '_ez-toc-s_blockqoute_checkbox', true );
			if ($blockqoute_checkbox == "") {
				$blockqoute_checkbox = ezTOC_Option::get( 'blockqoute_checkbox' );
			}

			$custom_img_number  = get_post_meta( $post->ID, '_ez-toc-s_custom_img_number', true );
			if (empty($custom_img_number)) {
				$custom_img_number = ezTOC_Option::get( 'custom_img_number' );
			}
			

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
					<th scope="row"><?php esc_html_e( 'Header Label', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::text(
							array(
								'id' => 'header-label',
								'desc' => '<br>'.__( 'Eg: Contents, Table of Contents, Page Contents', 'easy-table-of-contents' ),
								'default' => $header_label,
							),
							$header_label
						);
						?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Position', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::select(
							array(
								'id' => 'position-specific',
								'desc' => __( 'Choose where where you want to display the table of contents.', 'easy-table-of-contents' ), 
								'options' => array(
									'before' => __( 'Before first heading (default)', 'easy-table-of-contents' ),
									'after' => __( 'After first heading', 'easy-table-of-contents' ),
									'afterpara' => __( 'After first paragraph', 'easy-table-of-contents' ),
									'aftercustompara' => __( 'After paragraph number', 'easy-table-of-contents' ),
									'aftercustomimg' => __( 'After Image number', 'easy-table-of-contents' ),
									'top' => __( 'Top', 'easy-table-of-contents' ),
									'bottom' => __( 'Bottom', 'easy-table-of-contents' ),
								),
								'default' => $position,
							),
							$position
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Select Image', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
							ezTOC_Option::number(
								array(
									'id' => 's_custom_img_number',
									'name' => __( 'Select Paragraph', 'easy-table-of-contents' ),
									'desc' => __( 'Select Image after which ETOC should get display', 'easy-table-of-contents' ),
									'type' => 'number',
									'size' => 'small',
									'default' => $custom_img_number,
							),
								$custom_img_number
							);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Select Paragraph', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
							ezTOC_Option::number(
								array(
									'id' => 's_custom_para_number',
									'desc' => __( 'Select paragraph after which ETOC should get display', 'easy-table-of-contents' ),
									'type' => 'number',
									'size' => 'small',
									'default' => $custom_para_number,
							),
								$custom_para_number
							);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Exclude Blockqoute', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
							ezTOC_Option::checkbox(
								array(
								'id' => 's_blockqoute_checkbox',
								'name' => __( 'Exclude Blockqoute', 'easy-table-of-contents' ),
								'desc' => __( 'Do not consider Paragraphs which are inside Blockqoute.', 'easy-table-of-contents' ),
								'default' => false,
							),
								$blockqoute_checkbox
							);
						?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Appearance:', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::descriptive_text(
							array(
								'id' => 'appearance-desc',
								'desc' => '<p><strong>' . esc_html__( 'NOTE:', 'easy-table-of-contents' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . esc_html__( 'Using the appearance options below will override the global Appearance settings.', 'easy-table-of-contents' ) . '</li>' .
								          '</ul>',
							)
						);
						?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Alignment', 'easy-table-of-contents' ); ?></th>
					<td>
						<?php
						ezTOC_Option::select(
							array(
								'id' => 'toc-alignment',
								'options' => array(
									'none' => __( 'None (Default)', 'easy-table-of-contents' ),
									'left' => __( 'Left', 'easy-table-of-contents' ),
									'right' => __( 'Right', 'easy-table-of-contents' ),
									'center' => __( 'Center', 'easy-table-of-contents' ),
								),
								'default' => $alignment,
							),
							$alignment
						);
						?>
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
                                                $initial_view
                                            );
                                        ?>
                                    </td>
                                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Hide Counter', 'easy-table-of-contents' ); ?></th>
                    <td>
                        <?php
                            ezTOC_Option::checkbox(
                                array(
                            		'id' => 'hide_counter',
                            		'name' => __( 'Hide Counter', 'easy-table-of-contents' ),
                            		'desc' => __( 'Do not show counters for the table of contents.', 'easy-table-of-contents' ),
                            		'default' => false,
                                ),
                                $hide_counter
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

				if ( isset( $_REQUEST['ez-toc-settings']['header-label'] )) {
					$header_label = sanitize_text_field( $_REQUEST['ez-toc-settings']['header-label'] );					
					update_post_meta( $post_id, '_ez-toc-header-label', $header_label );
				} 

				if ( isset( $_REQUEST['ez-toc-settings']['toc-alignment'] ) ) {
				    $align_values = array(
				                        'none',
				                        'left',
				                        'right',
				                        'center'
				                    );
				    $alignment = sanitize_text_field( $_REQUEST['ez-toc-settings']['toc-alignment'] );					
				    if( in_array( $alignment, $align_values ) ) {
				        update_post_meta( $post_id, '_ez-toc-alignment', $alignment );
				    }
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

					$alttext = '';
					
					if ( is_string( $_REQUEST['ez-toc-settings']['alttext'] ) ) {

						$alttext = trim( $_REQUEST['ez-toc-settings']['alttext'] );

							/*
						* This is basically `esc_html()` but does not encode quotes.
						* This is to allow angle brackets and such which `wp_kses_post` would strip as "evil" scripts.
						*/
						$alttext = wp_check_invalid_utf8( $alttext );
						$alttext = _wp_specialchars( $alttext, ENT_NOQUOTES );

					} 					

					update_post_meta( $post_id, '_ez-toc-alttext', wp_kses_post( $alttext ) );

				} else {

					update_post_meta( $post_id, '_ez-toc-alttext', '' );
				}
                                
                                if ( isset( $_REQUEST['ez-toc-settings']['visibility_hide_by_default'] ) && ! empty( $_REQUEST['ez-toc-settings']['visibility_hide_by_default'] ) ) {

					update_post_meta( $post_id, '_ez-toc-visibility_hide_by_default', true );

				} else {

					update_post_meta( $post_id, '_ez-toc-visibility_hide_by_default', false );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['hide_counter'] ) ) {

					update_post_meta( $post_id, '_ez-toc-hide_counter', true );

				} else {

					update_post_meta( $post_id, '_ez-toc-hide_counter', false );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['exclude'] ) && ! empty( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

					$exclude = '';
					if ( is_string( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

						$exclude = trim( $_REQUEST['ez-toc-settings']['exclude'] );

							/*
						* This is basically `esc_html()` but does not encode quotes.
						* This is to allow angle brackets and such which `wp_kses_post` would strip as "evil" scripts.
						*/
						$exclude = wp_check_invalid_utf8( $exclude );
						$exclude = _wp_specialchars( $exclude, ENT_NOQUOTES );

					} 					

					update_post_meta( $post_id, '_ez-toc-exclude', wp_kses_post( $exclude ) );

				} else {

					update_post_meta( $post_id, '_ez-toc-exclude', '' );
				}

				if ( isset( $_REQUEST['ez-toc-settings']['position-specific'] ) ) {
				    $align_values = array(
						'before',
						'after',
						'afterpara',
						'aftercustompara',
						'aftercustomimg',
						'top',
						'bottom',
					);
				    $position = sanitize_text_field( $_REQUEST['ez-toc-settings']['position-specific'] );					
				    if( in_array( $position, $align_values ) ) {
				        update_post_meta( $post_id, '_ez-toc-position-specific', $position );
				    }

					
					if($position == 'aftercustompara' ||  $position == 'afterpara') {
						if (isset($_REQUEST['ez-toc-settings']['s_blockqoute_checkbox'])) {
							$s_blockqoute_checkbox = sanitize_text_field( $_REQUEST['ez-toc-settings']['s_blockqoute_checkbox'] );					
							update_post_meta( $post_id, '_ez-toc-s_blockqoute_checkbox', 1 );
						}else{
							update_post_meta( $post_id, '_ez-toc-s_blockqoute_checkbox', 0 );
						}
					}

				    if($position == 'aftercustompara' ) {	
						if (isset($_REQUEST['ez-toc-settings']['s_custom_para_number'])) {	
						$s_custom_para_number = sanitize_text_field( $_REQUEST['ez-toc-settings']['s_custom_para_number'] );			
				        update_post_meta( $post_id, '_ez-toc-s_custom_para_number', $s_custom_para_number );
						}
				    }

				    if($position == 'aftercustomimg' ) {
						if (isset($_REQUEST['ez-toc-settings']['s_custom_img_number'])) {	
						$s_custom_img_number = sanitize_text_field( $_REQUEST['ez-toc-settings']['s_custom_img_number'] );					
				        update_post_meta( $post_id, '_ez-toc-s_custom_img_number', $s_custom_img_number );
						}
				    }
				}

			}

		}


	     /**
	     * Enqueue Admin js scripts
	     *
	     */
		public function load_scripts($pagenow){
			
			 if($pagenow == 'settings_page_table-of-contents'){
			 	$min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_enqueue_script( 'eztoc-admin-js', EZ_TOC_URL . "assets/js/eztoc-admin$min.js",array('jquery'), ezTOC::VERSION,true );

				 $data = array(     
					'ajax_url'      		       => admin_url( 'admin-ajax.php' ),
					'eztoc_security_nonce'         => wp_create_nonce('eztoc_ajax_check_nonce'),  
				);								

				$data = apply_filters('eztoc_localize_filter',$data,'eztoc_admin_data');

				wp_localize_script( 'eztoc-admin-js', 'eztoc_admin_data', $data );

				$this->eztoc_dequeue_scripts();

			 }
			  
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
				if ( !current_user_can( 'manage_options' ) ) {
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

		/**
		 * Function used to dequeue unwanted scripts on ETOC settings page.
		 *
		 * @since  2.0.52
		 */
		public function eztoc_dequeue_scripts() {						
				wp_dequeue_script( 'chats-js' ); 
				wp_dequeue_script( 'custom_wp_admin_js' );						            
		}
	}

	new ezTOC_Admin();

}