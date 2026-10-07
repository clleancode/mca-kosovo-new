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

    $spotlight = new FieldsBuilder('spotlight_fields');

    $spotlight
        ->setLocation('block', '==', 'acf/spotlight')

        ->addTab('Content')
            ->addButtonGroup('spotlight_layout', [
                'label'   => 'Banner style',
                'choices' => [
                    'purple' => 'Purple - image left',
                    'green'  => 'Green - image right',
                ],
            ])
            ->addImage('spotlight_image', [
                'label'         => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('spotlight_label', [
                'label' => 'Pill label',
            ])
            ->addTextarea('spotlight_title', [
                'label' => 'Title',
                'rows'  => 2,
            ])
            ->addTextarea('spotlight_description', [
                'label' => 'Description',
                'rows'  => 4,
            ])
            ->addRepeater('spotlight_tags', [
                'label'        => 'Tags (optional)',
                'max'          => 3,
                'layout'       => 'table',
                'button_label' => 'Add tag',
            ])
                ->addText('tag', [
                    'label'    => 'Tag',
                    'required' => 1,
                ])
            ->endRepeater()
            ->addLink('spotlight_primary_link', [
                'label' => 'Primary button',
            ])
            ->addLink('spotlight_secondary_link', [
                'label' => 'Secondary button (optional)',
            ])

        ->addTab('Heading')
            ->addFields($heading)

        ->addTab('Text Settings')
            ->addFields($text)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($spotlight) {
        acf_add_local_field_group($spotlight->build());
    });
}