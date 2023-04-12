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


if( !function_exists( 'ez_toc_wp_check_browser_version' ) ) {
    /**
     * wp_check_browser_version Method
     * if not predefined in 
     * latest wordpress version
     * @since 2.0.44
     * @return boolean|array
     */
    function ez_toc_wp_check_browser_version() {
	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return false;
	}

	$key = md5( $_SERVER['HTTP_USER_AGENT'] );

	$response = get_site_transient( 'browser_' . $key );

	if ( false === $response ) {
		// Include an unmodified $wp_version.
		require ABSPATH . WPINC . '/version.php';

		$url     = 'http://api.wordpress.org/core/browse-happy/1.1/';
		$options = array(
			'body'       => array( 'useragent' => $_SERVER['HTTP_USER_AGENT'] ),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		);

		if ( wp_http_supports( array( 'ssl' ) ) ) {
			$url = set_url_scheme( $url, 'https' );
		}

		$response = wp_remote_post( $url, $options );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		/**
		 * Response should be an array with:
		 *  'platform' - string - A user-friendly platform name, if it can be determined
		 *  'name' - string - A user-friendly browser name
		 *  'version' - string - The version of the browser the user is using
		 *  'current_version' - string - The most recent version of the browser
		 *  'upgrade' - boolean - Whether the browser needs an upgrade
		 *  'insecure' - boolean - Whether the browser is deemed insecure
		 *  'update_url' - string - The url to visit to upgrade
		 *  'img_src' - string - An image representing the browser
		 *  'img_src_ssl' - string - An image (over SSL) representing the browser
		 */
		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $response ) ) {
			return false;
		}

		set_site_transient( 'browser_' . $key, $response, WEEK_IN_SECONDS );
	}

	return $response;
    }
}

/**
 * get_browser_name Method
 * for getting browser name
 * @since 2.0.44
 * @return string
 */
function ez_toc_get_browser_name() {
    $browserDetails = ez_toc_wp_check_browser_version();
    
    if( $browserDetails !== null && !empty( $browserDetails ) && is_array( $browserDetails ) && key_exists( 'name', $browserDetails ) ) {
        if( !empty( $browserDetails['name'] ) ){
            return $browserDetails['name'];
        }
    }
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