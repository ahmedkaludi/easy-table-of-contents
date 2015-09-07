<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Admin' ) ) {

	/**
	 * Class ezTOC_Admin
	 */
	final class ezTOC_Admin {

		public function __construct() {

			$this->hooks();
			//$this->registerMetaboxes();
		}

		public function hooks() {

			add_action( 'admin_init', array( $this, 'registerScripts' ) );
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'init', array( $this, 'registerMetaboxes' ), 99 );

			//add_filter( 'plugin_action_links', array( $this, 'pluginActionLinks' ), 10, 2 );
		}

		//public  function pluginActionLinks( $links, $file ) {
		//
		//	if ( $file == 'table-of-contents-plus/' . basename( __FILE__ ) ) {
		//		$settings_link = '<a href="options-general.php?page=toc">' . __( 'Settings', 'toc+' ) . '</a>';
		//		$links         = array_merge( array( $settings_link ), $links );
		//	}
		//
		//	return $links;
		//}

		public function registerScripts() {

			wp_register_script( 'cn_toc_admin_script', EZ_TOC_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), ezTOC::VERSION, TRUE );
			wp_register_style( 'cn_toc_admin_style', EZ_TOC_URL . 'assets/css/admin.css', array( 'wp-color-picker' ), ezTOC::VERSION );
		}

		public function menu() {

			$page = add_submenu_page(
				'options-general.php',
				__( 'Table of Contents', 'ez_toc' ),
				__( 'Table of Contents', 'ez_toc' ),
				'manage_options',
				'connections-toc',
				array( $this, 'page' )
			);

			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueueScripts' ) );
		}

		/**
		 * Load needed scripts and styles only on the toc administration interface.
		 */
		public function enqueueScripts() {

			wp_enqueue_script( 'cn_toc_admin_script' );
			wp_enqueue_style( 'cn_toc_admin_style' );
		}

		public function registerMetaboxes() {

			foreach ( get_post_types() as $type ) {

				if ( in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ) ) ) {

					add_action( "add_meta_boxes_$type", array( $this, 'metabox' ) );
					add_action( "save_post_$type", array( $this, 'save' ), 10, 3 );
				}
			}
		}

		public function metabox() {

			add_meta_box( 'ez-toc', __( 'Table of Contents', 'ez-toc' ), array( $this, 'displayMetabox' ) );
		}

		/**
		 * @param object $post The post object.
		 * @param        $atts
		 */
		public function displayMetabox( $post, $atts ) {

			// Add an nonce field so we can check for it on save.
			wp_nonce_field( 'ez_toc_save', '_ez_toc_nonce' );

			$suppress = get_post_meta( $post->ID, '_ez-toc-disabled', TRUE ) == 1 ? TRUE : FALSE;
			$insert   = get_post_meta( $post->ID, '_ez-toc-insert', TRUE ) == 1 ? TRUE : FALSE;
			$headings = get_post_meta( $post->ID, '_ez-toc-heading-levels', TRUE );
			$exclude  = get_post_meta( $post->ID, '_ez-toc-exclude', TRUE );

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
									'desc'    => __( 'Disable the automatic insertion of the table of contents.', 'ez_toc' ),
									'default' => $suppress,
								),
								$suppress
							);

						elseif( in_array( get_post_type( $post ), ezTOC_Option::get( 'enabled_post_types', array() ) ) ):

							ezTOC_Option::checkbox(
								array(
									'id'      => 'insert-toc',
									'desc'    => __( 'Insert table of contents.', 'ez_toc' ),
									'default' => $insert,
								),
								$insert
							);

						endif; ?>

					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Advanced:', 'ez_toc' ); ?></th>
					<td>
						<?php
						ezTOC_Option::descriptive_text(
							array(
								'id' => 'exclude-desc',
								'name' => '',
								'desc' => '<p><strong>' . __( 'NOTE:', 'ez_toc' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . __( 'Using the advanced options below will override the global advanced settings.', 'ez_toc' ) . '</li>' .
								          '</ul>',
							)
						);
						?>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Headings:', 'ez_toc' ); ?></th>
					<td>
						<?php
						ezTOC_Option::checkboxgroup(
							array(
								'id' => 'heading-levels',
								'desc' => __( 'Select the heading to consider when generating the table of contents. Deselecting a heading will exclude it.', 'ez_toc' ),
								'options' => array(
									'1' => __( 'Heading 1 (h1)', 'ez_toc' ),
									'2' => __( 'Heading 2 (h2)', 'ez_toc' ),
									'3' => __( 'Heading 3 (h3)', 'ez_toc' ),
									'4' => __( 'Heading 4 (h4)', 'ez_toc' ),
									'5' => __( 'Heading 5 (h5)', 'ez_toc' ),
									'6' => __( 'Heading 6 (h6)', 'ez_toc' ),
								),
								'default' => array(),
							),
							array_map( 'absint', $headings )
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Exclude Headings', 'ez_toc' ); ?></th>
					<td>
						<?php
						ezTOC_Option::text(
							array(
								'id' => 'exclude',
								'desc' => __( 'Specify headings to be excluded from appearing in the table of contents. Separate multiple headings with a pipe <code>|</code>. Use an asterisk <code>*</code> as a wildcard to match other text.', 'ez_toc' ),
								'size' => 'large',
								'default' => '',
							),
							esc_textarea( $exclude )
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
								'desc' => '<p><strong>' . __( 'Examples:', 'ez_toc' ) . '</strong></p>' .
								          '<ul>' .
								          '<li>' . __( '<code>Fruit*</code> Ignore headings starting with "Fruit".', 'ez_toc' ) . '</li>' .
								          '<li>' . __( '<code>*Fruit Diet*</code> Ignore headings with "Fruit Diet" somewhere in the heading.', 'ez_toc' ) . '</li>' .
								          '<li>' . __( '<code>Apple Tree|Oranges|Yellow Bananas</code> Ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas".', 'ez_toc' ) . '</li>' .
								          '</ul>' .
								          '<p>' . __( '<strong>Note:</strong> This is not case sensitive.', 'ez_toc' ) . '</p>',
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
		 * @param int    $post_id The post ID.
		 * @param object $post    The post object.
		 * @param bool   $update  Whether this is an existing post being updated or not.
		 */
		public function save( $post_id, $post, $update ) {

			if ( current_user_can( 'edit_post', $post_id ) && wp_verify_nonce( $_REQUEST['_ez_toc_nonce'], 'ez_toc_save' ) ) {

				// Checkboxes are present if checked, absent if not.
				if ( isset( $_REQUEST['ez-toc-settings']['disabled-toc'] ) ) {

					update_post_meta( $post_id, '_ez-toc-disabled', TRUE );

				} else {

					update_post_meta( $post_id, '_ez-toc-disabled', FALSE );

				}

				if ( isset( $_REQUEST['ez-toc-settings']['insert-toc'] ) ) {

					update_post_meta( $post_id, '_ez-toc-insert', TRUE );

				} else {

					update_post_meta( $post_id, '_ez-toc-insert', FALSE );
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

				if ( isset( $_REQUEST['ez-toc-settings']['exclude'] ) && ! empty( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

					if ( is_string( $_REQUEST['ez-toc-settings']['exclude'] ) ) {

						$exclude = stripslashes( trim( $_REQUEST['ez-toc-settings']['exclude'] ) );

					} else {

						$exclude = '';
					}

					update_post_meta( $post_id, '_ez-toc-exclude', $exclude );

				} else {

					update_post_meta( $post_id, '_ez-toc-exclude', '' );
				}

			}

		}

		public function page() {

			include EZ_TOC_PATH . 'includes/inc.admin-options-page.php';
		}
	}

	new ezTOC_Admin();

}
