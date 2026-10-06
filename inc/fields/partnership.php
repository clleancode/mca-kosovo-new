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

    $partnership = new FieldsBuilder('partnership_fields');

    $partnership
        ->setLocation('block', '==', 'acf/partnership')
        ->addTab('Content')
        ->addText('partnership_label', [
            'label' => 'Badge label',
        ])
        ->addTextarea('partnership_title', [
            'label' => 'Title',
            'rows'  => 2,
        ])
        ->addTextarea('partnership_description', [
            'label' => 'Description',
            'rows'  => 3,
        ])
        ->addImage('partnership_main_image', [
            'label'         => 'Main image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addText('partnership_image_caption', [
            'label' => 'Image caption',
        ])
        ->addImage('partnership_side_image', [
            'label'         => 'Side image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addTab('Funding');

    $funding_fields = [
        'funding_label'          => 'Funding partner label',
        'funding_name'           => 'Funding partner name',
        'funding_description'    => 'Funding partner description',
        'funding_amount'         => 'Funding partner amount',
        'total_label'            => 'Total label',
        'total_amount'           => 'Total amount',
        'government_label'       => 'Government label',
        'government_name'        => 'Government name',
        'government_description' => 'Government description',
        'government_amount'      => 'Government amount',
    ];

    foreach ($funding_fields as $name => $label) {
        $partnership->addText('partnership_' . $name, [
            'label' => $label,
        ]);
    }

    $partnership
        ->addTab('Implementation')
        ->addImage('partnership_logo', [
            'label'         => 'Implementing institution logo',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addText('partnership_implemented_label', [
            'label' => 'Institution label',
        ])
        ->addText('partnership_implemented_name', [
            'label' => 'Institution name',
        ])
        ->addTextarea('partnership_implemented_description', [
            'label' => 'Institution description',
            'rows'  => 3,
        ])
        ->addText('partnership_partners_label', [
            'label' => 'Partners label',
        ])
        ->addRepeater('partnership_partners', [
            'label'        => 'Implementing partners',
            'layout'       => 'table',
            'button_label' => 'Add partner',
        ])
            ->addText('name', [
                'label'    => 'Partner name',
                'required' => true,
            ])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($partnership) {
        acf_add_local_field_group($partnership->build());
    });
}