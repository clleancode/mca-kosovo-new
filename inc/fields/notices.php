<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $notices = new FieldsBuilder('notices_fields');

    $notices
        ->setLocation('block', '==', 'acf/notices')

        ->addTab('Content')
        ->addSelect('notices_layout', [
            'label' => 'Layout',
            'choices' => ['listing' => 'Latest notices - full listing', 'selected' => 'More notices - 3 cards'],
            'default_value' => 'listing',
            'return_format' => 'value',
        ])
        ->addRelationship('notices_selected', [
            'label' => 'Select up to 3 notices',
            'post_type' => ['procurement'],
            'post_status' => ['publish'],
            'return_format' => 'id',
            'max' => 3,
            'instructions' => 'Drag to reorder. Leave empty to show the latest 3 notices, excluding the current post.',
            'conditional_logic' => [[['field' => 'notices_layout', 'operator' => '==', 'value' => 'selected']]],
        ])
        ->addLink('notices_header_link', ['label' => 'Header button', 'instructions' => 'For example: All procurements.'])
        ->addText('notices_title', [
            'label' => 'Title',
        ])
        ->addNumber('notices_per_page', [
            'label' => 'Notices per page',
            'min'   => 3,
            'max'   => 24,
        ])
        ->addTrueFalse('notices_featured', [
            'label' => 'Feature the first notice',
            'ui'    => 1,
        ])

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($notices) {
        acf_add_local_field_group($notices->build());
    });
}