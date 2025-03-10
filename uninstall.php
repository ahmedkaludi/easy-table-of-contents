<?php
/**
 * Delete plugin data when plugin is uninstalled
 * @since   2.0.73
 * */
// Exit if accessed directly
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
   exit;

/**
 * Delete data when delete data option is enabled
 * @since   2.0.73
 * */
function ez_toc_delete_data_on_uninstall() {

   $ez_toc_options  =  get_option( 'ez-toc-settings' );
   if ( is_array( $ez_toc_options ) && ! empty( $ez_toc_options['delete-data-on-uninstall'] ) ) {

      delete_option( 'ez-toc-settings' );
      delete_option( 'ez-toc-post-meta-content' );
      delete_option( 'ez_toc_do_activation_redirect' );

   }

}

ez_toc_delete_data_on_uninstall();