<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'about-procurement', 'title' => __('MCA - About Procurement', 'MCA'),
        'description' => __('Procurement facts, documents, application steps and contact.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/about-procurement/about-procurement.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['procurement', 'documents'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
