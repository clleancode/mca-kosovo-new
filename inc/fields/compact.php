<?php
/**
 * Compact Block Fields
 */

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists('StoutLogic\AcfBuilder\FieldsBuilder')) {
    $spacingFields = \MCA\Fields\Reusable\get_spacing_fields();
    $deviceFields  = \MCA\Fields\Reusable\get_device_fields();
    $headingFields = \MCA\Fields\Reusable\get_heading_fields();
    $textFields    = \MCA\Fields\Reusable\get_text_fields();

    $compactFields = new FieldsBuilder('compact_fields');

    $compactFields
        ->setLocation('block', '==', 'acf/compact')

        ->addTab('Content')
            ->addText('compact_label')
            ->addTextarea('compact_title', [
                'rows' => 2,
            ])
            ->addTextarea('compact_description', [
                'rows' => 4,
            ])
            ->addLink('compact_link', [
                'label' => 'How the Compact works link',
            ])

        ->addTab('Investment')
            ->addImage('compact_image', [
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('compact_image_label')
            ->addRepeater('compact_statistics', [
                'label'        => 'Statistics',
                'min'          => 1,
                'max'          => 3,
                'layout'       => 'block',
                'button_label' => 'Add statistic',
            ])
                ->addText('value', [
                    'label'        => 'Value',
                    'instructions' => 'For example: $202, $34.7 or 1.78.',
                    'required'     => true,
                ])
                ->addText('unit', [
                    'label' => 'Unit',
                ])
                ->addTextarea('description', [
                    'rows' => 2,
                ])
                ->addColorPicker('accent')
            ->endRepeater()
            ->addText('compact_total', [
                'label' => 'Total investment',
            ])
            ->addNumber('compact_us_share', [
                'label'        => 'U.S. share (%)',
                'min'          => 0,
                'max'          => 100,
                'instructions' => 'Kosovo share is the remainder.',
            ])
            ->addText('compact_us_amount', [
                'label' => 'United States contribution',
            ])
            ->addText('compact_kosovo_amount', [
                'label' => 'Kosovo contribution',
            ])
            ->addText('compact_cell_note', [
                'label' => 'Funding cells caption',
            ])
            ->addText('compact_funding_note', [
                'label' => 'Funding source caption',
            ])

        ->addTab('Countdown')
            ->addImage('compact_countdown_image', [
                'label'         => 'Countdown background (optional)',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('compact_countdown_title')
            ->addDateTimePicker('compact_end_date', [
                'label'          => 'Compact completion date',
                'return_format'  => 'Y-m-d H:i:s',
                'display_format' => 'd/m/Y H:i',
                'instructions'   => 'Uses the WordPress site timezone. Leave empty to hide the countdown.',
            ])

        ->addFields($spacingFields)

        ->addTab('Heading', [
            'instructions' => 'Settings for the main Compact heading.',
        ])
            ->addFields($headingFields)

        ->addTab('Text Settings', [
            'instructions' => 'Settings for the introductory description.',
        ])
            ->addFields($textFields)

        
            ->addFields($deviceFields);

    add_action('acf/init', function () use ($compactFields) {
        acf_add_local_field_group($compactFields->build());
    });
}