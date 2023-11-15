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

/**
 * Adding page/post title in TOC list
 * @since 2.0.56
 */
add_action( 'init', function() {
    if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin())
    {
        ob_start();
    }
} );
add_action('shutdown', function() {
    if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){
        $final = '';
        $levels = ob_get_level();
    
        for ($i = 0; $i < $levels; $i++) {
            $final .= ob_get_clean();
        }
        echo apply_filters('eztoc_wordpress_final_output', $final);
    }
 
}, 10);

    add_filter('eztoc_wordpress_final_output', function($content){
        if(!is_singular('post') && !is_page()) { return $content;}
        if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){ 
        return preg_replace_callback(
            '/<h1(.*?)>(.*?)<\/h1>/i',
            function ($matches) {
                $title = $matches[2];
                $added_link ='<h1'.$matches[1].'><span class="ez-toc-section" id="'.esc_attr(ezTOCGenerateHeadingIDFromTitle($title)).'" ez-toc-data-id="#'.esc_attr(ezTOCGenerateHeadingIDFromTitle($title)).'"></span>';
                $added_link .= esc_attr($title);
                $added_link .= '<span class="ez-toc-section-end"></span></h1>';
                return $added_link;
            },
            $content
        );
    }
    }, 10, 1);
    
    add_filter( 'ez_toc_modify_process_page_content', 'ez_toc_page_content_include_page_title', 10, 1 );
    function ez_toc_page_content_include_page_title( $content ) {
        if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){ 
            $title = get_the_title();
            $added_page_title= '<h1 class="entry-title">'.wp_kses_post($title).'</h1>';
            $content = $added_page_title.$content;
        }
        return $content;
    }
     function ezTOCGenerateHeadingIDFromTitle( $heading ) {
        $return = false;
        if ( $heading ) {
            $heading = apply_filters( 'ez_toc_url_anchor_target_before', $heading );
            $return = html_entity_decode( $heading, ENT_QUOTES, get_option( 'blog_charset' ) );
            $return = trim( strip_tags( $return ) );
            $return = remove_accents( $return );
            $return = str_replace( array( "\r", "\n", "\n\r", "\r\n" ), ' ', $return );
            $return = htmlentities2( $return );
            $return = str_replace( array( '&amp;', '&nbsp;'), ' ', $return );
            $return = str_replace( array( '&shy;' ),'', $return );					// removed silent hypen 
            $return = html_entity_decode( $return, ENT_QUOTES, get_option( 'blog_charset' ) );
            $return = preg_replace( '/[\x00-\x1F\x7F]*/u', '', $return );
            $return = str_replace(
                array( '*', '\'', '(', ')', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']' ),
                '',
                $return
            );
            $return = str_replace(
                array( '%', '{', '}', '|', '\\', '^', '~', '[', ']', '`' ),
                '',
                $return
            );
            $return = str_replace(
                array( '$', '.', '+', '!', '*', '\'', '(', ')', ',', '’' ),
                '',
                $return
            );
            $return = str_replace(
                array( '-', '-', 'â€“', 'â€”' ),
                '-',
                $return
            );
            $return = str_replace(
                array( 'â€˜', 'â€™', 'â€œ', 'â€' ),
                '',
                $return
            );
            $return = str_replace( array( ':' ), '_', $return );
            $return = preg_replace( '/\s+/', '_', $return );
            $return = preg_replace( '/-+/', '-', $return );
            $return = preg_replace( '/_+/', '_', $return );
            $return = rtrim( $return, '-_' );
            $return = preg_replace_callback(
                "{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i",
                function( $m ) {
    
                    return sprintf( '%%%02X', ord( $m[0] ) );
                },
                $return
            );
            if ( ezTOC_Option::get( 'lowercase' ) ) {
    
                $return = strtolower( $return );
            }
            if ( ! $return ) {
    
                $return = ( ezTOC_Option::get( 'fragment_prefix' ) ) ? ezTOC_Option::get( 'fragment_prefix' ) : '_';
            }
            if ( ezTOC_Option::get( 'hyphenate' ) ) {
    
                $return = str_replace( '_', '-', $return );
                $return = preg_replace( '/-+/', '-', $return );
            }
        }
        return apply_filters( 'ez_toc_url_anchor_target', $return, $heading );
    }

add_filter( 'ez_toc_sticky_visible', 'ez_toc_sticky_visible_func' ,20);
function ez_toc_sticky_visible_func( $visible ) {
    $sticky_include_homepage = ezTOC_Option::get('sticky_include_homepage');
    $sticky_include_category = ezTOC_Option::get('sticky_include_category');
    $sticky_include_product_category = ezTOC_Option::get('sticky_include_product_category');
    if ( is_front_page() ) {
      $visible = ($sticky_include_homepage=='1')?true:false;
    } elseif ( is_category() ) {
      $visible = ($sticky_include_category=='1')?true:false;
    } elseif ( is_tax( 'product_cat' ) ) {
      $visible = ($sticky_include_product_category=='1')?true:false;
    }
    return $visible;
}

/**
 * Helps exclude blockquote
 * @since 2.0.58
 */
if(!function_exists('ez_toc_para_blockquote_replace')){
function ez_toc_para_blockquote_replace($blockquotes, $content, $step){
    $bId = 0;
    if($step == 1){    
        foreach($blockquotes[0] as $blockquote){
            $replace = '#eztocbq' . $bId . '#';
            $content = str_replace( trim($blockquote), $replace, $content );
            $bId++;
        }
    }elseif($step == 2){    
        foreach($blockquotes[0] as $blockquote){
            $search = '#eztocbq' . $bId . '#'; 
            $content = str_replace( $search, trim($blockquote), $content );
            $bId++;
        }
    }
    return $content;
}
}