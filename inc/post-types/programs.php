<?php
/**
 * Programs Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function register_programs_post_type() {
    $labels = [
        'name'                  => _x('Programs', 'Post Type General Name', 'mca'),
        'singular_name'         => _x('Program', 'Post Type Singular Name', 'mca'),
        'menu_name'             => __('Programs', 'mca'),
        'name_admin_bar'        => __('Program', 'mca'),
        'all_items'             => __('All Programs', 'mca'),
        'add_new_item'           => __('Add New Program', 'mca'),
        'add_new'               => __('Add Program', 'mca'),
        'new_item'              => __('New Program', 'mca'),
        'edit_item'             => __('Edit Program', 'mca'),
        'update_item'           => __('Update Program', 'mca'),
        'view_item'             => __('View Program', 'mca'),
        'search_items'          => __('Search Programs', 'mca'),
        'not_found'             => __('No programs found', 'mca'),
        'not_found_in_trash'    => __('No programs found in Trash', 'mca'),
        'featured_image'        => __('Featured Image', 'mca'),
        'set_featured_image'    => __('Set featured image', 'mca'),
        'remove_featured_image' => __('Remove featured image', 'mca'),
        'use_featured_image'    => __('Use as featured image', 'mca'),
        'items_list'            => __('Programs list', 'mca'),
        'items_list_navigation' => __('Programs list navigation', 'mca'),
    ];

    register_post_type('program', [
        'label'               => __('Programs', 'mca'),
        'description'         => __('Programs associated with MCA projects.', 'mca'),
        'labels'              => $labels,
        'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-welcome-learn-more',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest'        => true,
        'rewrite'             => [
            'slug'       => 'programs',
            'with_front' => false,
        ],
        'capability_type'     => 'post',
    ]);
}

add_action('init', __NAMESPACE__ . '\\register_programs_post_type');
