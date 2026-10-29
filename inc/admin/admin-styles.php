<?php
/**
 * Enqueue styles for the WordPress admin dashboard.
 *
 * @package MCA
 */

namespace MCA\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue admin styles only when the stylesheet contains CSS.
 */
function enqueue_admin_styles() {
    $path = get_theme_file_path( '/assets/css/admin-style.css' );
    if ( ! file_exists( $path ) || 0 === filesize( $path ) ) {
        return;
    }
    wp_enqueue_style( 'admin_css', get_theme_file_uri( '/assets/css/admin-style.css' ), [], filemtime( $path ) );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
