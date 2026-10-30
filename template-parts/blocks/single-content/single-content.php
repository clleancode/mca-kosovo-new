<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-single-content',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");

        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$post_id = get_the_ID();
$post_url = get_permalink($post_id);
$post_title = get_the_title($post_id);
$related_project_id = absint(get_field('single_content_related_project'));
$related_project_image_id = $related_project_id ? get_post_thumbnail_id($related_project_id) : 0;
$related_project_url = $related_project_id ? get_permalink($related_project_id) : '';
$related_project_title = $related_project_id ? get_the_title($related_project_id) : '';
$sections = get_field('single_content_sections') ?: [];
$toc_title = get_field('single_content_toc_title');
$related_label = get_field('single_content_related_label');
$related_link_label = get_field('single_content_related_link_label');
$share_label = get_field('single_content_share_label');
$linkedin_label = get_field('single_content_linkedin_label');
$facebook_label = get_field('single_content_facebook_label');
$x_label = get_field('single_content_x_label');
$copy_label = get_field('single_content_copy_label');
$copy_success = get_field('single_content_copy_success');
$copy_error = get_field('single_content_copy_error');
$article_markup = '';
$toc_items = [];
$heading_counts = [];

$add_heading = static function ($heading, $level = 'h2') use (&$toc_items, &$heading_counts) {
    $plain_heading = trim(wp_strip_all_tags((string) $heading));
    if ($plain_heading === '') {
        return '';
    }

    $base_id = sanitize_title($plain_heading) ?: 'article-section';
    $heading_counts[$base_id] = ($heading_counts[$base_id] ?? 0) + 1;
    $heading_id = $heading_counts[$base_id] > 1 ? $base_id . '-' . $heading_counts[$base_id] : $base_id;
    $tag = $level === 'h3' ? 'h3' : 'h2';
    $size = $tag === 'h3' ? 'h6' : 'h5';
    $toc_items[] = ['id' => $heading_id, 'title' => $plain_heading];

    return '<' . $tag . ' id="' . esc_attr($heading_id) . '" class="a-heading a-heading--' . $size . ' e-reveal e-reveal--text">' . nl2br(esc_html($heading)) . '</' . $tag . '>';
};

foreach ($sections as $section) {
    $layout = $section['acf_fc_layout'] ?? '';

    if ($layout === 'compact_about') {
        $about_label = trim((string) ($section['compact_about_label'] ?? ''));
        $about_text = trim((string) ($section['compact_about_text'] ?? ''));
        if ($about_text !== '') {
            $article_markup .= '<div class="m-single-content__compact-about e-reveal">';
            if ($about_label !== '') $article_markup .= '<span class="a-text a-text--xs">' . esc_html($about_label) . '</span>';
            $article_markup .= '<div class="m-single-content__compact-about-text a-text a-text--xl">' . wp_kses_post(wpautop($about_text)) . '</div></div>';
        }
        continue;
    }

    if ($layout === 'text_section') {
        $heading = $section['heading'] ?? '';
        $level = ($section['heading_level'] ?? '') === 'h3' ? 'h3' : 'h2';
        $content = trim((string) ($section['content'] ?? ''));
        $article_markup .= '<div class="m-single-content__section">';
        $article_markup .= $add_heading($heading, $level);
        if ($content !== '') {
            $formatted_content = apply_filters('the_content', $content);
            $formatted_content = preg_replace_callback('/<p\b([^>]*)>/i', static function ($matches) {
                $attributes = $matches[1];
                $global_class = 'a-text a-text--xl';

                if (preg_match('/\bclass\s*=\s*(["\'])(.*?)\1/i', $attributes)) {
                    $attributes = preg_replace_callback('/\bclass\s*=\s*(["\'])(.*?)\1/i', static function ($class_match) use ($global_class) {
                        $existing = preg_split('/\s+/', trim($class_match[2]));
                        $classes = array_unique(array_merge($existing, explode(' ', $global_class)));
                        return 'class=' . $class_match[1] . esc_attr(implode(' ', array_filter($classes))) . $class_match[1];
                    }, $attributes, 1);
                } else {
                    $attributes .= ' class="' . esc_attr($global_class) . '"';
                }

                return '<p' . $attributes . '>';
            }, $formatted_content);
            $article_markup .= '<div class="m-single-content__rich-text e-reveal" data-delay="150">' . $formatted_content . '</div>';
        }
        $article_markup .= '</div>';
        continue;
    }

    if ($layout === 'quote') {
        $quote_text = trim((string) ($section['quote_text'] ?? ''));
        if ($quote_text === '') {
            continue;
        }
        $quote_style = ($section['quote_style'] ?? '') === 'pullquote' ? 'pullquote' : 'card';
        $author_image = absint($section['quote_author_image'] ?? 0);
        $author_name = trim((string) ($section['quote_author_name'] ?? ''));
        $author_role = trim((string) ($section['quote_author_role'] ?? ''));
        $article_markup .= '<figure class="m-single-content__quote m-single-content__quote--' . esc_attr($quote_style) . ' e-reveal">';
        $article_markup .= '<blockquote><p class="a-heading a-heading--h6">' . nl2br(esc_html($quote_text)) . '</p></blockquote>';
        if ($author_name !== '' || $author_role !== '' || $author_image) {
            $article_markup .= '<figcaption class="m-single-content__quote-author">';
            if ($author_image) {
                $article_markup .= wp_get_attachment_image($author_image, 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async']);
            }
            $article_markup .= '<span class="m-single-content__quote-details">';
            if ($author_name !== '') {
                $article_markup .= '<span class="a-text a-text--m">' . esc_html($author_name) . '</span>';
            }
            if ($author_role !== '') {
                $article_markup .= '<span class="a-text a-text--m">' . esc_html($author_role) . '</span>';
            }
            $article_markup .= '</span></figcaption>';
        }
        $article_markup .= '</figure>';
        continue;
    }

    if ($layout === 'list_section') {
        $heading = $section['heading'] ?? '';
        $article_markup .= '<div class="m-single-content__section m-single-content__list-section">';
        $article_markup .= $add_heading($heading, 'h3');
        $is_bullet_list = ($section['list_style'] ?? '') === 'bullets';
        $use_numbered_rows = !$is_bullet_list && !empty($section['numbered_steps_rows']);
        $list_tag = $is_bullet_list ? 'ul' : 'ol';
        $list_class = 'm-single-content__list' . ($use_numbered_rows ? ' m-single-content__list--rows' : '');
        $article_markup .= '<' . $list_tag . ' class="' . esc_attr($list_class) . '">';
        foreach ((array) ($section['items'] ?? []) as $item_index => $item) {
            $item_text = trim((string) ($item['text'] ?? ''));
            if ($item_text !== '') {
                $article_markup .= '<li class="a-text a-text--xl e-reveal" data-delay="' . esc_attr(150 + min($item_index, 6) * 100) . '">' . nl2br(esc_html($item_text)) . '</li>';
            }
        }
        $article_markup .= '</' . $list_tag . '></div>';
        continue;
    }

    if ($layout === 'image') {
        $image_id = absint($section['image'] ?? 0);
        if (!$image_id) {
            continue;
        }
        $caption = trim((string) ($section['caption'] ?? ''));
        $article_markup .= '<figure class="m-single-content__image e-reveal e-reveal--image">' . wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']);
        if ($caption !== '') {
            $article_markup .= '<figcaption class="a-text a-text--m">' . esc_html($caption) . '</figcaption>';
        }
        $article_markup .= '</figure>';
        continue;
    }

    if ($layout === 'video') {
        $video_markup = '';
        if (($section['video_source'] ?? '') === 'upload') {
            $video_file_id = absint($section['video_file'] ?? 0);
            $video_file_url = $video_file_id ? wp_get_attachment_url($video_file_id) : '';
            $poster_id = absint($section['video_poster'] ?? 0);
            $video_args = ['preload' => 'metadata'];
            if ($video_file_url) {
                $video_args['src'] = $video_file_url;
                if ($poster_id) {
                    $video_args['poster'] = wp_get_attachment_image_url($poster_id, 'large');
                }
                $video_markup = wp_video_shortcode($video_args);
            }
        } else {
            $video_markup = trim((string) ($section['video_url'] ?? ''));
        }
        if ($video_markup === '') {
            continue;
        }
        $caption = trim((string) ($section['caption'] ?? ''));
        $allowed_video_html = wp_kses_allowed_html('post');
        $allowed_video_html['iframe'] = [
            'src' => true,
            'width' => true,
            'height' => true,
            'frameborder' => true,
            'allow' => true,
            'allowfullscreen' => true,
            'title' => true,
            'loading' => true,
            'referrerpolicy' => true,
        ];
        $allowed_video_html['video'] = [
            'class' => true,
            'id' => true,
            'controls' => true,
            'preload' => true,
            'width' => true,
            'height' => true,
            'poster' => true,
        ];
        $allowed_video_html['source'] = [
            'src' => true,
            'type' => true,
        ];
        $article_markup .= '<figure class="m-single-content__video e-reveal"><div>' . wp_kses($video_markup, $allowed_video_html) . '</div>';
        if ($caption !== '') {
            $article_markup .= '<figcaption class="a-text a-text--m">' . esc_html($caption) . '</figcaption>';
        }
        $article_markup .= '</figure>';
        continue;
    }

    if ($layout === 'gallery') {
        $heading = $section['heading'] ?? '';
        $images = array_values(array_filter(array_map('absint', (array) ($section['images'] ?? []))));
        if (!$images) {
            continue;
        }
        $photos_label = trim((string) ($section['photos_label'] ?? ''));
        $gallery_followup_text = trim((string) ($section['gallery_followup_text'] ?? ''));
        $compact_about_label = trim((string) ($section['compact_about_label'] ?? ''));
        $compact_about_text = trim((string) ($section['compact_about_text'] ?? ''));
        $gallery_tags = array_values(array_filter(array_map(static function ($tag_row) {
            return trim((string) ($tag_row['tag'] ?? ''));
        }, (array) ($section['gallery_tags'] ?? []))));
        $article_markup .= '<div class="m-single-content__gallery-section">';
        $article_markup .= '<div class="m-single-content__gallery-heading">' . $add_heading($heading, 'h3');
        $article_markup .= '<span class="a-text a-text--xs e-reveal" data-delay="150">' . esc_html(count($images) . ' ' . $photos_label) . '</span></div>';
        $article_markup .= '<div class="m-single-content__gallery">';
        foreach (array_slice($images, 0, 3) as $index => $image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $gallery_image_class = 'm-single-content__gallery-image m-single-content__gallery-image--' . ($index + 1) . ' e-reveal e-reveal--image';
            if ($index === 2 && count($images) > 3) {
                $gallery_image_class .= ' m-single-content__gallery-image--more';
            }
            $article_markup .= '<a class="' . esc_attr($gallery_image_class) . '" data-delay="' . esc_attr($index * 150) . '" href="' . esc_url($image_url) . '" target="_blank" rel="noopener">';
            $article_markup .= wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']);
            if ($index === 2 && count($images) > 3) {
                $article_markup .= '<span class="m-single-content__gallery-more a-heading a-heading--h2">+' . esc_html((string) (count($images) - 3)) . '</span>';
            }
            $article_markup .= '</a>';
        }
        $article_markup .= '</div><div class="m-single-content__gallery-thumbs">';
        foreach ($images as $thumb_index => $image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $article_markup .= '<a class="e-reveal e-reveal--image" data-delay="' . esc_attr(300 + min($thumb_index, 8) * 80) . '" href="' . esc_url($image_url) . '" target="_blank" rel="noopener">' . wp_get_attachment_image($image_id, 'thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async']) . '</a>';
        }
        $article_markup .= '</div></div>';
        if ($gallery_followup_text !== '') {
            $article_markup .= '<p class="a-text a-text--xl m-single-content__gallery-followup e-reveal e-reveal--text">' . nl2br(esc_html($gallery_followup_text)) . '</p>';
        }
        if ($compact_about_label !== '' || $compact_about_text !== '') {
            $article_markup .= '<div class="m-single-content__compact-about e-reveal">';
            if ($compact_about_label !== '') {
                $article_markup .= '<span class="a-text a-text--xs">' . esc_html($compact_about_label) . '</span>';
            }
            if ($compact_about_text !== '') {
                $article_markup .= '<p class="a-text a-text--xl">' . nl2br(esc_html($compact_about_text)) . '</p>';
            }
            $article_markup .= '</div>';
        }
        if ($gallery_tags) {
            $article_markup .= '<ul class="m-single-content__tags">';
            foreach ($gallery_tags as $tag_index => $tag) {
                $article_markup .= '<li class="a-text a-text--m e-reveal" data-delay="' . esc_attr(100 + $tag_index * 80) . '">' . esc_html($tag) . '</li>';
            }
            $article_markup .= '</ul>';
        }
    }
}

$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($post_url);
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($post_url);
$x_url = 'https://twitter.com/intent/tweet?url=' . rawurlencode($post_url) . '&text=' . rawurlencode($post_title);
$copy_id = 'single-content-copy-' . sanitize_html_class($block['id'] ?? wp_unique_id());
if (!$toc_items && get_post_type($post_id) === 'post' && trim(wp_strip_all_tags($article_markup)) !== '') {
    $overview_id = 'article-overview-' . sanitize_html_class($block['id'] ?? (string) $post_id);
    $toc_items[] = ['id' => $overview_id, 'title' => __('Overview', 'mca')];
    $article_markup = '<div id="' . esc_attr($overview_id) . '">' . $article_markup . '</div>';
}
?>
<?php
// Document-only procurements do not need an empty article and share column.
if (get_post_type($post_id) === 'procurement' && has_block('acf/documents', $post_id)) {
    $article_text = preg_replace('/[\s\x{00A0}]+/u', '', html_entity_decode(wp_strip_all_tags($article_markup), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $has_media = preg_match('/<(?:img|video|audio|iframe|embed|object)\b/i', $article_markup);
    if ($article_text === '' && !$has_media) return;
}
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-single-content">
            <aside class="m-single-content__share" aria-label="<?php echo esc_attr($share_label); ?>">
                <span class="a-text a-text--xs e-reveal"><?php echo esc_html($share_label); ?></span>
                <a class="a-text a-text--m e-reveal" data-delay="100" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($linkedin_label); ?>">in</a>
                <a class="a-text a-text--m e-reveal" data-delay="200" href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($facebook_label); ?>">f</a>
                <a class="a-text a-text--m e-reveal" data-delay="300" href="<?php echo esc_url($x_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($x_label); ?>">x</a>
                <button class="a-text a-text--m e-reveal" data-delay="400" type="button" id="<?php echo esc_attr($copy_id); ?>" data-single-content-copy="<?php echo esc_url($post_url); ?>" data-copy-success="<?php echo esc_attr($copy_success); ?>" data-copy-error="<?php echo esc_attr($copy_error); ?>" aria-label="<?php echo esc_attr($copy_label); ?>">⧉</button>
                <span class="a-text a-text--xs" data-single-content-copy-status role="status" aria-live="polite"></span>
            </aside>

            <article class="m-single-content__article">
                <?php echo $article_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </article>

            <aside class="m-single-content__sidebar">
                <?php if ($toc_items) : ?>
                    <nav class="m-single-content__toc e-reveal" data-delay="250" aria-label="<?php echo esc_attr($toc_title); ?>">
                        <span class="a-text a-text--xs"><?php echo esc_html($toc_title); ?></span>
                        <ul>
                            <?php foreach ($toc_items as $index => $toc_item) : ?>
                                <li><a class="a-text a-text--l" href="#<?php echo esc_attr($toc_item['id']); ?>" data-single-content-toc-link<?php if ($index === 0) : ?> aria-current="location"<?php endif; ?>><?php echo esc_html($toc_item['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

                <?php if ($related_project_id && $related_project_url && $related_project_title) : ?>
                    <a class="m-single-content__project e-reveal" data-delay="400" href="<?php echo esc_url($related_project_url); ?>">
                        <?php if ($related_project_image_id) : ?>
                            <?php echo wp_get_attachment_image($related_project_image_id, 'medium_large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php endif; ?>
                        <span class="m-single-content__project-copy">
                            <span class="a-text a-text--xs"><?php echo esc_html($related_label); ?></span>
                            <span class="a-heading a-heading--h3"><?php echo esc_html($related_project_title); ?></span>
                            <span class="a-text a-text--m"><?php echo esc_html($related_link_label); ?> <span aria-hidden="true">→</span></span>
                        </span>
                    </a>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
