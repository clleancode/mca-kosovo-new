<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'join', 'title' => __('MCA - Join', 'MCA'),
        'description' => __('Image banner with contact and subscription links.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/join/join.php',
        'category' => 'formatting', 'icon' => 'megaphone', 'keywords' => ['join', 'contact', 'subscribe'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
