<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'benefits', 'title' => __('MCA - Benefits', 'MCA'),
        'category' => 'formatting', 'icon' => 'grid-view', 'mode' => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/benefits/benefits.php',
        'supports' => ['anchor' => true, 'customClassName' => true],
    ]);
});
