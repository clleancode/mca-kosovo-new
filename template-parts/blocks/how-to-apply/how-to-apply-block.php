<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name'            => 'how-to-apply',
        'title'           => __('MCA - How to Apply', 'MCA'),
        'description'     => __('Application steps displayed as numbered cards.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/how-to-apply/how-to-apply.php',
        'category'        => 'formatting',
        'icon'            => 'list-view',
        'keywords'        => ['apply', 'application', 'steps', 'jobs'],
        'mode'            => 'preview',
        'supports'        => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
