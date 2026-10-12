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
        'name'                  => _x('Programs', 'Post Type General Name', 'MCA'),
        'singular_name'         => _x('Program', 'Post Type Singular Name', 'MCA'),
        'menu_name'             => __('Programs', 'MCA'),
        'name_admin_bar'        => __('Program', 'MCA'),
        'all_items'             => __('All Programs', 'MCA'),
        'add_new_item'           => __('Add New Program', 'MCA'),
        'add_new'               => __('Add Program', 'MCA'),
        'new_item'              => __('New Program', 'MCA'),
        'edit_item'             => __('Edit Program', 'MCA'),
        'update_item'           => __('Update Program', 'MCA'),
        'view_item'             => __('View Program', 'MCA'),
        'search_items'          => __('Search Programs', 'MCA'),
        'not_found'             => __('No programs found', 'MCA'),
        'not_found_in_trash'    => __('No programs found in Trash', 'MCA'),
        'featured_image'        => __('Featured Image', 'MCA'),
        'set_featured_image'    => __('Set featured image', 'MCA'),
        'remove_featured_image' => __('Remove featured image', 'MCA'),
        'use_featured_image'    => __('Use as featured image', 'MCA'),
        'items_list'            => __('Programs list', 'MCA'),
        'items_list_navigation' => __('Programs list navigation', 'MCA'),
    ];

    register_post_type('program', [
        'label'               => __('Programs', 'MCA'),
        'description'         => __('Programs associated with MCA projects.', 'MCA'),
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
