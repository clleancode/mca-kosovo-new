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
		'name'                  => _x( 'Galleries', 'Post Type General Name', 'mca' ),
		'singular_name'         => _x( 'Gallery', 'Post Type Singular Name', 'mca' ),
		'menu_name'             => __( 'Galleries', 'mca' ),
		'name_admin_bar'        => __( 'Gallery', 'mca' ),
		'archives'              => __( 'Gallery Archives', 'mca' ),
		'attributes'            => __( 'Gallery Attributes', 'mca' ),
		'parent_item_colon'     => __( 'Parent Gallery:', 'mca' ),
		'all_items'             => __( 'All Galleries', 'mca' ),
		'add_new_item'          => __( 'Add New Gallery', 'mca' ),
		'add_new'               => __( 'Add Gallery', 'mca' ),
		'new_item'              => __( 'New Gallery', 'mca' ),
		'edit_item'             => __( 'Edit Gallery', 'mca' ),
		'update_item'           => __( 'Update Gallery', 'mca' ),
		'view_item'             => __( 'View Gallery', 'mca' ),
		'view_items'            => __( 'View Galleries', 'mca' ),
		'search_items'          => __( 'Search Galleries', 'mca' ),
		'not_found'             => __( 'Not found', 'mca' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'mca' ),
		'featured_image'        => __( 'Featured Image', 'mca' ),
		'set_featured_image'    => __( 'Set featured image', 'mca' ),
		'remove_featured_image' => __( 'Remove featured image', 'mca' ),
		'use_featured_image'    => __( 'Use as featured image', 'mca' ),
		'insert_into_item'      => __( 'Insert into gallery', 'mca' ),
		'uploaded_to_this_item' => __( 'Uploaded to this gallery', 'mca' ),
		'items_list'            => __( 'Galleries list', 'mca' ),
		'items_list_navigation' => __( 'Galleries list navigation', 'mca' ),
		'filter_items_list'     => __( 'Filter galleries list', 'mca' ),
	);

	$args = array(
		'label'                 => __( 'gallery', 'mca' ),
		'description'           => __( 'Custom post type for image galleries.', 'mca' ),
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
