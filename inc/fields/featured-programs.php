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
        ->modifyField('header_color', ['default_value' => 'h-dark-blue'])
        ->modifyField('header_line_height', ['default_value' => 's']);

    $text = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', ['default_value' => 'm'])
        ->modifyField('text_color', ['default_value' => 'h-dark-blue']);

    $programs = new FieldsBuilder('featured_programs_fields');

    $programs
        ->setLocation('block', '==', 'acf/featured-programs')

        ->addTab('Content')
            ->addText('featured_programs_label', [
                'label' => 'Badge label',
            ])
            ->addTextarea('featured_programs_title', [
                'label' => 'Section title',
                'rows'  => 2,
            ])
            ->addTextarea('featured_programs_description', [
                'label' => 'Section description',
                'rows'  => 3,
            ])

        ->addTab('Featured card')
            ->addImage('featured_programs_image', [
                'label'         => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addTextarea('featured_programs_feature_title', [
                'label' => 'Featured title',
                'rows'  => 2,
            ])
            ->addTextarea('featured_programs_feature_description', [
                'label' => 'Featured description',
                'rows'  => 3,
            ])

        ->addTab('Programs')
            ->addRepeater('featured_programs_items', [
                'label'        => 'Program cards',
                'min'          => 4,
                'max'          => 4,
                'layout'       => 'block',
                'button_label' => 'Add program',
            ])
                ->addImage('image', [
                    'label'         => 'Image',
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                ])
                ->addText('title', [
                    'label'    => 'Title',
                    'required' => 1,
                ])
                ->addTextarea('description', [
                    'label' => 'Description',
                    'rows'  => 3,
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading)

        ->addTab('Text Settings')
            ->addFields($text)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($programs) {
        acf_add_local_field_group($programs->build());
    });
}