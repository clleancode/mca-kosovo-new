<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name'            => 'vacancies',
        'title'           => __('MCA - Vacancies', 'mca'),
        'description'     => __('Open and closed job vacancies.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/vacancies/vacancies.php',
        'category'        => 'formatting',
        'icon'            => 'businessperson',
        'keywords'        => ['vacancies', 'jobs', 'careers'],
        'mode'            => 'preview',
        'supports'        => [
            'align'           => true,
            'anchor'          => true,
            'customClassName' => true,
        ],
    ]);
});