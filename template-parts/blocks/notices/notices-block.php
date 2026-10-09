<?php
namespace MCA\Blocks;
require_once get_template_directory() . '/inc/procurement-notices.php';
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'notices', 'title' => __('MCA - Latest Notices', 'MCA'),
        'description' => __('Filterable procurement notices with pagination.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/notices/notices.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['notices', 'procurement', 'filter'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
