<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();
    $report = new FieldsBuilder('how_to_report_fields');

    $report
        ->setLocation('block', '==', 'acf/how-to-report')
        ->addTab('Section heading')
        ->addText('how_to_report_title', ['label' => 'Section title'])
        ->addTab('MCC hotline')
        ->addText('how_to_report_mcc_label', ['label' => 'Eyebrow label'])
        ->addText('how_to_report_mcc_title', ['label' => 'Title'])
        ->addTextarea('how_to_report_mcc_description', ['label' => 'Description', 'rows' => 3])
        ->addRepeater('how_to_report_mcc_contacts', [
            'label' => 'Hotline contacts', 'max' => 3, 'layout' => 'block', 'button_label' => 'Add contact method',
        ])
            ->addText('label', ['label' => 'Contact label'])
            ->addText('value', ['label' => 'Contact detail'])
            ->addLink('link', ['label' => 'Contact link'])
        ->endRepeater()
        ->addTab('Inspector General hotline')
        ->addText('how_to_report_oig_label', ['label' => 'Eyebrow label'])
        ->addText('how_to_report_oig_title', ['label' => 'Title'])
        ->addTextarea('how_to_report_oig_description', ['label' => 'Description', 'rows' => 3])
        ->addRepeater('how_to_report_oig_steps', [
            'label' => 'Reporting steps', 'max' => 5, 'layout' => 'table', 'button_label' => 'Add step',
        ])
            ->addText('text', ['label' => 'Step text'])
        ->endRepeater()
        ->addTextarea('how_to_report_oig_note', ['label' => 'Note below reporting steps', 'rows' => 4])
        ->addRepeater('how_to_report_oig_options', [
            'label' => 'Contact options', 'max' => 4, 'layout' => 'table', 'button_label' => 'Add option',
        ])
            ->addLink('link', ['label' => 'Option link'])
        ->endRepeater()
        ->addLink('how_to_report_oig_cta', ['label' => 'Hotline button'])
        ->addTab('Anonymous reporting note')
        ->addText('how_to_report_anonymous_title', ['label' => 'Title'])
        ->addTextarea('how_to_report_anonymous_description', ['label' => 'Description', 'rows' => 3])
        ->addTab('Policy resources')
        ->addRepeater('how_to_report_resources', [
            'label' => 'Resource cards', 'max' => 2, 'layout' => 'block', 'button_label' => 'Add resource',
        ])
            ->addText('title', ['label' => 'Title'])
            ->addTextarea('description', ['label' => 'Description', 'rows' => 2])
            ->addText('file_label', ['label' => 'File type or reference'])
            ->addLink('link', ['label' => 'Open link'])
            ->addFile('pdf', [
                'label' => 'PDF file',
                'return_format' => 'array',
                'mime_types' => 'pdf',
            ])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($report) {
        acf_add_local_field_group($report->build());
    });
}
