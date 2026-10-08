<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $heading = \MCA\Fields\Reusable\get_heading_fields();

    $join = new FieldsBuilder('join_fields');

    $join
        ->setLocation('block', '==', 'acf/join')
        ->addTab('Content')
        ->addText('join_label', [
            'label' => 'Eyebrow label',
        ])
        ->addTextarea('join_title', [
            'label' => 'Title',
            'rows'  => 3,
        ])
        ->addImage('join_image', [
            'label'         => 'Background image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addLink('join_contact_link', [
            'label' => 'Contact button',
        ])
        ->addLink('join_subscribe_link', [
            'label' => 'Subscribe button',
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($join) {
        acf_add_local_field_group($join->build());
    });
}