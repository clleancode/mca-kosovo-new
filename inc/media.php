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
                $file['error'] = __( 'The SVG file contains invalid or unsafe content.', 'mca' );
            }
        } else {
            $file['error'] = __( 'SVG-Sanitizer library not found. SVG uploads are disabled.', 'mca' );
        }
    }
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', __NAMESPACE__ . '\\sanitize_svg_upload' );

/**
 * Generate WebP versions of JPEGs when supported, retaining plugin mappings.
 * Leave PNGs unchanged and use WordPress's default compression quality.
 */
add_filter( 'image_editor_output_format', function ( $formats ) {
    if ( ! isset( $formats['image/jpeg'] )
        && wp_image_editor_supports( [ 'mime_type' => 'image/webp' ] ) ) {
        $formats['image/jpeg'] = 'image/webp';
    }
    return $formats;
} );
