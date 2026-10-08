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

    $updates = new FieldsBuilder('project_updates_fields');

    $updates
        ->setLocation('block', '==', 'acf/project-updates')
        ->addTab('Updates')
        ->addText('project_updates_label', [
            'label' => 'Badge label',
        ])
        ->addText('project_updates_title', [
            'label'        => 'Title',
            'instructions' => 'Leave empty to show Latest from followed by the current project title.',
        ])
        ->addRelationship('project_updates_posts', [
            'label'         => 'Project procurements',
            'post_type'     => ['procurement'],
            'return_format' => 'id',
            'max'           => 3,
            'filters'       => ['search'],
            'instructions'  => 'Select procurements for this project. Leave empty to show the three latest published procurements.',
        ])
        ->addLink('project_updates_archive_link', [
            'label' => 'All project news link',
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($updates) {
        acf_add_local_field_group($updates->build());
    });
}