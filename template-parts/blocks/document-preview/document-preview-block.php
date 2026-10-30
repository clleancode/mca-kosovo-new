<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'document-preview', 'title' => __('MCA - Document Preview', 'mca'),
        'category' => 'formatting', 'icon' => 'media-document', 'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/document-preview/document-preview.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
