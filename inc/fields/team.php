<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $team = new FieldsBuilder('team_fields');

    $team
        ->setLocation('block', '==', 'acf/team')

        ->addTab('Content')
        ->addButtonGroup('team_layout', [
            'label'   => 'Layout',
            'choices' => [
                'board'  => 'Board with tabs',
                'people' => 'Meet our people',
            ],
        ])
        ->addText('team_section_id', [
            'label'        => 'Section ID',
            'instructions' => 'Without #, for example our-people.',
        ])
        ->addText('team_label', [
            'label' => 'Badge label',
        ])
        ->addText('team_title', [
            'label' => 'Section title',
        ])
        ->addText('team_alternate_label', [
            'label' => 'Show all section label',
        ])

        ->addTab('Groups and members')
        ->addRepeater('team_groups', [
            'label'        => 'Member groups',
            'layout'       => 'block',
            'button_label' => 'Add group',
        ])
            ->addSelect('tab', [
                'label'        => 'Board tab',
                'choices'      => [
                    'board'      => 'Board of Directors',
                    'management' => 'Senior management',
                    'mcc'        => 'MCC Kosovo',
                ],
                'instructions' => 'Used only in the Board layout.',
            ])
            ->addText('title', [
                'label'        => 'Group heading (optional)',
                'instructions' => 'For example Government Board Members, Non-Governmental Board Members, or Meet the team.',
            ])
            ->addButtonGroup('columns', [
                'label'   => 'Members per row',
                'choices' => [
                    'default' => 'Default layout',
                    'three'   => '3 members',
                    'six'     => '6 members',
                ],
            ])
            ->addButtonGroup('show_filters', [
                'label'   => 'Department filters',
                'choices' => [
                    'hide' => 'Hide filters',
                    'show' => 'Show filters',
                ],
            ])
            ->addTrueFalse('alternate', [
                'label' => 'Show only after clicking Show all',
                'ui'    => 1,
            ])
            ->addRepeater('members', [
                'label'        => 'Members',
                'layout'       => 'block',
                'button_label' => 'Add member',
            ])
                ->addImage('image', [
                    'label'         => 'Photo',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ])
                ->addText('label', [
                    'label'        => 'Role badge or category',
                    'instructions' => 'For example Board Member, Chair of the Board, Observer, or Senior Management.',
                ])
                ->addText('name', [
                    'label'    => 'Name',
                    'required' => 1,
                ])
                ->addText('role', [
                    'label' => 'Position or institution',
                ])
                ->addText('department', [
                    'label'        => 'Department',
                    'instructions' => 'For example Program, Legal or Communications. Used by the filters for this group.',
                ])
                ->addLink('link', [
                    'label' => 'Profile link (optional)',
                ])
            ->endRepeater()
        ->endRepeater()

        ->addTab('Organisation structure')
        ->addText('team_structure_title', [
            'label' => 'Title',
        ])
        ->addText('team_structure_description', [
            'label' => 'Description',
        ])
        ->addFile('team_structure_pdf', [
            'label'         => 'Organisation PDF',
            'return_format' => 'array',
            'mime_types'    => 'pdf',
            'instructions'  => 'The banner appears only when a PDF is selected.',
        ])
        ->addText('team_structure_button_label', [
            'label' => 'Button label',
        ])

        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($team) {
        acf_add_local_field_group($team->build());
    });
}