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

    $mission = new FieldsBuilder('mission_fields');

    $mission
        ->setLocation('block', '==', 'acf/mission')

        ->addTab('Content')
        ->addButtonGroup('mission_layout', [
            'label'   => 'Layout',
            'choices' => [
                'mission' => 'Mission cards',
                'news'    => 'News by project',
            ],
        ])
        ->addText('mission_section_id', [
            'label'        => 'Section ID',
            'instructions' => 'Without #, for example our-mission.',
        ])
        ->addText('mission_label', [
            'label' => 'Badge label (mission layout)',
        ])
        ->addText('mission_title', [
            'label' => 'Title',
        ])
        ->addTextarea('mission_description', [
            'label' => 'Intro description (optional)',
            'rows'  => 3,
        ])

        ->addTab('Cards')
        ->addRepeater('mission_items', [
            'label'        => 'Project cards',
            'min'          => 3,
            'max'          => 3,
            'layout'       => 'block',
            'button_label' => 'Add project',
        ])
            ->addImage('image', [
                'label'         => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('label', [
                'label' => 'Project label',
            ])
            ->addText('title', [
                'label'    => 'Card title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addSelect('tone', [
                'label'   => 'Gradient color',
                'choices' => [
                    'blue'   => 'Blue',
                    'purple' => 'Purple',
                    'green'  => 'Green',
                ],
            ])
            ->addLink('link', [
                'label' => 'Project link (optional)',
            ])
        ->endRepeater()

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($mission) {
        acf_add_local_field_group($mission->build());
    });
}