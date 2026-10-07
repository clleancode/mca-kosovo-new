<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-spotlight',
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

$layout      = Helpers\get_field_choice('spotlight_layout', ['purple', 'green'], 'purple');
$image       = get_field('spotlight_image');
$label       = get_field('spotlight_label');
$description = get_field('spotlight_description');
$tags        = array_slice(get_field('spotlight_tags') ?: [], 0, 3);

$links = [
    'primary'   => get_field('spotlight_primary_link'),
    'secondary' => get_field('spotlight_secondary_link'),
];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>
        id="<?php echo esc_attr($block['anchor']); ?>"
    <?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-spotlight m-spotlight--<?php echo esc_attr($layout); ?>">

            <div class="m-spotlight__image">
                <?php
                if ($image) {
                    echo wp_get_attachment_image($image, 'large', false, [
                        'loading'  => 'lazy',
                        'decoding' => 'async',
                    ]);
                }
                ?>
            </div>

            <div class="m-spotlight__content">

                <?php if ($label) : ?>
                    <div class="m-spotlight__label s-d-b-s s-m-b-xs">
                        <span class="a-spotlight-pill a-text a-text--xs h-semibold">
                            <?php echo esc_html($label); ?>
                        </span>
                    </div>
                <?php endif; ?>

                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo nl2br(esc_html(get_field('spotlight_title'))); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if ($description) : ?>
                    <div class="m-spotlight__description s-d-t-s s-d-b-s s-m-t-xs s-m-b-xs">
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo nl2br(esc_html($description)); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ($tags) : ?>
                    <ul class="m-spotlight__tags s-d-b-s s-m-b-xs">
                        <?php foreach ($tags as $row) : ?>
                            <?php if (empty($row['tag'])) continue; ?>
                            <li>
                                <span class="a-text a-text--xs h-semibold">
                                    <?php echo esc_html($row['tag']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="m-spotlight__buttons">
                    <?php foreach ($links as $type => $link) : ?>
                        <?php if (empty($link['url'])) continue; ?>
                        <a
                            class="h-semibold a-spotlight-button a-spotlight-button--<?php echo esc_attr($type); ?> a-text a-text--m"
                            href="<?php echo esc_url($link['url']); ?>"
                            target="<?php echo esc_attr(($link['target'] ?? '') ?: '_self'); ?>"
                            <?php if (($link['target'] ?? '') === '_blank') : ?>
                                rel="noopener noreferrer"
                            <?php endif; ?>
                        >
                            <?php echo esc_html($link['title'] ?? ''); ?>
                            <?php if ($type === 'primary') : ?>
                                <span aria-hidden="true">→</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>