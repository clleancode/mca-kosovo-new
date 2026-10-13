<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $single_hero = new FieldsBuilder('single_hero_fields');

    $single_hero
        ->setLocation('block', '==', 'acf/single-hero')
        ->addTab('Reading time')
        ->addNumber('single_hero_read_time_override', [
            'label' => 'Reading time override (minutes)',
            'instructions' => 'Leave empty to calculate reading time from the news content.',
            'min' => 1,
            'step' => 1,
        ])
        ->addTab('Labels')
        ->addText('single_hero_home_label', [
            'label' => 'Home breadcrumb label',
            'default_value' => 'Home',
        ])
        ->addText('single_hero_media_label', [
            'label' => 'Media breadcrumb label',
            'default_value' => 'Media',
        ])
        ->addText('single_hero_news_label', [
            'label' => 'News breadcrumb label',
            'default_value' => 'News',
        ])
        ->addText('single_hero_read_time_label', [
            'label' => 'Reading time label',
            'default_value' => 'min read',
        ])
        ->addText('single_hero_share_label', [
            'label' => 'Share label',
            'default_value' => 'Share',
        ])
        ->addText('single_hero_linkedin_label', [
            'label' => 'LinkedIn label',
            'default_value' => 'LinkedIn',
        ])
        ->addText('single_hero_facebook_label', [
            'label' => 'Facebook label',
            'default_value' => 'Facebook',
        ])
        ->addText('single_hero_x_label', [
            'label' => 'X label',
            'default_value' => 'X',
        ])
        ->addText('single_hero_copy_label', [
            'label' => 'Copy link label',
            'default_value' => 'Copy link',
        ])
        ->addText('single_hero_copy_success', [
            'label' => 'Copy success message',
            'default_value' => 'Link copied',
        ])
        ->addText('single_hero_copy_error', [
            'label' => 'Copy error message',
            'default_value' => 'Could not copy link',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($single_hero) {
        acf_add_local_field_group($single_hero->build());
    });
}
