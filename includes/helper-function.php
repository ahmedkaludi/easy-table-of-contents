<?php

/**
 * Helper Functions
 *
 * @package     saswp
 * @subpackage  Helper/Templates
 * @copyright   Copyright (c) 2016, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) )
    exit;

/**
 * Helper method to check if user is in the plugins page.
 *
 * @author René Hermenau
 * @since  1.4.0
 *
 * @return bool
 */
 
/**
 * display deactivation logic on plugins page
 * 
 * @since 1.4.0
 */
function eztoc_is_plugins_page() {

    if(function_exists('get_current_screen')){
        $screen = get_current_screen();
            if(is_object($screen)){
                if($screen->id == 'plugins' || $screen->id == 'plugins-network'){
                    return true;
                }
            }
    }
    return false;
}

add_filter( 'admin_footer', 'eztoc_add_deactivation_feedback_modal' );

function eztoc_add_deactivation_feedback_modal() {

    if ( is_admin() && eztoc_is_plugins_page() ) {

        require_once EZ_TOC_PATH ."/includes/deactivate-feedback.php";    

    }
    
}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function eztoc_send_feedback() {
//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Since form is serialised nonce is verified after parsing the recieved data.
    if( isset( $_POST['data'] ) ) {
        //phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason : Since form is serialised nonce is verified after parsing the recieved data.
        parse_str( wp_unslash( $_POST['data'] ), $form ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Data is sanitized below.
    }
    
    if( !isset( $form['eztoc_security_nonce'] ) || isset( $form['eztoc_security_nonce'] ) && !wp_verify_nonce( sanitize_text_field( $form['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
        echo 'security_nonce_not_verified';
        wp_die();
    }
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die();
    }
    
    $text = '';
    if( isset( $form['eztoc_disable_text'] ) && !is_array($form['eztoc_disable_text']) ) {
        $text = implode( "\n\r", $form['eztoc_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['eztoc_disable_from'] ) ? $form['eztoc_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = isset( $form['eztoc_disable_reason'] ) ? $form['eztoc_disable_reason'] : '(no reason given)';

    if($subject == 'technical issue'){

          $subject  = 'Easy Table of Contents '.$subject;
          $text = trim($text);

          if(!empty($text)){

            $text = 'technical issue description: '.$text;

          }else{

            $text = 'no description: '.$text;
          }
      
    }

    wp_mail( 'team@magazine3.in', $subject, $text, $headers );
    
    echo 'sent';
    wp_die();

}

add_action( 'wp_ajax_eztoc_send_feedback', 'eztoc_send_feedback' );

function eztoc_enqueue_makebetter_email_js() {

    if ( is_admin() && eztoc_is_plugins_page() ) {
        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script( 'eztoc-make-better-js', EZ_TOC_URL . "assets/js/feedback{$min}.js", array( 'jquery' ),  ezTOC::VERSION, true );
        wp_enqueue_style( 'eztoc-make-better-css', EZ_TOC_URL . "assets/css/feedback{$min}.css", false,  ezTOC::VERSION );
    }
    
}

add_action( 'admin_enqueue_scripts', 'eztoc_enqueue_makebetter_email_js' );


/* * BFCM Banner Integration
 * Loads assets from assets/css and assets/js
 */
add_action('admin_enqueue_scripts', 'eztoc_enqueue_bfcm_assets');

function eztoc_enqueue_bfcm_assets($hook) { 
 
    
    if ( $hook !== 'settings_page_table-of-contents') {
        return;
    }

    // 2. define settings
    $expiry_date_str = '2025-12-25 23:59:59'; 
    $offer_link      = 'https://tocwp.com/bfcm-25/';

    // 3. Expiry Check (Server Side)
    if ( current_time('timestamp') > strtotime($expiry_date_str) ) {
        return; 
    }

    // 4. Register & Enqueue CSS    
    wp_enqueue_style(
        'etoc-bfcm-style', 
        EZ_TOC_URL. 'assets/css/bfcm-style.css', 
        array(), 
        '1.0'
    );

    // 5. Register & Enqueue JS
    wp_enqueue_script(
        'etoc-bfcm-script', 
        EZ_TOC_URL. 'assets/js/bfcm-script.js', 
        array('jquery'), // jQuery dependency
        '1.0', 
        true 
    );

    // 6. Data Pass (PHP to JS)
    wp_localize_script('etoc-bfcm-script', 'bfcmData', array(
        'targetDate' => $expiry_date_str,
        'offerLink'  => $offer_link
    ));
}