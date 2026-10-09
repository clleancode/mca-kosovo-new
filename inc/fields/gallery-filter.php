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

    $gallery = new FieldsBuilder('gallery_filter_fields');

    $gallery
        ->setLocation('block', '==', 'acf/gallery-filter')

        ->addTab('Content')
        ->addText('gallery_filter_badge', [
            'label'         => 'Badge',
            'default_value' => 'Featured',
        ])
        ->addText('gallery_filter_category', [
            'label'         => 'Category',
            'default_value' => 'Energy Storage',
        ])
        ->addText('gallery_filter_date', [
            'label'         => 'Date label',
            'default_value' => 'Oct 2026',
        ])
        ->addText('gallery_filter_title', [
            'label'         => 'Title',
            'default_value' => 'The procurement of the Battery Energy Systems in its final phase',
        ])
        ->addTextarea('gallery_filter_description', [
            'label'         => 'Description',
            'default_value' => 'MCA Kosovo is implementing the Energy Storage Project to strengthen the security, reliability, flexibility and resilience of Kosovo’s electricity system — supporting new generation, balancing supply and demand, and improving energy-sector efficiency.',
            'rows'          => 5,
        ])
        ->addLink('gallery_filter_link', [
            'label' => 'Story link',
        ])

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($gallery) {
        acf_add_local_field_group($gallery->build());
    });
}
