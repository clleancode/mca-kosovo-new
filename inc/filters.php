<?php
/**
 * Theme filters.
 *
 * @package MCA
 */

namespace MCA\Filters;

add_filter('mca_projects_hero_breadcrumbs', function ($items, $post_id) {
    $projects_page = get_page_by_path('projects');
    return [
        ['title' => __('Home', 'MCA'), 'url' => home_url('/')],
        ['title' => __('Projects', 'MCA'), 'url' => $projects_page ? get_permalink($projects_page) : get_post_type_archive_link('project')],
        ['title' => get_the_title($post_id), 'url' => ''],
    ];
}, 10, 2);

// add_filter( 'allowed_block_types_all', function( $allowed_blocks, $block_editor_context ) {
//     // Allow only ACF Averitas blocks
//     $averitas_blocks = [
//         'acf/rapture-article',
//         'acf/rapture-breadcrumb',
//         'acf/rapture-cards-filter',
//         'acf/rapture-carousel',
//         'acf/rapture-content',
//         'acf/rapture-featured',
//         'acf/rapture-footnotes',
//         'acf/rapture-heading',
//         'acf/rapture-information',
//         'acf/rapture-card-icon',
//         'acf/rapture-newsletter',
//         'acf/rapture-related',
//         'acf/rapture-slider',
//         'acf/rapture-summary',
//         'acf/rapture-tabs',
//         'acf/rapture-highlight',
//         'acf/rapture-text',
//         'acf/rapture-image',
//         'acf/rapture-separator',
//         'acf/rapture-content-icon',
//         'acf/rapture-publication-card',
//         'acf/rapture-card-promo',
//         'acf/rapture-program',
//         'acf/rapture-video',
//         'acf/rapture-iframe',
//         'acf/rapture-pdf',
//         'acf/rapture-card',
//         'acf/rapture-events-filter',
//         'acf/rapture-accordion',
//         'acf/rapture-icon',
//         'acf/rapture-spacing',
//         'acf/rapture-buttons',
//         'acf/rapture-info-box',
//         'acf/rapture-shortcode',
//         'acf/rapture-navigation',
//         // Add other ACF Averitas block names here

//         // Add support for the WordPress Grid and related blocks
//         'core/group',
//         'core/columns',
//         'core/column',
//         'core/row',
//         'core/grid',
//         'core/paragraph',
//     ];

//     return $averitas_blocks;
// }, 10, 2 );

/**
 * Hide WordPress Version Info
 */
function hide_wordpress_version() {
	return '';
}
add_filter('the_generator', __NAMESPACE__ . '\\hide_wordpress_version');

/**
 * Remove WordPress Version Number In URL Parameters From JS/CSS
 */
// function hide_wordpress_version_in_script($src, $handle) {
// 	$src = remove_query_arg('ver', $src);
// 	return $src;
// }
// add_filter( 'style_loader_src', __NAMESPACE__ . '\\hide_wordpress_version_in_script', 10, 2 );
// add_filter( 'script_loader_src', __NAMESPACE__ . '\\hide_wordpress_version_in_script', 10, 2 );

/**
 * Add lazy-loading attribute to images.
 *
 * This filter adds the `loading="lazy"` attribute to all images to improve performance
 * by deferring the loading of offscreen images until they are needed.
 *
 * @param array $attr The image attributes.
 * @return array Modified image attributes with lazy-loading enabled.
 */
function theme_lazy_load_images( $attr ) {
    $attr['loading'] = 'lazy';
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', __NAMESPACE__ . '\\theme_lazy_load_images' );

/**
 * Remove "Private" and "Protected" prefixes from post titles.
 *
 * This filter modifies the title format for private and protected posts to remove the prefixes.
 *
 * @param string $format The current title format.
 * @return string Modified title format.
 */
function remove_private_protected_prefix( $format ) {
    return '%s'; // Keep the title as-is without the prefix.
}
add_filter( 'private_title_format', __NAMESPACE__ . '\\remove_private_protected_prefix' );
add_filter( 'protected_title_format', __NAMESPACE__ . '\\remove_private_protected_prefix' );


/**
 * Populate select field using filter
 * This function is not used anywhere - ACF field 'surfcamp_menu' doesn't exist in templates
 */

/**
 * Add container wrapper to columns block
 * 
 * @param string $block_content The block content.
 * @param array  $block        The full block, including name and attributes.
 * @return string Modified block content.
 */
function add_container_wrapper($block_content, $block) {
    if (isset($block['blockName']) && $block['blockName'] === 'core/columns') {
        // Only wrap if not already wrapped (prevent double wrapping)
        if (strpos($block_content, 'wp-block-columns-wrapper') === false) {
            $block_content = '<section class="wp-block-columns-wrapper"><div class="container">' . $block_content . '</div></section>';
        }
    } else if (isset($block['blockName']) && $block['blockName'] === 'core/heading') {
        $block_content = '<section class="wp-block-heading-wrapper"><div class="container">' . $block_content . '</div></section>';
        $block_content = str_replace(
            'class="wp-block-heading"',
            'class="wp-block-heading a-heading a-heading--2xs s-m-b-xs s-d-b-xs"',
            $block_content
        );
    } else if (isset($block['blockName']) && $block['blockName'] === 'core/paragraph') {
        $block_content = '<section class="wp-block-paragraph-wrapper s-m-b-xs s-d-b-xs"><div class="container">' . $block_content . '</div></section>';
        $block_content = str_replace(
            '<p>',
            '<p class="a-text a-text--m h-white-600 h-text-lh--m">',
            $block_content
        );
    }
    return $block_content;
}

add_filter('render_block', __NAMESPACE__ . '\\add_container_wrapper', 10, 2);

function mca_procurement_filter_link($label, $value, $current) {
    $base_url = \MCA\Helpers\rapture_get_current_request_url();
    $url      = ($value === 'all')
        ? $base_url
        : add_query_arg('procurement_status', $value, remove_query_arg('procurement_status', $base_url));
    $active   = ($current === $value) ? 'active' : '';
    return '<a href="' . esc_url($url) . '" class="a-list-item a-list-item--s ' . esc_attr($active) . '">' . esc_html($label) . '</a>';
}

/**
 * Disable big image size threshold
 */
add_filter('big_image_size_threshold', '__return_false');

/**
 * Admin columns: ACF Deadline for Jobs list table
 */
function mca_jobs_admin_columns($cols) {
    // place "Deadline" after Title
    $new = [];
    foreach ($cols as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['mca_deadline'] = __('Deadline', 'mca');
        }
    }
    return $new;
}
add_filter('manage_edit-job_columns', __NAMESPACE__ . '\\mca_jobs_admin_columns');

function mca_jobs_admin_columns_content($column, $post_id) {
    if ($column === 'mca_deadline') {
        $deadline = function_exists('get_field') ? get_field('deadline', $post_id) : '';
        if ($deadline) {
            echo '<span class="a-text a-text--m">' . esc_html($deadline) . '</span>';
        }
    }
}
add_action('manage_job_posts_custom_column', __NAMESPACE__ . '\\mca_jobs_admin_columns_content', 10, 2);

// Disable CF7 styles & scripts globally
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );

/**
 * ACF Local JSON support.
 */
if ( ! function_exists( 'mca_acf_json_path' ) ) {
    function mca_acf_json_path() {
        return function_exists( 'get_stylesheet_directory' ) ? get_stylesheet_directory() . '/acf-json' : '';
    }
}
if ( ! function_exists( 'mca_acf_json_save_point' ) ) {
    function mca_acf_json_save_point( $path ) {
        $path = mca_acf_json_path();
        if ( $path && ! is_dir( $path ) && function_exists( 'wp_mkdir_p' ) ) {
            wp_mkdir_p( $path );
        }
        return $path;
    }
}
if ( ! function_exists( 'mca_acf_json_load_point' ) ) {
    function mca_acf_json_load_point( $paths ) {
        $path = mca_acf_json_path();
        if ( $path && ! in_array( $path, $paths, true ) ) {
            $paths[] = $path;
        }
        return $paths;
    }
}
add_filter( 'acf/settings/save_json', 'MCA\Filters\mca_acf_json_save_point' );
add_filter( 'acf/settings/load_json', 'MCA\Filters\mca_acf_json_load_point' );

/**
 * ACF Blocks - Force V3.
 */
if ( ! function_exists( 'mca_acf_blocks_default_to_v3' ) ) {
    function mca_acf_blocks_default_to_v3( $version, $block ) {
        return 3;
    }
}
add_filter( 'acf/blocks/default_block_version', 'MCA\Filters\mca_acf_blocks_default_to_v3', 10, 2 );

/**
 * ACF Blocks V3 compatibility.
 */
if ( ! function_exists( 'mca_acf_register_block_v3_args' ) ) {
    function mca_acf_register_block_v3_args( $args ) {
        $args['api_version']       = 3;
        $args['acf_block_version'] = 3;
        /**
         * Hide ACF fields from Gutenberg sidebar.
         * Fields will be edited in the Expanded Editor.
         */
        $args['hide_fields_in_sidebar'] = true;
        return $args;
    }
}
add_filter(
    'acf/register_block_type_args',
    'MCA\Filters\mca_acf_register_block_v3_args',
    20
);

/**
 * Expanded Editor button text.
 */
if ( ! function_exists( 'mca_acf_expanded_editor_button_text' ) ) {
    function mca_acf_expanded_editor_button_text( $button_text, $block ) {
        return __( 'Open Expanded Editor', 'balkan-nature-adventure' );
    }
}
add_filter( 'acf/blocks/default_expanded_editor_button_text', 'MCA\Filters\mca_acf_expanded_editor_button_text', 10, 2 );
