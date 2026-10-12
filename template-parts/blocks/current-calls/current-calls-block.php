<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'current-calls',
        'title' => __('MCA - Current Calls', 'MCA'),
        'description' => __('Current procurement calls with status filters.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/current-calls/current-calls.php',
        'category' => 'formatting',
        'icon' => 'megaphone',
        'keywords' => ['current calls', 'procurement', 'calls'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
