<?php
/**
 * Media customization for the theme.
 *
 * @package MCA
 */

namespace MCA\Media;

/**
 * Add SVG MIME type support.
 */
function add_svg_support( $mimes ) {
    $mimes['svg'] = 'image/svg+xml'; // Add SVG support
    return $mimes;
}
add_filter( 'upload_mimes', __NAMESPACE__ . '\\add_svg_support' );

/**
 * Check and set the file type for SVG uploads.
 */
function check_filetype_and_ext( $data, $file, $filename, $mimes ) {
    $filetype = wp_check_filetype( $filename, $mimes );

    if ( $filetype['ext'] === 'svg' ) {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
    }

    return $data;
}
add_filter( 'wp_check_filetype_and_ext', __NAMESPACE__ . '\\check_filetype_and_ext', 10, 4 );

/**
 * Sanitize SVG uploads for security.
 */
function sanitize_svg_upload( $file ) {
    if ( isset( $file['type'] ) && $file['type'] === 'image/svg+xml' ) {
        $file_path = $file['tmp_name'];

        // Load and sanitize the SVG file
        $svg = file_get_contents( $file_path );

        // Check if SVG-Sanitizer is available
        if ( class_exists( '\enshrined\svgSanitize\Sanitizer' ) ) {
            $sanitizer = new \enshrined\svgSanitize\Sanitizer();
            $sanitized_svg = $sanitizer->sanitize( $svg );

            if ( $sanitized_svg ) {
                file_put_contents( $file_path, $sanitized_svg );
            } else {
                $file['error'] = __( 'The SVG file contains invalid or unsafe content.', 'medical-averitas' );
            }
        } else {
            $file['error'] = __( 'SVG-Sanitizer library not found. SVG uploads are disabled.', 'medical-averitas' );
        }
    }
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\sanitize_svg_upload' );

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