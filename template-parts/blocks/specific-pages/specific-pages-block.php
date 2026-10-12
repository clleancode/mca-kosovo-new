<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'specific-pages',
        'title' => __('MCA - Specific Pages', 'MCA'),
        'description' => __('Displays manually configured image cards.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/specific-pages/specific-pages.php',
        'category' => 'formatting',
        'icon' => 'screenoptions',
        'keywords' => ['specific pages', 'pages', 'links'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
