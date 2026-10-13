<?php

namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'job-role',
        'title'           => __('MCA - Job Role', 'MCA'),
        'category'        => 'formatting',
        'icon'            => 'businessperson',
        'mode'            => 'preview',
        'render_template' => get_template_directory() . '/template-parts/blocks/job-role/job-role.php',
        'supports'        => [
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});