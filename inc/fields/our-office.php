<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $our_office = new FieldsBuilder('our_office_fields');

    $our_office
        ->setLocation('block', '==', 'acf/our-office')
        ->addTab('Content')
        ->addText('our_office_label', [
            'label' => 'Eyebrow label',
        ])
        ->addTextarea('our_office_title', [
            'label' => 'Heading',
            'rows' => 2,
        ])
        ->addTextarea('our_office_address', [
            'label' => 'Address',
            'rows' => 3,
        ])
        ->addImage('our_office_map_image', [
            'label' => 'Map image',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
            'required' => 1,
        ])
        ->addLink('our_office_link', [
            'label' => 'Map link',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($our_office) {
        acf_add_local_field_group($our_office->build());
    });
}
