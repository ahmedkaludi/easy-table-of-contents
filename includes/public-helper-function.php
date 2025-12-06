<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/*
 * Read the contents of a file using the WordPress filesystem API.
 * Since: 2.0.68
 * @param string $file_path The path to the file.
 * @return string|false The file contents or false on failure.
 */
function eztoc_read_file_contents($file_path) {
    global $wp_filesystem;

    // Initialize the WordPress filesystem, no more using file_get_contents function
    if (empty($wp_filesystem)) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }

    // Check if the file exists and is readable
    if ($wp_filesystem->exists($file_path) && $wp_filesystem->is_readable($file_path)) {
        return $wp_filesystem->get_contents($file_path);
    }

    return false;
}
/*
 * Check if a plugin is active.
 * Since: 2.0.79
 * @param string $plugin_slug The plugin slug (e.g., 'plugin-folder/plugin-file.php').
 * @return bool True if the plugin is active, false otherwise.
 */
function eztoc_is_plugin_active( $plugin_slug ) {
    if ( empty( $plugin_slug ) ) {
        return false;
    }
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
	return is_plugin_active( $plugin_slug );
}