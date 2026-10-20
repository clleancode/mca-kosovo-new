<?php
namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';

if ( ! class_exists( FieldsBuilder::class ) ) {
    return;
}

$fields = new FieldsBuilder( 'benefit_banner_fields' );

$fields
    ->setLocation( 'block', '==', 'acf/benefit-banner' )

    ->addTab( 'Content' )
    ->addText( 'benefit_banner_breadcrumb', [
        'label' => 'Breadcrumb label',
    ] )
    ->addText( 'benefit_banner_title', [
        'label' => 'Title',
    ] )
    ->addTextarea( 'benefit_banner_description', [
        'label' => 'Description',
        'rows'  => 3,
    ] )
    ->addLink( 'benefit_banner_scroll_link', [
        'label'        => 'Bottom arrow link',
        'instructions' => 'Use a section anchor such as #benefits to scroll down.',
    ] )
    ->addRepeater( 'benefit_banner_cards', [
        'label'        => 'Benefit panels',
        'min'          => 2,
        'max'          => 2,
        'layout'       => 'block',
        'instructions' => 'First panel: Citizen Benefits. Second panel: Business Benefits.',
    ] )
        ->addImage( 'image', [
            'label'         => 'Background image',
            'return_format' => 'id',
            'required'      => true,
        ] )
        ->addText( 'label', [
            'label' => 'Label (For people / For organizations)',
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
            'label' => 'Benefits link',
        ] )
    ->endRepeater()

    ->addTab( 'Heading' )
    ->addFields( \MCA\Fields\Reusable\get_heading_fields() )
    ->addFields( \MCA\Fields\Reusable\get_spacing_fields() )
    ->addFields( \MCA\Fields\Reusable\get_device_fields() );

add_action( 'acf/init', function () use ( $fields ) {
    acf_add_local_field_group( $fields->build() );
} );
