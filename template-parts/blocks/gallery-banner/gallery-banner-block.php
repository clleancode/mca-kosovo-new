<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name'            => 'gallery-banner',
        'title'           => __('MCA - Gallery Banner', 'MCA'),
        'description'     => __('Banner for the media and gallery page.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/gallery-banner/gallery-banner.php',
        'category'        => 'formatting',
        'icon'            => 'cover-image',
        'keywords'        => ['gallery', 'media', 'banner'],
        'mode'            => 'preview',
        'supports'        => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
