<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC_Widget' ) ) {

	class ezTOC_Widget extends WP_Widget {

		public function __construct() {

			//$widget_options  = array(
			//	'classname'   => 'ez-toc-widget',
			//	'description' => __( ' with this widget', 'ez_toc' )
			//);
			//$control_options = array(
			//	'width'   => 250,
			//	'height'  => 350,
			//	'id_base' => 'toc-widget'
			//);
			//$this->WP_Widget( 'toc-widget', 'TOC+', $widget_options, $control_options );

			$options = array(
				'classname'   => 'ez-toc',
				'description' => __( 'Display the table of contents.', 'ez_toc' )
			);

			parent::__construct(
				'ezw_tco',
				__( 'Table of Contents', 'ez_toc' ),
				$options
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
			add_action( 'admin_footer-widgets.php', array( $this, 'printScripts' ), 9999 );
		}

		/**
		 * Registers the widget with the WordPress Widget API.
		 *
		 * @access public
		 * @since  2.0
		 *
		 * @return void
		 */
		public static function register() {

			register_widget( __CLASS__ );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.0
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
		 * Print scripts.
		 *
		 * @since 1.0
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
		 * Widget output to the public
		 *
		 * @param array $args
		 * @param array $instance
		 */
		function widget( $args, $instance ) {

			global $wp_query;

			$css_classes = '';

			$find  = $replace = array();
			$post  = get_post( $wp_query->post->ID );

			/**
			 * @var string $before_widget
			 * @var string $after_widget
			 * @var string $before_title
			 * @var string $after_title
			 */
			extract( $args );

			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			//$items = ezTOC::extract_headings( $find, $replace, $this->the_content() );
			$items = ezTOC::get( $post->ID );

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

			// bullets?
			//if ( ezTOC_Option::get( 'bullet_spacing' ) ) {
			//
			//	$css_classes = ' have_bullets';
			//
			//} else {
			//
			//	$css_classes = ' no_bullets';
			//}

			$css_classes = trim( $css_classes );

			// an empty class="" is invalid markup!
			if ( ! $css_classes ) {

				$css_classes = ' ';
			}

			if ( $items ) {

				echo $before_widget;

				echo '<div class="ez-toc-widget-container ' . $css_classes . '">' . PHP_EOL;

				do_action( 'ez_toc_before_widget' );

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

								echo '<a class="pull-right btn btn-xs btn-default ez-toc-toggle"><i class="glyphicon ez-toc-icon-toggle"></i></a>';
							}
							?>
						</span>

					</span>

					<?php echo $after_title; ?>

					<?php
				}

				echo '<ul class="ez-toc-list">'. PHP_EOL . $items . '</ul>' . PHP_EOL;

				do_action( 'ez_toc_after_widget' );

				echo '</div>' . PHP_EOL;

				echo $after_widget;

				// Enqueue the script.
				wp_enqueue_script( 'ez-toc-js' );
			}
		}

		/**
		 * Update the widget settings
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 */
		function update( $new_instance, $old_instance ) {

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
		 * Displays the widget settings on the widget panel.
		 *
		 * @param array $instance
		 *
		 * @return string|void
		 */
		function form( $instance ) {

			$defaults = array(
				'affix' => '0',
				'highlight_color' => '#ededed',
				'title' => '',
			);

			$instance = wp_parse_args( (array) $instance, $defaults );

			$highlight_color = esc_attr( $instance[ 'highlight_color' ] );

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'ez_toc' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"
				       style="width:100%;"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'highlight_color' ); ?>"><?php _e( 'Active Section Highlight Color:', 'ez_toc' ); ?></label><br>
				<input type="text" name="<?php echo $this->get_field_name( 'highlight_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'highlight_color' ); ?>" value="<?php echo $highlight_color; ?>" data-default-color="<?php echo $defaults['highlight_color']; ?>" />
			</p>

			<p style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<input class="checkbox" type="checkbox" <?php checked( $instance['affix'], 1 ); ?>
				       id="<?php echo $this->get_field_id( 'affix' ); ?>"
				       name="<?php echo $this->get_field_name( 'affix' ); ?>" value="1"/>
				<label for="<?php echo $this->get_field_id( 'affix' ); ?>"> <?php _e( 'Affix or pin the widget.', 'ez_toc' ); ?></label>
			</p>

			<p class="description" style="display: <?php echo ezTOC_Option::get( 'widget_affix_selector' ) ? 'block' : 'none'; ?>;">
				<?php _e( 'If you choose to affix the widget, do not add any other widgets on the sidebar. Also, make sure you have only one instance Table of Contents widget on the page.', 'ez_toc' ); ?>
			</p>
			<?php
		}

	} // end class

	add_action( 'widgets_init', array( 'ezTOC_Widget', 'register' ) );
}
