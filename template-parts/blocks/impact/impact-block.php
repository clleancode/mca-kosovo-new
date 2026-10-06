<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'impact',
        'title' => __('MCA - Impact', 'MCA'),
        'description' => __('Four ways the Compact benefits Kosovo.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/impact/impact.php',
        'category' => 'formatting',
        'icon' => 'chart-bar',
        'keywords' => ['impact', 'energy', 'Kosovo'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
