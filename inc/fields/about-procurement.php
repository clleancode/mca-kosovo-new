<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('about_procurement_fields');

    $fields
        ->setLocation('block', '==', 'acf/about-procurement')
        ->addTab('Key facts')
        ->addText('ap_method_note', [
            'label' => 'Method description',
        ])
        ->addText('ap_deadline_time', [
            'label'       => 'Deadline time',
            'placeholder' => '15:00 CET',
        ])
        ->addText('ap_submission', [
            'label' => 'Submission',
        ])
        ->addText('ap_submission_note', [
            'label' => 'Submission note',
        ])
        ->addTab('About & participation')
        ->addText('ap_about_title', [
            'label' => 'About title',
        ])
        ->addTextarea('ap_intro', [
            'label' => 'Introduction',
            'rows'  => 4,
        ])
        ->addTextarea('ap_description', [
            'label' => 'Additional description',
            'rows'  => 4,
        ])
        ->addText('ap_participation_title', [
            'label' => 'Participation title',
        ])
        ->addRepeater('ap_steps', [
            'label'        => 'Participation steps',
            'layout'       => 'block',
            'max'          => 3,
            'button_label' => 'Add step',
        ])
            ->addText('title', [
                'label' => 'Title',
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addLink('link', [
                'label' => 'Registration link (optional)',
            ])
        ->endRepeater()
        ->addText('ap_warning', [
            'label' => 'Warning',
        ])
        ->addTab('Documents')
        ->addRepeater('ap_documents', [
            'label'        => 'Documents',
            'layout'       => 'block',
            'button_label' => 'Add document',
        ])
            ->addText('title', [
                'label' => 'Document title',
            ])
            ->addText('description', [
                'label' => 'Reference or description',
            ])
            ->addFile('file', [
                'label'         => 'Download file',
                'return_format' => 'array',
            ])
            ->addLink('link', [
                'label' => 'External link (used when no file is selected)',
            ])
        ->endRepeater()
        ->addTab('Contact')
        ->addText('ap_contact_intro', [
            'label' => 'Contact introduction',
        ])
        ->addEmail('ap_email', [
            'label' => 'Email',
        ])
        ->addTextarea('ap_address', [
            'label' => 'Address',
            'rows'  => 2,
        ])
        ->addTab('Process')
        ->addText('ap_process_title', [
            'label' => 'Process title',
        ])
        ->addText('ap_evaluation_note', [
            'label' => 'Evaluation note',
        ])
        ->addText('ap_award_note', [
            'label' => 'Award note',
        ])
        ->addSelect('ap_process_stage', [
            'label'         => 'Current stage',
            'choices'       => [
                'Published' => 'Published',
                'Submission deadline' => 'Submission deadline',
                'Evaluation' => 'Evaluation',
                'Award notice' => 'Award notice',
            ],
            'return_format' => 'value',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}