<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'opportunities', 'title' => __('MCA - Opportunities', 'MCA'),
        'description' => __('Opportunity cards and latest procurements.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/opportunities/opportunities.php',
        'category' => 'formatting', 'icon' => 'megaphone', 'keywords' => ['opportunities', 'procurement', 'careers'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
