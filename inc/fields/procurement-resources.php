<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();
    $text    = \MCA\Fields\Reusable\get_text_fields();

    $resources = new FieldsBuilder('procurement_resources_fields');

    $resources
        ->setLocation('block', '==', 'acf/procurement-resources')

        ->addTab('Content')
        ->addText('procurement_resources_title', [
            'label' => 'Section title',
        ])
        ->addRepeater('procurement_resources_cards', [
            'label'        => 'Resource cards',
            'min'          => 2,
            'max'          => 2,
            'layout'       => 'block',
            'button_label' => 'Add resource',
        ])
            ->addImage('image', [
                'label'         => 'Background image (optional)',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('title', [
                'label'    => 'Card title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addLink('link', [
                'label' => 'Button link',
            ])
        ->endRepeater()

        ->addTab('Newsletter')
        ->addText('procurement_resources_signup_title', [
            'label' => 'Title',
        ])
        ->addText('procurement_resources_signup_description', [
            'label' => 'Description',
        ])
        ->addText('procurement_resources_shortcode', [
            'label'        => 'Newsletter form shortcode',
            'instructions' => 'Use your connected subscription form for direct email signup.',
        ])
        ->addLink('procurement_resources_signup_link', [
            'label'        => 'Subscription page link',
            'instructions' => 'Without a shortcode, the email is passed to this page to continue signup.',
        ])

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($resources) {
        acf_add_local_field_group($resources->build());
    });
}