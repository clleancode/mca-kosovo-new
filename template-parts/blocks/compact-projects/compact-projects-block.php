<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'compact-projects',
        'title' => __('MCA - Compact Projects', 'MCA'),
        'description' => __('Showcase the three projects included in the MCC-Kosovo Compact.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/compact-projects/compact-projects.php',
        'category' => 'formatting',
        'icon' => 'screenoptions',
        'keywords' => ['compact', 'projects', 'energy'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
