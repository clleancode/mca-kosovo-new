<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'overview', 'title' => __('MCA - Overview', 'MCA'),
        'description' => __('Project navigation, overview and key benefits.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/overview/overview.php',
        'category' => 'formatting', 'icon' => 'info', 'keywords' => ['overview', 'project', 'benefits'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
