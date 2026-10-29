<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'how-to-report',
        'title' => __('MCA - How to Report', 'mca'),
        'description' => __('Hotline reporting options and related anti-fraud resources.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/how-to-report/how-to-report.php',
        'category' => 'formatting',
        'icon' => 'warning',
        'keywords' => ['report', 'hotline', 'fraud', 'corruption'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
