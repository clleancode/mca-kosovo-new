<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) return;

    acf_register_block_type([
        'name' => 'contact-form',
        'title' => __('MCA - Contact Form', 'MCA'),
        'description' => __('Contact form and contact information panels.', 'MCA'),
        'render_template' => get_template_directory() . '/template-parts/blocks/contact-form/contact-form.php',
        'category' => 'formatting',
        'icon' => 'email-alt',
        'keywords' => ['contact', 'form', 'email'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
