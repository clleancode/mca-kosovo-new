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
        'funding_description'    => 'Funding description (top right text)',
        'funding_amount'         => 'Funding partner amount',
        'funding_percent'        => 'Funding partner percent (e.g. 85)',
        'funding_sub'            => 'Funding partner sub-label (e.g. Through the MCC)',
        'total_label'            => 'Total label',
        'total_amount'           => 'Total amount',
        'government_label'       => 'Government label',
        'government_name'        => 'Government name',
        'government_amount'      => 'Government amount',
        'government_percent'     => 'Government partner percent (e.g. 15)',
        'government_sub'         => 'Government partner sub-label (e.g. Through the Government of Kosovo)',
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
        ->addLink('partnership_implemented_link', [
            'label'        => 'Institution link',
            'instructions' => 'Optional link for the arrow button.',
        ])
        ->addText('partnership_partners_label', [
            'label' => 'Partners label',
        ])
        ->addText('partnership_partners_count', [
            'label'        => 'Partners count text',
            'instructions' => 'e.g. 4 Institutions',
        ])
        ->addRepeater('partnership_partners', [
            'label'        => 'Implementing partners',
            'layout'       => 'block',
            'button_label' => 'Add partner',
        ])
            ->addImage('image', [
                'label'         => 'Partner logo/image',
                'return_format' => 'id',
                'preview_size'  => 'thumbnail',
            ])
            ->addText('image_url', [
                'label'        => 'Partner image path (optional)',
                'instructions' => 'Relative path from theme root, e.g. assets/img/pics/home/uni.png. Used only if no image is uploaded above.',
            ])
            ->addTrueFalse('logo_wide', [
                'label'        => 'Wide logo',
                'instructions' => 'Enable to expand logo area to 140px width.',
                'ui'           => 1,
            ])
            ->addText('partner_label', [
                'label' => 'Category label (e.g. ENERGY STORAGE)',
            ])
            ->addText('name', [
                'label'    => 'Partner name',
                'required' => true,
            ])
            ->addTextarea('description', [
                'label' => 'Partner description',
                'rows'  => 2,
            ])
            ->addLink('link', [
                'label' => 'Partner link',
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