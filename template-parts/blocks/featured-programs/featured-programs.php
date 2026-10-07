<?php
use MCA\Helpers;
$visibility = Helpers\rapture_get_device_visibility();
if (!$visibility['show']) return;
$classes = ['o-featured-programs', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) $classes[] = "s-{$prefix}-{$suffix}-{$space}";
    }
}
[
    'header_tag' => $tag, 'header_size' => $size, 'header_color' => $color,
    'header_line_height' => $height, 'text_type' => $text_size,
    'text_color' => $text_color, 'text_line_height' => $text_height,
] = Helpers\get_block_typography(['header_color' => 'h-dark-blue', 'text_color' => 'h-dark-blue']);
$image = get_field('featured_programs_image');
$description = get_field('featured_programs_description');
$feature_description = get_field('featured_programs_feature_description');
$items = array_slice(get_field('featured_programs_items') ?: [], 0, 4);
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-featured-programs">
            <div class="m-featured-programs__intro s-d-b-m s-m-b-s">
                <div class="m-featured-programs__heading">
                    <p class="a-badge h-purple h-semibold"><?php echo esc_html(get_field('featured_programs_label')); ?></p>
                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>"><?php echo nl2br(esc_html(get_field('featured_programs_title'))); ?></<?php echo tag_escape($tag); ?>>
                </div>
                <?php if ($description): ?><div class="m-featured-programs__description"><p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($description)); ?></p></div><?php endif; ?>
            </div>
            <div class="m-featured-programs__layout">
                <article class="m-featured-programs__feature">
                    <?php if ($image) echo wp_get_attachment_image($image, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                    <div class="m-featured-programs__feature-copy">
                        <h3 class="a-heading a-heading--h5 h-white"><?php echo nl2br(esc_html(get_field('featured_programs_feature_title'))); ?></h3>
                        <?php if ($feature_description): ?><p class="a-text a-text--<?php echo esc_attr($text_size); ?> h-white h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($feature_description)); ?></p><?php endif; ?>
                    </div>
                </article>
                <?php if ($items): ?>
                    <div class="m-featured-programs__grid">
                        <?php foreach ($items as $index => $item): ?>
                            <article class="m-featured-programs__card">
                                <div class="m-featured-programs__image"><?php if (!empty($item['image'])) echo wp_get_attachment_image($item['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?></div>
                                <div class="m-featured-programs__card-copy">
                                    <div class="m-featured-programs__number"><span class="a-text a-text--xs h-purple h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span></div>
                                    <h3 class="a-text a-text--xxl h-dark-blue h-semibold"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                                    <?php if (!empty($item['description'])): ?><p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($item['description'])); ?></p><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
