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

    $documents = new FieldsBuilder('documents_fields');

    $documents
        ->setLocation('block', '==', 'acf/documents')
        ->addTab('Documents')
        ->addText('project_documents_label', [
            'label' => 'Badge label',
        ])
        ->addText('project_documents_title', [
            'label' => 'Title',
        ])
        ->addTextarea('project_documents_description', [
            'label' => 'Description',
            'rows'  => 3,
        ])
        ->addRepeater('project_documents', [
            'label'        => 'Documents',
            'layout'       => 'block',
            'button_label' => 'Add document',
        ])
            ->addFile('file', [
                'label'         => 'Document file',
                'return_format' => 'array',
                'mime_types'    => 'pdf,xls,xlsx',
                'required'      => 1,
            ])
            ->addButtonGroup('document_type', [
                'label' => 'Document icon',
                'choices' => ['pdf' => 'PDF', 'xls' => 'XLS / XLSX'],
                'default_value' => 'pdf',
            ])
            ->addText('project_documents_title', [
                'key'      => 'field_documents_item_title',
                'label'    => 'Title',
                'required' => 1,
            ])
            ->addText('project_documents_description', [
                'key'   => 'field_documents_item_description',
                'label' => 'Description',
            ])
        ->endRepeater()
        ->addText('project_documents_download_label', [
            'label' => 'Download button label',
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($documents) {
        acf_add_local_field_group($documents->build());
    });
}
