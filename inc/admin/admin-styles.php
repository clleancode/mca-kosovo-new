<?php
/**
 * Enqueue styles for the WordPress admin dashboard.
 *
 * @package Medical_Averitas
 */

namespace MedicalAveritas\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue admin styles.
 */
function enqueue_admin_styles() {
    wp_enqueue_style( 'admin_css', get_theme_file_uri( '/assets/css/admin-style.css' ), [], '1.0' );
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );