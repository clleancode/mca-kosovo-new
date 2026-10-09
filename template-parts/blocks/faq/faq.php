<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-faq',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

$devices = [
    'mobile'  => 'm',
    'desktop' => 'd',
];

$sides = [
    'top'    => 't',
    'bottom' => 'b',
];

foreach ($devices as $device => $prefix) {
    foreach ($sides as $side => $suffix) {
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

$section_id     = sanitize_title(get_field('faq_section_id') ?: ($block['anchor'] ?? ''));
$accordion_name = wp_unique_id('faq-');
$items          = get_field('faq_items') ?: [];
$label          = get_field('faq_label');
$description    = get_field('faq_description');
$email          = get_field('faq_email');
$open_first     = get_field('faq_open_first');
?>
<section <?php if ($section_id): ?>id="<?php echo esc_attr($section_id); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-faq">

            <div class="m-faq__heading">
                <?php if ($label): ?>
                    <div>
                        <p class="a-badge h-dark-blue h-semibold">
                            <?php echo esc_html($label); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div>
                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                        <?php echo nl2br(esc_html(get_field('faq_title'))); ?>
                    </<?php echo tag_escape($tag); ?>>
                </div>

                <?php if ($description || $email): ?>
                    <div>
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo nl2br(esc_html($description)); ?>
                            <?php if ($email): ?>
                                <a class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?>"
                                   href="<?php echo esc_url('mailto:' . sanitize_email($email)); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="m-faq__items">
                <?php foreach ($items as $index => $item): ?>
                    <details class="m-faq__item"
                             name="<?php echo esc_attr($accordion_name); ?>"
                             <?php if ($open_first && $index === 0): ?>open<?php endif; ?>>
                        <summary>
                            <span class="a-text a-text--s h-semibold">
                                <?php echo esc_html(sprintf('%02d', $index + 1)); ?>
                            </span>
                            <span class="a-text a-text--xxl h-semibold">
                                <?php echo esc_html($item['question']); ?>
                            </span>
                            <span aria-hidden="true">
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/+.svg')); ?>" alt="">
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/-.svg')); ?>" alt="">
                            </span>
                        </summary>

                        <div class="m-faq__answer">
                            <p class="a-text a-text--<?php echo esc_attr($text_size); ?> h--<?php echo esc_attr($text_height); ?>">
                                <?php echo nl2br(esc_html($item['answer'])); ?>
                            </p>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>