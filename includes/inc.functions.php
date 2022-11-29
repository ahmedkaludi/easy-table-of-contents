<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the current post's TOC list or supplied post's TOC list.
 *
 * @access public
 * @since  2.0
 *
 * @param int|null|WP_Post $post                 An instance of WP_Post or post ID. Defaults to current post.
 * @param bool             $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 *
 * @return string
 */
function get_ez_toc_list( $post = null, $apply_content_filter = true ) {

	if ( ! $post instanceof WP_Post ) {

		$post = get_post( $post );
	}

	if ( $apply_content_filter ) {

		$ezPost = new ezTOC_Post( $post );

	} else {

		$ezPost = new ezTOC_Post( $post, false );
	}

	return $ezPost->getTOCList();
}

/**
 * Display the current post's TOC list or supplied post's TOC list.
 *
 * @access public
 * @since  2.0
 *
 * @param null|WP_Post $post                 An instance of WP_Post
 * @param bool         $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 */
function ez_toc_list( $post = null, $apply_content_filter = true ) {

	echo get_ez_toc_list( $post, $apply_content_filter );
}

/**
 * Get the current post's TOC content block or supplied post's TOC content block.
 *
 * @access public
 * @since  2.0
 *
 * @param int|null|WP_Post $post                 An instance of WP_Post or post ID. Defaults to current post.
 * @param bool             $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 *
 * @return string
 */
function get_ez_toc_block( $post = null, $apply_content_filter = true ) {

	if ( ! $post instanceof WP_Post ) {

		$post = get_post( $post );
	}

	if ( $apply_content_filter ) {

		$ezPost = new ezTOC_Post( $post );

	} else {

		$ezPost = new ezTOC_Post( $post, false );
	}

	return $ezPost->getTOC();
}

/**
 * Display the current post's TOC content or supplied post's TOC content.
 *
 * @access public
 * @since  2.0
 *
 * @param null|WP_Post $post                 An instance of WP_Post
 * @param bool         $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 */
function ez_toc_block( $post = null, $apply_content_filter = true ) {

	echo get_ez_toc_block( $post, $apply_content_filter );
}

function ez_toc_inline_styles(){
    $screen_min_css = file_get_contents( EZ_TOC_PATH . '/assets/css/screen.min.css' );
    echo "<style>$screen_min_css</style>";
}
if (ezTOC_Option::get( 'inline_css' )) {
	add_action('wp_head', 'ez_toc_inline_styles');
}

add_action( 'woocommerce_archive_description', 'ez_toc_woo_category_desc' );
function ez_toc_woo_category_desc() {
  if (!function_exists('vtde_php_upgrade_notice')) {
    return false;
  }
  $term_object = get_queried_object();
  $desc = $term_object->description;
  preg_match_all( '@<h1.*?>(.*?)<\/h1>@', $desc, $matches );
  $array = $matches[1];
  $container = '<div id="ez-toc-container" class="counter-hierarchy counter-decimal ez-toc-grey"><div class="ez-toc-title-container"><p class="ez-toc-title">' . esc_html_e('Table of Contents', 'easy-table-of-contents') . '</p><span class="ez-toc-title-toggle"><a href="#" class="ez-toc-pull-right ez-toc-btn ez-toc-btn-xs ez-toc-btn-default ez-toc-toggle" style="display: flex;" aria-label="ez-toc-toggle-icon-3">' . ezTOC::getTOCToggleIcon() . '</a></span></div><nav><ul class="ez-toc-list">';
    foreach ( $array as $val ) { 
      $vals .= '<li><a class="anchor" href="#'.$val.'">'.$val.'</a></li>';
      $desc = preg_replace('/<h1>(.*?)<\/h1>/', "<h1 id='$1'>$1</h1>", $desc);
    }
    $last .= '</ul></nav></div>';
    $desc = $container . $vals . $last . $desc;
    echo $desc;
}
add_action('wp_head', 'ez_toc_woo_cat_desc_remove');
function ez_toc_woo_cat_desc_remove(){
  if (!function_exists('vtde_php_upgrade_notice')) {
    return false;
  }
  remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
}

/**
 * EzPrintR method
 * to print_r content with pre tags
 * @since 2.0.34
 * @param $content
 * @return void
*/
function EzPrintR($content){
	echo "<pre>";
    print_r($content);
    echo "</pre>";
}

/**
 * EzDumper method
 * to var_dump content with pre tags
 * @since 2.0.34
 * @param $content
 * @return void
*/
function EzDumper($content){
	echo "<pre>";
    var_dump($content);
    echo "</pre>";
}