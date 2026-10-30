<?php
/**
 * Custom taxonomies for MCA theme
 */

namespace MCA\Taxonomies;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Job Status taxonomy for Job post type
 */
function register_job_status_taxonomy() {
    $labels = [
        'name'                       => _x( 'Job Statuses', 'taxonomy general name', 'mca' ),
        'singular_name'              => _x( 'Job Status', 'taxonomy singular name', 'mca' ),
        'search_items'               => __( 'Search Job Statuses', 'mca' ),
        'popular_items'              => __( 'Popular Job Statuses', 'mca' ),
        'all_items'                  => __( 'All Job Statuses', 'mca' ),
        'edit_item'                  => __( 'Edit Job Status', 'mca' ),
        'update_item'                => __( 'Update Job Status', 'mca' ),
        'add_new_item'               => __( 'Add New Job Status', 'mca' ),
        'new_item_name'              => __( 'New Job Status Name', 'mca' ),
        'separate_items_with_commas' => __( 'Separate statuses with commas', 'mca' ),
        'add_or_remove_items'        => __( 'Add or remove statuses', 'mca' ),
        'choose_from_most_used'      => __( 'Choose from the most used statuses', 'mca' ),
        'menu_name'                  => __( 'Job Status', 'mca' ),
    ];

    $args = [
        'hierarchical'          => true, // Like tags
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => [ 'slug' => 'job-status' ],
        'show_in_rest'          => true,
    ];

    register_taxonomy( 'job_status', [ 'job' ], $args );

    // Ensure default terms exist
    $default_terms = [
        'ongoing' => __( 'Ongoing', 'mca' ),
        'closed'  => __( 'Closed', 'mca' ),
    ];

    foreach ( $default_terms as $slug => $name ) {
        if ( ! term_exists( $name, 'job_status' ) && ! term_exists( $slug, 'job_status' ) ) {
            wp_insert_term( $name, 'job_status', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', __NAMESPACE__ . '\\register_job_status_taxonomy' );

/**
 * Register Procurement Status taxonomy for Procurement post type
 */
function register_procurement_status_taxonomy() {
    $labels = [
        'name'                       => _x( 'Procurement Statuses', 'taxonomy general name', 'mca' ),
        'singular_name'              => _x( 'Procurement Status', 'taxonomy singular name', 'mca' ),
        'search_items'               => __( 'Search Procurement Statuses', 'mca' ),
        'popular_items'              => __( 'Popular Procurement Statuses', 'mca' ),
        'all_items'                  => __( 'All Procurement Statuses', 'mca' ),
        'edit_item'                  => __( 'Edit Procurement Status', 'mca' ),
        'update_item'                => __( 'Update Procurement Status', 'mca' ),
        'add_new_item'               => __( 'Add New Procurement Status', 'mca' ),
        'new_item_name'              => __( 'New Procurement Status Name', 'mca' ),
        'separate_items_with_commas' => __( 'Separate statuses with commas', 'mca' ),
        'add_or_remove_items'        => __( 'Add or remove statuses', 'mca' ),
        'choose_from_most_used'      => __( 'Choose from the most used statuses', 'mca' ),
        'menu_name'                  => __( 'Procurement Status', 'mca' ),
    ];

    $args = [
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => [ 'slug' => 'procurement-status' ],
        'show_in_rest'          => true,
    ];

    register_taxonomy( 'procurement_status', [ 'procurement' ], $args );

    // Ensure default terms exist
    $default_terms = [
        'ongoing'                    => __( 'Ongoing', 'mca' ),
        'upcoming'                   => __( 'Upcoming', 'mca' ),
        'closing-soon'               => __('Closing soon', 'mca'),
        'in-evaluation'              => __('In evaluation', 'mca'),
        'specific-procurement-notice' => __('Specific Procurement Notice', 'mca'),
        'closed'                     => __( 'Closed', 'mca' ),
        'general-procurement-notice' => __( 'General Procurement Notice', 'mca' ),
        'procurement-guidelines'     => __( 'Procurement Guidelines', 'mca' ),
        'bidchallenge-system'        => __( 'Bidchallenge System', 'mca' ),
        'award-notice'               => __( 'Award notice', 'mca' ),
        'funding-opportunities'      => __( 'Funding opportunities', 'mca' ),
    ];

    foreach ( $default_terms as $slug => $name ) {
        if ( ! term_exists( $name, 'procurement_status' ) && ! term_exists( $slug, 'procurement_status' ) ) {
            wp_insert_term( $name, 'procurement_status', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', __NAMESPACE__ . '\\register_procurement_status_taxonomy' );

/**
 * Register Publication Type taxonomy for Publication post type
 */
function register_publication_type_taxonomy() {
    $labels = [
        'name'                       => _x( 'Publication Types', 'taxonomy general name', 'mca' ),
        'singular_name'              => _x( 'Publication Type', 'taxonomy singular name', 'mca' ),
        'search_items'               => __( 'Search Publication Types', 'mca' ),
        'popular_items'              => __( 'Popular Publication Types', 'mca' ),
        'all_items'                  => __( 'All Publication Types', 'mca' ),
        'edit_item'                  => __( 'Edit Publication Type', 'mca' ),
        'update_item'                => __( 'Update Publication Type', 'mca' ),
        'add_new_item'               => __( 'Add New Publication Type', 'mca' ),
        'new_item_name'              => __( 'New Publication Type Name', 'mca' ),
        'separate_items_with_commas' => __( 'Separate types with commas', 'mca' ),
        'add_or_remove_items'        => __( 'Add or remove types', 'mca' ),
        'choose_from_most_used'      => __( 'Choose from the most used types', 'mca' ),
        'menu_name'                  => __( 'Publication Type', 'mca' ),
    ];

    $args = [
        'hierarchical'          => true, // Like categories
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => [ 'slug' => 'publication-type' ],
        'show_in_rest'          => true,
    ];

    register_taxonomy( 'publication_type', [ 'publication' ], $args );

    // Optionally, add some default terms
    $default_terms = [
        'publications' => __('Publications', 'mca'),
        'project-documents' => __('Project documents', 'mca'),
        'legal-framework' => __('Legal framework', 'mca'),
        'policies' => __('Policies', 'mca'),
        'board-records' => __('Board records', 'mca'),
    ];

    foreach ( $default_terms as $slug => $name ) {
        if ( ! term_exists( $name, 'publication_type' ) && ! term_exists( $slug, 'publication_type' ) ) {
            wp_insert_term( $name, 'publication_type', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', __NAMESPACE__ . '\\register_publication_type_taxonomy' );

// Album categories for the All Albums filters.
add_action('init', function () {
    register_taxonomy('gallery_category', ['gallery'], [
        'label' => __('Album categories', 'mca'),
        'public' => true, 'hierarchical' => true, 'show_in_rest' => true,
        'show_admin_column' => true, 'rewrite' => ['slug' => 'album-category'],
    ]);
});
