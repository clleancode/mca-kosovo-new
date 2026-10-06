<?php
/**
 * Loader for all custom post types.
 *
 * @package MCA
 */

namespace MCA\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Array of custom post type files to include.
$custom_post_types = [
    'galleries.php',
    'jobs.php',
    'procurements.php',
    'projects.php',
    'publications.php',
];

// Include each custom post type file.
foreach ( $custom_post_types as $file ) {
    $filepath = get_template_directory() . '/inc/post-types/' . $file;
    if ( file_exists( $filepath ) ) {
        require_once $filepath;
    } else {
        error_log( "Custom post type file not found: $filepath" );
    }
}