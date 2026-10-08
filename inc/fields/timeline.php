<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_size', ['default_value' => 'h4'])
        ->modifyField('header_color', ['default_value' => 'h-white'])
        ->modifyField('header_line_height', ['default_value' => 's']);

    $text = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', ['default_value' => 'm'])
        ->modifyField('text_color', ['default_value' => 'h-white']);

    $timeline = new FieldsBuilder('timeline_fields');

    $timeline
        ->setLocation('block', '==', 'acf/timeline')

        ->addTab('Content')
            ->addText('timeline_label', [
                'label' => 'Badge label',
            ])
            ->addText('timeline_title', [
                'label' => 'Title',
            ])
            ->addTextarea('timeline_description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addText('timeline_active_label', [
                'label' => 'Active milestone pill',
            ])

        ->addTab('Milestones')
            ->addRepeater('timeline_milestones', [
                'label'        => 'Milestones',
                'min'          => 4,
                'max'          => 4,
                'layout'       => 'block',
                'button_label' => 'Add milestone',
                'instructions' => 'Add four milestones in chronological order. Set only one milestone to In progress.',
            ])
                ->addText('year', [
                    'label'    => 'Year',
                    'required' => 1,
                ])
                ->addText('date', [
                    'label' => 'Date label (optional)',
                ])
                ->addText('title', [
                    'label'    => 'Title',
                    'required' => 1,
                ])
                ->addTextarea('description', [
                    'label' => 'Description',
                    'rows'  => 3,
                ])
                ->addButtonGroup('status', [
                    'label'   => 'Status',
                    'choices' => [
                        'complete' => 'Completed',
                        'active'   => 'In progress',
                        'upcoming' => 'Upcoming',
                    ],
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading)

        ->addTab('Text Settings')
            ->addFields($text)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($timeline) {
        acf_add_local_field_group($timeline->build());
    });
}