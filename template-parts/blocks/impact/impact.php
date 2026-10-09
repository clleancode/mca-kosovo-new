<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();
if (!$visibility['show']) return;

$classes = ['o-impact', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) $classes[] = "s-{$prefix}-{$suffix}-{$space}";
    }
}

$label = get_field('impact_label') ?: 'Impact';
$title = get_field('impact_title') ?: '';
$description = get_field('impact_description') ?: '';
$items = array_slice(get_field('impact_items') ?: [], 0, 4);

[
    'header_tag' => $heading_tag,
    'header_size' => $heading_size,
    'header_color' => $heading_color,
    'header_line_height' => $heading_height,
    'text_type' => $text_type,
    'text_color' => $text_color,
    'text_line_height' => $text_height,
] = Helpers\get_block_typography(['header_color' => 'h-dark-blue', 'text_color' => 'h-dark-blue']);
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-impact">

            <div class="m-impact__intro s-d-b-l s-m-b-s">
                <div class="m-impact__heading">
                    <p class="a-badge h-dark-blue"><?php echo esc_html($label); ?></p>
                    <<?php echo tag_escape($heading_tag); ?> class="a-heading a-heading--<?php echo esc_attr($heading_size); ?> <?php echo esc_attr($heading_color); ?> h--<?php echo esc_attr($heading_height); ?>"><?php echo nl2br(esc_html($title)); ?></<?php echo tag_escape($heading_tag); ?>>
                </div>
                <div class="m-impact__description">
                    <p class="a-text a-text--<?php echo esc_attr($text_type); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($description)); ?></p>
                </div>
            </div>

            <?php if ($items): ?>
                <div class="m-impact__grid">
                    <?php foreach ($items as $index => $item): ?>
                        <article class="m-impact__card">
                            <div class="m-impact__image">
                                <?php if (!empty($item['image'])) echo wp_get_attachment_image($item['image'], 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?>
                            </div>

                            <div class="m-impact__number s-d-t-xs s-d-b-xs s-m-t-xs s-m-b-xs">
                                <span class="a-text a-text--xs h-semibold"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                            </div>

                            <div class="m-impact__card-title">
                                <h3 class="a-heading a-heading--h6 h-dark-blue h-semibold"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                            </div>

                            <?php if (!empty($item['description'])): ?>
                                <div class="m-impact__card-description s-d-t-s s-m-t-xs">
                                    <p class="a-text a-text--<?php echo esc_attr($text_type); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>"><?php echo nl2br(esc_html($item['description'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>