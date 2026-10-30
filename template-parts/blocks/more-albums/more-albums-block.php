<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'more-albums',
        'title' => __('MCA - More Albums', 'mca'),
        'category' => 'formatting',
        'icon' => 'format-gallery',
        'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/more-albums/more-albums.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
