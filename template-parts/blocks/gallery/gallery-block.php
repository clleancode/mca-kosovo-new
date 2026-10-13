<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'gallery',
        'title' => __('MCA - Gallery', 'MCA'),
        'description' => __('Photo gallery with an album lightbox.', 'MCA'),
        'category' => 'formatting',
        'icon' => 'format-gallery',
        'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/gallery/gallery.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
