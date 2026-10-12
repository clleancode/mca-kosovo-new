<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $specific_pages = new FieldsBuilder('specific_pages_fields');

    $specific_pages
        ->setLocation('block', '==', 'acf/specific-pages')
        ->addTab('Content')
        ->addText('specific_pages_title', [
            'label' => 'Section title',
        ])
        ->addRepeater('specific_pages_items', [
            'label' => 'Specific page cards',
            'instructions' => 'Add up to four cards. Enter the card text, image and link for each one.',
            'layout' => 'row',
            'button_label' => 'Add page card',
            'max' => 4,
        ])
            ->addText('title', [
                'label' => 'Card title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Card description',
                'rows' => 3,
            ])
            ->addImage('image', [
                'label' => 'Card image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'required' => 1,
            ])
            ->addLink('link', [
                'label' => 'Card link',
                'required' => 1,
            ])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields(\MCA\Fields\Reusable\get_heading_fields())
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($specific_pages) {
        acf_add_local_field_group($specific_pages->build());
    });
}
