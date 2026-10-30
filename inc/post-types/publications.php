<?php
/**
 * Publication Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_publications_post_type() {
	$labels = array(
		'name'                  => _x( 'Publications', 'Post Type General Name', 'mca' ),
		'singular_name'         => _x( 'Publication', 'Post Type Singular Name', 'mca' ),
		'menu_name'             => __( 'Publications', 'mca' ),
		'name_admin_bar'        => __( 'Publication', 'mca' ),
		'archives'              => __( 'Publication Archives', 'mca' ),
		'attributes'            => __( 'Publication Attributes', 'mca' ),
		'parent_item_colon'     => __( 'Parent Publication:', 'mca' ),
		'all_items'             => __( 'All Publications', 'mca' ),
		'add_new_item'          => __( 'Add New Publication', 'mca' ),
		'add_new'               => __( 'Add Publication', 'mca' ),
		'new_item'              => __( 'New Publication', 'mca' ),
		'edit_item'             => __( 'Edit Publication', 'mca' ),
		'update_item'           => __( 'Update Publication', 'mca' ),
		'view_item'             => __( 'View Publication', 'mca' ),
		'view_items'            => __( 'View Publications', 'mca' ),
		'search_items'          => __( 'Search Publications', 'mca' ),
		'not_found'             => __( 'Not found', 'mca' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'mca' ),
		'featured_image'        => __( 'Featured Image', 'mca' ),
		'set_featured_image'    => __( 'Set featured image', 'mca' ),
		'remove_featured_image' => __( 'Remove featured image', 'mca' ),
		'use_featured_image'    => __( 'Use as featured image', 'mca' ),
		'insert_into_item'      => __( 'Insert into publication', 'mca' ),
		'uploaded_to_this_item' => __( 'Uploaded to this publication', 'mca' ),
		'items_list'            => __( 'Publications list', 'mca' ),
		'items_list_navigation' => __( 'Publications list navigation', 'mca' ),
		'filter_items_list'     => __( 'Filter publications list', 'mca' ),
	);

	$args = array(
		'label'                 => __( 'publication', 'mca' ),
		'description'           => __( 'Custom post type for publications.', 'mca' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-media-document',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'show_in_rest'          => true,
		'rewrite'               => array(
			'slug'       => 'single-publication',
			'with_front' => false,
		),
		'capability_type'       => 'post',
	);

	register_post_type( 'publication', $args );
}

add_action( 'init', __NAMESPACE__ . '\\register_publications_post_type' );
