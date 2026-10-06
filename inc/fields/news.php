<?php
/**
 * News Block Fields
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

	$newsFields = new FieldsBuilder( 'news_fields' );

	$newsFields
		->setLocation( 'block', '==', 'acf/news' )
		->addTab( 'Content' )
			->addText( 'news_label', [
				'label' => 'Badge label',
			])
			->addText( 'news_title', [
				'label' => 'Title',
			])
		->addTab( 'News settings' )
			->addPostObject( 'news_featured_post', [
				'label' => 'Featured story (optional)',
				'instructions' => 'Leave empty to feature the latest post.',
				'post_type' => ['post'],
				'return_format' => 'id',
				'allow_null' => 1,
			])
			->addTaxonomy( 'news_categories', [
				'label' => 'Category filters',
				'taxonomy' => 'category',
				'field_type' => 'multi_select',
				'return_format' => 'id',
				'add_term' => 0,
				'load_terms' => 0,
				'save_terms' => 0,
			])
			->addLink( 'news_archive_link', [
				'label' => 'All news link',
			])
		->addTab( 'Newsletter' )
			->addText( 'news_newsletter_label', [
				'label' => 'Label',
			])
			->addText( 'news_newsletter_title', [
				'label' => 'Title',
			])
			->addText( 'news_newsletter_shortcode', [
				'label' => 'Form shortcode',
				'instructions' => 'Enter the shortcode of your connected newsletter form.',
			])
			->addLink( 'news_newsletter_link', [
				'label' => 'Newsletter link',
				'instructions' => 'Used when no form shortcode is set.',
			])
		->addFields( $spacingFields )
		->addTab( 'Heading' )
		->addFields( $headingFields )
		->addTab( 'Text Settings' )
		->addFields( $textFields )
		->addTab( 'Settings' )
		->addFields( $deviceFields );

	add_action( 'acf/init', function() use ( $newsFields ) {
		acf_add_local_field_group( $newsFields->build() );
	} );
}