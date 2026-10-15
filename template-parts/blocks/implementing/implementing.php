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

$layout       = get_field('implementing_layout');
$show_numbers = (bool) get_field('implementing_show_numbers');
$section_id   = sanitize_title(get_field('implementing_section_id') ?: ($block['anchor'] ?? ''));
$title        = get_field('implementing_title');
$label        = get_field('implementing_label');
$item_limit   = $layout === 'media' ? 4 : ($layout === 'five-cards' ? 5 : 2);
$items        = array_slice(get_field('implementing_items') ?: [], 0, $item_limit);

$numbered_class = $show_numbers && $layout === 'five-cards' ? ' m-implementing--numbered' : '';
?>
<section
    <?php if ($section_id) : ?>
        id="<?php echo esc_attr($section_id); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-implementing m-implementing--<?php echo esc_attr($layout); ?><?php echo esc_attr($numbered_class); ?>">

            <?php if ($title || $label) : ?>
                <div class="m-implementing__heading">
                    <?php if ($label) : ?>
                        <p class="a-badge h-dark-blue s-d-b-s s-m-b-xs h-semibold">
                            <?php echo esc_html($label); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($title) : ?>
                        <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> s-d-b-xs s-m-b-xs">
                            <?php echo esc_html($title); ?>
                        </<?php echo tag_escape($tag); ?>>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="m-implementing__grid">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $tone             = in_array($item['tone'] ?? '', ['white', 'blue', 'purple', 'violet'], true) ? $item['tone'] : '';
                    $image            = $item['image'] ?? 0;
                    $dark             = $image || ($tone && $tone !== 'white');
                    $link             = $item['link'] ?? [];
                    $target           = $link['target'] ?? '';
                    $card_size        = ($layout === 'media' && $index > 0) || ($layout === 'five-cards' && $index > 0) ? 'h6' : $size;
                    $featured_card    = $layout === 'five-cards' && $index === 0;
                    $text_color_class = $dark ? 'h-white' : 'h-dark-blue';
                    $media_download = $layout === 'media' && $tone === 'purple';
                    $media_plain_link = $layout === 'media' && ($tone === 'purple' || ($tone === 'white' && !$image));

                    $card_classes = ['m-implementing__card'];

                    if ($tone) {
                        $card_classes[] = 'm-implementing__card--' . $tone;
                    }

                    if ($image) {
                        $card_classes[] = 'm-implementing__card--image';
                    }

                    if ($featured_card) {
                        $card_classes[] = 'm-implementing__card--featured';
                    }

                    if ($layout === 'five-cards' && $index > 0) {
                        $item_title_class = 'a-text a-text--xxl';
                    } else {
                        $item_title_class = 'a-heading a-heading--' . ($layout === 'five-cards' ? 'h5' : $card_size);
                    }
                    ?>
                    <article class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">

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
                            <div class="m-implementing__number" aria-hidden="true">
                                <span class="a-text a-text--xs">
                                    <?php echo esc_html(sprintf('%02d', $index + 1)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="m-implementing__copy">
                            <h3 class="<?php echo esc_attr($item_title_class); ?> <?php echo esc_attr($text_color_class); ?> s-d-b-xs s-m-b-xs h-semibold">
                                <?php echo esc_html($item['title'] ?? ''); ?>
                            </h3>

                            <?php if (!empty($item['description'])) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color_class); ?> h--<?php echo esc_attr($text_height); ?>">
                                    <?php echo wp_kses(nl2br($item['description']), ['br' => []]); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($link['url'])) : ?>
                            <div class="m-implementing__action s-d-t-xs s-m-t-xs">
                                <a
                                    class="<?php echo $media_plain_link ? 'm-implementing__download a-text a-text--m' : 'a-btn a-btn--white a-btn--smallest'; ?>"
                                    href="<?php echo esc_url($link['url']); ?>"
                                    target="<?php echo esc_attr($target); ?>"
                                    <?php if ($target === '_blank') : ?>
                                        rel="noopener noreferrer"
                                    <?php endif; ?>
                                >
                                    <span>
                                        <?php echo esc_html($link['title'] ?? ''); ?>
                                        <?php if ($media_download) : ?>
                                            <span aria-hidden="true">&#8595;</span>
                                        <?php elseif ($layout === 'media' && $index === 0) : ?>
                                            <span aria-hidden="true">&#8594;</span>
                                        <?php elseif ($layout === 'media' && $index === 3) : ?>
                                            <?php // Contact action uses its label without an arrow. ?>
                                        <?php else : ?>
                                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                        <?php endif; ?>
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
