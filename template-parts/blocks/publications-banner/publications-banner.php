<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-publications-banner',
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

$id          = absint(get_field('publications_banner_publication')) ?: get_the_ID();
$title       = get_field('publications_banner_title') ?: get_the_title($id);
$description = get_field('publications_banner_description') ?: wp_strip_all_tags(get_the_excerpt($id));
$cover       = absint(get_field('publications_banner_cover')) ?: get_post_thumbnail_id($id);
$pdf         = get_field('publication_pdf', $id) ?: [];
$project     = absint(get_field('publication_project', $id));
$terms       = get_the_terms($id, 'publication_type');
$terms       = $terms && !is_wp_error($terms) ? $terms : [];
$back        = get_field('publications_banner_back_link');
$back_url    = $back['url'] ?? get_post_type_archive_link('publication');
$url         = get_permalink($id);
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-publications-banner">
            <div class="m-publications-banner__cover">
                <?php
                if ($cover) {
                    echo wp_get_attachment_image($cover, 'large', false, ['loading' => 'eager']);
                }
                ?>
            </div>
            <div class="m-publications-banner__content">
                <?php if ($back_url) : ?>
                    <div class="m-publications-banner__back">
                        <a class="a-text a-text--m h-semibold" href="<?php echo esc_url($back_url); ?>">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <?php echo esc_html($back['title'] ?? __('All resources', 'MCA')); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="m-publications-banner__meta">
                    <?php foreach ($terms as $term) : ?>
                        <span class="a-text a-text--xs h-semibold"><?php echo esc_html($term->name); ?></span>
                    <?php endforeach; ?>
                    <?php if ($project) : ?>
                        <span class="a-text a-text--xs h-semibold is-project"><?php echo esc_html(get_the_title($project)); ?></span>
                    <?php endif; ?>
                    <time class="a-text a-text--xs h-semibold" datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>">
                        <?php echo esc_html(get_the_date('j M Y', $id)); ?>
                    </time>
                    <?php if (!empty($pdf['url'])) : ?>
                        <span class="a-text a-text--xs is-format">PDF</span>
                    <?php endif; ?>
                </div>
                <h1 class="a-heading a-heading--h3 h-white"><?php echo esc_html($title); ?></h1>
                <?php if ($description) : ?>
                    <p class="a-text a-text--xl h-white"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
                <div class="m-publications-banner__actions">
                    <?php if (!empty($pdf['url'])) : ?>
                        <a
                            class="a-text a-text--m h-semibold"
                            href="<?php echo esc_url($pdf['url']); ?>"
                            download
                        >
                            <span>Download PDF</span>
                            <img
                                src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/dark.svg')); ?>"
                                alt=""
                                aria-hidden="true"
                            >
                        </a>
                    <?php endif; ?>
                    <details class="m-publications-banner__share">
                        <summary class="a-text a-text--m">Share</summary>
                        <div>
                            <a
                                class="a-text a-text--m"
                                href="<?php echo esc_url('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url)); ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >LinkedIn</a>
                            <a
                                class="a-text a-text--m"
                                href="<?php echo esc_url('mailto:?subject=' . rawurlencode($title) . '&body=' . rawurlencode($url)); ?>"
                            >Email</a>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</section>