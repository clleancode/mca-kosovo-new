<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'small-hero', 'title' => __('MCA - Small Hero', 'MCA'),
        'description' => __('Page hero with three useful links.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/small-hero/small-hero.php',
        'category' => 'formatting', 'icon' => 'cover-image', 'keywords' => ['hero', 'procurement', 'page'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
