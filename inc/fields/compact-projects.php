<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {

    $heading_fields = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_size', [])
        ->modifyField('header_line_height', [])
        ->modifyField('header_color', []);

    $text_fields = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', [])
        ->modifyField('text_line_height', [])
        ->modifyField('text_color', []);

    $compact_projects = new FieldsBuilder('compact_projects_fields');

    $compact_projects
        ->setLocation('block', '==', 'acf/compact-projects')

        ->addTab('Content')
            ->addButtonGroup('compact_projects_layout', [
                'label' => 'Number of cards',
                'choices' => ['two' => '2 cards', 'three' => '3 cards'],
                'default_value' => 'three',
                'instructions' => 'Two cards stay expanded. Three cards expand when clicked.',
            ])
            ->addText('compact_projects_label', [
                'label' => 'Eyebrow label',
            ])
            ->addTextarea('compact_projects_title', [
                'label' => 'Section heading',
                'rows'  => 2,
            ])
            ->addTextarea('compact_projects_description', [
                'label' => 'Intro description',
                'rows'  => 3,
            ])

        ->addTab('Projects')
            ->addRepeater('compact_projects_items', [
                'label'        => 'Projects',
                'instructions' => 'Add up to three projects. The first opens initially; clicking another card expands that project.',
                'min'          => 1,
                'max'          => 3,
                'layout'       => 'block',
                'button_label' => 'Add project',
            ])
                ->addText('title', [
                    'label'    => 'Project name',
                    'required' => true,
                ])
                ->addText('category', [
                    'label' => 'Category pill',
                ])
                ->addTextarea('description', [
                    'label' => 'Expanded project description',
                    'rows'  => 3,
                ])
                ->addText('short_description', [
                    'label'        => 'Short caption (side projects)',
                    'instructions' => 'Keep this brief; it appears below the vertical project title.',
                    'wrapper'      => ['width' => '50'],
                ])
                ->addRepeater('tags', [
                    'label'        => 'Feature tags',
                    'instructions' => 'Add up to three tags shown below the project description.',
                    'min'          => 0,
                    'max'          => 3,
                    'layout'       => 'table',
                    'button_label' => 'Add tag',
                ])
                    ->addText('tag', [
                        'label'    => 'Tag',
                        'required' => true,
                    ])
                ->endRepeater()
                ->addImage('image', [
                    'label'         => 'Project image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ])
                ->addSelect('tone', [
                    'label'   => 'Side card tint',
                    'choices' => [
                        'purple' => 'Purple',
                        'green'  => 'Green',
                        'blue'   => 'Blue',
                    ],
                    'ui' => 1,
                ])
                ->addLink('link', [
                    'label' => 'Project link',
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading_fields)

        ->addTab('Text Settings')
            ->addFields($text_fields)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($compact_projects) {
        acf_add_local_field_group($compact_projects->build());
    });
}
