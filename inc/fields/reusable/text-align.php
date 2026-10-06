<?php
/**
 * Text Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	function get_textAlign_fields() {
		$textAlign = new FieldsBuilder( 'text_align' );

		$textAlign
        ->addSelect( 'text_align', [
            'label' => 'Text Alignment',
            'instructions' => 'Select alignment of the text.',
            'choices' => [
                'h-text-left'       => 'Left',
                'h-text-center'     => 'Center',
                'h-text-right'      => 'Right',
            ],
            'allow_null' => true,
            'default_value' => null,
            'wrapper' => [
                'width' => '100',
            ]
        ] );

		return $textAlign;
	}
}