<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $vacancies = new FieldsBuilder('vacancies_fields');

    $vacancies
        ->setLocation('block', '==', 'acf/vacancies')
        ->addTab('Content')
        ->addText('vacancies_section_id', [
            'label' => 'Section ID',
        ])
        ->addText('vacancies_title', [
            'label' => 'Title',
        ])
        ->addText('vacancies_empty_title', [
            'label' => 'No open vacancies title',
        ])
        ->addTextarea('vacancies_empty_description', [
            'label' => 'No open vacancies description',
            'rows'  => 3,
        ])
        ->addLink('vacancies_alerts_link', [
            'label' => 'Get job alerts link',
        ])
        ->addLink('vacancies_application_link', [
            'label' => 'Send open application link',
        ])
        ->addText('vacancies_closed_title', [
            'label' => 'Closed positions title',
        ])
        ->addNumber('vacancies_per_page', [
            'label' => 'Jobs per page',
            'min'   => 3,
            'max'   => 24,
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    $job = new FieldsBuilder('vacancy_job_details');

    $job
        ->setLocation('post_type', '==', 'job')
        ->addDatePicker('deadline', [
            'label'          => 'Application deadline',
            'return_format'  => 'd.m.Y',
            'display_format' => 'd.m.Y',
        ])
        ->addText('job_department', [
            'label'        => 'Department',
            'instructions' => 'For example Energy Storage, Legal, Administration & Finance.',
        ])
        ->addSelect('job_department_color', [
            'label'   => 'Department marker color',
            'choices' => [
                'blue'   => 'Blue',
                'purple' => 'Purple',
                'green'  => 'Green',
            ],
        ])
        ->addText('job_employer', [
            'label' => 'Employer',
        ]);

    add_action('acf/init', function () use ($vacancies, $job) {
        acf_add_local_field_group($vacancies->build());
        acf_add_local_field_group($job->build());
    });
}