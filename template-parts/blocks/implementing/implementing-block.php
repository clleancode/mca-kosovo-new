<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'implementing', 'title' => __('MCA - Implementing', 'MCA'),
        'description' => __('Implementation panels or media resource cards.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/implementing/implementing.php',
        'category' => 'formatting', 'icon' => 'screenoptions', 'keywords' => ['implementing', 'media', 'resources'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
