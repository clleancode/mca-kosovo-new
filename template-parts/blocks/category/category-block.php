<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'category', 'title' => __('MCA - Categories', 'MCA'),
        'description' => __('Browse publications by document category.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/category/category.php',
        'category' => 'formatting', 'icon' => 'category', 'keywords' => ['category', 'resources', 'publications'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
