<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-expected',
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
    'header_tag'         => $tag,
    'header_size'        => $size,
    'header_color'       => $color,
    'header_line_height' => $height,
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography(['text_type' => 's']);

$image = get_field('expected_image');
$items = array_slice(get_field('expected_items') ?: [], 0, 4);
?>
<section id="impact" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <?php if ($image) : ?>
        <div class="m-expected__image">
            <?php echo wp_get_attachment_image($image, 'full', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
        </div>
    <?php endif; ?>
    <div class="container">
        <div class="m-expected__content">
            <div class="m-expected__heading s-m-b-m">
                <p class="a-badge h-blue h-semibold"><?php echo esc_html(get_field('expected_label')); ?></p>
                <<?php echo tag_escape($tag); ?> class="s-d-t-xs a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo nl2br(esc_html(get_field('expected_title'))); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>
            <?php if ($items) : ?>
                <div class="m-expected__grid">
                    <?php foreach ($items as $index => $item) : ?>
                        <article class="m-expected__card">
                            <div class="m-expected__number">
                                <span class="a-text a-text--s h-semibold h-blue"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            </div>
                            <h3 class="a-heading a-heading--h6 h-semibold h-white"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                            <?php if (!empty($item['description'])) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                                    <?php echo nl2br(esc_html($item['description'])); ?>
                                </p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>