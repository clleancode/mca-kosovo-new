<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $transparency = new FieldsBuilder('transparency_fields');

    $transparency
        ->setLocation('block', '==', 'acf/transparency')

        ->addTab('Content')
            ->addText('transparency_section_id', [
                'label'        => 'Section ID',
                'instructions' => 'Without #, for example governance.',
            ])
            ->addText('transparency_label', [
                'label' => 'Badge label',
            ])
            ->addText('transparency_title', [
                'label' => 'Section title',
            ])

        ->addTab('Document collections')
            ->addRepeater('transparency_collections', [
                'label'        => 'Collections',
                'min'          => 3,
                'max'          => 3,
                'layout'       => 'block',
                'button_label' => 'Add collection',
            ])
                ->addText('title', [
                    'label'    => 'Collection title',
                    'required' => 1,
                ])
                ->addNumber('visible_count', [
                    'label'        => 'Documents displayed',
                    'min'          => 1,
                    'max'          => 20,
                    'instructions' => 'The badge counts all documents in this collection.',
                ])
                ->addRepeater('documents', [
                    'label'        => 'Documents',
                    'layout'       => 'block',
                    'button_label' => 'Add document',
                ])
                    ->addText('title', [
                        'label'    => 'Document title',
                        'required' => 1,
                    ])
                    ->addText('caption', [
                        'label'        => 'Date or caption',
                        'instructions' => 'For example 09 Jul 2026. PDF is appended automatically.',
                    ])
                    ->addFile('file', [
                        'label'         => 'PDF file',
                        'return_format' => 'array',
                        'mime_types'    => 'pdf',
                        'required'      => 1,
                    ])
                ->endRepeater()
                ->addLink('view_all', [
                    'label'        => 'View all link',
                    'instructions' => 'Link to the full collection page.',
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($transparency) {
        acf_add_local_field_group($transparency->build());
    });
}