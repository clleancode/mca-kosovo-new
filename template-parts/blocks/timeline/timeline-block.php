<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'timeline',
        'title'           => __('MCA - Compact Timeline', 'mca'),
        'description'     => __('Custom project milestones with dates and progress statuses.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/timeline/timeline.php',
        'category'        => 'formatting',
        'icon'            => 'clock',
        'keywords'        => ['timeline', 'compact', 'milestones'],
        'mode'            => 'preview',
        'supports'        => [
            'align'           => true,
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});
