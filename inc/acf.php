<?php

/**
 * ACF Block registrations
 */
function my_acf_init() {
	if( function_exists('acf_register_block') ) {
		
		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_gallery',
		// 	'title'				=> __('MCAcamps - Gallery'),
		// 	'description'		=> __('A custom MCAcamps gallery block.'),
		// 	'render_callback'	=> 'acf_gallery_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'gallery', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_video',
		// 	'title'				=> __('MCAcamps - Video'),
		// 	'description'		=> __('A custom MCAcamps video block.'),
		// 	'render_callback'	=> 'acf_video_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'video', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_image',
		// 	'title'				=> __('MCAcamps - Image'),
		// 	'description'		=> __('A custom MCAcamps image block.'),
		// 	'render_callback'	=> 'acf_image_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'image', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_campbox',
		// 	'title'				=> __('MCAcamps - Camp Box'),
		// 	'description'		=> __('A custom MCAcamps camp box block.'),
		// 	'render_callback'	=> 'acf_campbox_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'campbox', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_faqbox',
		// 	'title'				=> __('MCAcamps - FAQ Box'),
		// 	'description'		=> __('A custom MCAcamps FAQ box block.'),
		// 	'render_callback'	=> 'acf_faqbox_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'faqbox', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_accordion',
		// 	'title'				=> __('MCAcamps - Accordion'),
		// 	'description'		=> __('A custom MCAcamps accordion.'),
		// 	'render_callback'	=> 'acf_accordion_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'accordion', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_book_meeting',
		// 	'title'				=> __('MCAcamps - Book Meeting'),
		// 	'description'		=> __('A custom MCAcamps book meeting section.'),
		// 	'render_callback'	=> 'acf_book_meeting_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'book_meeting', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_flexible_form',
		// 	'title'				=> __('MCAcamps - Flexible Form'),
		// 	'description'		=> __('A custom MCAcamps flexible form section.'),
		// 	'render_callback'	=> 'acf_flexible_form_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'flexible_form', 'quote' ),
		// ));
		
		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_surfcamp_box',
		// 	'title'				=> __('MCAcamps - Surfcamp box'),
		// 	'description'		=> __('A custom MCAcamps Surfcamp box.'),
		// 	'render_callback'	=> 'acf_surfcamp_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_box', 'quote' ),
		// ));	
		
		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_info_box',
		// 	'title'				=> __('MCAcamps - Info box'),
		// 	'description'		=> __('A custom MCAcamps Surfcamp Info box.'),
		// 	'render_callback'	=> 'acf_surfcamp_info_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_info_box', 'quote' ),
		// ));	

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_review',
		// 	'title'				=> __('MCAcamps - Review'),
		// 	'description'		=> __('A custom MCAcamps Surfcamp review box.'),
		// 	'render_callback'	=> 'acf_surfcamp_review_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_review_box', 'quote' ),
		// ));	
		

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_social',
		// 	'title'				=> __('MCAcamps - Social Media'),
		// 	'description'		=> __('A custom MCAcamps Surfcamp social media box.'),
		// 	'render_callback'	=> 'acf_surfcamp_social_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_social_box', 'quote' ),
		// ));	

		// acf_register_block(array(
		// 	'name'				=> 'mcacamps_guide',
		// 	'title'				=> __('MCAcamps - Guide'),
		// 	'description'		=> __('A custom MCAcamps Surfcamp guide block.'),
		// 	'render_callback'	=> 'acf_surfcamp_guide_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_guide', 'quote' ),
		// ));	
	}
}
add_action('acf/init', 'my_acf_init');

