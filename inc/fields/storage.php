<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading_fields = \MCA\Fields\Reusable\get_heading_fields();
    $text_fields    = \MCA\Fields\Reusable\get_text_fields();

    $storage = new FieldsBuilder('storage_fields');

    $storage
        ->setLocation('block', '==', 'acf/storage')

        ->addTab('Content')
        ->addText('storage_label', [
            'label' => 'Eyebrow label',
        ])
        ->addText('storage_capacity', [
            'label' => 'Total capacity',
        ])
        ->addText('storage_unit', [
            'label' => 'Capacity unit',
        ])
        ->addTextarea('storage_heading', [
            'label' => 'Heading',
            'rows'  => 2,
        ])
        ->addTextarea('storage_description', [
            'label' => 'Description',
            'rows'  => 3,
        ])

        ->addTab('Heading')
        ->addFields($heading_fields)

        ->addTab('Text Settings')
        ->addFields($text_fields)

        ->addTab('Locations')
        ->addImage('storage_map_image', [
            'label'         => 'Map image',
            'instructions'  => 'Upload the complete map artwork, including its location labels.',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addRepeater('storage_locations', [
            'label'        => 'Storage locations',
            'instructions' => 'Add the two locations shown beside the map.',
            'min'          => 2,
            'max'          => 2,
            'layout'       => 'block',
            'button_label' => 'Add location',
        ])
            ->addText('name', [
                'label'    => 'Location',
                'required' => true,
            ])
            ->addText('capacity', [
                'label'    => 'Capacity',
                'required' => true,
            ])
            ->addText('note', [
                'label' => 'Supporting note',
            ])
        ->endRepeater()
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($storage) {
        acf_add_local_field_group($storage->build());
    });
}