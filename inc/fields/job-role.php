<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $fields = new FieldsBuilder('job_role_fields');

    $fields
        ->setLocation('block', '==', 'acf/job-role')
        ->addTab('Key facts');

    $key_facts = [
        'location'      => 'Location',
        'location_note' => 'Location note',
        'contract'      => 'Contract',
        'contract_note' => 'Contract note',
        'reports_to'    => 'Reports to',
        'reports_note'  => 'Reports to note',
    ];

    foreach ($key_facts as $key => $label) {
        $fields->addText('jr_' . $key, [
            'label' => $label,
        ]);
    }

    $fields
        ->addMessage(
            'Application deadline & status',
            'The application deadline and closed status are read from this Job.'
        )

        ->addTab('Role')
        ->addText('jr_about_title', [
            'label' => 'About heading',
        ])
        ->addTextarea('jr_about', [
            'label' => 'About the role',
            'rows'  => 4,
        ])
        ->addText('jr_duties_title', [
            'label' => 'Duties heading',
        ])
        ->addText('jr_qualifications_title', [
            'label' => 'Qualifications heading',
        ]);

    $lists = [
        'duties'    => 'Main duties',
        'required'  => 'Required qualifications',
        'preferred' => 'Preferred qualifications',
    ];

    foreach ($lists as $key => $label) {
        $fields
            ->addRepeater('jr_' . $key, [
                'label'        => $label,
                'layout'       => 'table',
                'button_label' => 'Add item',
            ])
                ->addTextarea('text', [
                    'label' => 'Text',
                    'rows'  => 2,
                ])
            ->endRepeater();
    }

    $fields
        ->addTab('Application')
        ->addTextarea('jr_apply_intro', [
            'label' => 'Application introduction',
            'rows'  => 2,
        ])
        ->addLink('jr_apply_link', [
            'label'        => 'Apply link (optional)',
            'instructions' => 'Defaults to the job application page for this position.',
        ])
        ->addLink('jr_alerts_link', [
            'label' => 'Get job alerts link',
        ])

        ->addTab('Documents')
        ->addRepeater('jr_documents', [
            'label'  => 'Documents',
            'layout' => 'block',
        ])
            ->addText('title', [
                'label' => 'Title',
            ])
            ->addText('description', [
                'label' => 'Description',
            ])
            ->addFile('file', [
                'label'         => 'File',
                'return_format' => 'array',
                'required'      => true,
            ])
        ->endRepeater()

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($fields) {
        acf_add_local_field_group($fields->build());
    });
}