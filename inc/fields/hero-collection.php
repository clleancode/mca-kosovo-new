<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if (class_exists(FieldsBuilder::class)) {
    $hero = new FieldsBuilder('hero_collection_fields');

    $hero
        ->setLocation('block', '==', 'acf/hero-collection')
        ->addTab('Layout')
        ->addButtonGroup('hero_collection_layout', [
            'label' => 'Hero layout',
            'choices' => [
                'album' => 'Gallery album',
                'vacancy' => 'Single vacancy',
                'integrity' => 'Report fraud',
                'policy' => 'Policy page',
                'vacancies' => 'Job vacancies archive',
            ],
            'default_value' => 'album',
            'layout' => 'horizontal',
        ])
        ->addTab('Gallery album')
        ->addLink('hero_album_back_link', ['label' => 'Back link'])
        ->addText('hero_album_title', ['label' => 'Album title'])
        ->addText('hero_album_category', ['label' => 'Category'])
        ->addText('hero_album_year', ['label' => 'Year'])
        ->addText('hero_album_photo_count', ['label' => 'Photo count'])
        ->addLink('hero_album_slideshow_link', ['label' => 'Start slideshow link'])
        ->addLink('hero_album_share_link', ['label' => 'Share album link'])
        ->addTab('Single vacancy')
        ->addLink('hero_vacancy_back_link', ['label' => 'Back link'])
        ->addText('hero_vacancy_notice', ['label' => 'Notice type'])
        ->addText('hero_vacancy_title', ['label' => 'Title'])
        ->addTab('Report fraud')
        ->addText('hero_integrity_label', ['label' => 'Category label'])
        ->addText('hero_integrity_title', ['label' => 'Title'])
        ->addTextarea('hero_integrity_description', ['label' => 'Description', 'rows' => 3])
        ->addText('hero_integrity_panel_title', ['label' => 'Anonymous panel title'])
        ->addTextarea('hero_integrity_panel_description', ['label' => 'Anonymous panel description', 'rows' => 3])
        ->addTab('Policy page')
        ->addText('hero_policy_title', ['label' => 'Title'])
        ->addRepeater('hero_policy_links', [
            'label' => 'Policy links', 'min' => 1, 'max' => 6, 'layout' => 'table', 'button_label' => 'Add policy link',
        ])
            ->addLink('link', ['label' => 'Policy link'])
        ->endRepeater()
        ->addText('hero_policy_updated_label', ['label' => 'Last updated label'])
        ->addText('hero_policy_updated_date', ['label' => 'Last updated date'])
        ->addTab('Job vacancies archive')
        ->addText('hero_vacancies_title', ['label' => 'Title'])
        ->addTextarea('hero_vacancies_description', ['label' => 'Description', 'rows' => 3])
        ->addRepeater('hero_vacancies_statistics', [
            'label' => 'Statistics', 'min' => 1, 'max' => 4, 'layout' => 'table', 'button_label' => 'Add statistic',
        ])
            ->addText('value', ['label' => 'Value'])
            ->addText('label', ['label' => 'Label'])
        ->endRepeater()
        ->addTab('Heading')
        ->addFields(\MCA\Fields\Reusable\get_heading_fields())
        ->addTab('Spacing and visibility')
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($hero) {
        acf_add_local_field_group($hero->build());
    });
}
