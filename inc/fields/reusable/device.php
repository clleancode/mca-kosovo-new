<?php
/**
 * Device Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	function get_device_fields() {
		$deviceFields = new FieldsBuilder( 'device_fields' );

		$deviceFields
			->addTab( 'Device' )
            ->addTrueFalse( 'desktop', [
                'label' => 'Desktop',
                'instructions' => 'Enable desktop visibility up to 1024px.',
                'required' => false,
                'default_value' => true,
                'ui' => 1,
                'wrapper' => [
                    'width' => '33.33%',
                ],
            ] )
            ->addTrueFalse( 'tablet', [
                'label' => 'Tablet',
                'instructions' => 'Enable tablet visibility from 768px to 1024px.',
                'required' => false,
                'default_value' => true,
                'ui' => 1,
                'wrapper' => [
                    'width' => '33.33%',
                ],
            ] )
            ->addTrueFalse( 'mobile', [
                'label' => 'Mobile',
                'instructions' => 'Enable mobile visibility below 768px.',
                'required' => false,
                'default_value' => true,
                'ui' => 1,
                'wrapper' => [
                    'width' => '33.33%',
                ],
            ] );

		return $deviceFields;
	}
}