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
$cover_image = $cover ? wp_get_attachment_image($cover, 'large', false, ['loading' => 'eager']) : '';
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
        <div class="m-publications-banner<?php echo $cover_image ? '' : ' m-publications-banner--no-cover'; ?>">
            <?php if ($cover_image) : ?>
            <div class="m-publications-banner__cover e-reveal e-reveal--image" data-offset="0">
                <?php echo $cover_image; ?>
            </div>
            <?php endif; ?>
            <div class="m-publications-banner__content">
                <?php if ($back_url) : ?>
                    <div class="m-publications-banner__back e-reveal" data-offset="0" data-delay="150">
                        <a class="a-text a-text--m h-semibold" href="<?php echo esc_url($back_url); ?>">
                            <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left-arrow.svg')); ?>" alt="" aria-hidden="true">
                            <?php echo esc_html($back['title'] ?? __('All resources', 'MCA')); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="m-publications-banner__meta">
                    <?php foreach ($terms as $term_index => $term) : ?>
                        <span class="a-text a-text--xs h-semibold e-reveal" data-offset="0" data-delay="<?php echo esc_attr(250 + $term_index * 80); ?>"><?php echo esc_html($term->name); ?></span>
                    <?php endforeach; ?>
                    <?php if ($project) : ?>
                        <span class="a-text a-text--xs h-semibold is-project e-reveal" data-offset="0" data-delay="<?php echo esc_attr(250 + count($terms) * 80); ?>"><?php echo esc_html(get_the_title($project)); ?></span>
                    <?php endif; ?>
                    <time class="a-text a-text--xs h-semibold e-reveal e-reveal--text" data-offset="0" data-delay="<?php echo esc_attr(250 + (count($terms) + ($project ? 1 : 0)) * 80); ?>" datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>">
                        <?php echo esc_html(get_the_date('j M Y', $id)); ?>
                    </time>
                    <?php if (!empty($pdf['url'])) : ?>
                        <span class="a-text a-text--xs is-format e-reveal" data-offset="0" data-delay="<?php echo esc_attr(330 + (count($terms) + ($project ? 1 : 0)) * 80); ?>">PDF</span>
                    <?php endif; ?>
                </div>
                <h1 class="a-heading a-heading--h3 h-white e-reveal e-reveal--text" data-offset="0" data-delay="400"><?php echo esc_html($title); ?></h1>
                <?php if ($description) : ?>
                    <p class="a-text a-text--xl h-white e-reveal e-reveal--text" data-offset="0" data-delay="600"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
                <div class="m-publications-banner__actions">
                    <?php if (!empty($pdf['url'])) : ?>
                        <a
                            class="a-text a-text--m h-semibold e-reveal"
                            data-offset="0"
                            data-delay="750"
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
                    <details class="m-publications-banner__share e-reveal" data-offset="0" data-delay="850">
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
