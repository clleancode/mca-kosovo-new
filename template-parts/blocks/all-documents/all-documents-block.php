<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'all-documents', 'title' => __('MCA - All Documents', 'MCA'),
        'description' => __('Filterable publication documents with downloads and load more.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/all-documents/all-documents.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['documents', 'resources', 'downloads'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
