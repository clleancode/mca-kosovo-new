<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $related_programs = new FieldsBuilder('related_programs_fields');

    $related_programs
        ->setLocation('block', '==', 'acf/related-programs')

        ->addTab('Content')
        ->addText('related_programs_title', [
            'label' => 'Section title',
        ])
        ->addText('related_programs_note', [
            'label' => 'Text beside title',
        ])

        ->addTab('Heading')
        ->addFields(\MCA\Fields\Reusable\get_heading_fields())
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($related_programs) {
        acf_add_local_field_group($related_programs->build());
    });
}
