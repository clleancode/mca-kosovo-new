<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'benefit-banner', 'title' => __('MCA - Benefit Banner', 'mca'),
        'category' => 'formatting', 'icon' => 'cover-image', 'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/benefit-banner/benefit-banner.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
