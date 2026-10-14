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

    $hero = new FieldsBuilder('careers_hero_fields');

    $hero
        ->setLocation('block', '==', 'acf/careers-hero')
        ->addTab('Content')
        ->addTextarea('careers_hero_title', [
            'label' => 'Title',
            'rows'  => 2,
        ])
        ->addTextarea('careers_hero_description', [
            'label' => 'Description',
            'rows'  => 3,
        ])
        ->addLink('careers_hero_primary_link', [
            'label' => 'Primary button (View vacancies)',
        ])
        ->addLink('careers_hero_secondary_link', [
            'label' => 'Secondary button (Get job alerts)',
        ])
        ->addTab('Team collage')
        ->addRepeater('careers_hero_photos', [
            'label'        => 'Fallback team photos',
            'min'          => 0,
            'max'          => 6,
            'layout'       => 'table',
            'button_label' => 'Add photo',
            'instructions' => 'The collage automatically selects up to six random published Persons with a Person photo. These photos are only used when no Persons photos are available. Manage portraits under Persons in the admin menu.',
        ])
            ->addImage('image', [
                'label'         => 'Photo',
                'required'      => 1,
                'return_format' => 'id',
                'preview_size'  => 'medium',
            ])
        ->endRepeater()
        ->addTextarea('careers_hero_statistic', [
            'label' => 'Collage label',
            'rows'  => 2,
        ])
        ->addTextarea('careers_hero_statistic_caption', [
            'label' => 'Collage caption',
            'rows'  => 2,
        ])
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
