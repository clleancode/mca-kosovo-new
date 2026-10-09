<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'procurement-resources', 'title' => __('MCA - Procurement Resources', 'MCA'),
        'description' => __('Procurement resource cards and newsletter signup.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/procurement-resources/procurement-resources.php',
        'category' => 'formatting', 'icon' => 'media-document', 'keywords' => ['procurement', 'resources', 'newsletter'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
