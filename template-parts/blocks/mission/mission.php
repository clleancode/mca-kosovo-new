<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-mission',
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
] = Helpers\get_block_typography();

$layout      = get_field('mission_layout');
$section_id  = sanitize_title(get_field('mission_section_id') ?: ($block['anchor'] ?? ''));
$items       = array_slice(get_field('mission_items') ?: [], 0, 3);
$label       = get_field('mission_label');
$description = get_field('mission_description');
?>
<section
    <?php if ($section_id) : ?>id="<?php echo esc_attr($section_id); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-mission m-mission--<?php echo esc_attr($layout); ?>">
            <div class="m-mission__intro s-d-b-s s-m-b-xs">
                <div class="m-mission__heading">
                    <?php if ($layout === 'mission' && $label) : ?>
                        <p class="a-badge h-dark-blue s-d-b-xs s-m-b-xs"><?php echo esc_html($label); ?></p>
                    <?php endif; ?>

                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                        <?php echo esc_html(get_field('mission_title')); ?>
                    </<?php echo tag_escape($tag); ?>>
                </div>

                <?php if ($description) : ?>
                    <div class="m-mission__description">
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo nl2br(esc_html($description)); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="m-mission__grid">
                <?php foreach ($items as $index => $item) : ?>
                    <?php
                    $tone   = in_array($item['tone'] ?? '', ['blue', 'purple', 'green'], true) ? $item['tone'] : '';
                    $link   = $item['link'] ?? [];
                    $target = $link['target'] ?? '';
                    ?>
                    <article class="m-mission__card<?php echo $tone ? ' m-mission__card--' . esc_attr($tone) : ''; ?>">
                        <div class="m-mission__image">
                            <?php
                            if (!empty($item['image'])) {
                                echo wp_get_attachment_image($item['image'], 'large', false, [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]);
                            }
                            ?>
                        </div>

                        <?php if ($layout === 'mission') : ?>
                            <div class="m-mission__number">
                                <span class="a-heading a-heading--h2 h-white"><?php echo esc_html($index + 1); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="m-mission__copy">
                            <?php if (!empty($item['label'])) : ?>
                                <div class="m-mission__label">
                                    <span class="h-semibold a-text a-text--xs<?php echo $layout === 'news' ? ' h-white' : ' h-dark-blue'; ?>">
                                        <?php echo esc_html($item['label']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <h3 class="a-heading a-heading--h5 h-white s-d-b-xs s-m-b-xs">
                                <?php echo esc_html($item['title'] ?? ''); ?>
                            </h3>

                            <?php if (!empty($item['description'])) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> h-white h--<?php echo esc_attr($text_height); ?>">
                                    <?php echo nl2br(esc_html($item['description'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($link['url'])) : ?>
                            <div class="m-mission__link">
                                <a
                                    class="a-mission-link"
                                    href="<?php echo esc_url($link['url']); ?>"
                                    target="<?php echo esc_attr($target); ?>"
                                    aria-label="<?php echo esc_attr(($link['title'] ?? '') ?: ($item['title'] ?? '')); ?>"
                                    <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                                >
                                    <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>