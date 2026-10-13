<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('gallery_block_fields');

    $fields
        ->setLocation('block', '==', 'acf/gallery')

        ->addTab('Photos')
        ->addPostObject('gallery_source', [
            'label'         => 'Source album (optional)',
            'post_type'     => ['gallery'],
            'return_format' => 'id',
            'allow_null'    => 1,
            'instructions'  => 'Leave empty on a single Gallery to use its images.',
        ])
        ->addGallery('gallery_photos', [
            'label'         => 'Custom photos (optional)',
            'return_format' => 'id',
            'instructions'  => 'These photos override the source album. Drag to reorder.',
        ])
        ->addText('gallery_caption', [
            'label'        => 'Album caption (optional)',
            'instructions' => 'Default: source album title and year. Attachment captions override this.',
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}