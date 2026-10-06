<?php
/**
 * Register the MCA Hero Banner block.
 */

namespace MCA\Blocks;

add_action( 'acf/init', function () {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( [
		'name'            => 'hero-banner',
		'title'           => __( 'MCA - Hero Banner', 'MCA' ),
		'description'     => __( 'A custom MCA theme hero banner block with swiper slider.', 'MCA' ),
		'render_template' => get_template_directory() . '/template-parts/blocks/hero/hero-banner.php',
		'category'        => 'formatting',
		'icon'            => 'format-image',
		'keywords'        => [ 'hero', 'banner', 'slider', 'swiper' ],
		'mode'            => 'preview',
		'supports'        => [
			'align'           => true,
			'anchor'          => true,
			'customClassName' => true,
		],
	] );
} );
