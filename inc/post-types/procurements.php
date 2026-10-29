<?php
/**
 * Procurements Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function register_procurements_post_type() {
    $labels = array(
        'name'                  => _x( 'Procurements', 'Post Type General Name', 'mca' ),
        'singular_name'         => _x( 'Procurement', 'Post Type Singular Name', 'mca' ),
        'menu_name'             => __( 'Procurements', 'mca' ),
        'name_admin_bar'        => __( 'Procurement', 'mca' ),
        'archives'              => __( 'Procurement Archives', 'mca' ),
        'attributes'            => __( 'Procurement Attributes', 'mca' ),
        'parent_item_colon'     => __( 'Parent Procurement:', 'mca' ),
        'all_items'             => __( 'All Procurements', 'mca' ),
        'add_new_item'          => __( 'Add New Procurement', 'mca' ),
        'add_new'               => __( 'Add Procurement', 'mca' ),
        'new_item'              => __( 'New Procurement', 'mca' ),
        'edit_item'             => __( 'Edit Procurement', 'mca' ),
        'update_item'           => __( 'Update Procurement', 'mca' ),
        'view_item'             => __( 'View Procurement', 'mca' ),
        'view_items'            => __( 'View Procurements', 'mca' ),
        'search_items'          => __( 'Search Procurements', 'mca' ),
        'not_found'             => __( 'Not found', 'mca' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'mca' ),
        'featured_image'        => __( 'Featured Image', 'mca' ),
        'set_featured_image'    => __( 'Set featured image', 'mca' ),
        'remove_featured_image' => __( 'Remove featured image', 'mca' ),
        'use_featured_image'    => __( 'Use as featured image', 'mca' ),
        'insert_into_item'      => __( 'Insert into procurement', 'mca' ),
        'uploaded_to_this_item' => __( 'Uploaded to this procurement', 'mca' ),
        'items_list'            => __( 'Procurements list', 'mca' ),
        'items_list_navigation' => __( 'Procurements list navigation', 'mca' ),
        'filter_items_list'     => __( 'Filter procurements list', 'mca' ),
    );

    $args = array(
        'label'                 => __( 'procurement', 'mca' ),
        'description'           => __( 'Custom post type for procurements.', 'mca' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-media-spreadsheet',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'       => 'procurements',
            'with_front' => false,
        ),
        'has_archive'           => 'procurements',
        'capability_type'       => 'post',
    );

    register_post_type( 'procurement', $args );
}

add_action( 'init', __NAMESPACE__ . '\\register_procurements_post_type' );
