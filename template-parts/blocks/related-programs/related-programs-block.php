<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'related-programs',
        'title' => __('MCA - Related Programs', 'MCA'),
        'description' => __('Displays the three latest programs.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/related-programs/related-programs.php',
        'category' => 'formatting',
        'icon' => 'welcome-learn-more',
        'keywords' => ['programs', 'related programs', 'projects'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
