<?php
/**
 * Icons Fields
 *
 * @package MCA
 */

namespace MCA\Fields\Reusable;

use StoutLogic\AcfBuilder\FieldsBuilder;

function get_icons_fields() {
	if ( ! class_exists( FieldsBuilder::class ) ) {
		return null;
	}

	$iconFields = new FieldsBuilder( 'icon_fields' );

	$iconFields
		->addTab( 'Icon' )
		->addSelect( 'icon', [
			'label'         => 'Select Icon Type',
			'instructions'  => 'Select icon type',
			'choices'       => [
				'icon-arrow-right'    => 'Arrow Right',
				'icon-filter'         => 'Filter',
				'icon-arrow-left-down'=> 'Arrow Left Down',
				'icon-arrow-right-up' => 'Arrow Right Up',
				'icon-cable'          => 'Cable',
				'icon-attach'         => 'Attach',
				'icon-pdf'            => 'PDF',
				'icon-search'         => 'Search',
				'icon-youtube'        => 'YouTube',
				'icon-instagram'      => 'Instagram',
				'icon-linkedin'       => 'LinkedIn',
				'icon-facebook'       => 'Facebook',
				'icon-message'        => 'Message',
			],
			'allow_null'    => false,
			'default_value' => '',
			'wrapper'       => [
				'width' => '50%',
			],
		]);

	return $iconFields;
}
