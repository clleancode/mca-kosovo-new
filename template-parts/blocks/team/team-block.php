<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'team', 'title' => __('MCA - Team / Board', 'mca'),
        'description' => __('Grouped board members or staff cards with tabs.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/team/team.php',
        'category' => 'formatting', 'icon' => 'groups', 'keywords' => ['team', 'board', 'people'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
