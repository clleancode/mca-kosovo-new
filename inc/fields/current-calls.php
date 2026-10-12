<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $current_calls = new FieldsBuilder('current_calls_fields');

    $current_calls
        ->setLocation('block', '==', 'acf/current-calls')

        ->addTab('Content')
        ->addText('current_calls_title', [
            'label' => 'Section title',
        ])
        ->addText('current_calls_empty_label', [
            'label' => 'Empty state label',
        ])
        ->addText('current_calls_empty_title', [
            'label' => 'Empty state title',
        ])
        ->addTextarea('current_calls_empty_description', [
            'label' => 'Empty state description',
            'rows'  => 3,
        ])
        ->addLink('current_calls_notify_link', [
            'label' => 'Notify button link',
        ])
        ->addText('current_calls_open_title', [
            'label' => 'Open calls title',
        ])
        ->addTextarea('current_calls_open_description', [
            'label' => 'Open calls description',
            'rows'  => 3,
        ])
        ->addLink('current_calls_open_link', [
            'label' => 'Open calls button link',
        ])

        ->addTab('Heading')
        ->addFields(\MCA\Fields\Reusable\get_heading_fields())
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($current_calls) {
        acf_add_local_field_group($current_calls->build());
    });
}
