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

    $overview = new FieldsBuilder('overview_fields');

    $overview
        ->setLocation('block', '==', 'acf/overview')
        ->addTab('Navigation')
        ->addText('overview_project_name', [
            'label' => 'Project name',
        ])
        ->addText('overview_navigation_label', [
            'label' => 'Overview navigation label',
        ])
        ->addRepeater('overview_navigation', [
            'label'        => 'Other project sections',
            'layout'       => 'table',
            'button_label' => 'Add section',
            'instructions' => 'Add Activities, Sites, Impact, Progress, Updates or Documents. Use a link such as #activities and set the matching HTML anchor on that section.',
        ])
            ->addLink('link', [
                'label'    => 'Section link',
                'required' => 1,
            ])
        ->endRepeater()
        ->addLink('overview_factsheet', [
            'label' => 'Project factsheet (PDF)',
            'conditional_logic' => [[[
                'field'    => 'overview_show_sort',
                'operator' => '!=',
                'value'    => '1',
            ]]],
        ])
        ->addTrueFalse('overview_show_sort', [
            'label'         => 'Show sort instead of media link',
            'instructions'  => 'Turn on to display a sort label in the navigation bar. Turn off to use the project factsheet link.',
            'default_value' => 0,
            'ui'            => 1,
        ])
        ->addText('overview_sort_label', [
            'label'             => 'Sort label',
            'default_value'     => 'Sort: Newest first',
            'conditional_logic' => [[[
                'field'    => 'overview_show_sort',
                'operator' => '==',
                'value'    => '1',
            ]]],
        ])
        ->addTab('Content')
        ->addText('overview_label', [
            'label' => 'Badge label',
        ])
        ->addTextarea('overview_title', [
            'label' => 'Title',
            'rows'  => 2,
        ])
        ->addTextarea('overview_description', [
            'label' => 'Description',
            'rows'  => 4,
        ])
        ->addRepeater('overview_benefits', [
            'label'        => 'Benefits',
            'layout'       => 'block',
            'button_label' => 'Add benefit',
        ])
            ->addImage('icon', [
                'label'         => 'Icon image',
                'return_format' => 'id',
                'preview_size'  => 'thumbnail',
            ])
            ->addText('title', [
                'label'    => 'Title',
                'required' => 1,
            ])
            ->addText('description', [
                'label' => 'Description',
            ])
        ->endRepeater()
        ->addTab('Images and statistic')
        ->addImage('overview_main_image', [
            'label'         => 'Main image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addImage('overview_detail_image', [
            'label'         => 'Overlapping image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
        ])
        ->addText('overview_statistic', [
            'label' => 'Statistic value',
        ])
        ->addButtonGroup('overview_statistic_color', [
            'label' => 'Statistic background color',
            'choices' => ['blue' => 'Blue', 'purple' => 'Purple', 'green' => 'Green'],
            'default_value' => 'blue',
        ])
        ->addText('overview_unit', [
            'label' => 'Statistic unit',
        ])
        ->addText('overview_statistic_label', [
            'label' => 'Statistic caption',
        ])
        ->addTab('Heading')
        ->addFields($heading)
        ->addTab('Text Settings')
        ->addFields($text)
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($overview) {
        acf_add_local_field_group($overview->build());
    });
}
