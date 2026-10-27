<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-single-hero',
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
$description = get_the_excerpt($post_id);
$image_id = get_post_thumbnail_id($post_id);
$image_caption = $image_id ? wp_get_attachment_caption($image_id) : '';
$posts_page_id = (int) get_option('page_for_posts');
$media_page = $posts_page_id ? get_post($posts_page_id) : get_page_by_path('media');
$media_url = $media_page ? get_permalink($media_page) : home_url('/media/');
$home_label = get_field('single_hero_home_label');
$media_label = get_field('single_hero_media_label');
$news_label = get_field('single_hero_news_label');
$read_time_label = get_field('single_hero_read_time_label');
$share_label = get_field('single_hero_share_label');
$linkedin_label = get_field('single_hero_linkedin_label');
$facebook_label = get_field('single_hero_facebook_label');
$x_label = get_field('single_hero_x_label');
$copy_label = get_field('single_hero_copy_label');
$copy_success = get_field('single_hero_copy_success');
$copy_error = get_field('single_hero_copy_error');
$override_read_time = absint(get_field('single_hero_read_time_override'));
$content = wp_strip_all_tags(get_post_field('post_content', $post_id));
preg_match_all('/[\p{L}\p{N}]+/u', $content, $word_matches);
$word_count = count($word_matches[0] ?? []);
$read_time = $override_read_time ?: max(1, (int) ceil($word_count / 200));
$news_terms = [];

foreach (['post_tag', 'category'] as $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);
    if (!is_array($terms)) {
        continue;
    }

    foreach ($terms as $term) {
        $news_terms[$term->term_id] = $term;
    }
}

$news_terms = array_slice(array_values($news_terms), 0, 2);
$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($post_url);
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($post_url);
$x_url = 'https://twitter.com/intent/tweet?url=' . rawurlencode($post_url) . '&text=' . rawurlencode($post_title);
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-single-hero">
            <div class="m-single-hero__content">
                <nav class="m-single-hero__breadcrumbs e-reveal" data-offset="0" aria-label="Breadcrumb">
                    <ol>
                        <li><a class="a-text a-text--xs" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($home_label); ?></a></li>
                        <li><a class="a-text a-text--xs" href="<?php echo esc_url($media_url); ?>"><?php echo esc_html($media_label); ?></a></li>
                        <li><span class="a-text a-text--xs" aria-current="page"><?php echo esc_html($news_label); ?></span></li>
                    </ol>
                </nav>

                <div class="m-single-hero__meta">
                    <?php foreach ($news_terms as $term_index => $term) : ?>
                        <?php $term_url = get_term_link($term); ?>
                        <?php if (is_wp_error($term_url)) : continue; endif; ?>
                        <span class="m-single-hero__badge e-reveal" data-offset="0" data-delay="<?php echo esc_attr(100 + $term_index * 80); ?>">
                            <a class="a-text a-text--xs" href="<?php echo esc_url($term_url); ?>">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        </span>
                    <?php endforeach; ?>
                    <time class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="260" datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>">
                        <?php echo esc_html(get_the_date('j F Y', $post_id)); ?>
                    </time>
                    <span class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="340"><?php echo esc_html($read_time . ' ' . $read_time_label); ?></span>
                </div>

                <h1 class="a-heading a-heading--h3 e-reveal e-reveal--text" data-offset="0" data-delay="400"><?php echo esc_html($post_title); ?></h1>

                <?php if ($description) : ?>
                    <p class="a-text a-text--xl e-reveal e-reveal--text" data-offset="0" data-delay="600"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <div class="m-single-hero__share">
                    <span class="a-text a-text--xs e-reveal e-reveal--text" data-offset="0" data-delay="750"><?php echo esc_html($share_label); ?></span>
                    <div class="m-single-hero__share-links">
                        <a class="a-text a-text--m e-reveal" data-offset="0" data-delay="830" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($linkedin_label); ?></a>
                        <a class="a-text a-text--m e-reveal" data-offset="0" data-delay="910" href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($facebook_label); ?></a>
                        <a class="a-text a-text--m e-reveal" data-offset="0" data-delay="990" href="<?php echo esc_url($x_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($x_label); ?></a>
                        <button
                            class="a-text a-text--m e-reveal"
                            data-offset="0"
                            data-delay="1070"
                            type="button"
                            data-single-hero-copy="<?php echo esc_url($post_url); ?>"
                            data-copy-success="<?php echo esc_attr($copy_success); ?>"
                            data-copy-error="<?php echo esc_attr($copy_error); ?>"
                        ><?php echo esc_html($copy_label); ?></button>
                    </div>
                    <span class="a-text a-text--xs" data-single-hero-copy-status role="status" aria-live="polite"></span>
                </div>
            </div>

            <?php if ($image_id) : ?>
                <figure class="m-single-hero__image e-reveal e-reveal--image" data-offset="0" data-delay="200">
                    <?php echo wp_get_attachment_image($image_id, 'large', false, [
                        'loading' => 'eager',
                        'fetchpriority' => 'high',
                        'decoding' => 'async',
                    ]); ?>
                    <?php if ($image_caption) : ?>
                        <figcaption class="a-text a-text--xs"><?php echo esc_html($image_caption); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>
        </div>
    </div>
</section>
