<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('privacy_content_fields');

    $fields
        ->setLocation('block', '==', 'acf/privacy-content')

        ->addTab('Content')
        ->addText('privacy_contents_title', [
            'label' => 'Contents heading',
        ])
        ->addRepeater('privacy_sections', [
            'label'        => 'Policy sections',
            'layout'       => 'block',
            'button_label' => 'Add section',
        ])
            ->addText('title', [
                'label'    => 'Section title',
                'required' => true,
            ])
            ->addTrueFalse('dashed_content', [
                'label'        => 'Dashed content box',
                'ui'           => 1,
                'instructions' => 'Wraps the Content field in a dashed border. Use Content below for the text inside the box, and Additional content for text below it.',
            ])
            ->addWysiwyg('content', [
                'label'        => 'Content',
                'media_upload' => 0,
                'instructions' => 'Use paragraphs, headings and bulleted lists.',
            ])
            ->addRepeater('details', [
                'label'  => 'Information panel (optional)',
                'layout' => 'table',
            ])
                ->addText('label', [
                    'label' => 'Label',
                ])
                ->addText('value', [
                    'label' => 'Value',
                ])
                ->addLink('link', [
                    'label' => 'Link (optional)',
                ])
            ->endRepeater()
            ->addWysiwyg('additional_content', [
                'label'        => 'Content below information panel (optional)',
                'media_upload' => 0,
            ])
            ->addText('column_one', [
                'label' => 'Table column 1 heading',
            ])
            ->addText('column_two', [
                'label' => 'Table column 2 heading',
            ])
            ->addText('column_three', [
                'label' => 'Table column 3 heading',
            ])
            ->addRepeater('table_rows', [
                'label'  => 'Table rows (optional)',
                'layout' => 'table',
            ])
                ->addText('one', [
                    'label' => 'Column 1',
                ])
                ->addText('two', [
                    'label' => 'Column 2',
                ])
                ->addText('three', [
                    'label' => 'Column 3',
                ])
            ->endRepeater()
        ->endRepeater()

        ->addTab('Contact')
        ->addText('privacy_contact_title', [
            'label' => 'Contact heading',
        ])
        ->addEmail('privacy_contact_email', [
            'label' => 'Contact email',
        ])
        ->addLink('privacy_settings_link', [
            'label' => 'Cookie settings link',
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}
