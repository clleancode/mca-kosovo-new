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

    $implementing = new FieldsBuilder('implementing_fields');

    $implementing
        ->setLocation('block', '==', 'acf/implementing')

        ->addTab('Content')
        ->addButtonGroup('implementing_layout', [
            'label'   => 'Layout',
            'choices' => [
                'implementation' => 'Implementation - 2 cards',
                'media'          => 'Media resources - 4 cards',
            ],
        ])
        ->addText('implementing_section_id', [
            'label'        => 'Section ID',
            'instructions' => 'Without #, for example media-resources.',
        ])
        ->addText('implementing_title', [
            'label' => 'Section title (optional)',
        ])
        ->addRepeater('implementing_items', [
            'label'        => 'Cards',
            'min'          => 2,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add card',
            'instructions' => 'Implementation displays the first two cards. Media displays: photo gallery, publications, logos, then press contact.',
        ])
            ->addSelect('tone', [
                'label'   => 'Card background',
                'choices' => [
                    'white'  => 'White',
                    'blue'   => 'Dark blue',
                    'purple' => 'Purple',
                ],
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
                'label' => 'Button or link (optional)',
            ])
        ->endRepeater()

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($implementing) {
        acf_add_local_field_group($implementing->build());
    });
}