<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'mission', 'title' => __('MCA - Mission', 'MCA'),
        'description' => __('Mission cards or compact project news links.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/mission/mission.php',
        'category' => 'formatting', 'icon' => 'screenoptions', 'keywords' => ['mission', 'projects', 'news'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
