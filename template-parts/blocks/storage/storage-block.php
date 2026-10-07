<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'storage',
        'title' => __('Storage', 'MCA'),
        'description' => __('Energy storage capacity and locations across Kosovo.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/storage/storage.php',
        'category' => 'formatting',
        'icon' => 'chart-area',
        'keywords' => ['energy', 'storage', 'battery', 'Kosovo'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
