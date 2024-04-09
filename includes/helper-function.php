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

add_filter('admin_footer', 'eztoc_add_deactivation_feedback_modal');
function eztoc_add_deactivation_feedback_modal() {

    if( !is_admin() && !eztoc_is_plugins_page()) {
        return;
    }
    
    require_once EZ_TOC_PATH ."/includes/deactivate-feedback.php";

}

/**
 * send feedback via email
 * 
 * @since 1.4.0
 */
function eztoc_send_feedback() {

    if( isset( $_POST['data'] ) ) {
        parse_str( $_POST['data'], $form );
    }
    
    if( !isset( $form['eztoc_security_nonce'] ) || isset( $form['eztoc_security_nonce'] ) && !wp_verify_nonce( sanitize_text_field( $form['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
        echo 'security_nonce_not_verified';
        die();
    }
    if ( !current_user_can( 'manage_options' ) ) {
        die();
    }
    
    $text = '';
    if( isset( $form['eztoc_disable_text'] ) ) {
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

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );
    
    echo 'sent';
    die();
}
add_action( 'wp_ajax_eztoc_send_feedback', 'eztoc_send_feedback' );

function eztoc_enqueue_makebetter_email_js(){

    if( !is_admin() && !eztoc_is_plugins_page()) {
        return;
    }

    wp_enqueue_script( 'eztoc-make-better-js', EZ_TOC_URL . 'includes/feedback.js', array( 'jquery' ));

    wp_enqueue_style( 'eztoc-make-better-css', EZ_TOC_URL . 'includes/feedback.css', false  );
}
add_action( 'admin_enqueue_scripts', 'eztoc_enqueue_makebetter_email_js' );


add_action('wp_ajax_eztoc_subscribe_newsletter','eztoc_subscribe_for_newsletter');
function eztoc_subscribe_for_newsletter(){
    if( !wp_verify_nonce( sanitize_text_field( $_POST['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
        echo 'security_nonce_not_verified';
        die();
    }
    if ( !current_user_can( 'manage_options' ) ) {
        die();
    }
    $api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';
    $api_params = array(
        'name' => sanitize_text_field($_POST['name']),
        'email'=> sanitize_email($_POST['email']),
        'website'=> sanitize_text_field($_POST['website']),
        'type'=> 'etoc'
    );
    $response = wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
    $response = wp_remote_retrieve_body( $response );
    echo $response;
    die;
}