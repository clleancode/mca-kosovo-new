<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $documents = new FieldsBuilder('all_documents_fields');

    $documents
        ->setLocation('block', '==', 'acf/all-documents')
        ->addTab('Content')
        ->addTrueFalse('all_documents_related', [
            'label' => 'Show only related documents (up to 4)',
            'ui' => 1,
            'instructions' => 'Shows selected cards without filters, sorting or Load more.',
        ])
        ->addRelationship('all_documents_selection', [
            'label' => 'Related documents',
            'post_type' => ['publication'],
            'post_status' => ['publish'],
            'return_format' => 'id',
            'max' => 4,
            'instructions' => 'Choose up to four publications with a document attached. Drag to reorder.',
            'conditional_logic' => [[['field' => 'all_documents_related', 'operator' => '==', 'value' => '1']]],
        ])
        ->addText('all_documents_title', [
            'label' => 'Title',
        ])
        ->addText('all_documents_section_id', [
            'label'        => 'Section ID',
            'instructions' => 'Without #, for example all-documents.',
        ])
        ->addNumber('all_documents_per_page', [
            'label' => 'Documents loaded at a time',
            'min'   => 4,
            'max'   => 48,
        ])
        ->addTaxonomy('all_documents_categories', [
            'label'         => 'Category filters (optional)',
            'taxonomy'      => 'publication_type',
            'field_type'    => 'multi_select',
            'return_format' => 'id',
            'add_term'      => 0,
            'load_terms'    => 0,
            'save_terms'    => 0,
            'instructions'  => 'Leave empty to show all Publication Types.',
        ])
        ->addText('all_documents_more_label', [
            'label' => 'Load more label',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($documents) {
        acf_add_local_field_group($documents->build());
    });
}
