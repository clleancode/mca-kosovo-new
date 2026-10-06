<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $gallery = new FieldsBuilder('gallery_slider_fields');

    $gallery
        ->setLocation('block', '==', 'acf/gallery-slider')
        ->addTab('Content')
        ->addText('gallery_slider_label', [
            'label' => 'Badge label',
        ])
        ->addText('gallery_slider_title', [
            'label' => 'Title',
        ])
        ->addRepeater('gallery_slider_items', [
            'label'        => 'Gallery images',
            'min'          => 1,
            'layout'       => 'block',
            'button_label' => 'Add image',
        ])
            ->addImage('image', [
                'label'         => 'Image',
                'required'      => true,
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
            ->addText('caption', [
                'label' => 'Caption',
            ])
            ->addSelect('layout', [
                'label'   => 'Image proportions',
                'choices' => [
                    'landscape' => 'Landscape',
                    'portrait'  => 'Portrait',
                ],
            ])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($gallery) {
        acf_add_local_field_group($gallery->build());
    });
}