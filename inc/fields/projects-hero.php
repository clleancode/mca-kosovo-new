<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {

    $heading = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_tag', [])
        ->modifyField('header_size', [])
        ->modifyField('header_color', [])
        ->modifyField('header_line_height', []);

    $text = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', [])
        ->modifyField('text_color', []);

    $hero = new FieldsBuilder('small_hero_fields');

    $hero
        ->setLocation('block', '==', 'acf/projects-hero')

        ->addTab('Content')
            ->addImage('small_hero_image', [
                'label'         => 'Background image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addSelect('projects_hero_tone', [
                'label'   => 'Project color',
                'choices' => [
                    'blue'   => 'Energy Storage - Blue',
                    'purple' => 'JETA - Purple',
                    'green'  => 'ACFD - Green',
                ],
            ])
            ->addText('small_hero_label', [
                'label' => 'Project pill',
            ])
            ->addTextarea('small_hero_title', [
                'label' => 'Title',
                'rows'  => 2,
            ])
            ->addTextarea('small_hero_description', [
                'label' => 'Description',
                'rows'  => 3,
            ])
            ->addLink('small_hero_primary_link', [
                'label' => 'Primary button',
            ])
            ->addLink('small_hero_secondary_link', [
                'label' => 'Secondary button',
            ])

        ->addTab('Statistics')
            ->addRepeater('small_hero_statistics', [
                'label'        => 'Statistics',
                'max'          => 4,
                'layout'       => 'table',
                'button_label' => 'Add statistic',
            ])
                ->addText('value', [
                    'label'    => 'Value',
                    'required' => 1,
                ])
                ->addText('unit', [
                    'label' => 'Unit',
                ])
                ->addText('label', [
                    'label' => 'Description',
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading)

        ->addTab('Text Settings')
            ->addFields($text)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($hero) {
        acf_add_local_field_group($hero->build());
    });
}