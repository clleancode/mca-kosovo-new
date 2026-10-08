<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();
    $text    = \MCA\Fields\Reusable\get_text_fields();

    $opportunities = new FieldsBuilder('opportunities_fields');

    $opportunities
        ->setLocation('block', '==', 'acf/opportunities')

        ->addTab('Content')
        ->addText('opportunities_label', [
            'label' => 'Badge label',
        ])
        ->addText('opportunities_title', [
            'label' => 'Title',
        ])
        ->addTextarea('opportunities_description', [
            'label' => 'Description',
            'rows'  => 3,
        ])

        ->addTab('Cards')
        ->addRepeater('opportunities_cards', [
            'label'        => 'Opportunity cards',
            'min'          => 4,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add card',
        ])
            ->addImage('image', [
                'label'         => 'Image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('label', [
                'label' => 'Pill label',
            ])
            ->addText('title', [
                'label'    => 'Title',
                'required' => true,
            ])
            ->addText('description', [
                'label' => 'Description',
            ])
            ->addLink('link', [
                'label' => 'Card link',
            ])
        ->endRepeater()

        ->addTab('Procurements')
        ->addText('opportunities_procurement_label', [
            'label' => 'Panel title',
        ])
        ->addLink('opportunities_procurement_link', [
            'label' => 'View all link',
        ])
        ->addLink('opportunities_email_link', [
            'label' => 'Get notices by email link',
        ])

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    $details = new FieldsBuilder('opportunities_procurement_details');

    $details
        ->setLocation('post_type', '==', 'procurement')
        ->addText('procurement_award_phase', [
            'label'        => 'Award phase',
            'instructions' => 'Optional award status displayed in the featured procurement card.',
        ])
        ->addText('procurement_notice_type', [
            'label'        => 'Notice type',
            'instructions' => 'For example: Prequalification or Specific Procurement Notice.',
        ])
        ->addDatePicker('deadline', [
            'label'          => 'Deadline',
            'display_format' => 'd.m.Y',
            'return_format'  => 'd.m.Y',
        ])
        ->addText('procurement_phase', [
            'label'        => 'Phase',
            'instructions' => 'Optional phase shown alongside the publication date.',
        ]);

    add_action('acf/init', function () use ($opportunities, $details) {
        acf_add_local_field_group($opportunities->build());
        acf_add_local_field_group($details->build());
    });
}