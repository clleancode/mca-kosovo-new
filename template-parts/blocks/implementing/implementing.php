<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-implementing',
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
    'header_tag'       => $tag,
    'header_size'      => $size,
    'header_color'     => $color,
    'text_type'        => $text_size,
    'text_line_height' => $text_height,
] = Helpers\get_block_typography();

$layout     = get_field('implementing_layout');
$show_numbers = (bool) get_field('implementing_show_numbers');
$section_id = sanitize_title(get_field('implementing_section_id') ?: ($block['anchor'] ?? ''));
$title      = get_field('implementing_title');
$item_limit = $layout === 'media' ? 4 : ($layout === 'five-cards' ? 5 : 2);
$items      = array_slice(get_field('implementing_items') ?: [], 0, $item_limit);
$numbered_class = $show_numbers && $layout === 'five-cards' ? ' m-implementing--numbered' : '';
?>
<section
    <?php if ($section_id) : ?>id="<?php echo esc_attr($section_id); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-implementing m-implementing--<?php echo esc_attr($layout); ?><?php echo esc_attr($numbered_class); ?>">
            <?php if ($title) : ?>
                <div class="m-implementing__heading">
                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> s-d-b-xs s-m-b-xs">
                        <?php echo esc_html($title); ?>
                    </<?php echo tag_escape($tag); ?>>
                </div>
            <?php endif; ?>

            <div class="m-implementing__grid">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $tone      = in_array($item['tone'] ?? '', ['white', 'blue', 'purple', 'violet'], true) ? $item['tone'] : '';
                    $image     = $item['image'] ?? 0;
                    $dark      = $image || ($tone && $tone !== 'white');
                    $link      = $item['link'] ?? [];
                    $target    = $link['target'] ?? '';
                    $card_size = ($layout === 'media' && $index > 0) || ($layout === 'five-cards' && $index > 0) ? 'h6' : $size;
                    $featured_card = $layout === 'five-cards' && $index === 0;
                    ?>
                    <article class="m-implementing__card<?php echo $tone ? ' m-implementing__card--' . esc_attr($tone) : ''; ?><?php echo $image ? ' m-implementing__card--image' : ''; ?><?php echo $featured_card ? ' m-implementing__card--featured' : ''; ?>">
                        <?php if ($image) : ?>
                            <div class="m-implementing__image">
                                <?php
                                echo wp_get_attachment_image($image, 'large', false, [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_numbers && $layout === 'five-cards' && $index > 0) : ?>
                            <span class="m-implementing__number a-text a-text--xs" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                        <?php endif; ?>

                        <div class="m-implementing__copy">
                            <h3 class="a-heading a-heading--<?php echo esc_attr($card_size); ?> <?php echo $dark ? 'h-white' : 'h-dark-blue'; ?> s-d-b-xs s-m-b-xs">
                                <?php echo esc_html($item['title'] ?? ''); ?>
                            </h3>

                            <?php if (!empty($item['description'])) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo $dark ? 'h-white' : 'h-dark-blue'; ?> h--<?php echo esc_attr($text_height); ?>">
                                    <?php echo nl2br(esc_html($item['description'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($link['url'])) : ?>
                            <div class="m-implementing__action s-d-t-xs s-m-t-xs">
                                <a
                                    class="a-btn a-btn--white a-btn--smallest"
                                    href="<?php echo esc_url($link['url']); ?>"
                                    target="<?php echo esc_attr($target); ?>"
                                    <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                                >
                                    <span>
                                        <?php echo esc_html($link['title'] ?? ''); ?>
                                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
