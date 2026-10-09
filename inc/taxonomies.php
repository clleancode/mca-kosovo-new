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
        'name'                       => _x( 'Job Statuses', 'taxonomy general name', 'MCA' ),
        'singular_name'              => _x( 'Job Status', 'taxonomy singular name', 'MCA' ),
        'search_items'               => __( 'Search Job Statuses', 'MCA' ),
        'popular_items'              => __( 'Popular Job Statuses', 'MCA' ),
        'all_items'                  => __( 'All Job Statuses', 'MCA' ),
        'edit_item'                  => __( 'Edit Job Status', 'MCA' ),
        'update_item'                => __( 'Update Job Status', 'MCA' ),
        'add_new_item'               => __( 'Add New Job Status', 'MCA' ),
        'new_item_name'              => __( 'New Job Status Name', 'MCA' ),
        'separate_items_with_commas' => __( 'Separate statuses with commas', 'MCA' ),
        'add_or_remove_items'        => __( 'Add or remove statuses', 'MCA' ),
        'choose_from_most_used'      => __( 'Choose from the most used statuses', 'MCA' ),
        'menu_name'                  => __( 'Job Status', 'MCA' ),
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
        'ongoing' => __( 'Ongoing', 'MCA' ),
        'closed'  => __( 'Closed', 'MCA' ),
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
        'name'                       => _x( 'Procurement Statuses', 'taxonomy general name', 'MCA' ),
        'singular_name'              => _x( 'Procurement Status', 'taxonomy singular name', 'MCA' ),
        'search_items'               => __( 'Search Procurement Statuses', 'MCA' ),
        'popular_items'              => __( 'Popular Procurement Statuses', 'MCA' ),
        'all_items'                  => __( 'All Procurement Statuses', 'MCA' ),
        'edit_item'                  => __( 'Edit Procurement Status', 'MCA' ),
        'update_item'                => __( 'Update Procurement Status', 'MCA' ),
        'add_new_item'               => __( 'Add New Procurement Status', 'MCA' ),
        'new_item_name'              => __( 'New Procurement Status Name', 'MCA' ),
        'separate_items_with_commas' => __( 'Separate statuses with commas', 'MCA' ),
        'add_or_remove_items'        => __( 'Add or remove statuses', 'MCA' ),
        'choose_from_most_used'      => __( 'Choose from the most used statuses', 'MCA' ),
        'menu_name'                  => __( 'Procurement Status', 'MCA' ),
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
        'ongoing'                    => __( 'Ongoing', 'MCA' ),
        'closing-soon'               => __('Closing soon', 'MCA'),
        'in-evaluation'              => __('In evaluation', 'MCA'),
        'specific-procurement-notice' => __('Specific Procurement Notice', 'MCA'),
        'closed'                     => __( 'Closed', 'MCA' ),
        'general-procurement-notice' => __( 'General Procurement Notice', 'MCA' ),
        'procurement-guidelines'     => __( 'Procurement Guidelines', 'MCA' ),
        'bidchallenge-system'        => __( 'Bidchallenge System', 'MCA' ),
        'award-notice'               => __( 'Award notice', 'MCA' ),
        'funding-opportunities'      => __( 'Funding opportunities', 'MCA' ),
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
        'name'                       => _x( 'Publication Types', 'taxonomy general name', 'MCA' ),
        'singular_name'              => _x( 'Publication Type', 'taxonomy singular name', 'MCA' ),
        'search_items'               => __( 'Search Publication Types', 'MCA' ),
        'popular_items'              => __( 'Popular Publication Types', 'MCA' ),
        'all_items'                  => __( 'All Publication Types', 'MCA' ),
        'edit_item'                  => __( 'Edit Publication Type', 'MCA' ),
        'update_item'                => __( 'Update Publication Type', 'MCA' ),
        'add_new_item'               => __( 'Add New Publication Type', 'MCA' ),
        'new_item_name'              => __( 'New Publication Type Name', 'MCA' ),
        'separate_items_with_commas' => __( 'Separate types with commas', 'MCA' ),
        'add_or_remove_items'        => __( 'Add or remove types', 'MCA' ),
        'choose_from_most_used'      => __( 'Choose from the most used types', 'MCA' ),
        'menu_name'                  => __( 'Publication Type', 'MCA' ),
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
    $default_terms = [ ];

    foreach ( $default_terms as $slug => $name ) {
        if ( ! term_exists( $name, 'publication_type' ) && ! term_exists( $slug, 'publication_type' ) ) {
            wp_insert_term( $name, 'publication_type', [ 'slug' => $slug ] );
        }
    }
}
add_action( 'init', __NAMESPACE__ . '\\register_publication_type_taxonomy' );
