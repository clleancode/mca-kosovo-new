<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-gallery',
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

$source = absint(get_field('gallery_source')) ?: get_the_ID();
$photos = get_field('gallery_photos') ?: [];

if (!$photos) {
    foreach ((array) get_field('images', $source) as $row) {
        if (is_array($row) && !empty($row['image'])) {
            $photos[] = $row['image'];
        }
    }
}

$photos = array_values(array_filter(array_map(static function ($image) {
    return absint(is_array($image) ? ($image['ID'] ?? 0) : $image);
}, $photos)));

$caption = (string) get_field('gallery_caption');
$group   = wp_unique_id('mca-gallery-');
$columns = $photos ? array_chunk($photos, (int) ceil(count($photos) / 3)) : [];

if (!$photos && empty($is_preview)) {
    return;
}
?>
<section
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
>
    <div class="container">
        <div class="m-gallery">
            <?php foreach ($columns as $column) : ?>
                <div class="m-gallery__column">
                    <?php
                    foreach ($column as $photo) :
                        $full = wp_get_attachment_image_url($photo, 'full');

                        if (!$full) {
                            continue;
                        }

                        $photo_caption  = '<span>' . esc_html(wp_get_attachment_caption($photo) ?: $caption) . '</span>';
                        $photo_caption .= '<a href="' . esc_url($full) . '" download>' . esc_html__('Download', 'MCA') . '</a>';
                        ?>
                        <div class="m-gallery__photo">
                            <a
                                href="<?php echo esc_url($full); ?>"
                                data-fancybox="<?php echo esc_attr($group); ?>"
                                data-mca-gallery
                                data-caption="<?php echo esc_attr($photo_caption); ?>"
                                data-download-src="<?php echo esc_url($full); ?>"
                                data-thumb="<?php echo esc_url(wp_get_attachment_image_url($photo, 'medium')); ?>"
                                aria-label="<?php echo esc_attr('Open photo: ' . (get_post_meta($photo, '_wp_attachment_image_alt', true) ?: get_the_title($photo))); ?>"
                            >
                                <?php echo wp_get_attachment_image($photo, 'large', false, ['loading' => 'lazy']); ?>
                            </a>
                            <div class="m-gallery__expand" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 4h6v6M20 4l-7 7M10 20H4v-6M4 20l7-7"/>
                                </svg>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>