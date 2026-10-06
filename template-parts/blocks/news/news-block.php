<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'news', 'title' => __('MCA - News', 'MCA'),
        'description' => __('Latest WordPress news with category filters.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/news/news.php',
        'category' => 'formatting', 'icon' => 'admin-post', 'keywords' => ['news', 'newsroom'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
