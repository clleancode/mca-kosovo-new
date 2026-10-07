<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'activities', 'title' => __('MCA - Activities', 'MCA'),
        'description' => __('Two or three project activity cards.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/activities/activities.php',
        'category' => 'formatting', 'icon' => 'screenoptions', 'keywords' => ['activities', 'project', 'cards'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
