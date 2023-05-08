<?php
/**
 * Filter to add plugins to the TOC list.
 *
 * @link https://rankmath.com/kb/filters-hooks-api-developer/#add-toc-plugin
 *
 * @since 2.0
 *
 * @param array TOC plugins.
 */
add_filter(
	'rank_math/researches/toc_plugins',
	function( $toc_plugins ) {

		$toc_plugins[ EZ_TOC_BASE_NAME ] = 'Easy Table of Contents';

		return $toc_plugins;
	}
);

/**
 * Filter to remove Connections Business Directory related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (
			'connections_form',
			'cn_multi_category_search',
			'cn_widget',
			'cn_carousel',
			'connections_categories',
			'connections_link_view',
			'connections_link_edit',
			'connections_login',
			'siteshot',
			'connections',
			'upcoming_list',
			'cn-mapblock',
			'connections_vcard',
			'connections_qtip',
			'cn_thumb',
			'cn_thumbr',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);


/**
 * Filter to remove Striking theme related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (
			'iconbox',
			'toggle',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);

/**
 * Remove the JetPack share buttons node from the post content before extracting headings.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['jetpack-sharedaddy']   = '.sharedaddy';
		$selectors['jetpack-relatedposts'] = '.jp-relatedposts';

		return $selectors;
	}
);

/**
 * Remove the Elegant Themes Bloom plugin node from the post content before extracting headings.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['elegant-themes-bloom'] = '.et_bloom_below_post';

		return $selectors;
	}
);

/**
 * Do not allow `the_content` TOC callback to run when editing a page in Visual Composer.
 *
 * @link https://wordpress.org/support/topic/correct-method-to-determine-if-using-frontend-editor/#post-12404679
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		if ( function_exists( 'vchelper' ) ) {

			//$sourceId = intval( vchelper( "Request" )->input( 'vcv-source-id' ) );
			//
			//if ( $sourceId === get_the_ID() ) {
			//
			//	$apply = false;
			//}

			if ( vchelper( 'Frontend' )->isPageEditable() ) {

				$apply = false;
			}
		}

		return $apply;
	}
);

/**
 * Do not allow `the_content` TOC callback to run when editing a page in WPBakery Page Builder.
 *
 * @link https://wordpress.org/support/topic/correct-method-to-determine-if-using-frontend-editor/#post-12404679
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		if ( function_exists( 'vc_is_page_editable' ) ) {

			if ( vc_is_page_editable() ) {

				$apply = false;
			}
		}

		return $apply;
	}
);

/**
 * Filter to remove WPBakery Page Builder related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (
			'vc_tta_section',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);

/**
 * Exclude the TOC shortcodes from being processed in the admin in the Divi Theme by Elegant Themes.
 *
 * @since 2.0
 */
add_action(
	'et_pb_admin_excluded_shortcodes',
	function( $shortcodes ) {

		$shortcodes[] = 'ez-toc';
		$shortcodes[] = apply_filters( 'ez_toc_shortcode', 'toc' );

		return $shortcodes;
	}
);

/**
 * Disabled for now as it does not appear to be required.
 *
 * Do not allow `the_content` TOC callback to run when rendering a Divi layout.
 *
 * @since 2.0
 */
//add_filter(
//	'ez_toc_maybe_apply_the_content_filter',
//	function( $apply ) {
//
//		global $wp_current_filter;
//
//		// Do not execute if root current filter is one of those in the array.
//		if ( in_array( $wp_current_filter[0], array( 'et_builder_render_layout' ), true ) ) {
//
//			$apply = false;
//		}
//
//		return $apply;
//	}
//);

/**
 * Callback the for `et_builder_render_layout` filter.
 *
 * Attaches the ezTOC `the_content` filter callback to the Divi layout content filter so the in page anchors will be
 * added the post content.
 *
 * NOTE: Set priority 12 to run after Divi runs the `do_shortcode` filter on its layout content.
 *
 * @link https://wordpress.org/support/topic/anchor-links-not-being-added-with-divi/
 *
 * @since 2.0
 */
add_filter(
	'et_builder_render_layout',
	array( 'ezTOC', 'the_content' ),
	12,
	1
);

/**
 * Add support for the Uncode Theme.
 *
 * @link http://www.undsgn.com/
 *
 * @since 2.0.11
 */
add_action(
	'after_setup_theme',
	function() {

		if ( function_exists( 'uncode_setup' ) ) {

			/**
			 * Callback the for `the_content` filter.
			 *
			 * Trick the theme into applying its content filter.
			 *
			 * In its page/post templates it applies `the_content` filter passing an empty string.
			 * If the value passed pack is `null` or an empty string, the theme will not run its content filter.
			 *
			 * This simply wraps the page/post content in comment tags that way it is not possible to return empty
			 * and its content filter will be run. Now ezTOC can hook into the theme's content filter to insert the TOC.
			 *
			 * @since 2.0.11
			 */
			add_filter(
				'the_content',
				function( $content ) {
					return '<!-- <ezTOC> -->' . $content . '<!-- </ezTOC> -->';
				},
				9,
				1
			);

			/**
			 * Callback the for `uncode_single_content` filter.
			 *
			 * Need to texturize the page/post content first.
			 *
			 * @since 2.0.11
			 */
			add_filter(
				'uncode_single_content',
				function( $content ) {
					return ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) || 'Pale Moon' == ez_toc_get_browser_name() ) ? $content : wptexturize($content);
				},
				10,
				1
			);
			add_filter(
				'uncode_single_content',
				array( 'ezTOC', 'the_content' ),
				11,
				1
			);
		}

	},
	11
);

/**
 * Remove the Starbox plugin node from the post content before extracting headings.
 * @link https://wordpress.org/plugins/starbox/
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['starbox'] = '.abh_box';

		return $selectors;
	}
);

/**
 * Remove the WordPress Related Posts plugin node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['wordpress-23-related-posts-plugin'] = '.wp_rp_content';

		return $selectors;
	}
);

/**
 * Remove the Atomic Blocks plugin node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['atomic-blocks-cta']         = '.ab-block-cta';
		$selectors['atomic-blocks-testimonial'] = '.ab-block-testimonial';

		return $selectors;
	}
);

/**
 * Remove the Ultimate Addons for VC Composer node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['ultimate-addons-for-vc-composer'] = '.ult_tabs';

		return $selectors;
	}
);

/**
 * Remove the WP Product Review node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['wp-product-review'] = '.wppr-review-container';

		return $selectors;
	}
);

/**
 * Remove the Create by Mediavine node from the post content before extracting headings.
 *
 * @link https://wordpress.org/plugins/mediavine-create/
 * @since 2.0.8
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['mediavine-create'] = '.mv-create-card';

		return $selectors;
	}
);

/**
 * Remove the Contextual Related Posts node from the post content before extracting headings.
 *
 * @link https://wordpress.org/plugins/contextual-related-posts/
 * @since 2.0.9
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['contextual-related-posts'] = '.crp_related';

		return $selectors;
	}
);

class ezTOC_Elementor {

	/**
	 * Whether the excerpt is being called.
	 *
	 * Used to determine whether the call to `the_content()` came from `get_the_excerpt()`.
	 *
	 * @since 2.0.2
	 *
	 * @var bool Whether the excerpt is being used. Default is false.
	 */
	private $_is_excerpt = false;

	/**
	 * ezTOC_Elementor constructor.
	 *
	 * @since 2.0.2
	 */
	public function __construct() {

		//add_filter( 'elementor/frontend/the_content', array( 'ezTOC', 'the_content' ), 100 );

		// Hack to avoid enqueue post CSS while it's a `the_excerpt` call.
		add_filter( 'get_the_excerpt', array( $this, 'start_excerpt_flag' ), 1 );
		add_filter( 'get_the_excerpt', array( $this, 'end_excerpt_flag' ), 20 );
		add_filter( 'ez_toc_maybe_apply_the_content_filter', array( $this, 'maybe_apply_the_content_filter' ) );

		add_filter(
			'ez_toc_strip_shortcodes_tagnames',
			function( $tags_to_remove ) {

				$shortcodes = array (
					'elementor-template',
				);

				$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

				return $tags_to_remove;
			}
		);
	}

	/**
	 * Callback for the `elementor/init` action.
	 *
	 * Add the compatibility filters for Elementor.
	 *
	 * @since 2.0.2
	 */
	public static function start() {

		new self();
	}

	/**
	 * Callback for the `get_the_excerpt` filter.
	 *
	 * Start excerpt flag.
	 *
	 * Flags when `the_excerpt` is called. Used to avoid enqueueing CSS in the excerpt.
	 *
	 * @since 2.0.2
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function start_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = true;
		return $excerpt;
	}

	/**
	 * Callback for the `get_the_excerpt` filter.
	 *
	 * End excerpt flag.
	 *
	 * Flags when `the_excerpt` call ended.
	 *
	 * @since 2.0.2
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function end_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = false;
		return $excerpt;
	}

	/**
	 * Callback for the `ez_toc_maybe_apply_the_content_filter` filter.
	 *
	 * If doing Elementor excerpt, which calls `the_content` filter, do not apply the ezTOC `the_content` filter.
	 *
	 * @since 2.0.2
	 *
	 * @param bool $apply
	 *
	 * @return bool mixed
	 */
	public function maybe_apply_the_content_filter( $apply ) {

		if ( $this->_is_excerpt ) {

			$apply = false;
		}

		return $apply;
	}
}
//new ezTOC_Elementor();
add_action( 'elementor/init', array( 'ezTOC_Elementor', 'start' ) );


/**
 * Do not allow `the_content` TOC callback to run when on WooCommerce pages.
 *
 * @link https://docs.woocommerce.com/document/conditional-tags/
 * @link https://wordpress.stackexchange.com/a/246525/59053
 *
 * @since 2.0.11
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		// Just in case an array is not returned.
		if ( ! is_array( $active_plugins ) ) {

			$active_plugins = array();
		}

		$string = implode( '|', $active_plugins );

		if ( class_exists( 'WooCommerce' ) && FALSE !== stripos( $string, 'woocommerce.php' ) ) {

			/** @noinspection PhpUndefinedFunctionInspection */
			if ( is_shop() ||
			     is_product_category() ||
			     is_product_tag() ||
			     is_cart() ||
			     is_checkout() ||
			     is_account_page() ||
			     is_wc_endpoint_url()
			) {

				$apply = false;
			}

		}

		return $apply;
	}
);
/**
 * Beaver Builder Plugin Customization
 * for remove excluding heading contents
 * @since 2.0.34
 */
add_filter(
	'fl_builder_layout_data',
	'flBuilderLayoutDataEZTOC',
	12,
	1
);
function flBuilderLayoutDataEZTOC( $data ) {
	if( has_action( 'the_content' ) ) {
		$post = get_post( get_the_ID() );
		foreach( $data as $nodeKey => $node )
		{
//			if( $node->type == 'module' )
//			{
////				$node->settings->text = ezTOC::the_content($post->post_content);
//			}
			$data[$nodeKey] = $node;
		}
	}
	return $data;
}

/**
 * Thrive Theme Builder Compatibility
 * add inline custom CSS to remove double line
 * on links of our Easy TOC container
 * @since 2.0.38
 */
if ( 'Thrive Theme Builder' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {

    add_action( 'wp_head', 'ezTocEnqueueScriptsforThriveThemeBuilder' );

    if( ! function_exists( 'ezTocEnqueueScriptsforThriveThemeBuilder' ) ) {
    	function ezTocEnqueueScriptsforThriveThemeBuilder() {
            echo <<<INLINECSSAVADA
<style>#ez-toc-container a:hover{text-decoration: none;}</style>
INLINECSSAVADA;
        }
    }
}

/**
 * Kalium - Medical Theme Compatibility
 * remove duplicate eztoc containers
 * in faq sections
 * @since 2.0.38
 */
if ( in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ( 'Kalium - Medical Theme' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) || 'Kalium' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) ) {
    add_shortcode( 'vc_toggle', 'eztoc_vc_toggle_modified' );
    function eztoc_vc_toggle_modified( $atts, $content, $tag ) {
        if ( 'vc_toggle' == $tag ) {

            /**
             * Shortcode attributes
             * @var $atts
             * @var $title
             * @var $el_class
             * @var $style
             * @var $color
             * @var $size
             * @var $open
             * @var $css_animation
             * @var $el_id
             * @var $content - shortcode content
             * @var $css
             * Shortcode class
             * @var WPBakeryShortCode_Vc_Toggle $this_WPBakeryShortCode_Vc_Toggle
             */
            $title = $el_class = $style = $color = $size = $open = $css_animation = $css = $el_id = '';

            $inverted = false;
            $atts = vc_map_get_attributes('vc_toggle', $atts);
            extract($atts);

    // checking is color inverted
            $style = str_replace('_outline', '', $style, $inverted);
            /**
             * @since 4.4
             */
            $elementClass = array(
                'base' => apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_toggle', 'vc_toggle', $atts),
                // TODO: check this code, don't know how to get base class names from params
                'style' => 'vc_toggle_' . $style,
                'color' => ( $color ) ? 'vc_toggle_color_' . $color : '',
                'inverted' => ( $inverted ) ? 'vc_toggle_color_inverted' : '',
                'size' => ( $size ) ? 'vc_toggle_size_' . $size : '',
                'open' => ( 'true' === $open ) ? 'vc_toggle_active' : '',
                'extra' => $atts['css'],
                'css_animation' => '',
                    // TODO: remove getCssAnimation as function in helpers
            );

            $class_to_filter = trim(implode(' ', $elementClass));
            $class_to_filter .= vc_shortcode_custom_css_class($css, ' ');
            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, 'vc_toggle', $atts);

            $heading_output = apply_filters('wpb_toggle_heading', $atts['title'], array(
                'title' => $title,
                'open' => $open,
                    ));
            $output = '<div ' . ( isset( $el_id ) && ! empty( $el_id ) ? 'id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="' . esc_attr( $css_class ) . '"><div class="vc_toggle_title">' . $heading_output . '<i class="vc_toggle_icon"></i></div><div class="vc_toggle_content">' . wpb_js_remove_wpautop( $content, true ) . '</div></div>';

            return $output;
        }
    }
}

/**
 * Foodie Pro Theme Compatibility
 * for working sticky toggle
 * in right way
 * @since 2.0.39
 */
if ( 'Foodie Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
    add_action( 'wp_head', 'ezTocEnqueueScriptsforFoodieProTheme' );

    if( ! function_exists( 'ezTocEnqueueScriptsforFoodieProTheme' ) ) {
    	function ezTocEnqueueScriptsforFoodieProTheme() {
            echo <<<INLINECSSFOODPRO
<style>@media only screen and (max-width: 940px){ .ez-toc-sticky #ez-toc-sticky-container .menu-toggle,#ez-toc-container .menu-toggle, #ez-toc-widget-container .menu-toggle{display:none} .ez-toc-sticky #ez-toc-sticky-container nav,#ez-toc-container nav, #ez-toc-widget-container nav {display:block}  }</style>
INLINECSSFOODPRO;
        }
    }

}

/**
 * Poka Theme Compatibility
 * remove duplicate eztoc containers
 * in header & footer sections
 * @since 2.0.46
 */
if ( 'PokaTheme' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
    add_action( 'poka_before_main', 'ez_toc_poka_before_main', 4, 0 );
    function ez_toc_poka_before_main() {
        remove_action('poka_before_main', 'poka_before_content', 5, 0);
        remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
        add_action( 'poka_before_main', 'poka_before_content', 5, 0 );
    }
    add_action( 'poka_before_main', 'ez_toc_poka_before_main_after', 6, 0 );
    function ez_toc_poka_before_main_after() {
        add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
    }
    add_action( 'poka_after_main', 'ez_toc_poka_after_main', 3, 0 );
    function ez_toc_poka_after_main() {
        remove_action( 'poka_after_main', 'poka_banner_footer', 4, 0 );
        remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
        add_action( 'poka_after_main', 'poka_banner_footer', 4, 0 );
    }
    add_action( 'poka_after_main', 'ez_toc_poka_after_main_after', 5, 0 );
    function ez_toc_poka_after_main_after() {
        add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
    }
}

if ( 'MAKE9 Divi zh-tw Child' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
    add_filter('ez_toc_regex_filteration', 'ez_toc_regex_filteration_for_divi_chinese');
    function ez_toc_regex_filteration_for_divi_chinese( $regEx ) {
        $regEx = '/(<h([1-6]{1})(?:(?!\bclass="et_pb_slide_title")[^>])*>)(.*)<\/h\2>/msuU';
    
        return $regEx;
    }
}

if ( in_array( 'lasso/affiliate-plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_filter('ez_toc_regex_filteration', 'ez_toc_regex_filteration_for_lasso_products');
    function ez_toc_regex_filteration_for_lasso_products( $regEx ) {
        $regEx = '/(<(?:h|H){1}([1-6]{1})[^>]*>)(.*)<\/(?:h|H){1}\2>/msuU';

        return $regEx;
    }
}
