<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('more_albums_fields');

    $fields
        ->setLocation('block', '==', 'acf/more-albums')

        ->addTab('Content')
        ->addText('more_albums_title', [
            'label' => 'Heading',
        ])
        ->addRelationship('more_albums_selection', [
            'label'         => 'Albums (optional)',
            'post_type'     => ['gallery'],
            'post_status'   => ['publish'],
            'return_format' => 'id',
            'max'           => 2,
            'instructions'  => 'Choose up to two albums. Leave empty to show the latest two. The current gallery is excluded.',
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}