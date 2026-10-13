<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('publications_banner_fields');

    $fields
        ->setLocation('block', '==', 'acf/publications-banner')

        ->addTab('Content')
        ->addPostObject('publications_banner_publication', [
            'label'         => 'Publication (optional)',
            'post_type'     => ['publication'],
            'return_format' => 'id',
            'allow_null'    => 1,
            'instructions'  => 'Leave empty to use the current single-publication. The cover, title, excerpt, PDF, category and project are read from the publication.',
        ])
        ->addText('publications_banner_title', [
            'label' => 'Title override (optional)',
        ])
        ->addTextarea('publications_banner_description', [
            'label' => 'Description override (optional)',
            'rows'  => 4,
        ])
        ->addImage('publications_banner_cover', [
            'label'         => 'Cover override (optional)',
            'return_format' => 'id',
        ])
        ->addLink('publications_banner_back_link', [
            'label' => 'All resources link',
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}