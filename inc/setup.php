<?php
/**
 * Theme setup.
 *
 * @package MCA
 */

namespace MCA\Setup;

/**
 * Theme setup function.
 */
function theme_setup() {
    // Translation
	load_theme_textdomain('mca', get_template_directory() . '/languages');

    // Add support for title tag.
    add_theme_support( 'title-tag' );

    // Add support for post thumbnails.
    //add_theme_support( 'post-thumbnails', [ 'post', 'page', 'surfcamp' ] );
    add_theme_support( 'post-thumbnails' );

    // Register custom image sizes.
    add_image_size( 'custom-thumbnail', 800, 600, true );

    // Add support for block styles. - CHECK
    add_theme_support( 'wp-block-styles' );

    // Add support for editor styles (for Gutenberg editor). - CHECK
    add_theme_support( 'editor-styles' );

    // Add support for responsive embeds. - CHECK
    add_theme_support( 'responsive-embeds' );

    // Add support for automatic feed links.
    add_theme_support( 'automatic-feed-links' );

    // Add support for post formats. - CHECK
    add_theme_support('post-formats', array('gallery', 'image', 'quote', 'video', 'audio'));

    // Comment form html5
	add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

    // Register exter_large image - CHECK
	add_image_size( 'exter_large', 1920, 1920 );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\theme_setup' );

/**
 * Load the compiled theme + block styles inside the (iframed) block editor.
 * `enqueue_block_assets` also fires on the front end, where inc/assets.php already enqueues them.
 */
function enqueue_editor_assets() {
    if ( ! is_admin() ) {
        return;
    }

    wp_enqueue_style(
        'mca-editor-style',
        get_theme_file_uri( '/assets/css/style.min.css' ),
        [],
        \MCA\Assets\mca_asset_ver( '/assets/css/style.min.css' )
    );

    wp_enqueue_style(
        'mca-editor-custom',
        get_theme_file_uri( '/assets/css/editor-style.css' ),
        [ 'mca-editor-style' ],
        \MCA\Assets\mca_asset_ver( '/assets/css/editor-style.css' )
    );
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );