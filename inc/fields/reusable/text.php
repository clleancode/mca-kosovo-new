<?php
/**
 * Text Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	function get_text_fields() {
		$textFields = new FieldsBuilder( 'text_fields' );

		$textFields
			->addSelect( 'text_type', [
				'label' => 'Text Type',
				'instructions' => 'Select text type',
				'choices' => [
					'xs'  => '12px',
					's'   => '14px',
					'm'   => '16px',
					'l'   => '18px',
					'xl'  => '20px',
					'xxl' => '24px',
				],
				'allow_null' => true,
				'default_value' => 'm',
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'text_line_height', [
				'label' => 'Text Line Height',
				'instructions' => 'Select text line height',
				'choices' => [
					'3xs'    	=> '1',
					'2xs'     	=> '1.1',
					'xs'    	=> '1.125',
					's'    		=> '1.15',
					'm'     	=> '1.25',
					'l'     	=> '1.5',
					'xl'    	=> '2'
				],
				'allow_null' => true,
				'default_value' => 'l',
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addRadio( 'text_color', [
				'label' => 'Text Color',
				'instructions' => 'Select text color',
				'choices'       => [
					'h-white'          	=> '<span class="header_radio"><i style="background-color: #ffffff "></i> White</span>',
					'h-dark-blue'       => '<span class="header_radio"><i style="background-color: #001F54"></i> Dark Blue</span>',
					'h-blue'            => '<span class="header_radio"><i style="background-color: #4297D1"></i> Blue</span>',
					'h-purple'          => '<span class="header_radio"><i style="background-color: #purple"></i> Purple</span>',
					'h-dark-green'      => '<span class="header_radio"><i style="background-color: #1C585A"></i> Dark Green</span>',
				],
				'allow_null' => false,
				'default_value' => 'h-dorado',
				'layout' => 'horizontal',
				'wrapper' => [
					'width' => '100',
				]
			] );

		return $textFields;
	}
}
