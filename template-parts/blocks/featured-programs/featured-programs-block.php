<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'featured-programs', 'title' => __('MCA - Featured Programs', 'MCA'),
        'description' => __('Featured story and four program cards.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/featured-programs/featured-programs.php',
        'category' => 'formatting', 'icon' => 'screenoptions', 'keywords' => ['programs', 'women', 'workforce'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
