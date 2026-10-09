<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-small-hero',
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

$page_id     = get_the_ID();
$image       = get_field('small_page_hero_image');
$description = get_field('small_page_hero_description');
$links       = array_slice(get_field('small_page_hero_links') ?: [], 0, 3);
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <?php if ($image) : ?>
        <div class="m-small-hero__image">
            <?php
            echo wp_get_attachment_image($image, 'full', false, [
                'loading'       => 'eager',
                'fetchpriority' => 'high',
                'decoding'      => 'async',
            ]);
            ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="m-small-hero">
            <div class="m-small-hero__content">
                <nav class="h-breadcrumbs s-d-b-xs s-m-b-xs" aria-label="Breadcrumb">
                    <ol>
                        <li>
                            <a class="a-text a-text--xs h-white" href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                        </li>

                        <?php foreach (array_reverse(get_post_ancestors($page_id)) as $parent_id) : ?>
                            <li>
                                <a class="a-text a-text--xs h-white" href="<?php echo esc_url(get_permalink($parent_id)); ?>">
                                    <?php echo esc_html(get_the_title($parent_id)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <li>
                            <span class="a-text a-text--xs" aria-current="page">
                                <?php echo esc_html(get_the_title($page_id)); ?>
                            </span>
                        </li>
                    </ol>
                </nav>

                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?> h-uppercase s-d-b-xs s-m-b-xs">
                    <?php echo esc_html(get_field('small_page_hero_title') ?: get_the_title($page_id)); ?>
                </<?php echo tag_escape($tag); ?>>

                <?php if ($description) : ?>
                    <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?>">
                        <?php echo nl2br(esc_html($description)); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($links) : ?>
                <div class="m-small-hero__links">
                    <?php foreach ($links as $row) : ?>
                        <?php
                        $link = $row['link'] ?? [];

                        if (empty($link['url'])) {
                            continue;
                        }

                        $target = $link['target'] ?? '';
                        ?>
                        <div class="m-small-hero__link-card">
                            <div class="m-small-hero__link-copy">
                                <h2 class="a-text a-text--xxl h-white h-semibold"><?php echo esc_html($link['title'] ?? ''); ?></h2>
                                <?php if (!empty($row['description'])) : ?>
                                    <p class="a-text a-text--m h-white h-regular"><?php echo esc_html($row['description']); ?></p>
                                <?php endif; ?>
                            </div>

                            <a
                                class="a-small-hero-link"
                                href="<?php echo esc_url($link['url']); ?>"
                                target="<?php echo esc_attr($target); ?>"
                                aria-label="<?php echo esc_attr($link['title'] ?? ''); ?>"
                                <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                            >
                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>