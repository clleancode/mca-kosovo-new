<?php
/**
 * Disable comments and trackbacks.
 *
 * @package MCA
 */

namespace MCA\Comments;

/**
 * Remove comments menu from the admin sidebar.
 */
function remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_menus' );

/**
 * Disable comments and trackbacks for all post types.
 */
function remove_comment_support() {
    $post_types = get_post_types();

    foreach ( $post_types as $post_type ) {
        remove_post_type_support( $post_type, 'comments' );
        remove_post_type_support( $post_type, 'trackbacks' );
    }
}
add_action( 'init', __NAMESPACE__ . '\\remove_comment_support', 100 );

/**
 * Remove comments from the admin bar.
 */
function remove_comments_from_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', __NAMESPACE__ . '\\remove_comments_from_admin_bar' );