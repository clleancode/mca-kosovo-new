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
                'instructions' => 'Show above 1024px viewport width using CSS.',
                'required' => false,
                'default_value' => true,
                'ui' => 1,
                'wrapper' => [
                    'width' => '33.33%',
                ],
            ] )
            ->addTrueFalse( 'tablet', [
                'label' => 'Tablet',
                'instructions' => 'Show from 768px to 1024px viewport width using CSS.',
                'required' => false,
                'default_value' => true,
                'ui' => 1,
                'wrapper' => [
                    'width' => '33.33%',
                ],
            ] )
            ->addTrueFalse( 'mobile', [
                'label' => 'Mobile',
                'instructions' => 'Show below 768px viewport width using CSS.',
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
