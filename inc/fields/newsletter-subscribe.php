<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $newsletter = new FieldsBuilder('newsletter_subscribe_fields');

    $newsletter
        ->setLocation('block', '==', 'acf/newsletter-subscribe')
        ->addTab('Newsletter content')
        ->addText('newsletter_subscribe_label', ['label' => 'Eyebrow label'])
        ->addTextarea('newsletter_subscribe_title', ['label' => 'Title', 'rows' => 2])
        ->addTextarea('newsletter_subscribe_description', ['label' => 'Description', 'rows' => 3])
        ->addRepeater('newsletter_subscribe_benefits', [
            'label' => 'Newsletter benefits', 'max' => 6, 'layout' => 'table', 'button_label' => 'Add benefit',
        ])
            ->addText('text', ['label' => 'Benefit text'])
        ->endRepeater()
        ->addTab('Signup form')
        ->addText('newsletter_subscribe_shortcode', [
            'label' => 'Newsletter form shortcode',
            'instructions' => 'Use the shortcode from the connected newsletter form to handle subscriptions.',
        ])
        ->addText('newsletter_subscribe_placeholder', ['label' => 'Email placeholder'])
        ->addLink('newsletter_subscribe_action', ['label' => 'Signup destination and button label'])
        ->addText('newsletter_subscribe_consent', ['label' => 'Privacy consent text'])
        ->addText('newsletter_subscribe_success_title', ['label' => 'Success title'])
        ->addText('newsletter_subscribe_success_description', ['label' => 'Success description'])
        ->addText('newsletter_subscribe_error_message', ['label' => 'Invalid email message'])
        ->addTab('Editorial preview')
        ->addImage('newsletter_subscribe_background', [
            'label' => 'Preview background image', 'return_format' => 'id', 'preview_size' => 'medium',
        ])
        ->addText('newsletter_subscribe_preview_label', ['label' => 'Preview eyebrow'])
        ->addImage('newsletter_subscribe_preview_image', [
            'label' => 'Preview story image', 'return_format' => 'id', 'preview_size' => 'medium',
        ])
        ->addTextarea('newsletter_subscribe_preview_title', ['label' => 'Preview story title', 'rows' => 3])
        ->addTextarea('newsletter_subscribe_preview_description', ['label' => 'Preview story description', 'rows' => 3])
        ->addLink('newsletter_subscribe_preview_link', [
            'label' => 'Read more button link',
            'instructions' => 'Set the button text, URL, and target.',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($newsletter) {
        acf_add_local_field_group($newsletter->build());
    });
}
