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
        'name'                  => _x( 'Procurements', 'Post Type General Name', 'MCA' ),
        'singular_name'         => _x( 'Procurement', 'Post Type Singular Name', 'MCA' ),
        'menu_name'             => __( 'Procurements', 'MCA' ),
        'name_admin_bar'        => __( 'Procurement', 'MCA' ),
        'archives'              => __( 'Procurement Archives', 'MCA' ),
        'attributes'            => __( 'Procurement Attributes', 'MCA' ),
        'parent_item_colon'     => __( 'Parent Procurement:', 'MCA' ),
        'all_items'             => __( 'All Procurements', 'MCA' ),
        'add_new_item'          => __( 'Add New Procurement', 'MCA' ),
        'add_new'               => __( 'Add Procurement', 'MCA' ),
        'new_item'              => __( 'New Procurement', 'MCA' ),
        'edit_item'             => __( 'Edit Procurement', 'MCA' ),
        'update_item'           => __( 'Update Procurement', 'MCA' ),
        'view_item'             => __( 'View Procurement', 'MCA' ),
        'view_items'            => __( 'View Procurements', 'MCA' ),
        'search_items'          => __( 'Search Procurements', 'MCA' ),
        'not_found'             => __( 'Not found', 'MCA' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'MCA' ),
        'featured_image'        => __( 'Featured Image', 'MCA' ),
        'set_featured_image'    => __( 'Set featured image', 'MCA' ),
        'remove_featured_image' => __( 'Remove featured image', 'MCA' ),
        'use_featured_image'    => __( 'Use as featured image', 'MCA' ),
        'insert_into_item'      => __( 'Insert into procurement', 'MCA' ),
        'uploaded_to_this_item' => __( 'Uploaded to this procurement', 'MCA' ),
        'items_list'            => __( 'Procurements list', 'MCA' ),
        'items_list_navigation' => __( 'Procurements list navigation', 'MCA' ),
        'filter_items_list'     => __( 'Filter procurements list', 'MCA' ),
    );

    $args = array(
        'label'                 => __( 'procurement', 'MCA' ),
        'description'           => __( 'Custom post type for procurements.', 'MCA' ),
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
