<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_size', ['default_value' => 'h5'])
        ->modifyField('header_color', ['default_value' => 'h-dark-blue']);

    $grid = new FieldsBuilder('news_grid_fields');

    $grid
        ->setLocation('block', '==', 'acf/news-grid')

        ->addTab('Content')
        ->addText('news_grid_title', [
            'label'         => 'Section title',
            'default_value' => 'Latest news',
        ])
        ->addNumber('news_grid_posts_per_page', [
            'label'         => 'News cards per page',
            'instructions'  => 'The newsletter card is added after the news cards.',
            'default_value' => 5,
            'min'           => 1,
            'max'           => 12,
        ])

        ->addTab('Newsletter')
        ->addText('news_grid_newsletter_label', [
            'label'         => 'Badge label',
            'default_value' => 'Newsletter',
        ])
        ->addText('news_grid_newsletter_title', [
            'label'         => 'Title',
            'default_value' => 'Never miss a Compact update',
        ])
        ->addTextarea('news_grid_newsletter_description', [
            'label'         => 'Description',
            'default_value' => 'News, procurement notices and opportunities — straight to your inbox.',
            'rows'          => 3,
        ])
        ->addText('news_grid_newsletter_placeholder', [
            'label'         => 'Email placeholder',
            'default_value' => 'Email address',
        ])
        ->addText('news_grid_newsletter_shortcode', [
            'label'        => 'Subscription form shortcode',
            'instructions' => 'Add a newsletter service form shortcode to handle subscriptions. Leave empty to show the visual fallback form.',
        ])
        ->addLink('news_grid_newsletter_link', [
            'label'        => 'Fallback form destination',
            'instructions' => 'Used as the fallback form destination when no shortcode is set.',
        ])

        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($grid) {
        acf_add_local_field_group($grid->build());
    });
}
