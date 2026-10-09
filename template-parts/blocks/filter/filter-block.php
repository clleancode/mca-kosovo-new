<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'filter', 'title' => __('MCA - Procurement Filter', 'MCA'),
        'description' => __('Search and filter controls for procurement notices.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/filter/filter.php',
        'category' => 'formatting', 'icon' => 'filter', 'keywords' => ['filter', 'procurement', 'search'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
