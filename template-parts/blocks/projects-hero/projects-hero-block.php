<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    $args = [
        'name' => 'projects-hero', 'title' => __('MCA - Projects Hero', 'MCA'),
        'description' => __('Project hero with links and statistics.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/projects-hero/projects-hero.php',
        'category' => 'formatting', 'icon' => 'cover-image', 'keywords' => ['hero', 'project', 'statistics'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ];
    acf_register_block_type($args);

});
