<?php
/**
 * Admin functionality for duplicating posts.
 *
 * @package Medical_Averitas
 */

namespace MedicalAveritas\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Duplicate a post as a draft.
 */
function duplicate_post_as_draft() {
    global $wpdb;

    // Validate the request
    if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'duplicate_post_as_draft' === $_REQUEST['action'] ) ) ) {
        wp_die( esc_html__( 'No post to duplicate has been supplied!', 'medical-averitas' ) );
    }

    // Verify the nonce
    if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ), 'duplicate_post_nonce' ) ) {
        wp_die( esc_html__( 'Invalid nonce. Action not allowed.', 'medical-averitas' ) );
    }

    // Get original post ID and object
    $post_id = absint( $_GET['post'] ?? $_POST['post'] ?? 0 );
    $post    = get_post( $post_id );

    if ( ! $post ) {
        wp_die( esc_html__( 'Post creation failed. Could not find the original post.', 'medical-averitas' ) );
    }

    $post_type_object = get_post_type_object( $post->post_type );
    if (
        ! $post_type_object
        || ! current_user_can( 'edit_post', $post_id )
        || ! current_user_can( $post_type_object->cap->create_posts )
    ) {
        wp_die( esc_html__( 'You are not allowed to duplicate this post.', 'medical-averitas' ), 403 );
    }

    // Set the new post author
    $new_post_author = wp_get_current_user()->ID;

    // Duplicate the post
    $args = [
        'comment_status' => $post->comment_status,
        'ping_status'    => $post->ping_status,
        'post_author'    => $new_post_author,
        'post_content'   => wp_slash($post->post_content), // Add wp_slash here
        'post_excerpt'   => wp_slash($post->post_excerpt), // Add wp_slash here
        'post_name'      => sanitize_text_field( $post->post_name ),
        'post_parent'    => $post->post_parent,
        'post_password'  => $post->post_password,
        'post_status'    => 'draft',
        'post_title'     => sanitize_text_field( $post->post_title ),
        'post_type'      => $post->post_type,
        'to_ping'        => $post->to_ping,
        'menu_order'     => $post->menu_order,
    ];
    $new_post_id = wp_insert_post( $args, true );

    if ( is_wp_error( $new_post_id ) || ! $new_post_id ) {
        wp_die( esc_html__( 'Post creation failed. Could not duplicate the original post.', 'medical-averitas' ) );
    }

    // Copy taxonomies
    $taxonomies = get_object_taxonomies( $post->post_type );
    foreach ( $taxonomies as $taxonomy ) {
        $post_terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'slugs' ] );
        wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
    }

    // Copy metadata
    $post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id ) );
    foreach ( $post_meta_infos as $meta_info ) {
        if ( '_wp_old_slug' !== $meta_info->meta_key ) {
            add_post_meta( $new_post_id, $meta_info->meta_key, maybe_unserialize( $meta_info->meta_value ) );
        }
    }

    // Redirect to the edit screen for the duplicated post
    wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
    exit;
}
add_action( 'admin_action_duplicate_post_as_draft', __NAMESPACE__ . '\\duplicate_post_as_draft' );

/**
 * Add a Duplicate link to post and page action rows.
 */
function add_duplicate_post_link( $actions, $post ) {
    $post_type_object = get_post_type_object( $post->post_type );

    if (
        $post_type_object
        && current_user_can( 'edit_post', $post->ID )
        && current_user_can( $post_type_object->cap->create_posts )
    ) {
        $url = wp_nonce_url(
            admin_url( 'admin.php?action=duplicate_post_as_draft&post=' . $post->ID ),
            'duplicate_post_nonce',
            'duplicate_nonce'
        );

        $actions['duplicate'] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( 'Duplicate this post', 'medical-averitas' ) . '">' . esc_html__( 'Duplicate', 'medical-averitas' ) . '</a>';
    }

    return $actions;
}
add_filter( 'post_row_actions', __NAMESPACE__ . '\\add_duplicate_post_link', 10, 2 );
add_filter( 'page_row_actions', __NAMESPACE__ . '\\add_duplicate_post_link', 10, 2 );