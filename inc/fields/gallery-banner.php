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

    $banner = new FieldsBuilder('gallery_banner_fields');

    $banner
        ->setLocation('block', '==', 'acf/gallery-banner')

        ->addTab('Content')
        ->addText('gallery_banner_title', [
            'label'        => 'Title',
            'default_value' => 'Media & News',
        ])
        ->addTextarea('gallery_banner_description', [
            'label'         => 'Description',
            'default_value' => 'News, stories and updates from the MCC-Kosovo Compact – energy storage, skills, partnerships and the people behind them.',
            'rows'          => 3,
        ])
        ->addText('gallery_banner_search_placeholder', [
            'label'         => 'Search placeholder',
            'default_value' => 'Search news and stories',
        ])

        ->addTab('Heading')
        ->addFields($heading)

        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($banner) {
        acf_add_local_field_group($banner->build());
    });
}
