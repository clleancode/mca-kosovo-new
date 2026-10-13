<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'hero-collection',
        'title' => __('MCA - Hero Collection', 'MCA'),
        'description' => __('One hero block with selectable layouts for albums, vacancies, integrity, policy and job archives.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/hero-collection/hero-collection.php',
        'category' => 'formatting',
        'icon' => 'cover-image',
        'keywords' => ['hero', 'header', 'banner'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
