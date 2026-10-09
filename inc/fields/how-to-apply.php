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
            'label'         => 'Section title',
            'default_value' => 'How to apply',
        ])
        ->addButtonGroup('how_to_apply_style', [
            'label'         => 'Section style',
            'choices'       => [
                'standard' => 'Standard',
                'blue'     => 'Blue',
            ],
            'default_value' => 'standard',
        ])
        ->addRepeater('how_to_apply_steps', [
            'label'        => 'Application steps',
            'min'          => 4,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add step',
            'instructions' => 'Add four steps. Their numbers and badge colors are generated automatically.',
        ])
            ->addText('title', [
                'label'    => 'Step title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
        ->endRepeater()

        ->addTab('Blue style details')
        ->addImage('how_to_apply_contact_image', [
            'label'             => 'Contact photo',
            'return_format'     => 'id',
            'preview_size'      => 'thumbnail',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_contact_label', [
            'label'             => 'Contact label',
            'default_value'     => 'Grants Team',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_contact_name', [
            'label'             => 'Contact name',
            'default_value'     => 'Arbene Myftari Abrashi',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_contact_role', [
            'label'             => 'Contact role',
            'default_value'     => 'Grants Director',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addEmail('how_to_apply_contact_email', [
            'label'             => 'Contact email',
            'default_value'     => 'info@mcakosovo.org',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_title', [
            'label'             => 'Newsletter title',
            'default_value'     => 'Be first to know',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_placeholder', [
            'label'             => 'Newsletter email placeholder',
            'default_value'     => 'Email address',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addText('how_to_apply_newsletter_shortcode', [
            'label'             => 'Newsletter form shortcode',
            'instructions'      => 'Optional. Use a newsletter service shortcode to handle subscriptions.',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
            ]]],
        ])
        ->addLink('how_to_apply_newsletter_link', [
            'label'             => 'Newsletter form destination',
            'conditional_logic' => [[[
                'field'    => 'how_to_apply_style',
                'operator' => '==',
                'value'    => 'blue',
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
