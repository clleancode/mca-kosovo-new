<?php
/** Front-end Content Security Policy monitoring (does not block resources). */
namespace MCA\Security;

function csp_nonce(): string {
    static $nonce;
    return $nonce ??= base64_encode(random_bytes(24));
}

function csp_policy(): string {
    $scripts = [
        "'self'", "'nonce-" . csp_nonce() . "'",
        'https://www.googletagmanager.com',
        'https://maps.googleapis.com', 'https://maps.gstatic.com',
        'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.8.69/',
    ];
    // Maps currently requires eval; keep that exception off other pages.
    if (function_exists('MCA\\Assets\\mca_needs_maps') && \MCA\Assets\mca_needs_maps()) {
        $scripts[] = "'unsafe-eval'";
    }
    $directives = [
        'default-src' => "'self'",
        'base-uri' => "'self'",
        'object-src' => "'none'",
        'frame-ancestors' => "'self'",
        'form-action' => "'self'",
        'script-src' => implode(' ', $scripts),
        'script-src-attr' => "'none'",
        // WordPress, GSAP and Maps use inline styles and style attributes.
        'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com",
        'font-src' => "'self' data: https://fonts.gstatic.com",
        'img-src' => "'self' data: blob: https:",
        'media-src' => "'self' blob: https:",
        'connect-src' => "'self' https://*.google-analytics.com https://*.analytics.google.com https://www.googletagmanager.com https://*.googleapis.com https://*.gstatic.com https://www.google.com https://cdn.jsdelivr.net/npm/pdfjs-dist@4.8.69/ blob:",
        'frame-src' => "'self' https://www.google.com https://maps.google.com https://www.googletagmanager.com https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com",
        'worker-src' => "'self' blob: https://cdn.jsdelivr.net/npm/pdfjs-dist@4.8.69/",
    ];
    $directives = apply_filters('mca_csp_directives', $directives);
    $policy = [];
    foreach ($directives as $name => $sources) {
        $policy[] = $name . ' ' . $sources;
    }
    return implode('; ', $policy);
}

add_action('template_redirect', function () {
    if (is_admin() || wp_doing_ajax() || is_feed() || is_trackback()
        || is_robots() || wp_is_json_request() || headers_sent()) return;

    // Do not modify admin/API responses. Cache this header and its HTML together:
    // cached markup must never be paired with a newly generated CSP nonce.
    header('Content-Security-Policy-Report-Only: ' . csp_policy());
    add_filter('wp_inline_script_attributes', function ($attributes) {
        $attributes['nonce'] = csp_nonce();
        return $attributes;
    });
}, 0);
