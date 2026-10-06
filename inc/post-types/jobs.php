<?php
/**
 * Jobs Custom Post Type
 *
 * @package MCA
 */

namespace MCA\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_jobs_post_type() {
	$labels = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'MCA' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'MCA' ),
		'menu_name'             => __( 'Jobs', 'MCA' ),
		'name_admin_bar'        => __( 'Job', 'MCA' ),
		'archives'              => __( 'Job Archives', 'MCA' ),
		'attributes'            => __( 'Job Attributes', 'MCA' ),
		'parent_item_colon'     => __( 'Parent Job:', 'MCA' ),
		'all_items'             => __( 'All Jobs', 'MCA' ),
		'add_new_item'          => __( 'Add New Job', 'MCA' ),
		'add_new'               => __( 'Add Job', 'MCA' ),
		'new_item'              => __( 'New Job', 'MCA' ),
		'edit_item'             => __( 'Edit Job', 'MCA' ),
		'update_item'           => __( 'Update Job', 'MCA' ),
		'view_item'             => __( 'View Job', 'MCA' ),
		'view_items'            => __( 'View Jobs', 'MCA' ),
		'search_items'          => __( 'Search Jobs', 'MCA' ),
		'not_found'             => __( 'Not found', 'MCA' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'MCA' ),
		'featured_image'        => __( 'Featured Image', 'MCA' ),
		'set_featured_image'    => __( 'Set featured image', 'MCA' ),
		'remove_featured_image' => __( 'Remove featured image', 'MCA' ),
		'use_featured_image'    => __( 'Use as featured image', 'MCA' ),
		'insert_into_item'      => __( 'Insert into job', 'MCA' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'MCA' ),
		'items_list'            => __( 'Jobs list', 'MCA' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'MCA' ),
		'filter_items_list'     => __( 'Filter jobs list', 'MCA' ),
	);

	$args = array(
		'label'                 => __( 'job', 'MCA' ),
		'description'           => __( 'Custom post type for jobs.', 'MCA' ),
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
			'slug'       => 'single-job',
			'with_front' => false,
		),
		'capability_type'       => 'post',
	);

	register_post_type( 'job', $args );
}

add_action( 'init', __NAMESPACE__ . '\\register_jobs_post_type' );
