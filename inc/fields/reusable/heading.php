<?php
/**
 * Heading Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

if ( class_exists( 'StoutLogic\AcfBuilder\FieldsBuilder' ) ) {
	function get_heading_fields() {
		$headingFields = new FieldsBuilder( 'heading_fields' );

		$headingFields
			->addSelect( 'header_tag', [
				'label' => 'Header Tag',
				'instructions' => 'Select header tag',
				'choices' => [
					'h1'    => 'Header 1',
					'h2'    => 'Header 2',
					'h3'    => 'Header 3',
					'h4'    => 'Header 4',
					'h5'    => 'Header 5',
					'h6'    => 'Header 6',
					'p'     => 'Paragraph'
				],
				'default_value' => 'h2',
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'header_size', [
				'label' => 'Header Size',
				'instructions' => 'Select header size',
				'choices' => [
					'h1' => '110px',
					'h2' => '88px',
					'h3' => '64px',
					'h4' => '52px',
					'h5' => '44px',
					'h6' => '30px',
				],
				'allow_null' => false,
				'default_value' => '',
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addSelect( 'header_line_height', [
				'label' => 'Header Line Height',
				'instructions' => 'Select header line height',
				'choices' => [
					'3xs'    	=> '1',
					'2xs'     	=> '1.1',
					'xs'    	=> '1.125',
					's'    		=> '1.15',
					'm'     	=> '1.25',
					'ls'        => '1.4',
					'l'     	=> '1.5',
					'xl'    	=> '2'
				],
				'allow_null' => false,
				'default_value' => 'l',
				'wrapper' => [
					'width' => '50',
				]
			] )
			->addRadio( 'header_color', [
				'label'         => 'Header Color',
				'instructions'  => 'Select header color',
				'choices'       => [
					'h-white'          	=> '<span class="header_radio"><i style="background-color: #ffffff "></i> White</span>',
					'h-dark-blue'       => '<span class="header_radio"><i style="background-color: #001F54"></i> Dark Blue</span>',
					'h-blue'            => '<span class="header_radio"><i style="background-color: #4297D1"></i> Blue</span>',
					'h-purple'          => '<span class="header_radio"><i style="background-color: #purple"></i> Purple</span>',
					'h-dark-green'      => '<span class="header_radio"><i style="background-color: #1C585A"></i> Dark Green</span>',
				],
				'allow_null'    => true,
				'default_value' => 'h-dorado',
				'layout'        => 'horizontal',
				'return_format' => 'value',
				'wrapper'       => [
					'width' => '50',
				],
			]);

		return $headingFields;
	}
}
