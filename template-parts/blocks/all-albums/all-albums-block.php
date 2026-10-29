<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'albums',
        'title'           => __('MCA - Albums', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/all-albums/all-albums.php',
        'category'        => 'formatting',
        'icon'            => 'format-gallery',
        'mode'            => 'preview',
        'supports'        => [
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});