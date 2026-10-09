<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name'            => 'gallery-filter',
        'title'           => __('MCA - Gallery Filter', 'MCA'),
        'description'     => __('Featured media story with category and date.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/gallery-filter/gallery-filter.php',
        'category'        => 'formatting',
        'icon'            => 'format-gallery',
        'keywords'        => ['gallery', 'media', 'featured', 'filter'],
        'mode'            => 'preview',
        'supports'        => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
