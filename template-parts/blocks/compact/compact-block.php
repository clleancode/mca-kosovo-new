<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'compact',
        'title' => __('MCA - Compact', 'MCA'),
        'description' => __('Compact investment, milestones and countdown.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/compact/compact.php',
        'category' => 'formatting',
        'icon' => 'chart-bar',
        'keywords' => ['compact', 'investment', 'timeline'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
