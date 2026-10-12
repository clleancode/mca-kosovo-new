<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $category = new FieldsBuilder('resource_category_fields');

    $category
        ->setLocation('block', '==', 'acf/category')
        ->addTab('Content')
        ->addText('resource_category_title', [
            'label' => 'Title',
        ])
        ->addNumber('resource_category_per_page', [
            'label' => 'Publications per page',
            'min'   => 1,
            'max'   => 12,
        ])
        ->addRepeater('resource_category_cards', [
            'label'        => 'Category cards',
            'max'          => 5,
            'layout'       => 'block',
            'button_label' => 'Add category',
        ])
            ->addTaxonomy('term', [
                'label'         => 'Publication Type',
                'taxonomy'      => 'publication_type',
                'field_type'    => 'select',
                'allow_null'    => 1,
                'return_format' => 'id',
                'save_terms'    => 0,
                'load_terms'    => 0,
                'add_term'      => 0,
            ])
            ->addText('title', [
                'label' => 'Card title override',
            ])
            ->addText('description', [
                'label' => 'Description',
            ])
            ->addLink('link', [
                'label' => 'External link (used when no category is selected)',
            ])
            ->addSelect('tone', [
                'label'   => 'Card color',
                'choices' => [
                    'blue'   => 'Dark blue',
                    'purple' => 'Purple',
                    'pink'   => 'Pink',
                    'green'  => 'Green',
                    'white'  => 'White',
                ],
            ])
        ->endRepeater()
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    $publication = new FieldsBuilder('resource_publication_fields');

    $publication
        ->setLocation('post_type', '==', 'publication')
        ->addFile('publication_pdf', [
            'label'         => 'Publication PDF',
            'return_format' => 'array',
            'mime_types'    => 'pdf',
        ])
        ->addPostObject('publication_project', [
            'label'         => 'Related project',
            'post_type'     => ['project'],
            'return_format' => 'id',
            'allow_null'    => 1,
        ]);

    add_action('acf/init', function () use ($category, $publication) {
        acf_add_local_field_group($category->build());
        acf_add_local_field_group($publication->build());
    });
}