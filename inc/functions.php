<?php

/**
 * Functions and definitions for Rapture Surfcamps Theme
 *
 * @package MCA
 */

namespace AngleVjosa;

/**
 * define constant
 */
define('THEME_DIR', get_template_directory());
define('THEME_URL', get_template_directory_uri());
define('THEME_VERSION', wp_get_theme()->get( 'Version' ));

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Enqueue assets.
require_once get_template_directory() . '/inc/assets.php';

// Remove comments.
require_once get_template_directory() . '/inc/comments.php';

// Theme setup.
require_once get_template_directory() . '/inc/setup.php';

// Theme media support.
require_once get_template_directory() . '/inc/media.php';

// Theme actions.
require_once get_template_directory() . '/inc/actions.php';

// Theme filters.
require_once get_template_directory() . '/inc/filters.php';

// Theme menus.
require_once get_template_directory() . '/inc/menus.php';

// Theme post types.
require_once get_template_directory() . '/inc/post-types.php';

// Theme taxonomies.
require_once get_template_directory() . '/inc/taxonomies.php';

// Theme helpers.
require_once get_template_directory() . '/inc/helpers.php'; 

// ACF setup.
require_once get_template_directory() . '/inc/acf.php'; 

// Theme options (ACF settings).
require_once get_template_directory() . '/inc/options.php';

// Theme admin functions.
require_once get_template_directory() . '/inc/admin/admin-functions.php';

// Load admin styles and scripts.
require_once get_template_directory() . '/inc/admin/admin-styles.php';

// Load Composer autoloader for dependencies
require_once get_template_directory() . '/vendor/autoload.php';

// Load ACF Builder
require_once get_template_directory() . '/inc/libs/acf-builder-master/autoload.php';

// Include all field definitions dynamically.
$fields_dir = get_template_directory() . '/inc/fields/';
$field_files = glob( $fields_dir . '*.php' );

if ( $field_files ) {
    foreach ( $field_files as $file ) {
        require_once $file;
    }
}

// Include all block registrations dynamically.
$blocks_dir = get_template_directory() . '/template-parts/blocks/';
$block_folders = glob( $blocks_dir . '*', GLOB_ONLYDIR );

if ( $block_folders ) {
    foreach ( $block_folders as $folder ) {
        $block_file = $folder . '/' . basename( $folder ) . '-block.php';
        if ( file_exists( $block_file ) ) {
            require_once $block_file;
        }
    }
}

?>
