<?php

namespace MCA\Fields\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

require_once get_template_directory() . '/inc/fields/reusable/spacing.php';
require_once get_template_directory() . '/inc/fields/reusable/device.php';

if (class_exists(FieldsBuilder::class)) {
    $single_content = new FieldsBuilder('single_content_fields');

    $single_content
        ->setLocation('block', '==', 'acf/single-content')
        ->addMessage(
            'Article sections',
            'Build the complete article below. Add text, quote, list, image, video and gallery sections in the order they should appear.'
        )
        ->addTab('Article content')
        ->addFlexibleContent('single_content_sections', [
            'label' => 'Article sections',
            'button_label' => 'Add article section',
            'layout' => 'block',
        ])
            ->addLayout('text_section', ['label' => 'Text section'])
                ->addTextarea('heading', [
                    'label' => 'Heading',
                    'rows' => 2,
                ])
                ->addSelect('heading_level', [
                    'label' => 'Heading size',
                    'choices' => ['h2' => 'Section heading', 'h3' => 'Subheading'],
                    'default_value' => 'h2',
                ])
                ->addWysiwyg('content', [
                    'label' => 'Text',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                ])
            ->addLayout('quote', ['label' => 'Quote'])
                ->addSelect('quote_style', [
                    'label' => 'Quote style',
                    'choices' => ['card' => 'Quote card', 'pullquote' => 'Pull quote'],
                    'default_value' => 'card',
                ])
                ->addTextarea('quote_text', [
                    'label' => 'Quote',
                    'rows' => 4,
                    'required' => 1,
                ])
                ->addImage('quote_author_image', [
                    'label' => 'Author photo',
                    'return_format' => 'id',
                    'preview_size' => 'thumbnail',
                ])
                ->addText('quote_author_name', ['label' => 'Author name'])
                ->addText('quote_author_role', ['label' => 'Author title and organization'])
            ->addLayout('list_section', ['label' => 'List'])
                ->addTextarea('heading', ['label' => 'Heading', 'rows' => 2])
                ->addSelect('list_style', [
                    'label' => 'List style',
                    'choices' => ['numbered' => 'Numbered steps', 'bullets' => 'Bullet points'],
                    'default_value' => 'numbered',
                ])
                ->addTrueFalse('numbered_steps_rows', [
                    'label' => 'Use horizontal numbered rows',
                    'instructions' => 'Turn on for purple numbers with divider lines. Turn off to use numbered circles.',
                    'default_value' => 0,
                    'ui' => 1,
                    'conditional_logic' => [[[
                        'field' => 'list_style',
                        'operator' => '==',
                        'value' => 'numbered',
                    ]]],
                ])
                ->addRepeater('items', [
                    'label' => 'List items',
                    'layout' => 'row',
                    'button_label' => 'Add item',
                    'min' => 1,
                ])
                    ->addTextarea('text', ['label' => 'Text', 'rows' => 2, 'required' => 1])
                ->endRepeater()
            ->addLayout('image', ['label' => 'Image'])
                ->addImage('image', [
                    'label' => 'Image',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'required' => 1,
                ])
                ->addTextarea('caption', ['label' => 'Caption', 'rows' => 2])
            ->addLayout('video', ['label' => 'Video'])
                ->addButtonGroup('video_source', [
                    'label' => 'Video source',
                    'choices' => ['embed' => 'YouTube / Vimeo', 'upload' => 'WordPress Media'],
                    'default_value' => 'embed',
                    'layout' => 'horizontal',
                ])
                ->addOembed('video_url', [
                    'label' => 'YouTube or Vimeo URL',
                    'conditional_logic' => [[[
                        'field' => 'video_source',
                        'operator' => '==',
                        'value' => 'embed',
                    ]]],
                ])
                ->addFile('video_file', [
                    'label' => 'Video file from Media Library',
                    'instructions' => 'Upload or select an MP4, WebM, or Ogg video.',
                    'return_format' => 'id',
                    'library' => 'all',
                    'mime_types' => 'mp4,webm,ogv,ogg',
                    'conditional_logic' => [[[
                        'field' => 'video_source',
                        'operator' => '==',
                        'value' => 'upload',
                    ]]],
                ])
                ->addImage('video_poster', [
                    'label' => 'Video poster image (optional)',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'conditional_logic' => [[[
                        'field' => 'video_source',
                        'operator' => '==',
                        'value' => 'upload',
                    ]]],
                ])
                ->addTextarea('caption', ['label' => 'Caption', 'rows' => 2])
            ->addLayout('gallery', ['label' => 'Gallery'])
                ->addTextarea('heading', ['label' => 'Gallery heading', 'rows' => 2])
                ->addGallery('images', [
                    'label' => 'Gallery photos',
                    'return_format' => 'id',
                    'preview_size' => 'medium',
                    'min' => 1,
                ])
                ->addText('photos_label', [
                    'label' => 'Text after photo count',
                    'default_value' => 'photos - Click to enlarge',
                ])
                ->addTextarea('gallery_followup_text', [
                    'label' => 'Text below gallery',
                    'rows' => 3,
                ])
                ->addText('compact_about_label', [
                    'label' => 'Compact information label',
                    'default_value' => 'About the MCC-Kosovo Compact',
                ])
                ->addTextarea('compact_about_text', [
                    'label' => 'Compact information text',
                    'rows' => 4,
                ])
                ->addRepeater('gallery_tags', [
                    'label' => 'Article tags',
                    'layout' => 'table',
                    'button_label' => 'Add tag',
                ])
                    ->addText('tag', ['label' => 'Tag'])
                ->endRepeater()
        ->endFlexibleContent()
        ->addTab('Sidebar')
        ->addText('single_content_toc_title', [
            'label' => 'Table of contents title',
            'default_value' => 'In this article',
        ])
        ->addPostObject('single_content_related_project', [
            'label' => 'Related project',
            'post_type' => ['project'],
            'return_format' => 'id',
            'allow_null' => 1,
        ])
        ->addText('single_content_related_label', [
            'label' => 'Related project label',
            'default_value' => 'Related project',
        ])
        ->addText('single_content_related_link_label', [
            'label' => 'Related project link label',
            'default_value' => 'Explore the project',
        ])
        ->addTab('Share')
        ->addText('single_content_share_label', [
            'label' => 'Share label',
            'default_value' => 'Share',
        ])
        ->addText('single_content_linkedin_label', [
            'label' => 'LinkedIn accessibility label',
            'default_value' => 'Share on LinkedIn',
        ])
        ->addText('single_content_facebook_label', [
            'label' => 'Facebook accessibility label',
            'default_value' => 'Share on Facebook',
        ])
        ->addText('single_content_x_label', [
            'label' => 'X accessibility label',
            'default_value' => 'Share on X',
        ])
        ->addText('single_content_copy_label', [
            'label' => 'Copy link accessibility label',
            'default_value' => 'Copy article link',
        ])
        ->addText('single_content_copy_success', [
            'label' => 'Copy success message',
            'default_value' => 'Link copied',
        ])
        ->addText('single_content_copy_error', [
            'label' => 'Copy error message',
            'default_value' => 'Could not copy link',
        ])
        ->addFields(\MCA\Fields\Reusable\get_spacing_fields())
        ->addFields(\MCA\Fields\Reusable\get_device_fields());

    add_action('acf/init', function () use ($single_content) {
        acf_add_local_field_group($single_content->build());
    });
}
