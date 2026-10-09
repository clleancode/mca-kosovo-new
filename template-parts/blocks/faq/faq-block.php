<?php
namespace MCA\Blocks;
add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;
    acf_register_block_type([
        'name' => 'faq', 'title' => __('MCA - FAQ', 'MCA'),
        'description' => __('Frequently asked questions with an accessible accordion.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/faq/faq.php',
        'category' => 'formatting', 'icon' => 'editor-help', 'keywords' => ['faq', 'questions', 'accordion'],
        'mode' => 'preview', 'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
