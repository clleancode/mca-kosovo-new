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
	load_theme_textdomain('rapture', get_template_directory() . '/languages');

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

    // Add editor stylesheet - now that we've created it, it should always load
    add_editor_style( '/assets/css/editor-style.css' );

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
 * Enqueue editor assets for Gutenberg
 */
function enqueue_editor_assets() {
    // Enqueue the main theme styles for the editor
    wp_enqueue_style( 
        'rapture-editor-style', 
        get_theme_file_uri( '/assets/css/style.css' ), 
        [], 
        wp_get_theme()->get( 'Version' )
    );

    // Enqueue additional editor-specific styles if needed
    wp_enqueue_style( 
        'rapture-editor-custom', 
        get_theme_file_uri( '/assets/css/editor-style.css' ), 
        ['rapture-editor-style'], 
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );