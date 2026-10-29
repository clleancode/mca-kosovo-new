<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'our-office',
        'title' => __('MCA - Our Office', 'mca'),
        'description' => __('Office information with a location map.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/our-office/our-office.php',
        'category' => 'formatting',
        'icon' => 'location-alt',
        'keywords' => ['office', 'address', 'map', 'contact'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
