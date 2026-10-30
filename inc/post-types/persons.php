<?php
/**
 * Portraits available for the careers hero collage.
 *
 * @package MCA
 */

namespace MCA\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function register_persons_post_type() {
    register_post_type('person', [
        'labels' => [
            'name'                  => __('Persons', 'mca'),
            'singular_name'         => __('Person', 'mca'),
            'menu_name'             => __('Persons', 'mca'),
            'all_items'             => __('All Persons', 'mca'),
            'add_new_item'          => __('Add New Person', 'mca'),
            'edit_item'             => __('Edit Person', 'mca'),
            'search_items'          => __('Search Persons', 'mca'),
            'not_found'             => __('No persons found', 'mca'),
            'not_found_in_trash'    => __('No persons found in Trash', 'mca'),
            'featured_image'        => __('Person photo', 'mca'),
            'set_featured_image'    => __('Set person photo', 'mca'),
            'remove_featured_image' => __('Remove person photo', 'mca'),
            'use_featured_image'    => __('Use as person photo', 'mca'),
        ],
        'description'         => __('Publish persons with a photo to include them in the careers hero.', 'mca'),
        'supports'            => ['title', 'thumbnail'],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'menu_icon'           => 'dashicons-groups',
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
    ]);
}

add_action('init', __NAMESPACE__ . '\\register_persons_post_type');
