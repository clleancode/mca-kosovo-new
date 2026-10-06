<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'partnership',
        'title' => __('MCA - Partnership', 'MCA'),
        'description' => __('Compact funding and implementing partners.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/partnership/partnership.php',
        'category' => 'formatting', 'icon' => 'groups',
        'keywords' => ['partnership', 'compact', 'funding'], 'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
