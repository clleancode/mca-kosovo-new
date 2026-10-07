<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'progress', 'title' => __('MCA - Progress', 'MCA'),
        'description' => __('Project phase and milestone progress.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/progress/progress.php',
        'category' => 'formatting', 'icon' => 'chart-bar', 'keywords' => ['progress', 'milestones', 'project'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
