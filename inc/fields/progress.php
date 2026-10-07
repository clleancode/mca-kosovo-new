<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();
    $text = \MCA\Fields\Reusable\get_text_fields();

    $progress = new FieldsBuilder('progress_fields');

    $progress
        ->setLocation('block', '==', 'acf/progress')
        ->addTab('Content')
        ->addText('progress_label', [
            'label' => 'Badge label',
        ])
        ->addText('progress_title', [
            'label' => 'Title',
        ])
        ->addText('progress_phase_label', [
            'label' => 'Phase label',
        ])
        ->addText('progress_phase', [
            'label' => 'Current phase',
        ])
        ->addRange('progress_percentage', [
            'label'  => 'Progress percentage',
            'min'    => 0,
            'max'    => 100,
            'step'   => 1,
            'append' => '%',
        ])
        ->addText('progress_active_label', [
            'label' => 'Active milestone label',
        ])
        ->addTab('Milestones')
        ->addRepeater('progress_milestones', [
            'label'        => 'Milestones',
            'min'          => 1,
            'max'          => 6,
            'layout'       => 'block',
            'button_label' => 'Add milestone',
        ])
            ->addText('date', [
                'label'        => 'Date label',
                'instructions' => 'For example: Dec 2024, Next or By 2030.',
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
        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($progress) {
        acf_add_local_field_group($progress->build());
    });
}