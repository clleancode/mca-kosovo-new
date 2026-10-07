<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'documents', 'title' => __('MCA - Documents', 'MCA'),
        'description' => __('Downloadable project documents.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/documents/documents.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['updates', 'procurement', 'documents'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
