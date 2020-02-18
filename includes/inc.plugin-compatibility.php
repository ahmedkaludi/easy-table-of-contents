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

		$selectors['jetpack-sharedaddy'] = '.sharedaddy';

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
