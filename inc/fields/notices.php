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