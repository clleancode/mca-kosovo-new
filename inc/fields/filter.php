<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $filter = new FieldsBuilder('procurement_filter_fields');

    $filter
        ->setLocation('block', '==', 'acf/filter')

        ->addTab('Content')
        ->addText('filter_placeholder', [
            'label' => 'Search placeholder',
        ])
        ->addText('filter_search_label', [
            'label' => 'Search button label',
        ])
        ->addText('filter_links_label', [
            'label' => 'Related links label',
        ])
        ->addRepeater('filter_links', [
            'label'        => 'Related links',
            'max'          => 3,
            'layout'       => 'block',
            'button_label' => 'Add link',
        ])
            ->addLink('link', [
                'label'    => 'Link',
                'required' => 1,
            ])
            ->addSelect('icon', [
                'label'   => 'Icon',
                'choices' => [
                    'icon-pdf'     => 'Document',
                    'icon-search'  => 'Search',
                    'icon-message' => 'Message',
                ],
            ])
        ->endRepeater()

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($filter) {
        acf_add_local_field_group($filter->build());
    });
}