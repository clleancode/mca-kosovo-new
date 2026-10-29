<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'single-content',
        'title' => __('MCA - Single Content', 'mca'),
        'description' => __('Build the full article in this block, with share links, an automatic table of contents and a related project.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/single-content/single-content.php',
        'category' => 'formatting',
        'icon' => 'editor-alignleft',
        'keywords' => ['single content', 'news', 'article'],
        'mode' => 'preview',
        'post_types' => ['post'],
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
