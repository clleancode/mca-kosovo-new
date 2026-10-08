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
            ->addRepeater('hero_slides', [
                'label'        => 'Hero Slides',
                'instructions' => 'Add hero slides for the swiper slider.',
                'required'     => true,
                'min'          => 1,
                'max'          => 10,
                'layout'       => 'block',
                'button_label' => 'Add Slide',
            ])
                ->addImage('background_image', [
                    'label'         => 'Background Image',
                    'instructions'  => 'Upload the background image for this slide.',
                    'required'      => true,
                    'return_format' => 'id',
                    'preview_size'  => 'medium',
                    'library'       => 'all',
                ])
                ->addText('title', [
                    'label'        => 'Title',
                    'instructions' => 'Enter the main title for this slide.',
                    'required'     => true,
                ])
                ->addTextarea('description', [
                    'label'        => 'Description',
                    'instructions' => 'Enter the description text for this slide.',
                    'required'     => false,
                    'rows'         => 3,
                ])
                ->addLink('button_link', [
                    'label'        => 'Button Link',
                    'instructions' => 'Enter the URL for the button.',
                    'required'     => false,
                ])
            ->endRepeater()

        ->addTab('Hero Card')
            ->addTrueFalse('show_last_vacancy', [
                'label'        => 'Show Last Vacancy Instead of News',
                'instructions' => 'If checked, the hero card will display the latest vacancy. Otherwise, you can select a specific news post below.',
                'ui'           => 1,
            ])
            ->addRelationship('selected_news', [
                'label'             => 'Select News Post',
                'instructions'      => 'Select a news post to display in the hero card. Leave empty to show the latest news automatically.',
                'post_type'         => ['post'],
                'filters'           => ['search', 'post_type', 'taxonomy'],
                'max'               => 1,
                'return_format'     => 'object',
                'conditional_logic' => [
                    [
                        [
                            'field'    => 'show_last_vacancy',
                            'operator' => '!=',
                            'value'    => 1,
                        ],
                    ],
                ],
            ])

        ->addFields($spacingFields)

        ->addTab('Heading')
            ->addFields($headingFields)

        ->addTab('Text Settings')
            ->addFields($textFields)

        ->addTab('Settings')
            ->addFields($deviceFields);

    add_action('acf/init', function () use ($heroBannerFields) {
        acf_add_local_field_group($heroBannerFields->build());
    });
}