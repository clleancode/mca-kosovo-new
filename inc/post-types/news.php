<?php
/**
 * News Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

if (!defined('ABSPATH')) {
    exit;
}

function register_news_post_type() {
    $labels = [
        'name'                  => _x('News', 'Post Type General Name', 'MCA'),
        'singular_name'         => _x('News', 'Post Type Singular Name', 'MCA'),
        'menu_name'             => __('News', 'MCA'),
        'name_admin_bar'        => __('News', 'MCA'),
        'all_items'             => __('All News', 'MCA'),
        'add_new_item'          => __('Add News', 'MCA'),
        'add_new'               => __('Add News', 'MCA'),
        'new_item'              => __('New News', 'MCA'),
        'edit_item'             => __('Edit News', 'MCA'),
        'update_item'           => __('Update News', 'MCA'),
        'view_item'             => __('View News', 'MCA'),
        'search_items'          => __('Search News', 'MCA'),
        'not_found'             => __('No news found', 'MCA'),
        'not_found_in_trash'    => __('No news found in Trash', 'MCA'),
        'featured_image'        => __('Featured Image', 'MCA'),
        'set_featured_image'    => __('Set featured image', 'MCA'),
        'remove_featured_image' => __('Remove featured image', 'MCA'),
        'use_featured_image'    => __('Use as featured image', 'MCA'),
        'items_list'            => __('News list', 'MCA'),
        'items_list_navigation' => __('News list navigation', 'MCA'),
    ];

    register_post_type('news', [
        'label'               => __('News', 'MCA'),
        'description'         => __('News stories displayed in the News Grid block.', 'MCA'),
        'labels'              => $labels,
        'supports'            => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'taxonomies'          => ['news_category', 'post_tag'],
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-media-text',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest'        => true,
        'rewrite'             => [
            'slug'       => 'news',
            'with_front' => false,
        ],
        'capability_type'     => 'post',
    ]);
}

add_action('init', __NAMESPACE__ . '\\register_news_post_type');

function register_news_category_taxonomy() {
    $labels = [
        'name'                       => _x('News Categories', 'Taxonomy General Name', 'MCA'),
        'singular_name'              => _x('News Category', 'Taxonomy Singular Name', 'MCA'),
        'menu_name'                  => __('News Categories', 'MCA'),
        'all_items'                  => __('All News Categories', 'MCA'),
        'parent_item'                => __('Parent News Category', 'MCA'),
        'parent_item_colon'          => __('Parent News Category:', 'MCA'),
        'new_item_name'              => __('New News Category Name', 'MCA'),
        'add_new_item'               => __('Add New News Category', 'MCA'),
        'edit_item'                  => __('Edit News Category', 'MCA'),
        'update_item'                => __('Update News Category', 'MCA'),
        'view_item'                  => __('View News Category', 'MCA'),
        'separate_items_with_commas' => __('Separate categories with commas', 'MCA'),
        'add_or_remove_items'        => __('Add or remove categories', 'MCA'),
        'choose_from_most_used'      => __('Choose from the most used categories', 'MCA'),
        'popular_items'              => __('Popular News Categories', 'MCA'),
        'search_items'               => __('Search News Categories', 'MCA'),
        'not_found'                  => __('No news categories found', 'MCA'),
    ];

    register_taxonomy('news_category', ['news'], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => [
            'slug'         => 'news-category',
            'with_front'   => false,
            'hierarchical' => true,
        ],
    ]);
}

add_action('init', __NAMESPACE__ . '\\register_news_category_taxonomy', 11);

add_action('wp_loaded', function () {
    if (get_option('mca_news_rewrite_version') === '2') return;

    flush_rewrite_rules(false);
    update_option('mca_news_rewrite_version', '2', false);
});
