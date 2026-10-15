<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'single-hero',
        'title' => __('MCA - Single Hero', 'MCA'),
        'description' => __('News article hero with metadata, sharing and featured image.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/single-hero/single-hero.php',
        'category' => 'formatting',
        'icon' => 'format-image',
        'keywords' => ['single hero', 'news', 'article'],
        'mode' => 'preview',
        'post_types' => ['post'],
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
