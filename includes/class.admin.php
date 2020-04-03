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

			add_action( 'admin_init', array( $this, 'registerScripts' ) );
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'init', array( $this, 'registerMetaboxes' ), 99 );
			add_filter( 'plugin_action_links_' . EZ_TOC_BASE_NAME, array( $this, 'pluginActionLinks' ), 10, 2 );
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

			$action = array();

			$action[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( add_query_arg( 'page', 'table-of-contents', self_admin_url( 'options-general.php' ) ) ),
				esc_html__( 'Settings', 'easy-table-of-contents' )
			);

			return array_merge( $action, $links );
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
		 * Callback used to render the admin options page.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public function page() {

			include EZ_TOC_PATH . 'includes/inc.admin-options-page.php';
		}
	}

	new ezTOC_Admin();

}
