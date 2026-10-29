<?php
namespace MCA\Blocks;

add_action('acf/init', function () {
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'newsletter-subscribe',
        'title' => __('MCA - Newsletter Subscribe', 'mca'),
        'description' => __('Newsletter signup section with an editorial preview card.', 'mca'),
        'render_template' => get_template_directory() . '/template-parts/blocks/newsletter-subscribe/newsletter-subscribe.php',
        'category' => 'formatting',
        'icon' => 'email-alt',
        'keywords' => ['newsletter', 'subscribe', 'email'],
        'mode' => 'preview',
        'supports' => ['align' => true, 'anchor' => true, 'customClassName' => true],
    ]);
});
