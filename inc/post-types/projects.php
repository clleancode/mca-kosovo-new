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
        'name'                  => _x( 'Projects', 'Post Type General Name', 'mca' ),
        'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'mca' ),
        'menu_name'             => __( 'Projects', 'mca' ),
        'name_admin_bar'        => __( 'Project', 'mca' ),
        'archives'              => __( 'Project Archives', 'mca' ),
        'attributes'            => __( 'Project Attributes', 'mca' ),
        'parent_item_colon'     => __( 'Parent Project:', 'mca' ),
        'all_items'             => __( 'All Projects', 'mca' ),
        'add_new_item'          => __( 'Add New Project', 'mca' ),
        'add_new'               => __( 'Add Project', 'mca' ),
        'new_item'              => __( 'New Project', 'mca' ),
        'edit_item'             => __( 'Edit Project', 'mca' ),
        'update_item'           => __( 'Update Project', 'mca' ),
        'view_item'             => __( 'View Project', 'mca' ),
        'view_items'            => __( 'View Projects', 'mca' ),
        'search_items'          => __( 'Search Projects', 'mca' ),
        'not_found'             => __( 'Not found', 'mca' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'mca' ),
        'featured_image'        => __( 'Featured Image', 'mca' ),
        'set_featured_image'    => __( 'Set featured image', 'mca' ),
        'remove_featured_image' => __( 'Remove featured image', 'mca' ),
        'use_featured_image'    => __( 'Use as featured image', 'mca' ),
        'insert_into_item'      => __( 'Insert into project', 'mca' ),
        'uploaded_to_this_item' => __( 'Uploaded to this project', 'mca' ),
        'items_list'            => __( 'Projects list', 'mca' ),
        'items_list_navigation' => __( 'Projects list navigation', 'mca' ),
        'filter_items_list'     => __( 'Filter projects list', 'mca' ),
    );

    $args = array(
        'label'                 => __( 'project', 'mca' ),
        'description'           => __( 'Custom post type for projects.', 'mca' ),
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


