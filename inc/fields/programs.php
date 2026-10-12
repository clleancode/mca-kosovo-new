<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

if (class_exists(FieldsBuilder::class)) {
    $program_details = new FieldsBuilder('program_details');

    $program_details
        ->setLocation('post_type', '==', 'program')
        ->addPostObject('program_project', [
            'label'         => 'Project category',
            'instructions'  => 'Choose the project this program belongs to.',
            'post_type'     => ['project'],
            'return_format' => 'id',
            'allow_null'    => 0,
            'multiple'      => 0,
            'ui'            => 1,
            'required'      => 1,
        ])
        ->addSelect('program_status', [
            'label'         => 'Program status label',
            'choices'       => [
                'coming-soon'              => 'Coming soon',
                'questionnaire-available' => 'Questionnaire available',
            ],
            'allow_null'    => 1,
            'ui'            => 1,
            'return_format' => 'value',
        ]);

    add_action('acf/init', function () use ($program_details) {
        acf_add_local_field_group($program_details->build());
    });
}
