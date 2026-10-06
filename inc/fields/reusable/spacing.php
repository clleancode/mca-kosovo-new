<?php
/**
 * Spacing Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	function get_spacing_fields() {
		$spacingFields = new FieldsBuilder( 'spacing_fields' );

		$spacingFields
			->addTab( 'Spacing' )
			->addSelect( 'space_mobile_top', [
				'label' => 'Space Mobile Top',
				'instructions' => 'Select spacing for mobile (top).',
				'choices' => [
					'xs'    => '15px',
					's'     => '30px',
					'm'     => '60px',
					'l'     => '90px',
					'xl'    => '120px',
					'2xl'   => '150px',
					'3xl'   => '210px',
					'4xl'   => '240px',
					'5xl'   => '270px',
					'6xl'   => '300px',
				],
				'allow_null' => true,
				'default_value' => null,
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'space_mobile_bottom', [
				'label' => 'Space Mobile Bottom',
				'instructions' => 'Select spacing for mobile (bottom).',
				'choices' => [
					'xs'    => '15px',
					's'     => '30px',
					'm'     => '60px',
					'l'     => '90px',
					'xl'    => '120px',
					'2xl'   => '150px',
					'3xl'   => '210px',
					'4xl'   => '240px',
					'5xl'   => '270px',
					'6xl'   => '300px',
				],
				'allow_null' => true,
				'default_value' => null,
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'space_desktop_top', [
				'label' => 'Space Desktop Top',
				'instructions' => 'Select spacing for desktop (top).',
				'choices' => [
					'xs'    => '15px',
					's'     => '30px',
					'm'     => '60px',
					'l'     => '90px',
					'xl'    => '120px',
					'2xl'   => '150px',
					'3xl'   => '210px',
					'4xl'   => '240px',
					'5xl'   => '270px',
					'6xl'   => '300px',
				],
				'allow_null' => true,
				'default_value' => null,
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'space_desktop_bottom', [
				'label' => 'Space Desktop Bottom',
				'instructions' => 'Select spacing for desktop (bottom).',
				'choices' => [
					'xs'    => '15px',
					's'     => '30px',
					'm'     => '60px',
					'l'     => '90px',
					'xl'    => '120px',
					'2xl'   => '150px',
					'3xl'   => '210px',
					'4xl'   => '240px',
					'5xl'   => '270px',
					'6xl'   => '300px',
				],
				'allow_null' => true,
				'default_value' => null,
				'wrapper' => [
					'width' => '50',
				]
			] );

		return $spacingFields;
	}
}