<?php
// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) )
    exit;

if ( ! class_exists ( 'ezTOC_WidgetSticky' ) )
{

    /**
     * Class ezTOC_WidgetSticky
     */
    class ezTOC_WidgetSticky extends WP_Widget
    {

        /**
         * Setup and register the table of contents widget.
         *
         * @access public
         * @since 2.0.41
         */
        public function __construct ()
        {

            $options = array(
                'classname' => 'ez-toc-widget-sticky',
                'description' => __ ( 'Display the table of contents.', 'easy-table-of-contents' )
            );

            parent::__construct (
                    'ez_toc_widget_sticky',
                    __ ( 'Sticky Sidebar Table of Contents', 'easy-table-of-contents' ),
                    $options
            );

            add_action ( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
            add_action ( 'admin_footer-widgets.php', array( $this, 'printScripts' ), 9999 );
        }

        /**
         * Callback which registers the widget with the Widget API.
         *
         * @access public
         * @since 2.0.41
         * @static
         *
         * @return void
         */
        public static function register ()
        {

            register_widget ( __CLASS__ );
        }

        /**
         * Callback to enqueue scripts on the Widgets admin page.
         *
         * @access private
         * @since 1 .0
         *
         * @param string $hook_suffix
         */
        public function enqueueScripts ( $hook_suffix )
        {
            $min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            if ( 'widgets.php' !== $hook_suffix )
            {
                return;
            }

            wp_enqueue_style ( 'wp-color-picker' );
            wp_enqueue_script ( 'wp-color-picker' );
            wp_enqueue_script ( 'underscore' );

            $widgetStickyAdminCSSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky-admin$min.css" );
            wp_register_style ( 'ez-toc-widget-sticky-admin', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky-admin$min.css", array(), $widgetStickyAdminCSSVersion );
            wp_enqueue_style ( 'ez-toc-widget-sticky-admin', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky-admin$min.css", array(), $widgetStickyAdminCSSVersion );
        }

        /**
         * Callback to print the scripts to the Widgets admin page footer.
         *
         * @access private
         * @since 2.0.41
         */
        public function printScripts ()
        {
            ?>
            <script>
                (function ($) {
                    function initColorPicker(widget) {
                        widget.find('.color-picker').wpColorPicker({
                            change: _.throttle(function () { // For Customizer
                                $(this).trigger('change');
                            }, 3000)
                        });
                    }

                    function onFormUpdate(event, widget) {
                        initColorPicker(widget);
                    }

                    $(document).on('widget-added widget-updated', onFormUpdate);

                    $(document).ready(function () {
                        $('#widgets-right .widget:has(.color-picker)').each(function () {
                            initColorPicker($(this));
                        });
                    });
                }(jQuery));
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
         * @since 2.0.41
         *
         * @param int $post_id Optional. Post ID.
         *
         * @return string
         */
        public function the_content ( $post_id = 0 )
        {

            global $post;
            $post = get_post ( $post_id );
            setup_postdata ( $post );
            ob_start ();
            the_content ();
            $content = ob_get_clean ();
            wp_reset_postdata ();

            return $content;
        }

        /**
         * Renders the widgets.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $args
         * @param array $instance
         */
        public function widget ( $args, $instance )
        {
            $min = defined ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            if ( is_404 () || is_archive () || is_search () || ( ! is_front_page () && is_home () ) )
                return;

            //global $wp_query;
            //$find    = $replace = array();
            //$post    = get_post( $wp_query->post->ID );
            //$post = ezTOC_Post::get( get_the_ID() );//->applyContentFilter()->process();
            $post = ezTOC::get ( get_the_ID () );

            if( function_exists( 'post_password_required' ) ) {
                if( post_password_required() ) return;
            }
            
            /**
             * @link https://wordpress.org/support/topic/fatal-error-when-trying-to-access-widget-area/
             */
            if ( ! $post instanceof ezTOC_Post )
                return;

            /*
             * Ensure the ezTOC content filter is not applied when running `the_content` filter.
             */
            //remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
            //$post->post_content = apply_filters( 'the_content', $post->post_content );
            //add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );

            if ( $post -> hasTOCItems () )
            {

                /**
                 * @var string $before_widget
                 * @var string $after_widget
                 * @var string $before_title
                 * @var string $after_title
                 */
                extract ( $args );

                /**
                 * $ezTocWidgetStickyScrollFixedPosition = '30'/manual;
                 * $ezTocWidgetStickySidebarWidth = $('#ez-toc-widget-sticky-container').parents('.widget-area').width()/manual;
                 * $ezTocWidgetStickyFixedTopPosition = '30px'/manual;
                 * $ezTocWidgetStickyNavigationScrollBar = true('auto')/false;
                 * $ezTocWidgetStickyScrollMaxHeight = auto('calc(100vh - 111px)')/manual;
                 *  
                  'advanced_options' => '',
                  'scroll_fixed_position' => '30',
                  'sidebar_width' => 'auto',
                  'sidebar_width_size_unit' => 'none',
                  'fixed_top_position' => '30',
                  'fixed_top_position_size_unit' => 'px',
                  'navigation_scroll_bar' => 'on',
                  'scroll_max_height' => 'auto',
                  'scroll_max_height_size_unit' => 'none',
                 */
                $js_vars = array();
                $js_vars[ 'advanced_options' ] = '';
                $js_vars[ 'scroll_fixed_position' ] = '30';
                $js_vars[ 'sidebar_width' ] = 'auto';
                $js_vars[ 'sidebar_width_size_unit' ] = 'none';
                $js_vars[ 'fixed_top_position' ] = '30';
                $js_vars[ 'fixed_top_position_size_unit' ] = 'px';
                $js_vars[ 'navigation_scroll_bar' ] = 'on';
                $js_vars[ 'scroll_max_height' ] = 'auto';
                $js_vars[ 'scroll_max_height_size_unit' ] = 'none';

                if ( 'on' == $instance[ 'advanced_options' ] ||
                        $js_vars[ 'scroll_fixed_position' ] != $instance[ 'scroll_fixed_position' ] ||
                        $js_vars[ 'scroll_fixed_position' ] != $instance[ 'scroll_fixed_position' ] ||
                        $js_vars[ 'sidebar_width' ] != $instance[ 'sidebar_width' ] ||
                        $js_vars[ 'sidebar_width_size_unit' ] != $instance[ 'sidebar_width_size_unit' ] ||
                        $js_vars[ 'fixed_top_position' ] != $instance[ 'fixed_top_position' ] ||
                        $js_vars[ 'fixed_top_position_size_unit' ] != $instance[ 'fixed_top_position_size_unit' ] ||
                        $js_vars[ 'navigation_scroll_bar' ] != $instance[ 'navigation_scroll_bar' ] ||
                        $js_vars[ 'scroll_max_height' ] != $instance[ 'scroll_max_height' ] ||
                        $js_vars[ 'scroll_max_height_size_unit' ] != $instance[ 'scroll_max_height_size_unit' ]
                )
                {
                    $js_vars[ 'advanced_options' ] = $instance[ 'advanced_options' ];

                    if ( empty ( $instance[ 'scroll_fixed_position' ] ) || ( ! empty ( $instance[ 'scroll_fixed_position' ] ) && ! is_int ( $instance[ 'scroll_fixed_position' ] ) && 'auto' != $instance[ 'scroll_fixed_position' ] ) )
                        $js_vars[ 'scroll_fixed_position' ] = '30';
                    else
                        $js_vars[ 'scroll_fixed_position' ] = $instance[ 'scroll_fixed_position' ];

                    if ( empty ( $instance[ 'sidebar_width' ] ) || ( ! empty ( $instance[ 'sidebar_width' ] ) && ! is_int ( $instance[ 'sidebar_width' ] ) && 'auto' != $instance[ 'sidebar_width' ] ) )
                        $js_vars[ 'sidebar_width' ] = 'auto';
                    else
                        $js_vars[ 'sidebar_width' ] = $instance[ 'sidebar_width' ];

                    $js_vars[ 'sidebar_width_size_unit' ] = $instance[ 'sidebar_width_size_unit' ];

                    if ( empty ( $instance[ 'fixed_top_position' ] ) || ( ! empty ( $instance[ 'fixed_top_position' ] ) && ! is_int ( $instance[ 'fixed_top_position' ] ) && '30' != $instance[ 'fixed_top_position' ] ) )
                        $js_vars[ 'fixed_top_position' ] = '30';
                    else
                        $js_vars[ 'fixed_top_position' ] = $instance[ 'fixed_top_position' ];

                    $js_vars[ 'fixed_top_position_size_unit' ] = $instance[ 'fixed_top_position_size_unit' ];
                    $js_vars[ 'navigation_scroll_bar' ] = $instance[ 'navigation_scroll_bar' ];

                    if ( empty ( $instance[ 'scroll_max_height' ] ) || ( ! empty ( $instance[ 'scroll_max_height' ] ) && ! is_int ( $instance[ 'scroll_max_height' ] ) && 'auto' != $instance[ 'scroll_max_height' ] ) )
                        $js_vars[ 'scroll_max_height' ] = 'auto';
                    else
                        $js_vars[ 'scroll_max_height' ] = $instance[ 'scroll_max_height' ];

                    $js_vars[ 'scroll_max_height_size_unit' ] = $instance[ 'scroll_max_height_size_unit' ];
                }

                $class = array(
                    'ez-toc-widget-sticky-v' . str_replace ( '.', '_', ezTOC::VERSION ),
                    'ez-toc-widget-sticky',
                );

                $title = apply_filters ( 'widget_title', $instance[ 'title' ], $instance, $this -> id_base );
                //$items = ezTOC::extract_headings( $find, $replace, $post );

                if ( false !== strpos ( $title, '%PAGE_TITLE%' ) || false !== strpos ( $title, '%PAGE_NAME%' ) )
                {

                    $title = str_replace ( '%PAGE_TITLE%', get_the_title (), $title );
                }

                if ( ezTOC_Option::get ( 'show_hierarchy' ) )
                {

                    $class[] = 'counter-hierarchy';
                } else
                {

                    $class[] = 'counter-flat';
                }

                if ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ) == 'ltr' )
                {
                    $class[] = 'ez-toc-widget-sticky-container';
                }
                if ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ) == 'rtl' )
                {
                    $class[] = 'ez-toc-widget-sticky-container-rtl';
                }

                $class[] = 'ez-toc-widget-sticky-direction';

                $custom_classes = ezTOC_Option::get ( 'css_container_class', '' );

                if ( 0 < strlen ( $custom_classes ) )
                {

                    $custom_classes = explode ( ' ', $custom_classes );
                    $custom_classes = apply_filters ( 'ez_toc_widget_sticky_container_class', $custom_classes, $this );

                    if ( is_array ( $custom_classes ) )
                    {

                        $class = array_merge ( $class, $custom_classes );
                    }
                }

                $class = array_filter ( $class );
                $class = array_map ( 'trim', $class );
                $class = array_map ( 'sanitize_html_class', $class );

                echo $before_widget;
                do_action ( 'ez_toc_widget_sticky_before_widget_container' );

                echo '<div id="ez-toc-widget-sticky-container" class="ez-toc-widget-sticky-container ' . implode ( ' ', $class ) . '">' . PHP_EOL;

                do_action ( 'ez_toc_widget_sticky_before_widget' );

                /**
                 * @todo Instead of inline style, use the shadow DOM.
                 * @link https://css-tricks.com/playing-shadow-dom/
                 *
                 * @todo Consider not outputting the style if CSS is disabled.
                 * @link https://wordpress.org/support/topic/inline-styling-triggers-html-validation-error/
                 */
                if ( 0 < strlen ( $title ) )
                {
                    ?>

                    <?php echo $before_title; ?>
                    <span class="ez-toc-widget-sticky-title-container">

                        <style type="text/css">
                            #<?php echo $this -> id ?> .ez-toc-widget-sticky-container ul.ez-toc-widget-sticky-list li.active{
                                background-color: <?php echo esc_attr ( $instance[ 'highlight_color' ] ); ?>;
                            }
                        </style>

                        <span class="ez-toc-widget-sticky-title"><?php echo $title; ?></span>
                        <span class="ez-toc-widget-sticky-title-toggle">
                            <?php if ( 'css' != ezTOC_Option::get ( 'toc_loading' ) ): ?>




                                <?php
                                if ( ezTOC_Option::get ( 'visibility' ) )
                                {

                                    echo '<a href="#" class="ez-toc-widget-sticky-pull-right ez-toc-widget-sticky-btn ez-toc-widget-sticky-btn-xs ez-toc-widget-sticky-btn-default ez-toc-widget-sticky-toggle" aria-label="Widget Easy TOC toggle icon">' . ezTOC::getTOCToggleIcon () . '</a>';
                                }
                                ?>




                            <?php else: ?>
                                <?php
                                $toggle_view = '';
                                if ( ezTOC_Option::get ( 'visibility_hide_by_default' ) == true )
                                {
                                    $toggle_view = "checked";
                                }
                                if( true == get_post_meta( get_the_ID(), '_ez-toc-visibility_hide_by_default', true ) ) {
                                    $toggle_view = "checked";
                                }
                                $cssIconID = uniqid ();
                                $htmlCSSIcon = '<label for="ez-toc-widget-sticky-cssicon-toggle-item-' . $cssIconID . '" class="ez-toc-widget-sticky-pull-right ez-toc-widget-sticky-btn ez-toc-widget-sticky-btn-xs ez-toc-widget-sticky-btn-default ez-toc-widget-sticky-toggle">' . ezTOC::getTOCToggleIcon () . '</label>';
                                echo $htmlCSSIcon;
                                ?>
                            <?php endif; ?>
                        </span>
                    </span>

                    <?php echo $after_title; ?>
                    <?php if ( 'css' == ezTOC_Option::get ( 'toc_loading' ) ): ?>
                        <label for="ez-toc-widget-sticky-cssicon-toggle-item-<?= $cssIconID ?>" class="cssiconcheckbox">1</label><input type="checkbox" id="ez-toc-widget-sticky-cssicon-toggle-item-<?= $cssIconID ?>" <?= $toggle_view ?> style="display:none" />
                    <?php endif; ?>
                    <?php
                }
                do_action ( 'ez_toc_widget_sticky_before' );
                echo '<nav>' . PHP_EOL . $post -> getTOCList ( 'ez-toc-widget-sticky' ) . '</nav>' . PHP_EOL;
                do_action ( 'ez_toc_widget_sticky_after' );
                do_action ( 'ez_toc_widget_sticky_after_widget' );

                echo '</div>' . PHP_EOL;
                do_action ( 'ez_toc_widget_sticky_after_widget_container' );

                echo $after_widget;

                // Enqueue the script.
                $widgetCSSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky$min.css" );
                wp_register_style ( 'ez-toc-widget-sticky', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky$min.css", array(), $widgetCSSVersion );
                wp_enqueue_style ( 'ez-toc-widget-sticky', EZ_TOC_URL . "assets/css/ez-toc-widget-sticky$min.css", array(), $widgetCSSVersion );

                wp_add_inline_style ( 'ez-toc-widget-sticky', ezTOC::InlineCountingCSS ( ezTOC_Option::get ( 'heading-text-direction', 'ltr' ), 'ez-toc-widget-sticky-direction', 'ez-toc-widget-sticky-container', 'counter', 'ez-toc-widget-sticky-container' ) );

                $widgetJSVersion = ezTOC::VERSION . '-' . filemtime ( EZ_TOC_PATH . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "ez-toc-widget-sticky$min.js" );
                wp_register_script ( 'ez-toc-widget-stickyjs', EZ_TOC_URL . "assets/js/ez-toc-widget-sticky$min.js", array( 'jquery' ), $widgetJSVersion );
                wp_enqueue_script ( 'ez-toc-widget-stickyjs', EZ_TOC_URL . "assets/js/ez-toc-widget-sticky$min.js", array( 'jquery' ), $widgetJSVersion );
                if ( 0 < count ( $js_vars ) )
                {
                    wp_localize_script ( 'ez-toc-widget-stickyjs', 'ezTocWidgetSticky', $js_vars );
                }
            }
        }

        /**
         * Update the widget settings.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $new_instance
         * @param array $old_instance
         *
         * @return array
         */
        public function update ( $new_instance, $old_instance )
        {

            $instance = $old_instance;

            $instance[ 'title' ] = strip_tags ( $new_instance[ 'title' ] );

            $instance[ 'highlight_color' ] = strip_tags ( $new_instance[ 'highlight_color' ] );

            $instance[ 'hide_inline' ] = array_key_exists ( 'hide_inline', $new_instance ) ? $new_instance[ 'hide_inline' ] : '0';

            //ezTOC_Option::set( 'show_toc_in_widget_only', $instance['hide_inline'] );
            //ezTOC_Option::set( 'show_toc_in_widget_only_post_types', $new_instance['show_toc_in_widget_only_post_types'] );

            if ( isset ( $new_instance[ 'advanced_options' ] ) && $new_instance[ 'advanced_options' ] == 'on' )
            {
                $instance[ 'advanced_options' ] = 'on';
                $instance[ 'scroll_fixed_position' ] = ( int ) strip_tags ( $new_instance[ 'scroll_fixed_position' ] );
                $instance[ 'sidebar_width' ] = ( 'auto' == $new_instance[ 'sidebar_width' ] ) ? $new_instance[ 'sidebar_width' ] : ( int ) strip_tags ( $new_instance[ 'sidebar_width' ] );
                $instance[ 'sidebar_width_size_unit' ] = strip_tags ( $new_instance[ 'sidebar_width_size_unit' ] );
                $instance[ 'fixed_top_position' ] = ( 'auto' == $new_instance[ 'fixed_top_position' ] ) ? $new_instance[ 'fixed_top_position' ] : ( int ) strip_tags ( $new_instance[ 'fixed_top_position' ] );
                $instance[ 'fixed_top_position_size_unit' ] = strip_tags ( $new_instance[ 'fixed_top_position_size_unit' ] );

                $instance[ 'navigation_scroll_bar' ] = strip_tags ( $new_instance[ 'navigation_scroll_bar' ] );

                $instance[ 'scroll_max_height' ] = ( 'auto' == $new_instance[ 'scroll_max_height' ] ) ? $new_instance[ 'scroll_max_height' ] : ( int ) strip_tags ( $new_instance[ 'scroll_max_height' ] );
                $instance[ 'scroll_max_height_size_unit' ] = strip_tags ( $new_instance[ 'scroll_max_height_size_unit' ] );
            } else
            {
                $instance[ 'advanced_options' ] = '';
                $instance[ 'scroll_fixed_position' ] = 30;
                $instance[ 'sidebar_width' ] = 'auto';
                $instance[ 'sidebar_width_size_unit' ] = 'none';
                $instance[ 'fixed_top_position' ] = 30;
                $instance[ 'fixed_top_position_size_unit' ] = 'px';
                $instance[ 'navigation_scroll_bar' ] = 'on';
                $instance[ 'scroll_max_height' ] = 'auto';
                $instance[ 'scroll_max_height_size_unit' ] = 'none';
            }


            return $instance;
        }

        /**
         * Displays the widget settings on the Widgets admin page.
         *
         * @access private
         * @since 2.0.41
         *
         * @param array $instance
         *
         * @return string|void
         */
        public function form ( $instance )
        {

            $defaults = array(
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
            );

            $instance = wp_parse_args ( ( array ) $instance, $defaults );

            $highlight_color = esc_attr ( $instance[ 'highlight_color' ] );
            ?>
            <p>
                <label for="<?php echo $this -> get_field_id ( 'title' ); ?>"><?php _e ( 'Title', 'easy-table-of-contents' ); ?>:</label>
                <input type="text" id="<?php echo $this -> get_field_id ( 'title' ); ?>"
                       name="<?php echo $this -> get_field_name ( 'title' ); ?>" value="<?php echo $instance[ 'title' ]; ?>"
                       style="width:100%;"/>
            </p>

            <p>
                <label for="<?php echo $this -> get_field_id ( 'highlight_color' ); ?>"><?php _e ( 'Active Section Highlight Color:', 'easy-table-of-contents' ); ?></label><br>
                <input type="text" name="<?php echo $this -> get_field_name ( 'highlight_color' ); ?>" class="color-picker" id="<?php echo $this -> get_field_id ( 'highlight_color' ); ?>" value="<?php echo $highlight_color; ?>" data-default-color="<?php echo $defaults[ 'highlight_color' ]; ?>" />
            </p>

            <div class="ez-toc-widget-sticky-advanced-title">
                <input type="checkbox" class="ez_toc_widget_sticky_advanced_options" id="<?php echo $this -> get_field_id ( 'advanced_options' ); ?>" name="<?php echo $this -> get_field_name ( 'advanced_options' ); ?>" <?= ( 'on' === $instance[ 'advanced_options' ] ) ? 'checked="checked"' : ''; ?>/><label for="<?php echo $this -> get_field_id ( 'advanced_options' ); ?>"><?= _e ( 'Advanced Options', 'easy-table-of-contents' ); ?></label>


                <div id="ez-toc-widget-sticky-advanced-options-container" class="ez-toc-widget-sticky-advanced-options-container">
                    <div class="ez-toc-widget-sticky-form-group">
                        <label for="<?php echo $this -> get_field_id ( 'scroll_fixed_position' ); ?>"><?php _e ( 'Scroll Fixed Position', 'easy-table-of-contents' ); ?>:</label>
                        <input type="number" id="<?php echo $this -> get_field_id ( 'scroll_fixed_position' ); ?>" name="<?php echo $this -> get_field_name ( 'scroll_fixed_position' ); ?>" value="<?php echo $instance[ 'scroll_fixed_position' ]; ?>" />
                    </div>

                    <div class="ez-toc-widget-sticky-form-group">
                        <label for="<?php echo $this -> get_field_id ( 'sidebar_width' ); ?>"><?php _e ( 'Sidebar Width', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo $this -> get_field_id ( 'sidebar_width' ); ?>" name="<?php echo $this -> get_field_name ( 'sidebar_width' ); ?>" value="<?php echo $instance[ 'sidebar_width' ]; ?>" />

                        <select id="<?php echo $this -> get_field_id ( 'sidebar_width_size_unit' ); ?>" name="<?php echo $this -> get_field_name ( 'sidebar_width_size_unit' ); ?>" data-placeholder="" >
                            <option value="pt" <?= ( 'pt' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php _e ( 'pt', 'easy-table-of-contents' ); ?></option>

                            <option value="px" <?= ( 'px' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?= ( '%' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?= ( 'em' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?= ( 'none' == $instance[ 'sidebar_width_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>


                    <div class="ez-toc-widget-sticky-form-group">
                        <label for="<?php echo $this -> get_field_id ( 'fixed_top_position' ); ?>"><?php _e ( 'Fixed Top Position', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo $this -> get_field_id ( 'fixed_top_position' ); ?>" name="<?php echo $this -> get_field_name ( 'fixed_top_position' ); ?>" value="<?php echo $instance[ 'fixed_top_position' ]; ?>" />

                        <select id="<?php echo $this -> get_field_id ( 'fixed_top_position_size_unit' ); ?>" name="<?php echo $this -> get_field_name ( 'fixed_top_position_size_unit' ); ?>" data-placeholder="" >
                            <option value="pt" <?= ( 'pt' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php _e ( 'pt', 'easy-table-of-contents' ); ?></option>
                            <option value="px" <?= ( 'px' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?= ( '%' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?= ( 'em' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?= ( 'none' == $instance[ 'fixed_top_position_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>


                    <div class="ez-toc-widget-sticky-form-group">
                        <label for="<?php echo $this -> get_field_id ( 'navigation_scroll_bar' ); ?>"><?php _e ( 'Navigation Scroll Bar', 'easy-table-of-contents' ); ?>:</label>
                        <input type="checkbox" id="<?php echo $this -> get_field_id ( 'navigation_scroll_bar' ); ?>" name="<?php echo $this -> get_field_name ( 'navigation_scroll_bar' ); ?>" <?= ( 'on' === $instance[ 'navigation_scroll_bar' ] ) ? 'checked="checked"' : ''; ?>/>

                    </div>

                    <div class="ez-toc-widget-sticky-form-group">
                        <label for="<?php echo $this -> get_field_id ( 'scroll_max_height' ); ?>"><?php _e ( 'Scroll Max Height', 'easy-table-of-contents' ); ?>:</label>
                        <input type="text" id="<?php echo $this -> get_field_id ( 'scroll_max_height' ); ?>" name="<?php echo $this -> get_field_name ( 'scroll_max_height' ); ?>" value="<?php echo $instance[ 'scroll_max_height' ]; ?>" />

                        <select id="<?php echo $this -> get_field_id ( 'scroll_max_height_size_unit' ); ?>" name="<?php echo $this -> get_field_name ( 'scroll_max_height_size_unit' ); ?>" data-placeholder="" >
                            <option value="pt" <?= ( 'pt' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?> ><?php _e ( 'pt', 'easy-table-of-contents' ); ?></option>
                            <option value="px" <?= ( 'px' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'px', 'easy-table-of-contents' ); ?></option>
                            <option value="%" <?= ( '%' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( '%', 'easy-table-of-contents' ); ?></option>
                            <option value="em" <?= ( 'em' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'em', 'easy-table-of-contents' ); ?></option>
                            <option value="none" <?= ( 'none' == $instance[ 'scroll_max_height_size_unit' ] ) ? 'selected="selected"' : ''; ?>><?php _e ( 'none', 'easy-table-of-contents' ); ?></option>
                        </select>
                    </div>

                </div>
            </div>
            <?php
        }

    }

    // end class

    add_action ( 'widgets_init', array( 'ezTOC_WidgetSticky', 'register' ) );
}
