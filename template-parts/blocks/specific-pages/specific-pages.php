<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-specific-pages',
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
] = Helpers\get_block_typography([
    'header_color' => 'h-dark-blue',
]);

$title = get_field('specific_pages_title');
$items = array_slice(get_field('specific_pages_items') ?: [], 0, 4);

if (!$items) {
    return;
}
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-specific-pages">
            <?php if ($title) : ?>
                <div class="m-specific-pages__header">
                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?>">
                        <?php echo esc_html($title); ?>
                    </<?php echo tag_escape($tag); ?>>
                </div>
            <?php endif; ?>

            <div class="m-specific-pages__grid">
                <?php foreach ($items as $item) : ?>
                    <?php
                    $card_title = $item['title'] ?? '';
                    $card_description = $item['description'] ?? '';
                    $card_image = $item['image'] ?? null;
                    $card_link = $item['link'] ?? [];
                    $image_id = is_array($card_image) ? absint($card_image['ID'] ?? $card_image['id'] ?? 0) : absint($card_image);
                    ?>
                    <article class="m-specific-pages__card">
                        <?php if (!empty($card_link['url'])) : ?>
                            <a class="m-specific-pages__link" href="<?php echo esc_url($card_link['url']); ?>" target="<?php echo esc_attr($card_link['target'] ?? ''); ?>" <?php if (($card_link['target'] ?? '') === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>>
                        <?php else : ?>
                            <div class="m-specific-pages__link">
                        <?php endif; ?>
                            <?php if ($image_id) : ?>
                                <div class="m-specific-pages__image">
                                    <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                                </div>
                            <?php else : ?>
                                <div class="m-specific-pages__image"></div>
                            <?php endif; ?>
                            <div class="m-specific-pages__copy">
                                <?php if ($card_title) : ?>
                                    <h3 class="a-heading a-heading--h5"><?php echo esc_html($card_title); ?></h3>
                                <?php endif; ?>
                                <?php if ($card_description) : ?>
                                    <p class="a-text a-text--m"><?php echo esc_html($card_description); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($card_link['title'])) : ?>
                                    <div class="m-specific-pages__action">
                                        <span class="a-text a-text--m">
                                            <?php echo esc_html($card_link['title']); ?>
                                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php if (!empty($card_link['url'])) : ?>
                            </a>
                        <?php else : ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
