<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'publications-banner', 'title' => __('MCA - Publications Banner', 'MCA'),
        'category' => 'formatting', 'icon' => 'book', 'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/publications-banner/publications-banner.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
