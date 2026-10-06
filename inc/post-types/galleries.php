<?php
/**
 * Gallery Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_galleries_post_type() {
	$labels = array(
		'name'                  => _x( 'Galleries', 'Post Type General Name', 'MCA' ),
		'singular_name'         => _x( 'Gallery', 'Post Type Singular Name', 'MCA' ),
		'menu_name'             => __( 'Galleries', 'MCA' ),
		'name_admin_bar'        => __( 'Gallery', 'MCA' ),
		'archives'              => __( 'Gallery Archives', 'MCA' ),
		'attributes'            => __( 'Gallery Attributes', 'MCA' ),
		'parent_item_colon'     => __( 'Parent Gallery:', 'MCA' ),
		'all_items'             => __( 'All Galleries', 'MCA' ),
		'add_new_item'          => __( 'Add New Gallery', 'MCA' ),
		'add_new'               => __( 'Add Gallery', 'MCA' ),
		'new_item'              => __( 'New Gallery', 'MCA' ),
		'edit_item'             => __( 'Edit Gallery', 'MCA' ),
		'update_item'           => __( 'Update Gallery', 'MCA' ),
		'view_item'             => __( 'View Gallery', 'MCA' ),
		'view_items'            => __( 'View Galleries', 'MCA' ),
		'search_items'          => __( 'Search Galleries', 'MCA' ),
		'not_found'             => __( 'Not found', 'MCA' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'MCA' ),
		'featured_image'        => __( 'Featured Image', 'MCA' ),
		'set_featured_image'    => __( 'Set featured image', 'MCA' ),
		'remove_featured_image' => __( 'Remove featured image', 'MCA' ),
		'use_featured_image'    => __( 'Use as featured image', 'MCA' ),
		'insert_into_item'      => __( 'Insert into gallery', 'MCA' ),
		'uploaded_to_this_item' => __( 'Uploaded to this gallery', 'MCA' ),
		'items_list'            => __( 'Galleries list', 'MCA' ),
		'items_list_navigation' => __( 'Galleries list navigation', 'MCA' ),
		'filter_items_list'     => __( 'Filter galleries list', 'MCA' ),
	);

	$args = array(
		'label'                 => __( 'gallery', 'MCA' ),
		'description'           => __( 'Custom post type for image galleries.', 'MCA' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-format-gallery',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'show_in_rest'          => true,
		'rewrite'               => array(
			'slug'       => 'single-gallery',
			'with_front' => false,
		),
		'capability_type'       => 'post',
	);

	register_post_type( 'gallery', $args );
}

add_action( 'init', __NAMESPACE__ . '\\register_galleries_post_type' );
