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

    $faq = new FieldsBuilder('faq_fields');

    $faq
        ->setLocation('block', '==', 'acf/faq')

        ->addTab('Content')
            ->addText('faq_section_id', [
                'label'        => 'Section ID',
                'instructions' => 'Without #, for example faq.',
            ])
            ->addText('faq_label', [
                'label' => 'Badge label',
            ])
            ->addTextarea('faq_title', [
                'label' => 'Title',
                'rows'  => 2,
            ])
            ->addTextarea('faq_description', [
                'label' => 'Contact introduction',
                'rows'  => 2,
            ])
            ->addEmail('faq_email', [
                'label' => 'Contact email',
            ])
            ->addTrueFalse('faq_open_first', [
                'label' => 'Open the first question initially',
                'ui'    => 1,
            ])

        ->addTab('Questions')
            ->addRepeater('faq_items', [
                'label'        => 'Questions and answers',
                'min'          => 1,
                'layout'       => 'block',
                'button_label' => 'Add question',
            ])
                ->addText('question', [
                    'label'    => 'Question',
                    'required' => 1,
                ])
                ->addTextarea('answer', [
                    'label'    => 'Answer',
                    'rows'     => 5,
                    'required' => 1,
                ])
            ->endRepeater()

        ->addTab('Heading')
            ->addFields($heading)

        ->addTab('Text Settings')
            ->addFields($text)

        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($faq) {
        acf_add_local_field_group($faq->build());
    });
}