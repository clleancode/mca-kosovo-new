<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'spotlight', 'title' => __('MCA - Spotlight', 'MCA'),
        'description' => __('Purple or green spotlight banner with image and links.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/spotlight/spotlight.php',
        'category' => 'formatting', 'icon' => 'star-filled', 'keywords' => ['spotlight', 'announcement', 'questionnaire'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
