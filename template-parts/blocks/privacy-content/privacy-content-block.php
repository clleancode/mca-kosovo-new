<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'privacy-content',
        'title'           => __('MCA - Privacy Content', 'mca'),
        'category'        => 'formatting',
        'icon'            => 'privacy',
        'mode'            => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/privacy-content/privacy-content.php',
        'supports'        => [
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});