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
    if( isset( $form['eztoc_disable_text'] ) && is_array( $form['eztoc_disable_text'] ) ) {
        $text = implode( "\n\r", array_map( 'sanitize_textarea_field', $form['eztoc_disable_text'] ) );
    }

    $subject = isset( $form['eztoc_disable_reason'] ) ? sanitize_text_field( $form['eztoc_disable_reason'] ) : '';

    // Only email for actionable reasons when the detail text has at least 2 words.
    $allowed_reasons = array( 'missing feature', 'technical issue', 'other' );
    $word_count      = count( array_filter( preg_split( '/\s+/', trim( $text ) ) ) );

    if ( in_array( $subject, $allowed_reasons, true ) && $word_count >= 2 ) {

        $headers = array();

        $from = isset( $form['eztoc_disable_from'] ) ? $form['eztoc_disable_from'] : '';
        if ( $from ) {
            $headers[] = "From: " . sanitize_email( $from );
            $headers[] = "Reply-To: " . sanitize_email( $from );
        }

        if ( $subject === 'technical issue' ) {
            $subject = 'Easy Table of Contents ' . $subject;
            $text    = 'technical issue description: ' . trim( $text );
        }

        wp_mail( 'team@magazine3.in', $subject, $text, $headers );
    }

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


/**
 * BFCM 2026 admin banner.
 *
 * Shown on the plugin settings screen through 31 December 2026.
 * Dismissing it hides the banner for 7 days, then it returns until the campaign ends.
 */

/**
 * User meta key that stores the Unix time the snooze lasts until.
 *
 * @return string
 */
function eztoc_bfcm_dismiss_meta_key() {
    return 'eztoc_bfcm_2026_dismissed_until';
}

/**
 * Site timezone, including WordPress versions before wp_timezone() existed.
 *
 * @return DateTimeZone
 */
function eztoc_bfcm_timezone() {
    if ( function_exists( 'wp_timezone' ) ) {
        return wp_timezone();
    }

    $timezone_string = get_option( 'timezone_string' );

    if ( is_string( $timezone_string ) && '' !== $timezone_string ) {
        try {
            return new DateTimeZone( $timezone_string );
        } catch ( Exception $e ) {
            return new DateTimeZone( 'UTC' );
        }
    }

    $offset  = (float) get_option( 'gmt_offset', 0 );
    $hours   = (int) $offset;
    $minutes = (int) abs( ( $offset - $hours ) * 60 );
    $sign    = ( $offset < 0 ) ? '-' : '+';

    return new DateTimeZone( sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes ) );
}

/**
 * Campaign end: 31 December 2026, 23:59:59 in the site timezone.
 *
 * @return DateTimeImmutable
 */
function eztoc_bfcm_campaign_end() {
    return new DateTimeImmutable( '2026-12-31 23:59:59', eztoc_bfcm_timezone() );
}

/**
 * Offer URL for the 2026 campaign.
 *
 * @return string
 */
function eztoc_bfcm_offer_url() {
    return 'https://tocwp.com/bfcm-2026/';
}

/**
 * Coupon code shown on the banner.
 *
 * @return string
 */
function eztoc_bfcm_coupon() {
    return 'BFCM2026';
}

/**
 * Whether the current user should see the banner.
 *
 * @return bool
 */
function eztoc_bfcm_is_active() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return false;
    }

    $now = new DateTimeImmutable( 'now', eztoc_bfcm_timezone() );

    if ( $now > eztoc_bfcm_campaign_end() ) {
        return false;
    }

    $dismissed_until = (int) get_user_meta( get_current_user_id(), eztoc_bfcm_dismiss_meta_key(), true );

    if ( $dismissed_until && time() < $dismissed_until ) {
        return false;
    }

    return true;
}

/**
 * Seconds left until the campaign ends.
 *
 * @return int
 */
function eztoc_bfcm_remaining_seconds() {
    $remaining = eztoc_bfcm_campaign_end()->getTimestamp() - time();

    if ( $remaining < 0 ) {
        return 0;
    }

    return (int) $remaining;
}

/**
 * Static SVG icons used in the banner.
 *
 * @param string $name Icon name.
 * @return string
 */
function eztoc_bfcm_icon( $name ) {
    $icons = array(
        'bolt' => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13 1 4 14h6.5L9 23l11-13h-6.2L13 1z"/></svg>',
        'copy' => '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 1H4c-1.1 0-2 .9-2 2v12h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>',
        'lock' => '<svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5zm-3 8V7a3 3 0 0 1 6 0v3H9z"/></svg>',
        'hourglass' => '<svg viewBox="0 0 24 24" width="12" height="12" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6 2h12v2.2c0 .3-.1.6-.3.8L13.2 12l4.5 7c.2.2.3.5.3.8V22H6v-2.2c0-.3.1-.6.3-.8l4.5-7-4.5-7c-.2-.2-.3-.5-.3-.8V2zm2 2v.6l4 6.2 4-6.2V4H8zm0 16h8v-.6l-4-6.2-4 6.2V20z"/></svg>',
    );

    if ( ! isset( $icons[ $name ] ) ) {
        return '';
    }

    return wp_kses(
        $icons[ $name ],
        array(
            'svg'  => array(
                'viewbox'     => true,
                'viewBox'     => true,
                'width'       => true,
                'height'      => true,
                'aria-hidden' => true,
                'focusable'   => true,
            ),
            'path' => array(
                'fill' => true,
                'd'    => true,
            ),
        )
    );
}

add_action( 'admin_enqueue_scripts', 'eztoc_enqueue_bfcm_assets' );

/**
 * Load banner assets on the plugin settings screen while the campaign is active.
 *
 * @param string $hook Admin page hook suffix.
 * @return void
 */
function eztoc_enqueue_bfcm_assets( $hook ) {
    if ( ! ezTOC_Admin::isAdminHook( $hook ) || ! eztoc_bfcm_is_active() ) {
        return;
    }

    $css_version = ezTOC::VERSION . '.' . filemtime( EZ_TOC_PATH . '/assets/css/bfcm-style.css' );
    $js_version  = ezTOC::VERSION . '.' . filemtime( EZ_TOC_PATH . '/assets/js/bfcm-script.js' );

    wp_enqueue_style(
        'eztoc-bfcm-style',
        EZ_TOC_URL . 'assets/css/bfcm-style.css',
        array(),
        $css_version
    );

    wp_enqueue_script(
        'eztoc-bfcm-script',
        EZ_TOC_URL . 'assets/js/bfcm-script.js',
        array( 'jquery' ),
        $js_version,
        true
    );

    wp_localize_script(
        'eztoc-bfcm-script',
        'bfcmData',
        array(
            'targetTimestamp' => eztoc_bfcm_campaign_end()->getTimestamp(),
            'offerLink'       => eztoc_bfcm_offer_url(),
            'coupon'          => eztoc_bfcm_coupon(),
            'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( 'eztoc_bfcm_dismiss' ),
            'copiedText'      => __( 'Copied', 'easy-table-of-contents' ),
        )
    );
}

/**
 * Print the banner. Caller should place it at the top of the settings screen.
 *
 * @return void
 */
function eztoc_render_bfcm_banner() {
    if ( ! eztoc_bfcm_is_active() ) {
        return;
    }

    $remaining = eztoc_bfcm_remaining_seconds();
    $days      = (int) floor( $remaining / DAY_IN_SECONDS );
    $hours     = (int) floor( ( $remaining % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
    $mins      = (int) floor( ( $remaining % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
    $secs      = (int) ( $remaining % MINUTE_IN_SECONDS );
    ?>
    <div id="eztoc-bfcm-banner" class="eztoc-bfcm" role="region" aria-label="<?php esc_attr_e( 'Black Friday, Cyber Monday and Christmas sale', 'easy-table-of-contents' ); ?>">
        <button type="button" class="eztoc-bfcm-dismiss" aria-label="<?php esc_attr_e( 'Dismiss for 7 days', 'easy-table-of-contents' ); ?>">&times;</button>
        <div class="eztoc-bfcm-inner">
            <div class="eztoc-bfcm-main">
                <span class="eztoc-bfcm-badge">
                    <strong>60%</strong>
                    <span><?php esc_html_e( 'OFF', 'easy-table-of-contents' ); ?></span>
                </span>
                <div class="eztoc-bfcm-text">
                    <span class="eztoc-bfcm-pill">
                        <?php echo eztoc_bfcm_icon( 'bolt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG from eztoc_bfcm_icon(). ?>
                        <?php esc_html_e( 'Black Friday / Cyber Monday / Christmas Sale', 'easy-table-of-contents' ); ?>
                    </span>
                    <p class="eztoc-bfcm-title"><?php esc_html_e( 'Biggest sale in our history — never before seen!', 'easy-table-of-contents' ); ?></p>
                    <p class="eztoc-bfcm-sub"><?php esc_html_e( 'Sale is here! 60% off on Everything for a very limited time!!!', 'easy-table-of-contents' ); ?></p>
                </div>
                <button type="button" class="eztoc-bfcm-code" aria-label="<?php esc_attr_e( 'Copy coupon code', 'easy-table-of-contents' ); ?>">
                    <span class="eztoc-bfcm-code-label"><?php esc_html_e( 'CODE:', 'easy-table-of-contents' ); ?> <?php echo esc_html( eztoc_bfcm_coupon() ); ?></span>
                    <?php echo eztoc_bfcm_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG from eztoc_bfcm_icon(). ?>
                </button>
            </div>
            <div class="eztoc-bfcm-side">
                <div class="eztoc-bfcm-timer-block">
                    <p class="eztoc-bfcm-expires">
                        <?php echo eztoc_bfcm_icon( 'hourglass' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG from eztoc_bfcm_icon(). ?>
                        <?php esc_html_e( 'Offer expires in:', 'easy-table-of-contents' ); ?>
                    </p>
                    <div class="eztoc-bfcm-timer" aria-hidden="true">
                        <span class="eztoc-bfcm-unit"><strong data-bfcm="days"><?php echo esc_html( sprintf( '%02d', $days ) ); ?></strong><span><?php esc_html_e( 'Days', 'easy-table-of-contents' ); ?></span></span>
                        <span class="eztoc-bfcm-sep">:</span>
                        <span class="eztoc-bfcm-unit"><strong data-bfcm="hours"><?php echo esc_html( sprintf( '%02d', $hours ) ); ?></strong><span><?php esc_html_e( 'Hours', 'easy-table-of-contents' ); ?></span></span>
                        <span class="eztoc-bfcm-sep">:</span>
                        <span class="eztoc-bfcm-unit"><strong data-bfcm="mins"><?php echo esc_html( sprintf( '%02d', $mins ) ); ?></strong><span><?php esc_html_e( 'Mins', 'easy-table-of-contents' ); ?></span></span>
                        <span class="eztoc-bfcm-sep">:</span>
                        <span class="eztoc-bfcm-unit"><strong data-bfcm="secs"><?php echo esc_html( sprintf( '%02d', $secs ) ); ?></strong><span><?php esc_html_e( 'Secs', 'easy-table-of-contents' ); ?></span></span>
                    </div>
                </div>
                <div class="eztoc-bfcm-cta-block">
                    <a class="eztoc-bfcm-cta" href="<?php echo esc_url( eztoc_bfcm_offer_url() ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Claim The Offer Now', 'easy-table-of-contents' ); ?>
                        <?php echo eztoc_bfcm_icon( 'bolt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG from eztoc_bfcm_icon(). ?>
                    </a>
                    <p class="eztoc-bfcm-guarantee">
                        <?php echo eztoc_bfcm_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG from eztoc_bfcm_icon(). ?>
                        <?php esc_html_e( '30-Day Money Back Guarantee', 'easy-table-of-contents' ); ?>
                    </p>
                </div>
            </div>
        </div>
        <span class="eztoc-bfcm-sr" aria-live="polite"></span>
    </div>
    <?php
}

add_action( 'wp_ajax_eztoc_dismiss_bfcm', 'eztoc_dismiss_bfcm' );

/**
 * Hide the banner for the current user for 7 days.
 *
 * @return void
 */
function eztoc_dismiss_bfcm() {
    check_ajax_referer( 'eztoc_bfcm_dismiss', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
    }

    $dismissed_until = time() + ( 7 * DAY_IN_SECONDS );
    update_user_meta( get_current_user_id(), eztoc_bfcm_dismiss_meta_key(), $dismissed_until );

    wp_send_json_success(
        array(
            'dismissedUntil' => $dismissed_until,
        )
    );
}