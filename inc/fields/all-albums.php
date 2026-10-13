<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('all_albums_fields');

    $fields
        ->setLocation('block', '==', 'acf/albums')

        ->addTab('Albums')
        ->addRelationship('all_albums_selection', [
            'label'         => 'Albums (optional)',
            'post_type'     => ['gallery'],
            'post_status'   => ['publish'],
            'return_format' => 'id',
            'instructions'  => 'Leave empty to show all published galleries. Drag to reorder; the first album is featured.',
        ])

        ->addTab('Latest photos')
        ->addTrueFalse('all_albums_show_photos', [
            'label' => 'Show latest photos',
            'ui'    => 1,
        ])
        ->addText('all_albums_photos_title', [
            'label' => 'Photos heading',
        ])
        ->addPostObject('all_albums_photos_source', [
            'label'         => 'Photo source album (optional)',
            'post_type'     => ['gallery'],
            'return_format' => 'id',
            'allow_null'    => 1,
            'instructions'  => 'Reads photos from the Gallery blocks. Photos at the end of the list appear first. Leave empty to use displayed albums, most recently updated first.',
        ])
        ->addNumber('all_albums_photo_count', [
            'label' => 'Number of photos',
            'min'   => 1,
            'max'   => 15,
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}