<?php
/**
 * Projects Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function register_projects_post_type() {
    $labels = array(
        'name'                  => _x( 'Projects', 'Post Type General Name', 'MCA' ),
        'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'MCA' ),
        'menu_name'             => __( 'Projects', 'MCA' ),
        'name_admin_bar'        => __( 'Project', 'MCA' ),
        'archives'              => __( 'Project Archives', 'MCA' ),
        'attributes'            => __( 'Project Attributes', 'MCA' ),
        'parent_item_colon'     => __( 'Parent Project:', 'MCA' ),
        'all_items'             => __( 'All Projects', 'MCA' ),
        'add_new_item'          => __( 'Add New Project', 'MCA' ),
        'add_new'               => __( 'Add Project', 'MCA' ),
        'new_item'              => __( 'New Project', 'MCA' ),
        'edit_item'             => __( 'Edit Project', 'MCA' ),
        'update_item'           => __( 'Update Project', 'MCA' ),
        'view_item'             => __( 'View Project', 'MCA' ),
        'view_items'            => __( 'View Projects', 'MCA' ),
        'search_items'          => __( 'Search Projects', 'MCA' ),
        'not_found'             => __( 'Not found', 'MCA' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'MCA' ),
        'featured_image'        => __( 'Featured Image', 'MCA' ),
        'set_featured_image'    => __( 'Set featured image', 'MCA' ),
        'remove_featured_image' => __( 'Remove featured image', 'MCA' ),
        'use_featured_image'    => __( 'Use as featured image', 'MCA' ),
        'insert_into_item'      => __( 'Insert into project', 'MCA' ),
        'uploaded_to_this_item' => __( 'Uploaded to this project', 'MCA' ),
        'items_list'            => __( 'Projects list', 'MCA' ),
        'items_list_navigation' => __( 'Projects list navigation', 'MCA' ),
        'filter_items_list'     => __( 'Filter projects list', 'MCA' ),
    );

    $args = array(
        'label'                 => __( 'project', 'MCA' ),
        'description'           => __( 'Custom post type for projects.', 'MCA' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'       => 'project',
            'with_front' => false,
        ),
        'capability_type'       => 'post',
    );

    register_post_type( 'project', $args );
}

add_action( 'init', __NAMESPACE__ . '\\register_projects_post_type' );

// Refresh existing installations once after enabling the project URLs.
add_action('wp_loaded', function () {
    if (get_option('mca_project_rewrite_version') === '1') return;
    flush_rewrite_rules(false);
    update_option('mca_project_rewrite_version', '1', false);
});


