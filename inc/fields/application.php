<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $application = new FieldsBuilder('application_fields');

    $application
        ->setLocation('block', '==', 'acf/application')
        ->addTab('Content')
        ->addText('application_section_id', [
            'label'        => 'Section ID',
            'instructions' => 'Without #.',
        ])
        ->addText('application_title', [
            'label' => 'Title',
        ])
        ->addText('application_note', [
            'label' => 'Form note',
        ])
        ->addLink('application_privacy_policy_link', [
            'label'        => 'Privacy Policy link',
            'instructions' => 'Select the Privacy Policy page. If empty, the WordPress privacy policy page is used.',
        ])
        ->addText('application_shortcode', [
            'label'        => 'Contact Form 7 shortcode',
            'instructions' => 'Paste the shortcode after creating the form using application-cf7.txt. Without a shortcode, only a non-submitting preview is shown.',
        ])
        ->addTab('Photos and HR contact')
        ->addImage('application_main_image', [
            'label'         => 'Main photo',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addImage('application_secondary_image', [
            'label'         => 'Overlapping photo',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addText('application_contact_title', [
            'label' => 'Contact title',
        ])
        ->addText('application_contact_description', [
            'label' => 'Contact description',
        ])
        ->addEmail('application_contact_email', [
            'label' => 'HR email',
        ])
        ->addFile('application_form_file', [
            'label'         => 'Blank application form',
            'return_format' => 'array',
            'mime_types'    => 'pdf,doc,docx',
        ])
        ->addText('application_download_label', [
            'label' => 'Download label',
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($application) {
        acf_add_local_field_group($application->build());
    });
}