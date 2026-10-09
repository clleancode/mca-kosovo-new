<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'transparency', 'title' => __('MCA - Transparency', 'MCA'),
        'description' => __('Governance and legal document collections.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/transparency/transparency.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['transparency', 'governance', 'documents'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
