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

    $activities = new FieldsBuilder('activities_fields');

    $activities
        ->setLocation('block', '==', 'acf/activities')
        ->addTab('Content')
        ->addButtonGroup('activities_layout', [
            'label'        => 'Number of cards',
            'choices'      => [
                'two'   => '2 cards',
                'three' => '3 cards',
            ],
            'instructions' => 'The two-card layout displays the first two cards. Switching layouts keeps all saved cards.',
        ])
        ->addText('activities_label', [
            'label' => 'Badge label',
        ])
        ->addTextarea('activities_title', [
            'label' => 'Section title',
            'rows'  => 2,
        ])
        ->addTextarea('activities_description', [
            'label' => 'Section description',
            'rows'  => 3,
        ])
        ->addButtonGroup('activities_arrows', [
            'label' => 'Arrows between cards',
            'choices' => ['show' => 'Show arrows', 'hide' => 'Hide arrows'],
            'default_value' => 'hide',
        ])
        ->addTab('Activity cards')
        ->addRepeater('activities_items', [
            'label'        => 'Activities',
            'min'          => 2,
            'max'          => 3,
            'layout'       => 'block',
            'button_label' => 'Add activity',
        ])
            ->addImage('image', [
                'label'         => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('label', [
                'label' => 'Image pill',
            ])
            ->addSelect('tone', [
                'label'   => 'Pill color',
                'choices' => [
                    'blue'   => 'Blue',
                    'green'  => 'Green',
                    'red'    => 'Red',
                    'purple' => 'Purple',
                    'white'  => 'White',
                ],
            ])
            ->addText('value', [
                'label'        => 'Main value or heading',
                'instructions' => 'For example: 90, 250, Regulation, Skills or Inclusion.',
                'required'     => 1,
            ])
            ->addText('unit', [
                'label'        => 'Unit (optional)',
                'instructions' => 'For example: MWh.',
            ])
            ->addText('title', [
                'label'    => 'Activity title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addRepeater('tags', [
                'label'        => 'Tags',
                'max'          => 3,
                'layout'       => 'table',
                'button_label' => 'Add tag',
            ])
                ->addText('tag', [
                    'label'    => 'Tag',
                    'required' => 1,
                ])
            ->endRepeater()
        ->endRepeater()
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($activities) {
        acf_add_local_field_group($activities->build());
    });
}
