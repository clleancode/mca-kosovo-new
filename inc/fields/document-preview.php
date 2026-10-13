<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('document_preview_fields');

    $fields
        ->setLocation('block', '==', 'acf/document-preview')

        ->addTab('Content')
        ->addPostObject('dp_publication', [
            'label'         => 'Publication (optional)',
            'post_type'     => ['publication'],
            'return_format' => 'id',
            'allow_null'    => 1,
            'instructions'  => 'Leave empty to use the current publication and its document.',
        ])
        ->addText('dp_title', [
            'label' => 'Preview heading',
        ])
        ->addFile('dp_file', [
            'label'         => 'Document override (optional)',
            'return_format' => 'array',
            'mime_types'    => 'pdf,xls,xlsx',
        ])
        ->addImage('dp_cover', [
            'label'         => 'Preview image override (optional)',
            'return_format' => 'id',
            'instructions'  => 'Used for documents other than PDF. PDFs use the browser document viewer.',
        ])
        ->addText('dp_activities', [
            'label' => 'Activities',
        ])
        ->addText('dp_contact_title', [
            'label' => 'Contact heading',
        ])
        ->addEmail('dp_email', [
            'label' => 'Contact email',
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}