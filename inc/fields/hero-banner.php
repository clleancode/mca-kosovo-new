<?php
/**
 * Hero Banner Block Fields
 */

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists('StoutLogic\AcfBuilder\FieldsBuilder')) {
    $spacingFields = \MCA\Fields\Reusable\get_spacing_fields();
    $deviceFields  = \MCA\Fields\Reusable\get_device_fields();
    $headingFields = \MCA\Fields\Reusable\get_heading_fields();
    $textFields    = \MCA\Fields\Reusable\get_text_fields();

    $heroBannerFields = new FieldsBuilder('hero_banner_fields');

    $heroBannerFields
        ->setLocation('block', '==', 'acf/hero-banner')

        ->addTab('Content')
            ->addButtonGroup('hero_layout', [
                'label' => 'Hero layout',
                'choices' => ['slider' => 'Home slider', 'about' => 'About with statistics'],
                'default_value' => 'slider',
                'instructions' => 'About uses the image, title and description from the first slide.',
            ])
            ->addRepeater('hero_slides', [
                'label'        => 'About content',
                'instructions' => 'About uses the image, title and description from the first item.',
                'conditional_logic' => [[['field' => 'hero_layout', 'operator' => '==', 'value' => 'about']]],
                'required'     => false,
                'min'          => 0,
                'max'          => 10,
                'layout'       => 'block',
                'button_label' => 'Add About content',
            ])
                ->addImage('background_image', [
                    'label'         => 'Background Image',
                    'instructions'  => 'Background image for the About layout.',
                    'required'      => true,
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                    'library'       => 'all',
                ])
                ->addText('title', [
                    'label'        => 'Title',
                    'instructions' => 'Heading for the About layout.',
                    'required'     => true,
                ])
                ->addTextarea('description', [
                    'label'        => 'Description',
                    'instructions' => 'Introduction for the About layout.',
                    'required'     => false,
                    'rows'         => 3,
                ])
            ->endRepeater()

            ->addRelationship('hero_projects', [
                'label' => 'Featured news & projects',
                'conditional_logic' => [[['field' => 'hero_layout', 'operator' => '!=', 'value' => 'about']]],
                'post_type' => ['post', 'project'],
                'filters' => ['search', 'post_type', 'taxonomy'],
                'post_status' => ['publish'],
                'return_format' => 'id',
                'max' => 6,
                'instructions' => 'Choose news and projects together, then drag to reorder. Leave empty for the latest three news/projects. Projects use their Projects Hero description; news uses its excerpt. ',
            ])

        ->addTab('About statistics')
            ->addRepeater('hero_about_statistics', [
                'label' => 'Statistics', 'max' => 4, 'layout' => 'table',
                'button_label' => 'Add statistic',
                'conditional_logic' => [[['field' => 'hero_layout', 'operator' => '==', 'value' => 'about']]],
            ])
                ->addText('value', ['label' => 'Value', 'required' => 1])
                ->addText('caption', ['label' => 'Caption'])
            ->endRepeater()
        ->addFields($spacingFields)

        ->addTab('Heading')
            ->addFields($headingFields)

        ->addTab('Text Settings')
            ->addFields($textFields)

        
            ->addFields($deviceFields);

    add_action('acf/init', function () use ($heroBannerFields) {
        acf_add_local_field_group($heroBannerFields->build());
    });
}
