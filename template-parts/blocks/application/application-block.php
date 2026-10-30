<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'application', 'title' => __('MCA - Job Application', 'mca'),
        'description' => __('Job application form layout with team photos and HR contact.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/application/application.php',
        'category' => 'formatting', 'icon' => 'feedback', 'keywords' => ['application', 'jobs', 'form'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});