<?php
/**
 * Media customization for the theme.
 *
 * @package MCA
 */

namespace MCA\Media;

/**
 * SVG Support owns MIME handling and content sanitization.
 * Keep SVG uploads restricted even if the plugin settings are relaxed.
 */
function restrict_svg_upload_mimes( $mimes ) {
    if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'bodhi_svgs_sanitize_svg' ) ) {
        unset( $mimes['svg'], $mimes['svgz'] );
    }
    return $mimes;
}
add_filter( 'upload_mimes', __NAMESPACE__ . '\\restrict_svg_upload_mimes', 100 );

/**
 * Require the plugin's validation and sanitization for every SVG upload.
 */
function guard_svg_upload( $file ) {
    $extension = strtolower( pathinfo( $file['name'] ?? '', PATHINFO_EXTENSION ) );
    if ( ! in_array( $extension, [ 'svg', 'svgz' ], true )
        && ! in_array( $file['type'] ?? '', [ 'image/svg+xml', 'image/svg' ], true ) ) {
        return $file;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        $file['error'] = __( 'Only administrators can upload SVG files.', 'mca' );
    } elseif ( ! function_exists( 'bodhi_svgs_sanitize_svg' )
        || ! has_filter( current_filter(), 'bodhi_svgs_sanitize_svg' ) ) {
        $file['error'] = __( 'SVG uploads require the SVG Support plugin.', 'mca' );
    } else {
        // The plugin reads this array when deciding whether to skip sanitization.
        global $bodhi_svgs_options;
        $bodhi_svgs_options['sanitize_on_upload_roles'] = [];
    }

    return $file;
}
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\guard_svg_upload', 9 );
add_filter( 'wp_handle_sideload_prefilter', __NAMESPACE__ . '\\guard_svg_upload', 9 );

/**
 * Convert JPEG and PNG images to WebP format during processing. - CHECK
 */
// add_filter( 'image_editor_output_format', function ( $formats ) {
//     $formats['image/jpeg'] = 'image/webp';
//     $formats['image/png']  = 'image/webp';
//     return $formats;
// });

/**
 * Set maximum quality for JPEG images.
 */
add_filter( 'jpeg_quality', function () {
    return 100; // Maximum quality for JPEG images.
});

/**
 * Set maximum quality for all images edited within WordPress.
 */
add_filter( 'wp_editor_set_quality', function () {
    return 100; // Maximum quality for images in the WordPress editor.
});