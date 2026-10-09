<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name'            => 'news-grid',
        'title'           => __('MCA - News Grid', 'MCA'),
        'description'     => __('Latest WordPress news cards with pagination.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/news-grid/news-grid.php',
        'category'        => 'formatting',
        'icon'            => 'grid-view',
        'keywords'        => ['news', 'grid', 'latest', 'pagination'],
        'mode'            => 'preview',
        'supports'        => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
