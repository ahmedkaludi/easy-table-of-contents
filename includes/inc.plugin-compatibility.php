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
 * Callback the for `et_builder_render_layout`.
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
