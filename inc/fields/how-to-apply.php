<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_size', ['default_value' => 'h6'])
        ->modifyField('header_color', ['default_value' => 'h-dark-blue']);
    $text = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', ['default_value' => 'xs'])
        ->modifyField('text_color', ['default_value' => 'h-dark-blue']);

    $apply = new FieldsBuilder('how_to_apply_fields');

    $apply
        ->setLocation('block', '==', 'acf/how-to-apply')

        ->addTab('Content')
        ->addText('how_to_apply_title', [
            'label' => 'Section title',
        ])
        ->addButtonGroup('how_to_apply_style', [
            'label'         => 'Section style',
            'choices'       => [
                'standard' => 'Standard',
                'blue'     => 'Blue',
                'alerts'   => 'Open application and job alerts',
            ],
            'default_value' => 'standard',
        ])
        ->addRepeater('how_to_apply_steps', [
            'label'        => 'Application steps',
            'min'          => 0,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add step',
            'instructions' => 'Optional. Leave empty to display only the contact and job alerts cards. Add up to four steps; empty rows are not displayed.',
        ])
            ->addText('title', [
                'label'    => 'Step title',
                'required' => 0,
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
        ->endRepeater()

        ->addTab('Contact and job alerts')
        ->addText('how_to_apply_open_title', ['label' => 'Open application title'])
        ->addTextarea('how_to_apply_open_description', ['label' => 'Open application description', 'rows' => 2])
        ->addLink('how_to_apply_open_link', ['label' => 'Open application link'])
        ->addImage('how_to_apply_contact_image', [
            'label'             => 'Contact photo',
            'return_format'     => 'id',
            'preview_size'      => 'thumbnail',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_contact_label', [
            'label'             => 'Contact label',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_contact_name', [
            'label'             => 'Contact name',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_contact_role', [
            'label'             => 'Contact role',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addEmail('how_to_apply_contact_email', [
            'label'             => 'Contact email',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_title', [
            'label'             => 'Newsletter title',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_placeholder', [
            'label'             => 'Newsletter email placeholder',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_shortcode', [
            'label'             => 'Newsletter form shortcode',
            'instructions'      => 'Optional. Use a newsletter service shortcode to handle subscriptions.',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])
        ->addLink('how_to_apply_newsletter_link', [
            'label'             => 'Newsletter form destination',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '!=',
                'value'    => 'standard',
            ]]],
        ])

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($apply) {
        acf_add_local_field_group($apply->build());
    });
}
