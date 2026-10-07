<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();
    $text = \MCA\Fields\Reusable\get_text_fields();

    $expected = new FieldsBuilder('expected_fields');

    $expected
        ->setLocation('block', '==', 'acf/expected')
        ->addTab('Content')
        ->addText('expected_label', [
            'label' => 'Badge label',
        ])
        ->addTextarea('expected_title', [
            'label' => 'Title',
            'rows'  => 2,
        ])
        ->addImage('expected_image', [
            'label'         => 'Background image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addRepeater('expected_items', [
            'label'        => 'Impact cards',
            'min'          => 1,
            'max'          => 4,
            'layout'       => 'block',
            'button_label' => 'Add impact card',
        ])
            ->addText('title', [
                'label'    => 'Card title',
                'required' => 1,
            ])
            ->addTextarea('description', [
                'label' => 'Card description',
                'rows'  => 3,
            ])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($expected) {
        acf_add_local_field_group($expected->build());
    });
}