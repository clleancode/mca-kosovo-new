<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'gallery-slider', 'title' => __('MCA - Gallery Slider', 'MCA'),
        'description' => __('A draggable gallery of Compact images.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/gallery-slider/gallery-slider.php',
        'category' => 'formatting', 'icon' => 'format-gallery', 'keywords' => ['gallery', 'slider', 'photos'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
