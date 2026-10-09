<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-gallery-slider',
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

[
    'header_tag' => $tag,
    'header_size' => $size,
    'header_color' => $color,
    'header_line_height' => $height,
] = Helpers\get_block_typography(['header_size' => 'h5', 'header_color' => 'h-dark-blue']);

$items = array_values(array_filter(
    get_field('gallery_slider_items') ?: [],
    static function ($item) {
        return !empty($item['image']);
    }
));
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-gallery-slider
>
    <div class="container s-d-b-xs">
        <div class="m-gallery-slider__header s-d-b-s s-m-b-xs">
            <div class="m-gallery-slider__heading">
                <p class="a-badge h-dark-blue h-semibold"><?php echo esc_html(get_field('gallery_slider_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html(get_field('gallery_slider_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <?php if (count($items) > 1) : ?>
                <div class="m-gallery-slider__navigation">
                    <button class="m-gallery-slider__previous" type="button" aria-label="Previous image">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/left.svg')); ?>" alt="" aria-hidden="true">
                    </button>
                    <button class="m-gallery-slider__next" type="button" aria-label="Next image">
                        <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/right.svg')); ?>" alt="" aria-hidden="true">
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($items) : ?>
        <div class="swiper m-gallery-slider__swiper" role="region" aria-label="Compact photo gallery">
            <div class="swiper-wrapper">
                <?php foreach ($items as $item) : ?>
                    <?php $layout = ($item['layout'] ?? '') === 'portrait' ? 'portrait' : 'landscape'; ?>
                    <div class="swiper-slide m-gallery-slider__slide m-gallery-slider__slide--<?php echo esc_attr($layout); ?>">
                        <figure class="m-gallery-slider__figure">
                            <div class="m-gallery-slider__image">
                                <?php echo wp_get_attachment_image($item['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            </div>
                            <figcaption class="a-text a-text--xs"><?php echo esc_html($item['caption'] ?? ''); ?></figcaption>
                        </figure>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>