<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if ( ! class_exists( FieldsBuilder::class ) ) {
    return;
}

$fields = new FieldsBuilder( 'benefits_fields' );

$fields
    ->setLocation( 'block', '==', 'acf/benefits' )

    ->addTab( 'Content' )
    ->addSelect( 'benefits_variant', [
        'label'         => 'Variant',
        'choices'       => [
            'citizen'  => 'Citizen benefits',
            'business' => 'Business benefits',
        ],
        'return_format' => 'value',
    ] )
    ->addText( 'benefits_label', [
        'label'        => 'Label (optional)',
        'instructions' => 'Defaults to the selected variant.',
    ] )
    ->addText( 'benefits_title', [
        'label' => 'Heading',
    ] )
    ->addTextarea( 'benefits_description', [
        'label' => 'Introduction',
        'rows'  => 3,
    ] )
    ->addImage( 'benefits_image', [
        'label'         => 'Image',
        'return_format' => 'id',
        'required'      => true,
    ] )

    ->addTab( 'Cards' )
    ->addRepeater( 'benefits_cards', [
        'label'        => 'Benefit cards',
        'min'          => 4,
        'max'          => 4,
        'layout'       => 'block',
        'instructions' => 'The first card uses the highlighted background for the selected variant.',
    ] )
        ->addText( 'badge', [
            'label' => 'Badge',
        ] )
        ->addSelect( 'badge_color', [
            'label'         => 'Badge color',
            'choices'       => [
                'blue'   => 'Blue',
                'purple' => 'Purple',
                'teal'   => 'Teal',
            ],
            'return_format' => 'value',
        ] )
        ->addText( 'title', [
            'label'    => 'Title',
            'required' => true,
        ] )
        ->addTextarea( 'description', [
            'label' => 'Description',
            'rows'  => 3,
        ] )
        ->addLink( 'link', [
            'label' => 'Link',
        ] )
    ->endRepeater()

    ->addTab( 'Notice' )
    ->addTextarea( 'benefits_notice', [
        'label' => 'Notice (optional)',
        'rows'  => 2,
    ] )
    ->addLink( 'benefits_notice_link', [
        'label' => 'Notice link (Subscribe)',
    ] )

    ->addFields( \MCA\Fields\Reusable\get_spacing_fields() )
    ->addFields( \MCA\Fields\Reusable\get_device_fields() );

add_action( 'acf/init', function () use ( $fields ) {
    acf_add_local_field_group( $fields->build() );
} );