<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Widget' ) ) {

	/**
	 * Class ezTOC_Widget
	 */
	class ezTOC_Widget extends WP_Widget {

		/**
		 * Setup and register the table of contents widget.
		 *
		 * @access public
		 * @since  1.0
		 */
		public function __construct() {

			$options = array(
				'classname'   => 'ez-toc',
				'description' => __( 'Display the table of contents.', 'easy-table-of-contents' )
			);

			parent::__construct(
				'ezw_tco',
				__( 'Table of Contents', 'easy-table-of-contents' ),
				$options
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
			add_action( 'admin_footer-widgets.php', array( $this, 'printScripts' ), 9999 );
		}

		/**
		 * Callback which registers the widget with the Widget API.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @return void
		 */
		public static function register() {

			register_widget( __CLASS__ );
		}

		/**
		 * Callback to enqueue scripts on the Widgets admin page.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param string $hook_suffix
		 */
		public function enqueueScripts( $hook_suffix ) {

			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'underscore' );
		}

		/**
		 * Callback to print the scripts to the Widgets admin page footer.
		 *
		 * @access private
		 * @since  1.0
		 */
		public function printScripts() {
			?>
			<script>
				( function( $ ){
					function initColorPicker( widget ) {
						widget.find( '.color-picker' ).wpColorPicker( {
							change: _.throttle( function() { // For Customizer
								$(this).trigger( 'change' );
							}, 3000 )
						});
					}

					function onFormUpdate( event, widget ) {
						initColorPicker( widget );
					}

					$( document ).on( 'widget-added widget-updated', onFormUpdate );

					$( document ).ready( function() {
						$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
							initColorPicker( $( this ) );
						} );
					} );
				}( jQuery ) );
			</script>
			<?php
		}

		/**
		 * Display the post content. Optionally allows post ID to be passed
		 *
		 * @link http://stephenharris.info/get-post-content-by-id/
		 * @link http://wordpress.stackexchange.com/a/143316
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param int $post_id Optional. Post ID.
		 *
		 * @return string
		 */
		public function the_content( $post_id = 0 ) {

			global $post;
			$post = get_post( $post_id );
			setup_postdata( $post );
			ob_start();
			the_content();
			$content = ob_get_clean();
			wp_reset_postdata();

			return $content;
		}

		/**
		 * Renders the widgets.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {

			if ( is_404() || is_archive() || is_search() || ( ! is_front_page() && is_home() )  ) return;

			global $wp_query;

			$css_classes = '';

			$find    = $replace = array();
			$post    = get_post( $wp_query->post->ID );

			/*
			 * Ensure the ezTOC content filter is not applied when running `the_content` filter.
			 */
			remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
			$post->post_content = apply_filters( 'the_content', $post->post_content );
			add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );

			/**
			 * @var string $before_widget
			 * @var string $after_widget
			 * @var string $before_title
			 * @var string $after_title
			 */
			extract( $args );

			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			$items = ezTOC::extract_headings( $find, $replace, $post );

			if ( FALSE !== strpos( $title, '%PAGE_TITLE%' ) || FALSE !== strpos( $title, '%PAGE_NAME%' ) ) {

				$title = str_replace( '%PAGE_TITLE%', get_the_title(), $title );
			}

			if ( ezTOC_Option::get( 'show_hierarchy' ) ) {

				$css_classes = ' counter-hierarchy';

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

			if ( $instance['affix'] ) {

				$css_classes .= ' ez-toc-affix';
			}

			$css_classes = trim( $css_classes );

			// an empty class="" is invalid markup!
			if ( ! $css_classes ) {

				$css_classes = ' ';
			}

			if ( $items ) {

				echo $before_widget;

				echo '<div class="ez-toc-widget-container ' . $css_classes . '">' . PHP_EOL;

				do_action( 'ez_toc_before_widget' );

				/**
				 * @todo Instead of inline style, use the shadow DOM.
				 * @link https://css-tricks.com/playing-shadow-dom/
				 *
				 * @todo Consider not outputting the style if CSS is disabled.
				 * @link https://wordpress.org/support/topic/inline-styling-triggers-html-validation-error/
				 */

				if ( 0 < strlen( $title ) ) {

					?>

					<?php echo $before_title; ?>

					<span class="ez-toc-title-container">

						<style type="text/css">
							#<?php echo $this->id ?> .ez-toc-widget-container ul.ez-toc-list li.active::before {
								background-color: <?php echo esc_attr( $instance['highlight_color'] ); ?>;
							}
						</style>

						<span class="ez-toc-title"><?php echo $title; ?></span>

						<span class="ez-toc-title-toggle">

							<?php
							if ( ezTOC_Option::get( 'visibility' ) ) {

								echo '<a class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle"><i class="ez-toc-glyphicon ez-toc-icon-toggle"></i></a>';
							}
							?>

						</span>

					</span>

					<?php echo $after_title; ?>

					<?php
				}

				echo '<nav><ul class="ez-toc-list">'. PHP_EOL . $items . '</ul></nav>' . PHP_EOL;

				do_action( 'ez_toc_after_widget' );

				echo '</div>' . PHP_EOL;

				echo $after_widget;

				// Enqueue the script.
				wp_enqueue_script( 'ez-toc-js' );
			}
		}

		/**
		 * Update the widget settings.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = $old_instance;

			$instance['title'] = strip_tags( $new_instance['title'] );

			$instance['affix'] = array_key_exists( 'affix', $new_instance ) ? $new_instance['affix'] : '0';

			$instance['highlight_color'] = strip_tags( $new_instance['highlight_color'] );

			$instance['hide_inline'] = array_key_exists( 'hide_inline', $new_instance ) ? $new_instance['hide_inline'] : '0';

			//ezTOC_Option::set( 'show_toc_in_widget_only', $instance['hide_inline'] );
			//ezTOC_Option::set( 'show_toc_in_widget_only_post_types', $new_instance['show_toc_in_widget_only_post_types'] );

			return $instance;
		}

		/**
		 * Displays the widget settings on the Widgets admin page.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		public function form( $instance ) {

			$defaults = array(
				'affix' => '0',
				'highlight_color' => '#ededed',
				'title' => '',
			);

			$instance = wp_parse_args( (array) $instance, $defaults );

			$highlight_color = esc_attr( $instance[ 'highlight_color' ] );

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'easy-table-of-contents' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"
				       style="width:100%;"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'highlight_color' ); ?>"><?php _e( 'Active Section Highlight Color:', 'easy-table-of-contents' ); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name( 'highlight_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'highlight_color' ); ?>" value="<?php echo $highlight_color; ?>" data-default-color="<?php echo $defaults['highlight_color']; ?>" />
			</p>

			<p style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<input class="checkbox" type="checkbox" <?php checked( $instance['affix'], 1 ); ?>
				       id="<?php echo $this->get_field_id( 'affix' ); ?>"
				       name="<?php echo $this->get_field_name( 'affix' ); ?>" value="1"/>
				<label for="<?php echo $this->get_field_id( 'affix' ); ?>"> <?php _e( 'Affix or pin the widget.', 'easy-table-of-contents' ); ?></label>
			</p>

			<p class="description" style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<?php _e( 'If you choose to affix the widget, do not add any other widgets on the sidebar. Also, make sure you have only one instance Table of Contents widget on the page.', 'easy-table-of-contents' ); ?>
			</p>
			<?php
		}

	} // end class

	add_action( 'widgets_init', array( 'ezTOC_Widget', 'register' ) );
}
