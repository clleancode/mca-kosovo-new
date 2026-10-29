<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'expected',
        'title'           => __('MCA - Expected Impact', 'mca'),
        'description'     => __('Expected project impact with a background image and four cards.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/expected/expected.php',
        'category'        => 'formatting',
        'icon'            => 'chart-area',
        'keywords'        => ['expected', 'impact', 'project'],
        'mode'            => 'preview',
        'supports'        => [
            'align'           => true,
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});