<?php
/**
 * Impact Block Fields
 */

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';
require_once get_template_directory() . '/inc/fields/reusable/heading.php';
require_once get_template_directory() . '/inc/fields/reusable/text.php';

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	$spacingFields = \MCA\Fields\Reusable\get_spacing_fields();
	$deviceFields = \MCA\Fields\Reusable\get_device_fields();
	$headingFields = \MCA\Fields\Reusable\get_heading_fields();
	$textFields = \MCA\Fields\Reusable\get_text_fields();

	$impactFields = new FieldsBuilder( 'impact_fields' );

	$impactFields
		->setLocation( 'block', '==', 'acf/impact' )
		->addTab( 'Content' )
			->addText( 'impact_label', [
				'label' => 'Badge label',
			])
			->addTextarea( 'impact_title', [
				'label' => 'Section title',
				'rows' => 2,
			])
			->addTextarea( 'impact_description', [
				'label' => 'Section description',
				'rows' => 3,
			])
		->addTab( 'Impact cards' )
			->addRepeater( 'impact_items', [
				'label' => 'Impact cards',
				'min' => 4,
				'max' => 4,
				'layout' => 'block',
				'button_label' => 'Add impact card',
			])
				->addImage( 'image', [
					'label' => 'Image',
					'required' => true,
					'return_format' => 'id',
					'preview_size' => 'medium',
				])
				->addText( 'title', [
					'label' => 'Title',
					'required' => true,
				])
				->addTextarea( 'description', [
					'label' => 'Description',
					'rows' => 3,
				])
			->endRepeater()
		->addFields( $spacingFields )
		->addTab( 'Heading' )
		->addFields( $headingFields )
		->addTab( 'Text Settings' )
		->addFields( $textFields )
		->addTab( 'Settings' )
		->addFields( $deviceFields );

	add_action( 'acf/init', function() use ( $impactFields ) {
		acf_add_local_field_group( $impactFields->build() );
	} );
}