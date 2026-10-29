<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'careers-hero', 'title' => __('MCA - Careers Hero', 'mca'),
        'description' => __('Careers introduction with a six-photo team collage.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/careers-hero/careers-hero.php',
        'category' => 'formatting', 'icon' => 'groups', 'keywords' => ['careers', 'jobs', 'hero'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
