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
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'mca' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'mca' ),
		'menu_name'             => __( 'Jobs', 'mca' ),
		'name_admin_bar'        => __( 'Job', 'mca' ),
		'archives'              => __( 'Job Archives', 'mca' ),
		'attributes'            => __( 'Job Attributes', 'mca' ),
		'parent_item_colon'     => __( 'Parent Job:', 'mca' ),
		'all_items'             => __( 'All Jobs', 'mca' ),
		'add_new_item'          => __( 'Add New Job', 'mca' ),
		'add_new'               => __( 'Add Job', 'mca' ),
		'new_item'              => __( 'New Job', 'mca' ),
		'edit_item'             => __( 'Edit Job', 'mca' ),
		'update_item'           => __( 'Update Job', 'mca' ),
		'view_item'             => __( 'View Job', 'mca' ),
		'view_items'            => __( 'View Jobs', 'mca' ),
		'search_items'          => __( 'Search Jobs', 'mca' ),
		'not_found'             => __( 'Not found', 'mca' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'mca' ),
		'featured_image'        => __( 'Featured Image', 'mca' ),
		'set_featured_image'    => __( 'Set featured image', 'mca' ),
		'remove_featured_image' => __( 'Remove featured image', 'mca' ),
		'use_featured_image'    => __( 'Use as featured image', 'mca' ),
		'insert_into_item'      => __( 'Insert into job', 'mca' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'mca' ),
		'items_list'            => __( 'Jobs list', 'mca' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'mca' ),
		'filter_items_list'     => __( 'Filter jobs list', 'mca' ),
	);

	$args = array(
		'label'                 => __( 'job', 'mca' ),
		'description'           => __( 'Custom post type for jobs.', 'mca' ),
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
