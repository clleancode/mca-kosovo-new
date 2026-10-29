<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'project-updates', 'title' => __('MCA - Project Updates', 'mca'),
        'description' => __('Project procurement updates and downloadable documents.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/project-updates/project-updates.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['updates', 'procurement', 'documents'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
