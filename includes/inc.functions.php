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
// Non amp checker
if ( ! function_exists('ez_toc_is_amp_activated') ){
    
    function ez_toc_is_amp_activated() {
        $result = false;
        if (is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || is_plugin_active('amp/amp.php')  ||
                is_plugin_active('better-amp/better-amp.php')  ||
                is_plugin_active('wp-amp/wp-amp.php') ||
                is_plugin_active('amp-wp/amp-wp.php') ||
                is_plugin_active('bunyad-amp/bunyad-amp.php') )
            $result = true;
        
        return $result;
    }
    
}

// Non amp checker
if ( ! function_exists('ez_toc_non_amp') ) {
    
    function ez_toc_non_amp() {

        $non_amp = true;

        if( function_exists('ampforwp_is_amp_endpoint') && @ampforwp_is_amp_endpoint() ) {                
            $non_amp = false;                       
        }     
        if( function_exists('is_amp_endpoint') && @is_amp_endpoint() ){
            $non_amp = false;           
        }
        if( function_exists('is_better_amp') && @is_better_amp() ){       
            $non_amp = false;           
        }
        if( function_exists('is_amp_wp') && @is_amp_wp() ){       
            $non_amp = false;           
        }

        return $non_amp;

    }
  
}

/**
 * MBString Extension Admin Notice
 * if not loaded then msg to user
 * @since 2.0.47
 */
if ( function_exists('extension_loaded') && extension_loaded('mbstring') == false ) {
    function ez_toc_admin_notice_for_mbstring_extension() {
        echo '<div class="notice notice-error is-not-dismissible"><p>' . esc_html__( 'PHP MBString Extension is not enabled in your php setup, please enabled to work perfectly', 'easy-table-of-contents' ) . ' <strong>' . esc_html__( 'Easy Table of Contents', 'easy-table-of-contents' ) . '</strong>. ' . esc_html__( 'Check official doc:', 'easy-table-of-contents' ). ' <a href="https://www.php.net/manual/en/mbstring.installation.php" target="_blank">' . esc_html__( 'PHP Manual', 'easy-table-of-contents' ) .'</a></p></div>';
    }
    add_action('admin_notices', 'ez_toc_admin_notice_for_mbstring_extension');
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

/**
 * Since version 2.0.52
 * Export all settings to json file
 */
add_action( 'wp_ajax_ez_toc_export_all_settings', 'ez_toc_export_all_settings'); 
function ez_toc_export_all_settings()
{
    if ( !current_user_can( 'manage_options' ) ) {
        die('-1');
    }
    if(!isset($_GET['_wpnonce'])){
        die('-1');
    }
    if( !wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), '_wpnonce' ) ){
        die('-1');
    }

    $export_settings_data = get_option('ez-toc-settings');
    if(!empty($export_settings_data)){
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=ez_toc_settings_backup.json');
        echo json_encode($export_settings_data);   
    }                             
    wp_die();
}