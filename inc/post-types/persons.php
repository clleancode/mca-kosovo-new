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
            'name'                  => __('Persons', 'MCA'),
            'singular_name'         => __('Person', 'MCA'),
            'menu_name'             => __('Persons', 'MCA'),
            'all_items'             => __('All Persons', 'MCA'),
            'add_new_item'          => __('Add New Person', 'MCA'),
            'edit_item'             => __('Edit Person', 'MCA'),
            'search_items'          => __('Search Persons', 'MCA'),
            'not_found'             => __('No persons found', 'MCA'),
            'not_found_in_trash'    => __('No persons found in Trash', 'MCA'),
            'featured_image'        => __('Person photo', 'MCA'),
            'set_featured_image'    => __('Set person photo', 'MCA'),
            'remove_featured_image' => __('Remove person photo', 'MCA'),
            'use_featured_image'    => __('Use as person photo', 'MCA'),
        ],
        'description'         => __('Publish persons with a photo to include them in the careers hero.', 'MCA'),
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
