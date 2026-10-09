<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-procurement-resources',
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

$cards       = array_slice(get_field('procurement_resources_cards') ?: [], 0, 2);
$shortcode   = get_field('procurement_resources_shortcode');
$signup_link = get_field('procurement_resources_signup_link') ?: [];
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-procurement-resources">
            <div class="m-procurement-resources__heading s-d-b-s s-m-b-xs">
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html(get_field('procurement_resources_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <div class="m-procurement-resources__grid">
                <?php foreach ($cards as $index => $card) : ?>
                    <?php
                    $link   = $card['link'] ?? [];
                    $target = $link['target'] ?? '';
                    ?>
                    <article class="m-procurement-resources__card<?php echo $index === 0 ? ' m-procurement-resources__card--image' : ''; ?>">
                        <?php if (!empty($card['image'])) : ?>
                            <div class="m-procurement-resources__image">
                                <?php
                                echo wp_get_attachment_image($card['image'], 'large', false, [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                ]);
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="m-procurement-resources__copy">
                            <h3 class="a-heading a-heading--h6 h-white s-d-b-xs s-m-b-xs"><?php echo esc_html($card['title'] ?? ''); ?></h3>

                            <?php if (!empty($card['description'])) : ?>
                                <p class="a-text a-text--<?php echo esc_attr($text_size); ?> h-white h--<?php echo esc_attr($text_height); ?>">
                                    <?php echo nl2br(esc_html($card['description'])); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($link['url'])) : ?>
                                <div class="m-procurement-resources__action s-d-t-xs s-m-t-xs">
                                    <a
                                        class="a-btn <?php echo $index === 0 ? 'a-btn--white' : 'a-btn--border-white'; ?> a-btn--smallest"
                                        href="<?php echo esc_url($link['url']); ?>"
                                        target="<?php echo esc_attr($target); ?>"
                                        <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                                    >
                                        <span>
                                            <?php echo esc_html($link['title'] ?? ''); ?>
                                            <?php if ($index === 0) : ?>
                                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/small-right.svg')); ?>" alt="" aria-hidden="true">
                                            <?php else : ?>
                                                <span aria-hidden="true">&rarr;</span>
                                            <?php endif; ?>
                                        </span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>

                <div class="m-procurement-resources__signup">
                    <div class="m-procurement-resources__signup-copy">
                        <h3 class="a-heading a-heading--h6 h-dark-blue s-d-b-xs">
                            <?php echo esc_html(get_field('procurement_resources_signup_title')); ?>
                        </h3>
                        <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                            <?php echo esc_html(get_field('procurement_resources_signup_description')); ?>
                        </p>
                    </div>

                    <div class="m-procurement-resources__form">
                        <?php if ($shortcode) : ?>
                            <?php echo do_shortcode($shortcode); ?>
                        <?php else : ?>
                            <form action="<?php echo esc_url($signup_link['url'] ?? ''); ?>" method="get">
                                <input
                                    class="a-text a-text--s"
                                    type="email"
                                    name="email"
                                    placeholder="Email address"
                                    aria-label="Email address"
                                    autocomplete="email"
                                    required
                                >
                                <button class="a-btn a-btn--blue a-btn--smallest" type="submit">
                                    <span><?php echo esc_html($signup_link['title'] ?? ''); ?></span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>