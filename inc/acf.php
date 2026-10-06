<?php

/**
 * ACF Block registrations
 */
function my_acf_init() {
	if( function_exists('acf_register_block') ) {
		
		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_gallery',
		// 	'title'				=> __('Rapturecamps - Gallery'),
		// 	'description'		=> __('A custom Rapturecamps gallery block.'),
		// 	'render_callback'	=> 'acf_gallery_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'gallery', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_video',
		// 	'title'				=> __('Rapturecamps - Video'),
		// 	'description'		=> __('A custom Rapturecamps video block.'),
		// 	'render_callback'	=> 'acf_video_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'video', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_image',
		// 	'title'				=> __('Rapturecamps - Image'),
		// 	'description'		=> __('A custom Rapturecamps image block.'),
		// 	'render_callback'	=> 'acf_image_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'image', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_campbox',
		// 	'title'				=> __('Rapturecamps - Camp Box'),
		// 	'description'		=> __('A custom Rapturecamps camp box block.'),
		// 	'render_callback'	=> 'acf_campbox_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'campbox', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_faqbox',
		// 	'title'				=> __('Rapturecamps - FAQ Box'),
		// 	'description'		=> __('A custom Rapturecamps FAQ box block.'),
		// 	'render_callback'	=> 'acf_faqbox_block_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'faqbox', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_accordion',
		// 	'title'				=> __('Rapturecamps - Accordion'),
		// 	'description'		=> __('A custom Rapturecamps accordion.'),
		// 	'render_callback'	=> 'acf_accordion_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'accordion', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_book_meeting',
		// 	'title'				=> __('Rapturecamps - Book Meeting'),
		// 	'description'		=> __('A custom Rapturecamps book meeting section.'),
		// 	'render_callback'	=> 'acf_book_meeting_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'book_meeting', 'quote' ),
		// ));

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_flexible_form',
		// 	'title'				=> __('Rapturecamps - Flexible Form'),
		// 	'description'		=> __('A custom Rapturecamps flexible form section.'),
		// 	'render_callback'	=> 'acf_flexible_form_render_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'flexible_form', 'quote' ),
		// ));
		
		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_surfcamp_box',
		// 	'title'				=> __('Rapturecamps - Surfcamp box'),
		// 	'description'		=> __('A custom Rapturecamps Surfcamp box.'),
		// 	'render_callback'	=> 'acf_surfcamp_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_box', 'quote' ),
		// ));	
		
		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_info_box',
		// 	'title'				=> __('Rapturecamps - Info box'),
		// 	'description'		=> __('A custom Rapturecamps Surfcamp Info box.'),
		// 	'render_callback'	=> 'acf_surfcamp_info_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_info_box', 'quote' ),
		// ));	

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_review',
		// 	'title'				=> __('Rapturecamps - Review'),
		// 	'description'		=> __('A custom Rapturecamps Surfcamp review box.'),
		// 	'render_callback'	=> 'acf_surfcamp_review_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_review_box', 'quote' ),
		// ));	
		

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_social',
		// 	'title'				=> __('Rapturecamps - Social Media'),
		// 	'description'		=> __('A custom Rapturecamps Surfcamp social media box.'),
		// 	'render_callback'	=> 'acf_surfcamp_social_box_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_social_box', 'quote' ),
		// ));	

		// acf_register_block(array(
		// 	'name'				=> 'rapturecamps_guide',
		// 	'title'				=> __('Rapturecamps - Guide'),
		// 	'description'		=> __('A custom Rapturecamps Surfcamp guide block.'),
		// 	'render_callback'	=> 'acf_surfcamp_guide_callback',
		// 	'category'			=> 'formatting',
		// 	'icon'				=> 'admin-comments',
		// 	'keywords'			=> array( 'surfcamp_guide', 'quote' ),
		// ));	
	}
}
add_action('acf/init', 'my_acf_init');

