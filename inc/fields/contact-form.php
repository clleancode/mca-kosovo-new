<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $contact_form = new FieldsBuilder('contact_form_fields');

    $contact_form
        ->setLocation('block', '==', 'acf/contact-form')
        ->addTab('Form')
        ->addText('contact_form_title', [
            'label' => 'Form title',
        ])
        ->addText('contact_form_note', [
            'label' => 'Form note',
        ])
        ->addText('contact_form_shortcode', [
            'label' => 'Contact Form 7 shortcode',
            'instructions' => 'Use a Contact Form 7 form with name, email, topic, message and privacy consent fields.',
        ])
        ->addTab('Sidebar')
        ->addText('contact_form_visit_label', [
            'label' => 'Visit label',
        ])
        ->addLink('contact_form_directions_link', [
            'label' => 'Get directions link',
        ])
        ->addText('contact_form_email_label', [
            'label' => 'Email label',
        ])
        ->addText('contact_form_social_title', [
            'label' => 'Social media heading',
            'instructions' => 'Heading displayed above the social media icons.',
        ])
        ->addRepeater('contact_form_social_links', [
            'label' => 'Social media links',
            'layout' => 'row',
            'button_label' => 'Add social media link',
        ])
            ->addText('label', [
                'label' => 'Accessible label',
                'required' => 1,
            ])
            ->addSelect('icon', [
                'label' => 'Social icon',
                'choices' => [
                    'icon-facebook' => 'Facebook',
                    'icon-instagram' => 'Instagram',
                    'icon-linkedin' => 'LinkedIn',
                    'icon-youtube' => 'YouTube',
                ],
                'required' => 1,
            ])
            ->addUrl('url', [
                'label' => 'URL',
                'required' => 1,
            ])
        ->endRepeater()
        ->addText('contact_form_fraud_title', [
            'label' => 'Fraud report title',
        ])
        ->addTextarea('contact_form_fraud_description', [
            'label' => 'Fraud report description',
            'rows' => 3,
        ])
        ->addLink('contact_form_fraud_link', [
            'label' => 'Fraud report link',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($contact_form) {
        acf_add_local_field_group($contact_form->build());
    });
}
