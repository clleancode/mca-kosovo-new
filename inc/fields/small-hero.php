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

    $hero = new FieldsBuilder('small_page_hero_fields');

    $hero
        ->setLocation('block', '==', 'acf/small-hero')

        ->addTab('Content')
        ->addImage('small_page_hero_image', [
            'label'         => 'Background image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addText('small_page_hero_title', [
            'label'        => 'Title',
            'instructions' => 'Leave empty to use the current page title.',
        ])
        ->addTextarea('small_page_hero_description', [
            'label' => 'Description',
            'rows'  => 4,
        ])
        ->addRepeater('small_page_hero_links', [
            'label'        => 'Useful links',
            'max'          => 3,
            'layout'       => 'block',
            'button_label' => 'Add link',
        ])
            ->addLink('link', [
                'label'    => 'Link',
                'required' => 1,
            ])
            ->addText('description', [
                'label' => 'Short description',
            ])
        ->endRepeater()

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        ->addTab('Settings')
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($hero) {
        acf_add_local_field_group($hero->build());
    });
}