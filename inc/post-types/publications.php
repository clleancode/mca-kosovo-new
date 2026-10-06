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
		'name'                  => _x( 'Publications', 'Post Type General Name', 'MCA' ),
		'singular_name'         => _x( 'Publication', 'Post Type Singular Name', 'MCA' ),
		'menu_name'             => __( 'Publications', 'MCA' ),
		'name_admin_bar'        => __( 'Publication', 'MCA' ),
		'archives'              => __( 'Publication Archives', 'MCA' ),
		'attributes'            => __( 'Publication Attributes', 'MCA' ),
		'parent_item_colon'     => __( 'Parent Publication:', 'MCA' ),
		'all_items'             => __( 'All Publications', 'MCA' ),
		'add_new_item'          => __( 'Add New Publication', 'MCA' ),
		'add_new'               => __( 'Add Publication', 'MCA' ),
		'new_item'              => __( 'New Publication', 'MCA' ),
		'edit_item'             => __( 'Edit Publication', 'MCA' ),
		'update_item'           => __( 'Update Publication', 'MCA' ),
		'view_item'             => __( 'View Publication', 'MCA' ),
		'view_items'            => __( 'View Publications', 'MCA' ),
		'search_items'          => __( 'Search Publications', 'MCA' ),
		'not_found'             => __( 'Not found', 'MCA' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'MCA' ),
		'featured_image'        => __( 'Featured Image', 'MCA' ),
		'set_featured_image'    => __( 'Set featured image', 'MCA' ),
		'remove_featured_image' => __( 'Remove featured image', 'MCA' ),
		'use_featured_image'    => __( 'Use as featured image', 'MCA' ),
		'insert_into_item'      => __( 'Insert into publication', 'MCA' ),
		'uploaded_to_this_item' => __( 'Uploaded to this publication', 'MCA' ),
		'items_list'            => __( 'Publications list', 'MCA' ),
		'items_list_navigation' => __( 'Publications list navigation', 'MCA' ),
		'filter_items_list'     => __( 'Filter publications list', 'MCA' ),
	);

	$args = array(
		'label'                 => __( 'publication', 'MCA' ),
		'description'           => __( 'Custom post type for publications.', 'MCA' ),
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
