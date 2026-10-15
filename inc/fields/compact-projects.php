<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if (class_exists(FieldsBuilder::class)) {

    $heading_fields = \MCA\Fields\Reusable\get_heading_fields()
        ->modifyField('header_size', [])
        ->modifyField('header_line_height', [])
        ->modifyField('header_color', []);

    $text_fields = \MCA\Fields\Reusable\get_text_fields()
        ->modifyField('text_type', [])
        ->modifyField('text_line_height', [])
        ->modifyField('text_color', []);

    $compact_projects = new FieldsBuilder('compact_projects_fields');

    $compact_projects
        ->setLocation('block', '==', 'acf/compact-projects')

        ->addTab('Content')
            ->addButtonGroup('compact_projects_layout', [
                'label' => 'Number of cards',
                'choices' => ['two' => '2 cards', 'three' => '3 cards'],
                'default_value' => 'three',
                'instructions' => 'Two cards stay expanded. Three cards expand when clicked.',
            ])
            ->addText('compact_projects_label', [
                'label' => 'Eyebrow label',
            ])
            ->addTextarea('compact_projects_title', [
                'label' => 'Section heading',
                'rows'  => 2,
            ])
            ->addTextarea('compact_projects_description', [
                'label' => 'Intro description',
                'rows'  => 3,
            ])

        ->addTab('Projects')
            ->addRepeater('compact_projects_tag_groups', [
                'label' => 'Project card labels and tags',
                'instructions' => 'Choose a project and edit its top label and tags here. Other card details are loaded automatically from the project.',
                'layout' => 'block',
                'max' => 3,
                'button_label' => 'Add project labels',
            ])
                ->addPostObject('project', [
                    'label' => 'Project', 'post_type' => ['project'],
                    'return_format' => 'id', 'required' => 1, 'ui' => 1,
                ])
                ->addText('card_label', [
                    'label' => 'Top label',
                    'instructions' => 'Enter the full label, for example: 01 · ENERGY STORAGE PROJECT. Leave empty to hide the expanded label.',
                ])
                ->addRepeater('tags', [
                    'label' => 'Tags', 'max' => 3, 'layout' => 'table', 'button_label' => 'Add tag',
                ])
                    ->addText('tag', ['label' => 'Tag', 'required' => 1])
                ->endRepeater()
            ->endRepeater()
            ->addMessage('Automatic projects', 'Cards are loaded automatically from published Projects, ordered by Order and then ID. Edit top labels and tags above. Titles and links come from the project; images, descriptions and colors come from its Projects Hero block (with featured image and excerpt supported). The 2-card layout excludes the current project on project pages.')

        ->addTab('Heading')
            ->addFields($heading_fields)

        ->addTab('Text Settings')
            ->addFields($text_fields)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())

        
            ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($compact_projects) {
        acf_add_local_field_group($compact_projects->build());
    });
}
